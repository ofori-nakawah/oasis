## PAY-2 — P2P Job Payment Integration Specification

### Summary
Implementation of split payment system for P2P (peer-to-peer) jobs using Paystack. Payments are split into two installments: an initial payment at quote approval and a final payment at job completion. Both percentages are configurable via environment variables, allowing flexibility for different payment structures (e.g., 10/90, 50/50, 0/100).

### Context
- Laravel 8 application with existing Paystack integration (see `specs/paystack-integration.md`)
- P2P jobs are created via `PostController::submitQuoteRequest()` and quotes are submitted via `PostController::submitQuote()`
- Existing `Post` and `JobApplication` models track job and quote information
- Payment flow uses Paystack hosted checkout page
- Frontend: Blade templates with AlpineJS/Tailwind CSS

### Problem Statement
Currently, P2P jobs do not have integrated payment processing. When a quote is approved or a job is completed, there's no mechanism to collect payments from the job poster. The system needs to:
1. Collect an initial payment (configurable percentage) when a quote is approved
2. Collect the remaining payment (configurable percentage) when the job is completed
3. Provide a seamless payment experience with Paystack checkout in a modal iframe
4. Update job status and track payment transactions

### Goals / Outcomes
- **Outcome A**: When a user approves a quote, they are prompted to pay the initial installment (configurable percentage) via Paystack before the quote is approved
- **Outcome B**: When a user closes/completes a P2P job, they are prompted to pay the remaining installment (configurable percentage) via Paystack before the job is closed
- **Outcome C**: Payment percentages are configurable via `.env` variables (`P2P_INITIAL_PAYMENT_PERCENTAGE` and `P2P_FINAL_PAYMENT_PERCENTAGE`)
- **Outcome D**: Payment checkout is displayed in a modal iframe for seamless user experience
- **Outcome E**: Post status and payment information are updated upon successful payment
- **Outcome F**: All payment transactions are tracked in the `transactions` table with proper metadata linking to posts and applications

### Non‑Goals
- Refund processing (to be handled separately)
- Payment method selection (uses Paystack default)
- Escrow/holding funds (payments go directly to service provider)
- Multiple payment attempts for failed transactions (user can retry manually)
- Payment reminders or automated retries

### Assumptions
- Paystack integration is already set up and working (see `specs/paystack-integration.md`)
- Users have valid email addresses for payment notifications
- Job posters are authenticated when approving quotes or closing jobs
- Quote amounts are stored in `JobApplication.quote` field as numeric values
- Post type "P2P" identifies peer-to-peer jobs
- Payment percentages in `.env` are integers (e.g., 10, 90) that sum to 100

### User Stories
- As a **job poster**, I want to **pay an initial installment when approving a quote**, so that **I can secure the service provider's commitment**
- As a **job poster**, I want to **pay the remaining balance when closing a job**, so that **I can complete payment for the service**
- As a **system administrator**, I want to **configure payment split percentages**, so that **I can adjust payment terms for different scenarios**
- As a **user**, I want to **see payment status on my jobs**, so that **I know which payments have been completed**
- As a **service provider**, I want to **receive payment confirmations**, so that **I know when payments are made**

### Acceptance Criteria
- **AC1**: Given a user is viewing a quote for approval, when they click "Approve Quote", then a payment modal appears with Paystack checkout iframe for the initial payment amount (configurable percentage of quote)
- **AC2**: Given a payment modal is displayed, when the user completes payment successfully, then the quote is approved, the post status is updated, and a transaction record is created
- **AC3**: Given a payment fails or is abandoned, when the user closes the modal, then the quote remains unapproved and no changes are made to the post
- **AC4**: Given a user is closing a P2P job, when they initiate job closure, then a payment modal appears with Paystack checkout iframe for the final payment amount (configurable percentage of quote)
- **AC5**: Given a final payment is completed successfully, when the payment webhook is processed, then the job is closed, the post status is updated, and a transaction record is created
- **AC6**: Given payment percentages are configured in `.env`, when calculating payment amounts, then the system uses these percentages (e.g., `P2P_INITIAL_PAYMENT_PERCENTAGE=10`, `P2P_FINAL_PAYMENT_PERCENTAGE=90`)
- **AC7**: Given a quote has been approved with initial payment, when closing the job, then only the final payment amount is requested (not the full quote amount)
- **AC8**: Given payment transactions are created, when storing transaction metadata, then `post_id` and `application_id` are included for tracking
- **AC9**: Given a post has payment transactions, when viewing the post, then payment status is displayed (initial payment paid, final payment pending, etc.)
- **Edge Cases**: 
  - Quote approval without payment (if initial percentage is 0)
  - Job closure without payment (if final percentage is 0)
  - Multiple quote approvals (only first approval triggers payment)
  - Payment webhook arrives before user returns from Paystack
  - User closes modal without completing payment
  - Invalid payment percentages (validation ensures they sum to 100)

