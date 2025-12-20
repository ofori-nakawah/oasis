<?php

namespace App\Http\Controllers\Web;

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
     * Initiate payment for quote approval
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function initiateQuoteApprovalPayment(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'post_id' => 'required',
            'application_id' => 'required',
        ]);

        if ($validation->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validation->errors(),
                ], 422);
            }
            return back()->withErrors($validation->errors())->with('danger', 'Please ensure all required fields are completed.');
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
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Quote has already been approved',
                    ], 400);
                }
                return back()->with('danger', 'Quote has already been approved');
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

                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Quote approved successfully (no payment required)',
                        'skip_payment' => true,
                    ], 200);
                }
                return back()->with('success', 'Quote approved successfully (no payment required)');
            }

            // Return authorization URL for frontend to display in modal
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Payment initialized successfully',
                    'data' => [
                        'authorization_url' => $result['data']['authorization_url'] ?? null,
                        'reference' => $result['data']['reference'] ?? null,
                        'access_code' => $result['data']['access_code'] ?? null,
                    ],
                ], 200);
            }

            // For web requests, redirect to payment page or return JSON for AJAX
            return response()->json([
                'status' => true,
                'authorization_url' => $result['data']['authorization_url'] ?? null,
                'reference' => $result['data']['reference'] ?? null,
            ]);

        } catch (\Exception $e) {
            Log::error('P2P Quote Approval Payment Initiation Failed', [
                'error' => $e->getMessage(),
                'post_id' => $request->post_id ?? null,
                'application_id' => $request->application_id ?? null,
                'user_id' => Auth::id(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to initiate payment',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()->with('danger', 'Failed to initiate payment: ' . $e->getMessage());
        }
    }

    /**
     * Initiate payment for job closure
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function initiateJobClosurePayment(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'post_id' => 'required',
            'application_id' => 'required',
        ]);

        if ($validation->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validation->errors(),
                ], 422);
            }
            return back()->withErrors($validation->errors())->with('danger', 'Please ensure all required fields are completed.');
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
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Job is already closed',
                    ], 400);
                }
                return back()->with('danger', 'Job is already closed');
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

                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Job closed successfully (no payment required)',
                        'skip_payment' => true,
                    ], 200);
                }
                return back()->with('success', 'Job closed successfully (no payment required)');
            }

            // Return authorization URL for frontend to display in modal
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Payment initialized successfully',
                    'data' => [
                        'authorization_url' => $result['data']['authorization_url'] ?? null,
                        'reference' => $result['data']['reference'] ?? null,
                        'access_code' => $result['data']['access_code'] ?? null,
                    ],
                ], 200);
            }

            return response()->json([
                'status' => true,
                'authorization_url' => $result['data']['authorization_url'] ?? null,
                'reference' => $result['data']['reference'] ?? null,
            ]);

        } catch (\Exception $e) {
            Log::error('P2P Job Closure Payment Initiation Failed', [
                'error' => $e->getMessage(),
                'post_id' => $request->post_id ?? null,
                'application_id' => $request->application_id ?? null,
                'user_id' => Auth::id(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to initiate payment',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()->with('danger', 'Failed to initiate payment: ' . $e->getMessage());
        }
    }

    /**
     * Handle payment callback after successful payment
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handlePaymentCallback(Request $request)
    {
        $reference = $request->query('reference');
        $paymentType = $request->query('payment_type', 'initial'); // 'initial' or 'final'

        if (!$reference) {
            return redirect()->route('home')->with('danger', 'Invalid payment reference');
        }

        try {
            // Find transaction
            $transaction = Transaction::where('paystack_reference', $reference)
                ->orWhere('client_reference', $reference)
                ->first();

            if (!$transaction) {
                return redirect()->route('home')->with('danger', 'Transaction not found');
            }

            // Check if payment was successful
            if ($transaction->status !== Transaction::STATUS_SUCCESS) {
                return redirect()->route('home')->with('danger', 'Payment was not successful');
            }

            // Handle payment success
            $this->p2pPaymentService->handlePaymentSuccess($reference, $paymentType);

            $message = $paymentType === 'initial' 
                ? 'Quote approved successfully! Payment completed.' 
                : 'Job closed successfully! Payment completed.';

            return redirect()->route('home')->with('success', $message);

        } catch (\Exception $e) {
            Log::error('P2P Payment Callback Failed', [
                'error' => $e->getMessage(),
                'reference' => $reference,
                'payment_type' => $paymentType,
            ]);

            return redirect()->route('home')->with('danger', 'Failed to process payment: ' . $e->getMessage());
        }
    }

    /**
     * Check payment status
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

            return response()->json([
                'status' => true,
                'transaction_status' => $transaction->status,
                'is_successful' => $transaction->isSuccessful(),
            ], 200);

        } catch (\Exception $e) {
            Log::error('P2P Payment Status Check Failed', [
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
