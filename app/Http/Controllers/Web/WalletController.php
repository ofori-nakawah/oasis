<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\TopupRequest;
use App\Http\Requests\WithdrawRequest;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->middleware('auth:web');
        $this->walletService = $walletService;
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
                    'created_at_formatted' => $transaction->created_at->format('M d, Y'),
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
}
