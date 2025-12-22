<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WalletService
{
    protected PaystackService $paystackService;

    public function __construct(PaystackService $paystackService)
    {
        $this->paystackService = $paystackService;
    }

    /**
     * Get user's current balance.
     *
     * @param User $user
     * @return float
     */
    public function getBalance(User $user): float
    {
        return (float) $user->available_balance;
    }

    /**
     * Initiate a wallet topup.
     *
     * @param User $user
     * @param float $amount
     * @return array
     * @throws \Exception
     */
    public function topup(User $user, float $amount): array
    {
        // Validate minimum amount
        if ($amount < 1) {
            throw new \Exception('Minimum topup amount is 1 GHS');
        }

        // Get user email
        if (!$user->email) {
            throw new \Exception('User email is required for topup');
        }

        // Initialize transaction with Paystack
        $paymentData = [
            'email' => $user->email,
            'amount' => (int) ($amount * 100), // Convert to pesewas
            'currency' => 'GHS',
            'user_id' => $user->id,
            'metadata' => [
                'transaction_type' => 'topup',
                'user_id' => $user->id,
            ],
        ];

        $result = $this->paystackService->initializeTransaction($paymentData);

        // Update transaction with wallet-specific fields
        if (isset($result['data']['reference'])) {
            $transaction = Transaction::where('paystack_reference', $result['data']['reference'])
                ->orWhere('client_reference', $result['data']['reference'])
                ->first();

            if ($transaction) {
                $transaction->update([
                    'transaction_type' => Transaction::TYPE_TOPUP,
                    'transaction_category' => Transaction::CATEGORY_CREDIT,
                ]);
            }
        }

        Log::info('Wallet topup initiated', [
            'user_id' => $user->id,
            'amount' => $amount,
            'reference' => $result['data']['reference'] ?? null,
        ]);

        return $result;
    }

    /**
     * Process a withdrawal request.
     *
     * @param User $user
     * @param float $amount
     * @param array $bankDetails
     * @return array
     * @throws \Exception
     */
    public function withdraw(User $user, float $amount, array $bankDetails): array
    {
        // Validate minimum amount
        $minWithdrawal = config('wallet.minimum_withdrawal', 10);
        if ($amount < $minWithdrawal) {
            throw new \Exception("Minimum withdrawal amount is {$minWithdrawal} GHS");
        }

        // Validate sufficient balance
        $currentBalance = $this->getBalance($user);
        if ($amount > $currentBalance) {
            throw new \Exception('Insufficient balance');
        }

        // Validate bank details
        $requiredFields = ['account_number', 'bank_code', 'account_name'];
        foreach ($requiredFields as $field) {
            if (empty($bankDetails[$field])) {
                throw new \Exception("Bank detail '{$field}' is required");
            }
        }

        // Create transaction record first
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'uuid' => Str::uuid()->toString(),
            'client_reference' => 'WTH-' . Str::random(12),
            'amount' => $amount,
            'currency' => 'GHS',
            'email' => $user->email,
            'status' => Transaction::STATUS_PENDING,
            'transaction_type' => Transaction::TYPE_WITHDRAWAL,
            'transaction_category' => Transaction::CATEGORY_DEBIT,
            'bank_account_details' => $bankDetails,
        ]);

        // Decrement balance immediately (hold the funds)
        $this->updateBalance($user, -$amount, 'withdrawal_hold');

        // Create transfer recipient
        try {
            $recipientData = [
                'type' => 'nuban',
                'name' => $bankDetails['account_name'],
                'account_number' => $bankDetails['account_number'],
                'bank_code' => $bankDetails['bank_code'],
                'currency' => 'GHS',
            ];

            $recipientResult = $this->paystackService->createTransferRecipient($recipientData);

            if (!($recipientResult['status'] ?? false)) {
                // Restore balance if recipient creation fails
                $this->updateBalance($user, $amount, 'withdrawal_hold_reversal');
                $transaction->update(['status' => Transaction::STATUS_FAILED]);
                throw new \Exception($recipientResult['message'] ?? 'Failed to create transfer recipient');
            }

            $recipientCode = $recipientResult['data']['recipient_code'] ?? null;
            $transaction->update(['recipient_code' => $recipientCode]);

            // Initiate transfer
            $transferData = [
                'source' => 'balance',
                'amount' => (int) ($amount * 100), // Convert to pesewas
                'recipient' => $recipientCode,
                'reason' => 'Wallet withdrawal',
                'reference' => $transaction->client_reference,
            ];

            $transferResult = $this->paystackService->initiateTransfer($transferData);

            if (!($transferResult['status'] ?? false)) {
                // Restore balance if transfer initiation fails
                $this->updateBalance($user, $amount, 'withdrawal_hold_reversal');
                $transaction->update(['status' => Transaction::STATUS_FAILED]);
                throw new \Exception($transferResult['message'] ?? 'Failed to initiate transfer');
            }

            // Update transaction with transfer reference
            $transferCode = $transferResult['data']['transfer_code'] ?? null;
            $transaction->update([
                'paystack_reference' => $transferCode,
                'metadata' => [
                    'transfer_code' => $transferCode,
                    'recipient_code' => $recipientCode,
                ],
            ]);

            // Update user's total_payouts
            $user->increment('total_payouts', $amount);

            Log::info('Wallet withdrawal initiated', [
                'user_id' => $user->id,
                'amount' => $amount,
                'transaction_id' => $transaction->id,
                'transfer_code' => $transferCode,
            ]);

            return [
                'status' => true,
                'message' => 'Withdrawal request submitted successfully',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'transfer_code' => $transferCode,
                    'amount' => $amount,
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Wallet withdrawal failed', [
                'user_id' => $user->id,
                'amount' => $amount,
                'transaction_id' => $transaction->id ?? null,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Update user balance.
     *
     * @param User $user
     * @param float $amount (positive for credit, negative for debit)
     * @param string $type
     * @return void
     */
    public function updateBalance(User $user, float $amount, string $type = 'transaction'): void
    {
        DB::transaction(function () use ($user, $amount, $type) {
            // Lock the user row to prevent race conditions
            $user = User::lockForUpdate()->find($user->id);

            $oldBalance = (float) $user->available_balance;
            $newBalance = $oldBalance + $amount;

            // Ensure balance doesn't go negative (unless it's a withdrawal hold)
            if ($newBalance < 0 && $type !== 'withdrawal_hold') {
                throw new \Exception('Insufficient balance');
            }

            $user->available_balance = max(0, $newBalance);
            $user->save();

            Log::info('Balance updated', [
                'user_id' => $user->id,
                'type' => $type,
                'amount' => $amount,
                'old_balance' => $oldBalance,
                'new_balance' => $user->available_balance,
            ]);
        });
    }

    /**
     * Get user transactions with optional filters.
     *
     * @param User $user
     * @param array $filters
     * @return Collection
     */
    public function getUserTransactions(User $user, array $filters = []): Collection
    {
        $query = Transaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        // Filter by transaction type
        if (!empty($filters['type'])) {
            $query->where('transaction_type', $filters['type']);
        }

        // Filter by transaction category
        if (!empty($filters['category'])) {
            $query->where('transaction_category', $filters['category']);
        }

        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by date range
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->get();
    }
}