### High-Level Approach
1. **Database Schema**: Add payment tracking fields to `posts` and `job_applications` tables
2. **Environment Configuration**: Add `.env` variables for payment percentages
3. **Payment Service**: Extend `PaystackService` or create `P2PPaymentService` for P2P-specific payment logic
4. **Controllers**: Modify `PostController` to handle payment initiation before quote approval and job closure
5. **Routes**: Add routes for payment initiation and callback handling
6. **Frontend**: Create modal component with iframe for Paystack checkout
7. **Webhook Handling**: Update webhook handler to process P2P payment events and update post status
8. **Views**: Update quote approval and job closure views to trigger payment flow

### Architecture / Design Notes

#### Payment Flow - Quote Approval
1. User clicks "Approve Quote" on a P2P job quote
2. System calculates initial payment amount: `quote_amount * (P2P_INITIAL_PAYMENT_PERCENTAGE / 100)`
3. If initial payment percentage > 0:
   - System calls `PaystackService::initializeTransaction()` with calculated amount
   - Returns authorization URL
   - Frontend displays modal with iframe loading Paystack checkout URL
   - User completes payment in iframe
4. On payment success (via webhook or callback):
   - Quote is approved (`JobApplication.status = 'confirmed'` or new `quote_approved_at` field)
   - Post is updated with payment information
   - Transaction record is created with metadata: `{post_id, application_id, payment_type: 'initial'}`
5. If initial payment percentage = 0, quote is approved immediately without payment

#### Payment Flow - Job Closure
1. User initiates job closure for a P2P job
2. System checks if initial payment was made
3. System calculates final payment amount: `quote_amount * (P2P_FINAL_PAYMENT_PERCENTAGE / 100)`
4. If final payment percentage > 0:
   - System calls `PaystackService::initializeTransaction()` with calculated amount
   - Returns authorization URL
   - Frontend displays modal with iframe loading Paystack checkout URL
   - User completes payment in iframe
5. On payment success (via webhook or callback):
   - Job is closed (`Post.status = 'closed'`, `Post.closed_at = now()`)
   - Transaction record is created with metadata: `{post_id, application_id, payment_type: 'final'}`
6. If final payment percentage = 0, job is closed immediately without payment

#### Database Schema Changes

##### Add to `posts` table:
```php
Schema::table('posts', function (Blueprint $table) {
    $table->decimal('initial_payment_amount', 15, 2)->nullable()->after('final_payment_amount');
    $table->timestamp('initial_payment_paid_at')->nullable()->after('initial_payment_amount');
    $table->foreignId('initial_payment_transaction_id')->nullable()->after('initial_payment_paid_at')
        ->constrained('transactions')->onDelete('set null');
    $table->decimal('final_payment_amount', 15, 2)->nullable()->after('initial_payment_transaction_id');
    $table->timestamp('final_payment_paid_at')->nullable()->after('final_payment_amount');
    $table->foreignId('final_payment_transaction_id')->nullable()->after('final_payment_paid_at')
        ->constrained('transactions')->onDelete('set null');
    $table->string('payment_status')->default('pending')->after('final_payment_transaction_id')
        ->comment('pending, initial_paid, fully_paid');
});
```

##### Add to `job_applications` table:
```php
Schema::table('job_applications', function (Blueprint $table) {
    $table->timestamp('quote_approved_at')->nullable()->after('quote');
    $table->foreignId('quote_approved_by')->nullable()->after('quote_approved_at')
        ->constrained('users')->onDelete('set null');
});
```

#### Environment Variables
Add to `.env`:
```env
# P2P Payment Configuration
P2P_INITIAL_PAYMENT_PERCENTAGE=10
P2P_FINAL_PAYMENT_PERCENTAGE=90
```

#### API Endpoints

