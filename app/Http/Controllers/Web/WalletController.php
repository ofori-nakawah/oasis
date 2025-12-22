<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\TopupRequest;
use App\Http\Requests\WithdrawRequest;
use App\Models\Transaction;
use App\Services\LinearService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WalletController extends Controller
{
    protected WalletService $walletService;
    protected LinearService $linearService;

    public function __construct(WalletService $walletService, LinearService $linearService)
    {
        $this->middleware('auth:web');
        $this->walletService = $walletService;
        $this->linearService = $linearService;
    }

    /**
     * Display the wallet page with balance and transactions.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $balance = $this->walletService->getBalance($user);
        $transactions = $this->walletService->getUserTransactions($user);

        return view('wallet.index', compact('balance', 'transactions'));
    }

    /**
     * Initiate a wallet topup.
     *
     * @param TopupRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function topup(TopupRequest $request)
    {
        try {
            $user = Auth::user();
            $amount = (float) $request->input('amount');

            $result = $this->walletService->topup($user, $amount);

            return response()->json([
                'status' => true,
                'message' => 'Topup initiated successfully',
                'data' => $result['data'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Process a withdrawal request.
     *
     * @param WithdrawRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function withdraw(WithdrawRequest $request)
    {
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
                'message' => $result['message'],
                'data' => $result['data'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get filtered transactions (AJAX endpoint).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function transactions(Request $request)
    {
        $user = Auth::user();
        
        $filters = [
            'type' => $request->input('type'),
            'category' => $request->input('category'),
            'status' => $request->input('status'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
        ];

        // Remove empty filters
        $filters = array_filter($filters, function ($value) {
            return !empty($value);
        });

        $transactions = $this->walletService->getUserTransactions($user, $filters);

        return response()->json([
            'status' => true,
            'data' => $transactions->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'uuid' => $transaction->uuid,
                    'type' => $transaction->transaction_type,
                    'category' => $transaction->transaction_category,
                    'amount' => $transaction->amount,
                    'formatted_amount' => $transaction->formatted_amount,
                    'status' => $transaction->status,
                    'description' => $this->getTransactionDescription($transaction),
                    'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                    'created_at_formatted' => $transaction->created_at->format('M d, Y H:i'),
                ];
            }),
        ]);
    }

    /**
     * Get transaction description based on type.
     *
     * @param \App\Models\Transaction $transaction
     * @return string
     */
    protected function getTransactionDescription($transaction): string
    {
        $type = $transaction->transaction_type;
        
        return match ($type) {
            'topup' => 'Wallet Topup',
            'withdrawal' => 'Withdrawal to Bank Account',
            'payment' => 'Payment for Service',
            'earning' => 'Earning from Completed Work',
            'refund' => 'Refund',
            default => 'Transaction',
        };
    }

    /**
     * Get transaction details for modal display.
     *
     * @param Request $request
     * @param string $id Transaction UUID or ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTransactionDetails(Request $request, $id)
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

            return response()->json([
                'status' => true,
                'data' => [
                    'id' => $transaction->id,
                    'uuid' => $transaction->uuid,
                    'type' => $transaction->transaction_type,
                    'category' => $transaction->transaction_category,
                    'amount' => $transaction->amount,
                    'formatted_amount' => $transaction->formatted_amount,
                    'status' => $transaction->status,
                    'description' => $this->getTransactionDescription($transaction),
                    'reference' => $transaction->paystack_reference ?? $transaction->client_reference,
                    'currency' => $transaction->currency ?? 'GHS',
                    'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                    'created_at_formatted' => $transaction->created_at->format('M d, Y H:i'),
                    'paid_at' => $transaction->paid_at ? $transaction->paid_at->format('M d, Y H:i') : null,
                    'gateway_response' => $transaction->gateway_response,
                    'channel' => $transaction->channel,
                    'metadata' => $transaction->metadata,
                    'has_linear_issue' => !empty($transaction->linear_issue_url),
                    'linear_issue_url' => $transaction->linear_issue_url,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get transaction details', [
                'transaction_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve transaction details',
            ], 500);
        }
    }

    /**
     * Create a Linear issue for a transaction.
     *
     * @param Request $request
     * @param string $id Transaction UUID or ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function createLinearIssue(Request $request, $id)
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

            // Get team ID directly from config - should be set in .env after first lookup
            $teamId = config('linear.team_id');
            
            if (empty($teamId)) {
                Log::error('Linear team ID not configured', [
                    'message' => 'LINEAR_TEAM_ID must be set in .env. Run the team lookup once to get the ID, then set it in .env',
                ]);
                
                return response()->json([
                    'status' => false,
                    'message' => 'Linear team ID not configured. Please set LINEAR_TEAM_ID in your .env file. Use the team lookup query to get your team ID first.',
                ], 500);
            }

            // Validate that teamId is a UUID (Linear requires UUID format)
            if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $teamId)) {
                Log::error('Invalid Linear team ID format', [
                    'team_id' => $teamId,
                ]);
                
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid team ID format. Team ID must be a UUID. Please check your LINEAR_TEAM_ID in .env',
                ], 500);
            }

            // Get transaction reference
            $reference = $transaction->paystack_reference ?? $transaction->client_reference ?? 'N/A';
            
            // Build issue title - start with transaction reference
            if ($transaction->status === 'pending') {
                $issueTitle = "[{$reference}] Pending Transaction: " . ucfirst($transaction->transaction_type);
            } elseif ($transaction->status === 'failed') {
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
            if ($transaction->status === 'failed') {
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
                'message' => 'Linear issue created successfully',
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
                'message' => 'Failed to create Linear issue: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Build transaction details string for Linear issue description.
     *
     * @param Transaction $transaction
     * @return string
     */
    protected function buildTransactionDetailsForLinear(Transaction $transaction): string
    {
        $details = [];
        $details[] = "Transaction Type: " . ucfirst($transaction->transaction_type);
        $details[] = "Status: " . ucfirst($transaction->status);
        $details[] = "Amount: GHS " . number_format($transaction->amount, 2);
        $details[] = "Reference: " . ($transaction->paystack_reference ?? $transaction->client_reference);
        $details[] = "Date: " . $transaction->created_at->format('M d, Y H:i');
        
        return implode("\n", $details);
    }
}
