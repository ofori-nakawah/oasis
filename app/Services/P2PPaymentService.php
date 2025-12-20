<?php

namespace App\Services;

use App\Models\JobApplication;
use App\Models\Post;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class P2PPaymentService
{
    protected PaystackService $paystackService;

    public function __construct(PaystackService $paystackService)
    {
        $this->paystackService = $paystackService;
    }

    /**
     * Calculate initial payment amount based on quote and configured percentage
     *
     * @param float $quoteAmount
     * @return float
     */
    public function calculateInitialPaymentAmount(float $quoteAmount): float
    {
        $percentage = config('p2p.initial_payment_percentage', 10);
        return ($quoteAmount * $percentage) / 100;
    }

    /**
     * Calculate final payment amount based on quote and configured percentage
     *
     * @param float $quoteAmount
     * @return float
     */
    public function calculateFinalPaymentAmount(float $quoteAmount): float
    {
        $percentage = config('p2p.final_payment_percentage', 90);
        return ($quoteAmount * $percentage) / 100;
    }

    /**
     * Initiate payment for quote approval
     *
     * @param Post $post
     * @param JobApplication $application
     * @return array
     * @throws \Exception
     */
    public function initiateQuoteApprovalPayment(Post $post, JobApplication $application): array
    {
        // Check if quote is already approved
        if ($application->isQuoteApproved()) {
            throw new \Exception('Quote has already been approved');
        }

        // Check if quote exists
        if (empty($application->quote) || !is_numeric($application->quote)) {
            throw new \Exception('Invalid quote amount');
        }

        $quoteAmount = (float) $application->quote;
        $initialPaymentAmount = $this->calculateInitialPaymentAmount($quoteAmount);

        // If initial payment percentage is 0, skip payment
        if ($initialPaymentAmount <= 0) {
            return [
                'skip_payment' => true,
                'message' => 'No initial payment required',
            ];
        }

        // Get user email
        $user = $post->user;
        if (!$user || !$user->email) {
            throw new \Exception('User email is required for payment');
        }

        // Initialize transaction with Paystack
        $paymentData = [
            'email' => $user->email,
            'amount' => (int) ($initialPaymentAmount * 100), // Convert to pesewas/kobo
            'currency' => 'GHS',
            'user_id' => $user->id,
            'metadata' => [
                'post_id' => $post->id,
                'application_id' => $application->id,
                'payment_type' => 'initial',
                'quote_amount' => $quoteAmount,
                'payment_amount' => $initialPaymentAmount,
            ],
        ];

        $result = $this->paystackService->initializeTransaction($paymentData);

        Log::info('P2P Payment Initiated', [
            'post_id' => $post->id,
            'application_id' => $application->id,
            'payment_type' => 'initial',
            'amount' => $initialPaymentAmount,
            'reference' => $result['data']['reference'] ?? null,
        ]);

        return $result;
    }

    /**
     * Initiate payment for job closure
     *
     * @param Post $post
     * @param JobApplication $application
     * @return array
     * @throws \Exception
     */
    public function initiateJobClosurePayment(Post $post, JobApplication $application): array
    {
        // Check if job is already closed
        if ($post->status === 'closed') {
            throw new \Exception('Job is already closed');
        }

        // Check if quote exists
        if (empty($application->quote) || !is_numeric($application->quote)) {
            throw new \Exception('Invalid quote amount');
        }

        $quoteAmount = (float) $application->quote;
        $finalPaymentAmount = $this->calculateFinalPaymentAmount($quoteAmount);

        // If final payment percentage is 0, skip payment
        if ($finalPaymentAmount <= 0) {
            return [
                'skip_payment' => true,
                'message' => 'No final payment required',
            ];
        }

        // Get user email
        $user = $post->user;
        if (!$user || !$user->email) {
            throw new \Exception('User email is required for payment');
        }

        // Initialize transaction with Paystack
        $paymentData = [
            'email' => $user->email,
            'amount' => (int) ($finalPaymentAmount * 100), // Convert to pesewas/kobo
            'currency' => 'GHS',
            'user_id' => $user->id,
            'metadata' => [
                'post_id' => $post->id,
                'application_id' => $application->id,
                'payment_type' => 'final',
                'quote_amount' => $quoteAmount,
                'payment_amount' => $finalPaymentAmount,
            ],
        ];

        $result = $this->paystackService->initializeTransaction($paymentData);

        Log::info('P2P Payment Initiated', [
            'post_id' => $post->id,
            'application_id' => $application->id,
            'payment_type' => 'final',
            'amount' => $finalPaymentAmount,
            'reference' => $result['data']['reference'] ?? null,
        ]);

        return $result;
    }

    /**
     * Handle successful payment and update post/application status
     *
     * @param string $reference Transaction reference
     * @param string $paymentType 'initial' or 'final'
     * @return void
     * @throws \Exception
     */
    public function handlePaymentSuccess(string $reference, string $paymentType): void
    {
        // Find transaction
        $transaction = Transaction::where('paystack_reference', $reference)
            ->orWhere('client_reference', $reference)
            ->first();

        if (!$transaction) {
            throw new \Exception("Transaction not found for reference: {$reference}");
        }

        // Get metadata
        $metadata = $transaction->metadata ?? [];
        $postId = $metadata['post_id'] ?? null;
        $applicationId = $metadata['application_id'] ?? null;

        if (!$postId || !$applicationId) {
            throw new \Exception('Transaction metadata missing post_id or application_id');
        }

        $post = Post::find($postId);
        $application = JobApplication::find($applicationId);

        if (!$post || !$application) {
            throw new \Exception('Post or application not found');
        }

        if ($paymentType === 'initial') {
            $this->handleInitialPaymentSuccess($post, $application, $transaction);
        } elseif ($paymentType === 'final') {
            $this->handleFinalPaymentSuccess($post, $application, $transaction);
        } else {
            throw new \Exception("Invalid payment type: {$paymentType}");
        }
    }

    /**
     * Handle initial payment success
     *
     * @param Post $post
     * @param JobApplication $application
     * @param Transaction $transaction
     * @return void
     */
    protected function handleInitialPaymentSuccess(Post $post, JobApplication $application, Transaction $transaction): void
    {
        // Update application - approve quote
        $application->quote_approved_at = now();
        $application->quote_approved_by = $post->user_id;
        $application->status = 'confirmed';
        $application->save();

        // Update post - mark initial payment as paid
        $metadata = $transaction->metadata ?? [];
        $post->initial_payment_amount = (string) ($metadata['payment_amount'] ?? $transaction->amount);
        $post->initial_payment_paid_at = now();
        $post->initial_payment_transaction_id = $transaction->id;
        $post->payment_status = 'initial_paid';
        $post->is_job_applicant_confirmed = '1';
        $post->confirmed_applicant_id = $application->user_id;
        $post->save();

        Log::info('P2P Initial Payment Success', [
            'post_id' => $post->id,
            'application_id' => $application->id,
            'transaction_id' => $transaction->id,
        ]);
    }

    /**
     * Handle final payment success
     *
     * @param Post $post
     * @param JobApplication $application
     * @param Transaction $transaction
     * @return void
     */
    protected function handleFinalPaymentSuccess(Post $post, JobApplication $application, Transaction $transaction): void
    {
        // Update post - mark final payment as paid and close job
        $metadata = $transaction->metadata ?? [];
        $post->final_payment_amount = (string) ($metadata['payment_amount'] ?? $transaction->amount);
        $post->final_payment_paid_at = now();
        $post->final_payment_transaction_id = $transaction->id;
        $post->payment_status = 'fully_paid';
        $post->status = 'closed';
        $post->closed_at = now();
        $post->save();

        Log::info('P2P Final Payment Success', [
            'post_id' => $post->id,
            'application_id' => $application->id,
            'transaction_id' => $transaction->id,
        ]);
    }
}