**POST `/api/p2p/initiate-payment`**
- Initiate payment for quote approval or job closure
- Request body:
  ```json
  {
    "post_id": "uuid",
    "application_id": "uuid",
    "payment_type": "initial|final",
    "amount": 5000.00
  }
  ```
- Response: `{ "authorization_url": "https://checkout.paystack.com/...", "reference": "..." }`

**GET `/api/p2p/payment-status/{transaction_reference}`**
- Check payment status
- Response: `{ "status": "success|pending|failed", "transaction": {...} }`

**POST `/api/p2p/approve-quote`** (Web route)
- Approve quote after payment (called from frontend after payment success)
- Request: `post_id`, `application_id`, `transaction_reference`

**POST `/api/p2p/close-job`** (Web route)
- Close job after payment (called from frontend after payment success)
- Request: `post_id`, `transaction_reference`

#### Frontend Components

**Payment Modal Component** (`resources/views/components/payment-modal.blade.php`):
- Modal with iframe loading Paystack checkout URL
- Handles payment success/failure callbacks
- Closes modal and refreshes page on success
- Shows error message on failure

**JavaScript Integration**:
- Intercept "Approve Quote" button click
- Call API to initiate payment
- Display modal with iframe
- Listen for payment completion (polling or callback)
- Update UI on success

### Small, Independent Tasks

#### Planning
- [ ] Review and align on spec with stakeholders
- [ ] Confirm database migration safety plan
- [ ] Identify test scenarios based on acceptance criteria

#### Database
- [ ] Create migration to add payment fields to `posts` table
- [ ] Create migration to add quote approval fields to `job_applications` table
- [ ] Update `Post` model with new fields and relationships
- [ ] Update `JobApplication` model with new fields and relationships
- [ ] Add model accessors/mutators for payment calculations

#### Configuration
- [ ] Add `.env` variables for payment percentages
- [ ] Add validation in `AppServiceProvider` to ensure percentages sum to 100
- [ ] Create config file `config/p2p.php` for payment settings

#### Backend - Payment Service
- [ ] Create `P2PPaymentService` class with methods:
  - `calculateInitialPaymentAmount(float $quoteAmount): float`
  - `calculateFinalPaymentAmount(float $quoteAmount): float`
  - `initiateQuoteApprovalPayment(Post $post, JobApplication $application): array`
  - `initiateJobClosurePayment(Post $post, JobApplication $application): array`
  - `handlePaymentSuccess(string $reference, string $paymentType): void`

#### Backend - Controllers
- [ ] Add `initiatePayment()` method to `PostController` or create `P2PPaymentController`
- [ ] Modify `approveQuote()` method to trigger payment flow
- [ ] Modify `close_post()` method to trigger payment flow for P2P jobs
- [ ] Add `handlePaymentCallback()` method for payment completion
- [ ] Add validation for payment initiation requests

#### Backend - Routes
- [ ] Add route for payment initiation: `POST /api/p2p/initiate-payment`
- [ ] Add route for payment status check: `GET /api/p2p/payment-status/{reference}`
- [ ] Add route for quote approval after payment: `POST /p2p/approve-quote`
- [ ] Add route for job closure after payment: `POST /p2p/close-job`

#### Backend - Webhook Integration
- [ ] Update `PaystackService::processWebhookEvent()` to handle P2P payments
- [ ] Add logic to update post payment status on successful payment
- [ ] Add logic to approve quote on initial payment success
- [ ] Add logic to close job on final payment success

#### Frontend - Views
- [ ] Update quote approval view to show payment modal trigger
- [ ] Update job closure view to show payment modal trigger
- [ ] Create payment modal component with iframe
- [ ] Add JavaScript for payment flow handling
- [ ] Add payment status display on post details page

#### Frontend - JavaScript
- [ ] Create `P2PPayment` JavaScript class/module
- [ ] Implement payment initiation on quote approval click
- [ ] Implement payment initiation on job closure click
- [ ] Implement iframe modal display
- [ ] Implement payment status polling
- [ ] Implement success/failure callback handling

#### Testing
- [ ] Write feature tests for payment initiation
- [ ] Write feature tests for quote approval with payment
- [ ] Write feature tests for job closure with payment
- [ ] Write feature tests for webhook processing
- [ ] Write unit tests for payment calculation methods
- [ ] Write browser tests for payment modal flow

#### Documentation
- [ ] Document `.env` configuration variables
- [ ] Document payment flow in README
- [ ] Document API endpoints if applicable

