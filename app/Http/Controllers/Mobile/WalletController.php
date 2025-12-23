<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\LinearService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    protected WalletService $walletService;
    protected LinearService $linearService;

    public function __construct(WalletService $walletService, LinearService $linearService)
    {
        $this->walletService = $walletService;
        $this->linearService = $linearService;
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
                    'uuid' => $transaction->uuid,
                    'title' => $this->getTransactionTitle($transaction),
                    'description' => $this->getTransactionDescription($transaction),
                    'type' => $transaction->transaction_category === Transaction::CATEGORY_CREDIT ? 'credit' : 'debit',
                    'amount' => (float) $transaction->amount,
                    'currency' => $transaction->currency ?? 'GHS',
                    'status' => $this->mapTransactionStatus($transaction->status),
                    'created_at' => $transaction->created_at->toISOString(),
                    'linear_issue_url' => $transaction->linear_issue_url ?? null,
                    'has_linear_issue' => !empty($transaction->linear_issue_url),
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

    /**
     * Report issue for a transaction (creates Linear ticket)
     *
     * @param Request $request
     * @param string $id Transaction UUID or ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function reportIssue(Request $request, $id)
    {
        try {
            $user = Auth::user();
            
            $transaction = Transaction::where('uuid', $id)
                ->orWhere('id', $id)
                ->first();

            if (!$transaction) {
                return response()->json([
                    'status' => false,
                    'message' => 'Transaction not found',
                ], 404);
            }

            // Verify transaction belongs to user
            if ($transaction->user_id != $user->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            // Check if Linear issue already exists
            if (!empty($transaction->linear_issue_url)) {
                return response()->json([
                    'status' => true,
                    'message' => 'Linear issue already exists',
                    'data' => [
                        'linear_issue_url' => $transaction->linear_issue_url,
                    ],
                ]);
            }

            // Get team ID directly from config
            $teamId = config('linear.team_id');
            
            if (empty($teamId)) {
                Log::error('Linear team ID not configured', [
                    'message' => 'LINEAR_TEAM_ID must be set in .env',
                ]);
                
                return response()->json([
                    'status' => false,
                    'message' => 'Linear team ID not configured. Please contact support.',
                ], 500);
            }

            // Validate that teamId is a UUID
            if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $teamId)) {
                Log::error('Invalid Linear team ID format', [
                    'team_id' => $teamId,
                ]);
                
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid team ID format. Please contact support.',
                ], 500);
            }

            // Get transaction reference
            $reference = $transaction->paystack_reference ?? $transaction->client_reference ?? 'N/A';
            
            // Build issue title
            if ($transaction->status === Transaction::STATUS_PENDING) {
                $issueTitle = "[{$reference}] Pending Transaction: " . ucfirst($transaction->transaction_type);
            } elseif ($transaction->status === Transaction::STATUS_FAILED) {
                $issueTitle = "[{$reference}] Failed Transaction: " . ucfirst($transaction->transaction_type);
            } else {
                $issueTitle = "[{$reference}] Transaction Issue: " . ucfirst($transaction->transaction_type) . " - " . ucfirst($transaction->status);
            }

            // Build description
            $description = "Transaction Issue Report\n\n";
            $description .= "**Transaction Details:**\n";
            $description .= "- Type: " . ucfirst($transaction->transaction_type) . "\n";
            $description .= "- Status: " . ucfirst($transaction->status) . "\n";
            $description .= "- Amount: GHS " . number_format($transaction->amount, 2) . "\n";
            $description .= "- Reference: " . ($transaction->paystack_reference ?? $transaction->client_reference) . "\n";
            $description .= "- Date: " . $transaction->created_at->format('M d, Y H:i') . "\n";
            $description .= "- User: " . $user->name . " (" . $user->email . ")\n";
            $description .= "- User ID: " . $user->id . "\n";
            $description .= "- Transaction ID: " . $transaction->id . "\n";
            $description .= "- Transaction UUID: " . $transaction->uuid . "\n\n";
            
            if ($transaction->gateway_response) {
                $description .= "**Gateway Response:**\n" . $transaction->gateway_response . "\n\n";
            }
            
            if ($transaction->metadata) {
                $description .= "**Metadata:**\n```json\n" . json_encode($transaction->metadata, JSON_PRETTY_PRINT) . "\n```\n\n";
            }

            $description .= "**Action Required:**\nPlease investigate this transaction issue and update the status accordingly.";

            // Prepare additional data
            $additionalData = [];
            
            // Set priority: 1 = Urgent, 2 = High, 3 = Normal, 4 = Low
            if ($transaction->status === Transaction::STATUS_FAILED) {
                $additionalData['priority'] = 2; // High priority for failed transactions
            } else {
                $additionalData['priority'] = 3; // Normal priority for pending transactions
            }

            // Create Linear issue using GraphQL API
            $issue = $this->linearService->createIssue(
                $teamId,
                $issueTitle,
                $description,
                $additionalData
            );

            // Save Linear issue URL to transaction
            $transaction->linear_issue_url = $issue['url'];
            $transaction->save();

            Log::info('Linear issue created for transaction', [
                'transaction_id' => $transaction->id,
                'user_id' => $user->id,
                'linear_issue_id' => $issue['id'],
                'linear_issue_identifier' => $issue['identifier'],
                'linear_issue_url' => $issue['url'],
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Issue reported successfully. Our team has been notified and will investigate shortly.',
                'data' => [
                    'linear_issue_url' => $issue['url'],
                    'linear_issue_id' => $issue['id'],
                    'linear_issue_identifier' => $issue['identifier'],
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create Linear issue', [
                'transaction_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to report issue: ' . $e->getMessage(),
            ], 500);
        }
    }
}

