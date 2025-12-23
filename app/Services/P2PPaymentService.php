<?php

namespace App\Services;

use App\Models\JobApplication;
use App\Models\Post;
use App\Models\RatingReview;
use App\Models\Transaction;
use App\Services\WalletService;
use App\Helpers\Notifications;
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
     * Calculate final payment amount as the remaining amount after initial payment
     * This ensures users only pay what's left, not a percentage of the full quote again
     *
     * @param float $quoteAmount
     * @return float
     */
    public function calculateFinalPaymentAmount(float $quoteAmount): float
    {
        $initialPercentage = config('p2p.initial_payment_percentage', 10);
        $initialPaymentAmount = ($quoteAmount * $initialPercentage) / 100;
        // Final payment is the remaining amount (quote - initial payment)
        $finalPaymentAmount = $quoteAmount - $initialPaymentAmount;
        
        // Ensure we don't return negative amounts due to rounding
        return max(0, round($finalPaymentAmount, 2));
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

        // Use database transaction with locking to prevent duplicate processing
        \DB::transaction(function () use ($postId, $applicationId, $transaction, $paymentType) {
            // Lock the post row to prevent concurrent processing
            $post = Post::lockForUpdate()->find($postId);
            $application = JobApplication::find($applicationId);

            if (!$post || !$application) {
                throw new \Exception('Post or application not found');
            }

            // For final payments, check if already processed to prevent duplicates
            if ($paymentType === 'final') {
                // Check if job is already closed
                if ($post->status === 'closed' && $post->final_payment_transaction_id) {
                    Log::info('P2P Final Payment already processed - skipping duplicate', [
                        'post_id' => $post->id,
                        'transaction_id' => $transaction->id,
                        'existing_final_payment_transaction_id' => $post->final_payment_transaction_id,
                    ]);
                    return; // Already processed, skip
                }

                // Check if earning transaction already exists for this payment transaction
                // Check by both payment_transaction_id and post_id/application_id to be thorough
                $existingEarning = Transaction::where('transaction_type', Transaction::TYPE_EARNING)
                    ->where(function($query) use ($transaction, $postId, $applicationId) {
                        $query->where('metadata->payment_transaction_id', $transaction->id)
                              ->orWhere(function($q) use ($postId, $applicationId) {
                                  $q->where('metadata->post_id', $postId)
                                    ->where('metadata->application_id', $applicationId);
                              });
                    })
                    ->first();

                if ($existingEarning) {
                    Log::info('P2P Earning transaction already exists - skipping duplicate', [
                        'post_id' => $post->id,
                        'payment_transaction_id' => $transaction->id,
                        'existing_earning_transaction_id' => $existingEarning->id,
                    ]);
                    return; // Already processed, skip
                }
            }

            // For initial payments, check if already processed
            if ($paymentType === 'initial') {
                if ($post->initial_payment_transaction_id && $post->initial_payment_transaction_id == $transaction->id) {
                    Log::info('P2P Initial Payment already processed - skipping duplicate', [
                        'post_id' => $post->id,
                        'transaction_id' => $transaction->id,
                    ]);
                    return; // Already processed, skip
                }
            }

            if ($paymentType === 'initial') {
                $this->handleInitialPaymentSuccess($post, $application, $transaction);
            } elseif ($paymentType === 'final') {
                $this->handleFinalPaymentSuccess($post, $application, $transaction);
            } else {
                throw new \Exception("Invalid payment type: {$paymentType}");
            }
        });
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
        // Double-check: If job is already closed with this transaction, skip processing
        if ($post->status === 'closed' && $post->final_payment_transaction_id == $transaction->id) {
            Log::info('P2P Final Payment already processed - job already closed', [
                'post_id' => $post->id,
                'transaction_id' => $transaction->id,
            ]);
            return;
        }

        // Check if earning transaction already exists for this payment transaction
        // Check by both payment_transaction_id and post_id/application_id to be thorough
        $existingEarning = Transaction::where('transaction_type', Transaction::TYPE_EARNING)
            ->where(function($query) use ($transaction, $post, $application) {
                $query->where('metadata->payment_transaction_id', $transaction->id)
                      ->orWhere(function($q) use ($post, $application) {
                          $q->where('metadata->post_id', $post->id)
                            ->where('metadata->application_id', $application->id);
                      });
            })
            ->first();

        if ($existingEarning) {
            Log::warning('P2P Earning transaction already exists - preventing duplicate payment', [
                'post_id' => $post->id,
                'payment_transaction_id' => $transaction->id,
                'existing_earning_transaction_id' => $existingEarning->id,
            ]);
            // Still update post status if not already closed, but don't credit again
            if ($post->status !== 'closed') {
                $post->final_payment_amount = (string) ($transaction->metadata['payment_amount'] ?? $transaction->amount);
                $post->final_payment_paid_at = now();
                $post->final_payment_transaction_id = $transaction->id;
                $post->payment_status = 'fully_paid';
                $post->status = 'closed';
                $post->closed_at = now();
                $post->save();
            }
            return;
        }

        // Update post - mark final payment as paid and close job
        $metadata = $transaction->metadata ?? [];
        $finalPaymentAmount = (float) ($metadata['payment_amount'] ?? $transaction->amount);
        $quoteAmount = (float) ($metadata['quote_amount'] ?? $application->quote ?? 0);
        
        $post->final_payment_amount = (string) $finalPaymentAmount;
        $post->final_payment_paid_at = now();
        $post->final_payment_transaction_id = $transaction->id;
        $post->payment_status = 'fully_paid';
        $post->status = 'closed';
        $post->closed_at = now();
        $post->save();

        // Get worker (participant)
        $worker = $application->user;
        if (!$worker) {
            Log::error('Worker not found for job closure', [
                'post_id' => $post->id,
                'application_id' => $application->id,
            ]);
            return;
        }

        // Credit the worker's wallet and update earnings (following old flow)
        try {
            if ($quoteAmount > 0) {
                // Create earning transaction for the worker
                $earningTransaction = Transaction::create([
                    'user_id' => $worker->id,
                    'uuid' => Str::uuid()->toString(),
                    'client_reference' => 'EARN-' . Str::random(12),
                    'amount' => $quoteAmount,
                    'currency' => 'GHS',
                    'email' => $worker->email,
                    'status' => Transaction::STATUS_SUCCESS,
                    'transaction_type' => Transaction::TYPE_EARNING,
                    'transaction_category' => Transaction::CATEGORY_CREDIT,
                    'pay_link_url' => null, // Earning transactions don't have payment links
                    'metadata' => [
                        'post_id' => $post->id,
                        'application_id' => $application->id,
                        'payment_transaction_id' => $transaction->id,
                        'quote_amount' => $quoteAmount,
                    ],
                    'paid_at' => now(),
                ]);

                // Update worker's wallet balance
                $walletService = app(WalletService::class);
                
                // Log before update
                Log::info('Updating worker balance before job closure', [
                    'worker_id' => $worker->id,
                    'current_balance' => $worker->available_balance,
                    'amount_to_add' => $quoteAmount,
                ]);
                
                $walletService->updateBalance($worker, $quoteAmount, 'job_earning');

                // Refresh worker to get updated balance from database
                $worker = \App\Models\User::find($worker->id);
                
                // Log after update
                Log::info('Worker balance updated after job closure', [
                    'worker_id' => $worker->id,
                    'new_balance' => $worker->available_balance,
                    'amount_added' => $quoteAmount,
                ]);

                // Update worker's total_earnings (following old flow)
                $worker->total_earnings = (float)$worker->total_earnings + (float)$quoteAmount;
            }

            // Update worker's rating (following old flow - recalculate average rating)
            $user_review_rating = 0;
            $ratingReviews = \App\Models\RatingReview::where('user_id', $worker->id)->get();
            if ($ratingReviews->count() >= 1) {
                $user_review_rating = $ratingReviews->sum("rating") / $ratingReviews->count();
            }
            $worker->rating = $user_review_rating;
            $worker->save();

            // Set job_done_overall_rating if rating review exists
            $ratingReview = RatingReview::where('post_id', $post->id)
                ->where('job_application_id', $application->id)
                ->first();
            
            if ($ratingReview) {
                $post->job_done_overall_rating = $ratingReview->rating;
                $post->save();
            }

            // Send notifications to both parties (following old flow)
            // Ensure relationships are loaded
            $post->load('user');
            $application->load('user');

            // Send notification to worker (participant)
            Notifications::PushUserNotification($post, $application, $worker, "JOB_CLOSED");

            // Send notification to job poster (issuer)
            $jobPoster = $post->user;
            if ($jobPoster && $jobPoster->id !== $worker->id) {
                Notifications::PushUserNotification($post, $application, $jobPoster, "JOB_CLOSED");
            }

            Log::info('P2P Final Payment Success - Job closed with notifications', [
                'post_id' => $post->id,
                'application_id' => $application->id,
                'transaction_id' => $transaction->id,
                'worker_id' => $worker->id,
                'job_poster_id' => $jobPoster->id ?? null,
                'amount_credited' => $quoteAmount,
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the job closure
            Log::error('Failed to complete job closure process', [
                'post_id' => $post->id,
                'application_id' => $application->id,
                'worker_id' => $worker->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