### Risks & Mitigations
- **Risk**: Payment webhook arrives before user returns from Paystack, causing race condition
  - **Mitigation**: Use polling on frontend to check payment status, and webhook as backup. Ensure idempotent operations.
- **Risk**: User closes modal without completing payment, leaving quote in limbo
  - **Mitigation**: Only approve quote after confirmed payment success. Allow user to retry payment.
- **Risk**: Invalid payment percentages in `.env` (not summing to 100)
  - **Mitigation**: Add validation in `AppServiceProvider` boot method to check and throw exception if invalid.
- **Risk**: Multiple quote approvals triggering multiple payments
  - **Mitigation**: Check `quote_approved_at` before initiating payment. Only allow one approval per quote.
- **Risk**: Payment amount mismatch due to quote changes
  - **Mitigation**: Lock quote amount when initial payment is initiated. Store original quote amount for final payment calculation.

### Dependencies
- Existing Paystack integration (see `specs/paystack-integration.md`)
- Paystack API keys configured in environment
- Webhook endpoint configured in Paystack dashboard
- SSL certificate for webhook endpoint (Paystack requires HTTPS)

### Test Strategy

#### Feature Tests
- Payment initiation for quote approval returns authorization URL
- Payment initiation for job closure returns authorization URL
- Quote approval without payment (when percentage is 0)
- Job closure without payment (when percentage is 0)
- Payment webhook updates post payment status correctly
- Payment webhook approves quote on initial payment success
- Payment webhook closes job on final payment success
- Invalid payment percentages throw validation error
- Multiple quote approvals are prevented

#### Unit Tests
- `P2PPaymentService::calculateInitialPaymentAmount()` calculates correctly
- `P2PPaymentService::calculateFinalPaymentAmount()` calculates correctly
- Payment amount calculations handle edge cases (0%, 100%, etc.)

#### Browser Tests
- Payment modal displays correctly with iframe
- User can complete payment in modal
- Payment success updates UI correctly
- Payment failure shows error message

### Rollout Plan
1. Deploy database migrations to staging
2. Configure `.env` variables in staging
3. Test payment flow end-to-end in staging with Paystack test mode
4. Update webhook URL in Paystack dashboard if needed
5. Gradually enable for a small cohort of users
6. Monitor payment success rates and errors
7. Full rollout after successful validation

### Metrics / Observability
- **Payment Initiation Rate**: Number of payment initiations per day
- **Payment Success Rate**: Percentage of successful payments vs initiated
- **Payment Completion Time**: Average time from initiation to completion
- **Failed Payment Rate**: Track failed payment reasons
- **Quote Approval Rate**: Percentage of quotes approved after payment
- **Job Closure Rate**: Percentage of jobs closed after final payment

Add structured logging:
- Payment initiation: `[P2P_PAYMENT_INIT] post_id, application_id, payment_type, amount`
- Payment success: `[P2P_PAYMENT_SUCCESS] post_id, transaction_id, payment_type`
- Payment failure: `[P2P_PAYMENT_FAILURE] post_id, transaction_id, error`
- Quote approval: `[P2P_QUOTE_APPROVED] post_id, application_id, transaction_id`
- Job closure: `[P2P_JOB_CLOSED] post_id, transaction_id`

### Open Questions
- Should we allow partial payments or require full payment at each stage?
- Do we need to support payment plans or installments beyond the two-stage split?
- Should service providers receive notifications when payments are made?
- Do we need an admin dashboard to view/manage P2P payments?
- Should we implement payment disputes or refunds?
- What happens if a job is cancelled after initial payment is made?

### Definition of Done
- [ ] All database migrations created and tested
- [ ] Environment variables configured and validated
- [ ] Payment service implemented with calculation methods
- [ ] Controllers updated to handle payment flow
- [ ] Routes added for payment endpoints
- [ ] Webhook handler updated for P2P payments
- [ ] Frontend modal component created and integrated
- [ ] JavaScript payment flow implemented
- [ ] All acceptance criteria pass in feature tests
- [ ] Unit tests pass for payment calculations
- [ ] Browser tests pass for payment modal flow
- [ ] Payment percentages configurable via `.env`
- [ ] Payment status displayed on post details
- [ ] No new linter errors
- [ ] Documentation updated
- [ ] Code review completed
- [ ] Staging validation successful

