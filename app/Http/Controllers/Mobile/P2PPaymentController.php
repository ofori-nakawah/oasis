<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\Post;
use App\Models\Transaction;
use App\Services\P2PPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class P2PPaymentController extends Controller
{
    protected P2PPaymentService $p2pPaymentService;

    public function __construct(P2PPaymentService $p2pPaymentService)
    {
        $this->p2pPaymentService = $p2pPaymentService;
    }

    /**
     * Initiate payment for quote approval (Mobile endpoint)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function initiateQuoteApprovalPayment(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'post_id' => 'required',
            'application_id' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validation->errors(),
            ], 422);
        }

        try {
            $post = Post::find($request->post_id);
            if (!$post) {
                throw new \Exception('Post not found');
            }

            // Verify user owns the post (use loose comparison to handle string/int mismatch)
            if ((string)$post->user_id !== (string)Auth::id()) {
                throw new \Exception('Unauthorized: You can only approve quotes for your own posts');
            }

            $application = JobApplication::find($request->application_id);
            if (!$application) {
                throw new \Exception('Application not found');
            }

            // Verify application belongs to post
            if ($application->post_id !== $post->id) {
                throw new \Exception('Application does not belong to this post');
            }

            // Check if quote is already approved
            if ($application->isQuoteApproved()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Quote has already been approved',
                ], 400);
            }

            // Initiate payment
            $result = $this->p2pPaymentService->initiateQuoteApprovalPayment($post, $application);

            // If payment is skipped (percentage is 0)
            if (isset($result['skip_payment']) && $result['skip_payment']) {
                // Approve quote without payment
                $application->quote_approved_at = now();
                $application->quote_approved_by = Auth::id();
                $application->status = 'confirmed';
                $application->save();

                $post->is_job_applicant_confirmed = '1';
                $post->confirmed_applicant_id = $application->user_id;
                $post->payment_status = 'initial_paid';
                $post->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Quote approved successfully (no payment required)',
                    'skip_payment' => true,
                ], 200);
            }

            // Return authorization URL for mobile app to display in WebView
            return response()->json([
                'status' => true,
                'message' => 'Payment initialized successfully',
                'data' => [
                    'authorization_url' => $result['data']['authorization_url'] ?? null,
                    'reference' => $result['data']['reference'] ?? null,
                    'access_code' => $result['data']['access_code'] ?? null,
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Mobile P2P Quote Approval Payment Initiation Failed', [
                'error' => $e->getMessage(),
                'post_id' => $request->post_id ?? null,
                'application_id' => $request->application_id ?? null,
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to initiate payment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Initiate payment for job closure (Mobile endpoint)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function initiateJobClosurePayment(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'post_id' => 'required',
            'application_id' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validation->errors(),
            ], 422);
        }

        try {
            $post = Post::find($request->post_id);
            if (!$post) {
                throw new \Exception('Post not found');
            }

            // Verify user owns the post (use loose comparison to handle string/int mismatch)
            if ((string)$post->user_id !== (string)Auth::id()) {
                throw new \Exception('Unauthorized: You can only close your own posts');
            }

            $application = JobApplication::find($request->application_id);
            if (!$application) {
                throw new \Exception('Application not found');
            }

            // Verify application belongs to post
            if ($application->post_id !== $post->id) {
                throw new \Exception('Application does not belong to this post');
            }

            // Check if job is already closed
            if ($post->status === 'closed') {
                return response()->json([
                    'status' => false,
                    'message' => 'Job is already closed',
                ], 400);
            }

            // Initiate payment
            $result = $this->p2pPaymentService->initiateJobClosurePayment($post, $application);

            // If payment is skipped (percentage is 0)
            if (isset($result['skip_payment']) && $result['skip_payment']) {
                // Close job without payment
                $post->status = 'closed';
                $post->closed_at = now();
                $post->payment_status = 'fully_paid';
                $post->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Job closed successfully (no payment required)',
                    'skip_payment' => true,
                ], 200);
            }

            // Return authorization URL for mobile app to display in WebView
            return response()->json([
                'status' => true,
                'message' => 'Payment initialized successfully',
                'data' => [
                    'authorization_url' => $result['data']['authorization_url'] ?? null,
                    'reference' => $result['data']['reference'] ?? null,
                    'access_code' => $result['data']['access_code'] ?? null,
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Mobile P2P Job Closure Payment Initiation Failed', [
                'error' => $e->getMessage(),
                'post_id' => $request->post_id ?? null,
                'application_id' => $request->application_id ?? null,
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to initiate payment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check payment status (Mobile endpoint)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkPaymentStatus(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return response()->json([
                'status' => false,
                'message' => 'Reference is required',
            ], 422);
        }

        try {
            $transaction = Transaction::where('paystack_reference', $reference)
                ->orWhere('client_reference', $reference)
                ->first();

            if (!$transaction) {
                return response()->json([
                    'status' => false,
                    'message' => 'Transaction not found',
                ], 404);
            }

            // If transaction is still pending, verify with Paystack directly
            if ($transaction->status === Transaction::STATUS_PENDING) {
                try {
                    $paystackService = app(\App\Services\PaystackService::class);
                    $verification = $paystackService->verifyTransaction($reference);
                    
                    // If Paystack says it's successful, update our record
                    if (isset($verification['data']['status']) && $verification['data']['status'] === 'success') {
                        $transaction->update([
                            'status' => Transaction::STATUS_SUCCESS,
                            'paid_at' => now(),
                        ]);
                        
                        // Process P2P payment if applicable
                        $metadata = $transaction->metadata ?? [];
                        $paymentType = $metadata['payment_type'] ?? null;
                        if (in_array($paymentType, ['initial', 'final'])) {
                            $this->p2pPaymentService->handlePaymentSuccess($reference, $paymentType);
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to verify transaction with Paystack', [
                        'reference' => $reference,
                        'error' => $e->getMessage(),
                    ]);
                }
                
                // Refresh transaction from database
                $transaction->refresh();
            }

            // Extract metadata for response
            $metadata = $transaction->metadata ?? [];
            
            return response()->json([
                'status' => true,
                'data' => [
                    'transaction_status' => $transaction->status,
                    'reference' => $transaction->paystack_reference ?? $transaction->client_reference,
                    'payment_type' => $metadata['payment_type'] ?? null,
                    'post_id' => $metadata['post_id'] ?? null,
                    'application_id' => $metadata['application_id'] ?? null,
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Mobile P2P Payment Status Check Failed', [
                'error' => $e->getMessage(),
                'reference' => $reference,
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to check payment status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

