<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaystackService
{
    protected string $baseUrl = 'https://api.paystack.co';
    protected string $secretKey;

    public function __construct()
    {
        $secretKey = config('services.paystack.secret_key');
        
        if (empty($secretKey)) {
            throw new \RuntimeException(
                'Paystack secret key is not configured. Please set PAYSTACK_SECRET_KEY in your .env file.'
            );
        }
        
        $this->secretKey = $secretKey;
    }

    /**
     * Initialize a transaction with Paystack.
     *
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function initializeTransaction(array $data): array
    {
        $reference = $data['reference'] ?? Str::uuid()->toString();
        
        $payload = [
            'email' => $data['email'],
            'amount' => $data['amount'], // Amount in pesewas/smallest currency unit
            'reference' => $reference,
            'currency' => $data['currency'] ?? 'GHS',
        ];

        // Add channels if provided
        if (isset($data['channel'])) {
            $payload['channels'] = is_array($data['channel']) ? $data['channel'] : [$data['channel']];
        }

        // Add metadata if provided
        if (isset($data['metadata'])) {
            $payload['metadata'] = $data['metadata'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/transaction/initialize", $payload);

            $responseData = $response->json();

            if (!$response->successful() || !($responseData['status'] ?? false)) {
                Log::error('Paystack API Error', [
                    'status' => $response->status(),
                    'response' => $responseData,
                ]);

                throw new \Exception($responseData['message'] ?? 'Failed to initialize payment');
            }

            // Create transaction record
            // Amount is stored in main currency unit (divide by 100 to convert from pesewas to GHS)
            $amount = is_numeric($data['amount']) ? ($data['amount'] / 100) : $data['amount'];
            $authorizationUrl = $responseData['data']['authorization_url'] ?? null;
            
            $transaction = Transaction::create([
                'user_id' => $data['user_id'] ?? null,
                'uuid' => Str::uuid()->toString(),
                'client_reference' => $reference,
                'paystack_reference' => $responseData['data']['reference'] ?? null,
                'amount' => $amount,
                'currency' => $payload['currency'],
                'email' => $data['email'],
                'pay_link_url' => $authorizationUrl, // Legacy field - use same as authorization_url
                'authorization_url' => $authorizationUrl,
                'access_code' => $responseData['data']['access_code'] ?? null,
                'status' => Transaction::STATUS_PENDING,
                'channel' => $data['channel'] ?? null,
                'metadata' => $data['metadata'] ?? null,
            ]);

            Log::info('Payment initialized', [
                'transaction_id' => $transaction->id,
                'reference' => $reference,
            ]);

            return [
                'status' => true,
                'message' => 'Payment initialized successfully',
                'data' => [
                    'authorization_url' => $responseData['data']['authorization_url'],
                    'access_code' => $responseData['data']['access_code'],
                    'reference' => $responseData['data']['reference'],
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Paystack Service Error', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            throw $e;
        }
    }

    /**
     * Verify a transaction with Paystack.
     *
     * @param string $reference
     * @return array
     * @throws \Exception
     */
    public function verifyTransaction(string $reference): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->get("{$this->baseUrl}/transaction/verify/{$reference}");

            $responseData = $response->json();

            if (!$response->successful() || !($responseData['status'] ?? false)) {
                throw new \Exception($responseData['message'] ?? 'Failed to verify transaction');
            }

            return $responseData;
        } catch (\Exception $e) {
            Log::error('Paystack Verification Error', [
                'error' => $e->getMessage(),
                'reference' => $reference,
            ]);

            throw $e;
        }
    }

    /**
     * Verify webhook signature.
     *
     * @param string $payload
     * @param string $signature
     * @return bool
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        $computedSignature = hash_hmac('sha512', $payload, $this->secretKey);

        return hash_equals($computedSignature, $signature);
    }

    /**
     * Process webhook event.
     *
     * @param array $eventData
     * @return void
     */
    public function processWebhookEvent(array $eventData): void
    {
        $eventType = $eventData['event'] ?? '';
        $paystackEventId = $eventData['id'] ?? '';
        $data = $eventData['data'] ?? [];

        // Find transaction by reference
        $reference = $data['reference'] ?? null;
        if (!$reference) {
            Log::warning('Webhook event missing reference', ['event' => $eventData]);
            return;
        }

        $transaction = Transaction::where('paystack_reference', $reference)
            ->orWhere('client_reference', $reference)
            ->first();

        if (!$transaction) {
            Log::warning('Transaction not found for webhook', ['reference' => $reference]);
            return;
        }

        // Check if event already processed (idempotency)
        // Only check if event_id is not empty (some webhooks may not have event_id)
        if (!empty($paystackEventId)) {
            $existingEvent = \App\Models\TransactionEvent::where('paystack_event_id', $paystackEventId)->first();
            if ($existingEvent && $existingEvent->processed) {
                Log::info('Webhook event already processed', ['event_id' => $paystackEventId]);
                return;
            }
        }

        // Create transaction event (only if event_id exists, or create with unique identifier)
        $event = null;
        if (!empty($paystackEventId)) {
            $event = \App\Models\TransactionEvent::firstOrCreate(
                ['paystack_event_id' => $paystackEventId],
                [
                    'transaction_id' => $transaction->id,
                    'event_type' => $eventType,
                    'payload' => $eventData,
                    'processed' => false,
                ]
            );
        } else {
            // If no event_id, create with reference + timestamp as unique identifier
            $uniqueId = $reference . '_' . time();
            $event = \App\Models\TransactionEvent::firstOrCreate(
                ['paystack_event_id' => $uniqueId],
                [
                    'transaction_id' => $transaction->id,
                    'event_type' => $eventType,
                    'payload' => $eventData,
                    'processed' => false,
                ]
            );
        }

        // Update transaction status based on event type
        $status = $this->mapEventTypeToStatus($eventType);
        if ($status) {
            $transaction->update([
                'status' => $status,
                'last_webhook_event' => $eventData,
                'paid_at' => $status === Transaction::STATUS_SUCCESS ? now() : null,
                'gateway_response' => $data['gateway_response'] ?? null,
                'channel' => $data['channel'] ?? $transaction->channel,
                'customer_data' => $data['customer'] ?? null,
            ]);
        }

        // Handle P2P payment success if this is a P2P transaction
        // Process even if event was already seen (in case previous processing failed)
        if ($status === Transaction::STATUS_SUCCESS) {
            $metadata = $transaction->metadata ?? [];
            $paymentType = $metadata['payment_type'] ?? null;
            
            if (in_array($paymentType, ['initial', 'final'])) {
                try {
                    $p2pPaymentService = app(\App\Services\P2PPaymentService::class);
                    $p2pPaymentService->handlePaymentSuccess($reference, $paymentType);
                    
                    Log::info('P2P payment processed via webhook', [
                        'transaction_id' => $transaction->id,
                        'payment_type' => $paymentType,
                        'reference' => $reference,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to process P2P payment via webhook', [
                        'transaction_id' => $transaction->id,
                        'payment_type' => $paymentType,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    // Don't throw - webhook should still be marked as processed
                }
            }
        }

        // Handle wallet transactions (topup and withdrawal)
        if ($transaction->user_id && in_array($transaction->transaction_type, ['topup', 'withdrawal'])) {
            try {
                $walletService = app(\App\Services\WalletService::class);
                $user = \App\Models\User::find($transaction->user_id);

                if (!$user) {
                    Log::warning('User not found for wallet transaction', [
                        'transaction_id' => $transaction->id,
                        'user_id' => $transaction->user_id,
                    ]);
                } else {
                    if ($transaction->transaction_type === 'topup') {
                        // Handle topup success/failure
                        if ($status === Transaction::STATUS_SUCCESS) {
                            // Update balance and total_topups
                            $walletService->updateBalance($user, $transaction->amount, 'topup');
                            $user->increment('total_topups', $transaction->amount);
                            
                            Log::info('Wallet topup processed via webhook', [
                                'transaction_id' => $transaction->id,
                                'user_id' => $user->id,
                                'amount' => $transaction->amount,
                            ]);
                        }
                    } elseif ($transaction->transaction_type === 'withdrawal') {
                        // Handle withdrawal success/failure
                        if ($status === Transaction::STATUS_SUCCESS) {
                            // Balance was already decremented when withdrawal was initiated
                            // Just log the success
                            Log::info('Wallet withdrawal processed via webhook', [
                                'transaction_id' => $transaction->id,
                                'user_id' => $user->id,
                                'amount' => $transaction->amount,
                            ]);
                        } elseif ($status === Transaction::STATUS_FAILED) {
                            // Restore balance if withdrawal failed
                            $walletService->updateBalance($user, $transaction->amount, 'withdrawal_failed_reversal');
                            
                            Log::info('Wallet withdrawal failed, balance restored', [
                                'transaction_id' => $transaction->id,
                                'user_id' => $user->id,
                                'amount' => $transaction->amount,
                            ]);
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('Failed to process wallet transaction via webhook', [
                    'transaction_id' => $transaction->id,
                    'transaction_type' => $transaction->transaction_type,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                // Don't throw - webhook should still be marked as processed
            }
        }

        // Mark event as processed (only if event was created/found)
        if ($event && method_exists($event, 'markAsProcessed')) {
            $event->markAsProcessed();
        } elseif ($event) {
            $event->update(['processed' => true, 'processed_at' => now()]);
        }

        Log::info('Webhook event processed', [
            'event_id' => $paystackEventId,
            'transaction_id' => $transaction->id,
            'status' => $status,
        ]);
    }

    /**
     * Map Paystack event type to transaction status.
     *
     * @param string $eventType
     * @return string|null
     */
    protected function mapEventTypeToStatus(string $eventType): ?string
    {
        return match ($eventType) {
            'charge.success' => Transaction::STATUS_SUCCESS,
            'charge.failed' => Transaction::STATUS_FAILED,
            'transfer.success' => Transaction::STATUS_SUCCESS,
            'transfer.failed' => Transaction::STATUS_FAILED,
            'transfer.reversed' => Transaction::STATUS_REVERSED,
            default => null,
        };
    }

    /**
     * Create a transfer recipient.
     *
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function createTransferRecipient(array $data): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/transferrecipient", $data);

            $responseData = $response->json();

            if (!$response->successful() || !($responseData['status'] ?? false)) {
                Log::error('Paystack Create Recipient Error', [
                    'status' => $response->status(),
                    'response' => $responseData,
                ]);

                throw new \Exception($responseData['message'] ?? 'Failed to create transfer recipient');
            }

            Log::info('Transfer recipient created', [
                'recipient_code' => $responseData['data']['recipient_code'] ?? null,
            ]);

            return $responseData;
        } catch (\Exception $e) {
            Log::error('Paystack Create Recipient Service Error', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            throw $e;
        }
    }

    /**
     * Initiate a transfer.
     *
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function initiateTransfer(array $data): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/transfer", $data);

            $responseData = $response->json();

            if (!$response->successful() || !($responseData['status'] ?? false)) {
                Log::error('Paystack Transfer Error', [
                    'status' => $response->status(),
                    'response' => $responseData,
                ]);

                throw new \Exception($responseData['message'] ?? 'Failed to initiate transfer');
            }

            Log::info('Transfer initiated', [
                'transfer_code' => $responseData['data']['transfer_code'] ?? null,
                'reference' => $data['reference'] ?? null,
            ]);

            return $responseData;
        } catch (\Exception $e) {
            Log::error('Paystack Transfer Service Error', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            throw $e;
        }
    }

    /**
     * Verify a transfer.
     *
     * @param string $transferCode
     * @return array
     * @throws \Exception
     */
    public function verifyTransfer(string $transferCode): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->get("{$this->baseUrl}/transfer/{$transferCode}");

            $responseData = $response->json();

            if (!$response->successful() || !($responseData['status'] ?? false)) {
                throw new \Exception($responseData['message'] ?? 'Failed to verify transfer');
            }

            return $responseData;
        } catch (\Exception $e) {
            Log::error('Paystack Transfer Verification Error', [
                'error' => $e->getMessage(),
                'transfer_code' => $transferCode,
            ]);

            throw $e;
        }
    }
}

