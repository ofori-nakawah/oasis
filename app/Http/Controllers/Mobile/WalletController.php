<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Get wallet balance (Mobile endpoint)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBalance()
    {
        try {
            $user = Auth::user();
            $balance = $this->walletService->getBalance($user);

            return response()->json([
                'status' => true,
                'data' => [
                    'balance' => (float) $balance,
                    'currency' => 'GHS',
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get wallet balance', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch wallet balance',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get wallet transactions with cursor pagination (Mobile endpoint)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTransactions(Request $request)
    {
        try {
            $user = Auth::user();
            $limit = (int) $request->input('limit', 20);
            $cursor = $request->input('cursor');

            // Validate limit
            if ($limit < 1 || $limit > 100) {
                $limit = 20;
            }

            // Build query
            $query = Transaction::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc');

            // Apply cursor if provided
            if ($cursor) {
                // Decode cursor (format: "id:timestamp")
                $parts = explode(':', base64_decode($cursor));
                if (count($parts) === 2) {
                    $cursorId = (int) $parts[0];
                    $cursorTimestamp = $parts[1];
                    $query->where(function ($q) use ($cursorId, $cursorTimestamp) {
                        $q->where('created_at', '<', $cursorTimestamp)
                            ->orWhere(function ($q2) use ($cursorId, $cursorTimestamp) {
                                $q2->where('created_at', '=', $cursorTimestamp)
                                    ->where('id', '<', $cursorId);
                            });
                    });
                }
            }

            // Get transactions
            $transactions = $query->limit($limit + 1)->get();

            // Check if there are more transactions
            $hasMore = $transactions->count() > $limit;
            if ($hasMore) {
                $transactions = $transactions->take($limit);
            }

            // Generate next cursor
            $nextCursor = null;
            if ($hasMore && $transactions->count() > 0) {
                $lastTransaction = $transactions->last();
                $nextCursor = base64_encode($lastTransaction->id . ':' . $lastTransaction->created_at->toDateTimeString());
            }

            // Format transactions for mobile
            $formattedTransactions = $transactions->map(function ($transaction) {
                return [
                    'id' => (string) $transaction->id,
                    'title' => $this->getTransactionTitle($transaction),
                    'description' => $this->getTransactionDescription($transaction),
                    'type' => $transaction->transaction_category === Transaction::CATEGORY_CREDIT ? 'credit' : 'debit',
                    'amount' => (float) $transaction->amount,
                    'currency' => $transaction->currency ?? 'GHS',
                    'status' => $this->mapTransactionStatus($transaction->status),
                    'created_at' => $transaction->created_at->toISOString(),
                ];
            });

            return response()->json([
                'status' => true,
                'data' => [
                    'data' => $formattedTransactions,
                    'next_cursor' => $nextCursor,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get wallet transactions', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch transactions',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Initiate wallet top-up (Mobile endpoint)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function initiateTopUp(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'amount' => 'nullable|numeric|min:1',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validation->errors(),
            ], 422);
        }

        try {
            $user = Auth::user();
            $amount = $request->input('amount') ? (float) $request->input('amount') : null;

            // If amount not provided, we'll let user select in payment flow
            // For now, we'll require it
            if (!$amount) {
                return response()->json([
                    'status' => false,
                    'message' => 'Amount is required',
                ], 422);
            }

            $result = $this->walletService->topup($user, $amount);

            // Extract payment URL from result
            $paymentUrl = $result['data']['authorization_url'] ?? null;
            $reference = $result['data']['reference'] ?? null;

            if (!$paymentUrl) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to generate payment URL',
                ], 500);
            }

            return response()->json([
                'status' => true,
                'message' => 'Top-up initiated successfully',
                'data' => [
                    'redirect_url' => $paymentUrl,
                    'payment_url' => $paymentUrl,
                    'authorization_url' => $paymentUrl,
                    'reference' => $reference,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to initiate top-up', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Initiate withdrawal (Mobile endpoint)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function withdraw(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'account_number' => 'required|string',
            'bank_code' => 'required|string',
            'account_name' => 'required|string',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validation->errors(),
            ], 422);
        }

        try {
            $user = Auth::user();
            $amount = (float) $request->input('amount');

            $bankDetails = [
                'account_number' => $request->input('account_number'),
                'bank_code' => $request->input('bank_code'),
                'account_name' => $request->input('account_name'),
            ];

            $result = $this->walletService->withdraw($user, $amount, $bankDetails);

            return response()->json([
                'status' => true,
                'message' => $result['message'] ?? 'Withdrawal request submitted successfully',
                'data' => $result['data'] ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to initiate withdrawal', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get transaction title based on type
     *
     * @param Transaction $transaction
     * @return string
     */
    protected function getTransactionTitle(Transaction $transaction): string
    {
        return match ($transaction->transaction_type) {
            Transaction::TYPE_TOPUP => 'Wallet Top-up',
            Transaction::TYPE_WITHDRAWAL => 'Withdrawal',
            Transaction::TYPE_PAYMENT => 'Payment',
            Transaction::TYPE_EARNING => 'Earning',
            Transaction::TYPE_REFUND => 'Refund',
            default => 'Transaction',
        };
    }

    /**
     * Get transaction description
     *
     * @param Transaction $transaction
     * @return string|null
     */
    protected function getTransactionDescription(Transaction $transaction): ?string
    {
        if ($transaction->transaction_type === Transaction::TYPE_PAYMENT) {
            return 'Payment for service';
        } elseif ($transaction->transaction_type === Transaction::TYPE_EARNING) {
            return 'Earning from completed work';
        } elseif ($transaction->transaction_type === Transaction::TYPE_WITHDRAWAL) {
            return 'Withdrawal to bank account';
        } elseif ($transaction->transaction_type === Transaction::TYPE_TOPUP) {
            return 'Wallet top-up';
        } elseif ($transaction->transaction_type === Transaction::TYPE_REFUND) {
            return 'Refund';
        }

        return null;
    }

    /**
     * Map transaction status to mobile format
     *
     * @param string $status
     * @return string
     */
    protected function mapTransactionStatus(string $status): string
    {
        return match ($status) {
            Transaction::STATUS_SUCCESS => 'success',
            Transaction::STATUS_PENDING => 'pending',
            Transaction::STATUS_FAILED => 'failed',
            default => 'pending',
        };
    }
}

