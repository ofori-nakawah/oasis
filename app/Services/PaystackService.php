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
        $this->secretKey = config('services.paystack.secret_key');
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
        $existingEvent = \App\Models\TransactionEvent::where('paystack_event_id', $paystackEventId)->first();
        if ($existingEvent) {
            Log::info('Webhook event already processed', ['event_id' => $paystackEventId]);
            return;
        }

        // Create transaction event
        $event = \App\Models\TransactionEvent::create([
            'transaction_id' => $transaction->id,
            'event_type' => $eventType,
            'paystack_event_id' => $paystackEventId,
            'payload' => $eventData,
            'processed' => false,
        ]);

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

        // Mark event as processed
        $event->markAsProcessed();

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
            default => null,
        };
    }
}

