## PAY-1 — Paystack Payment Integration Specification

### Summary
High-level plan and implementation details for integrating Paystack payment gateway into the Oasis application. This spec covers payment initiation, transaction verification, webhook handling, and comprehensive transaction tracking. The implementation will enable users to make payments through Paystack, track transaction statuses, and handle webhook events securely.

### Context
- Laravel 8 app with streamlined structure (`bootstrap/app.php`, etc.).
- Frontend stack: Blade/AlpineJS/Tailwind v3.
- Testing: Pest v4 for feature/unit and optional browser tests.
- Existing `transactions` table exists but needs enhancement for Paystack integration.
- Current payment flow uses manual/cash payments; need to integrate online payment processing.

### Problem Statement
Currently, users can only process payments manually (cash) through the platform. There's no integrated payment gateway to facilitate online transactions. Users need a secure, reliable way to make payments directly through the application. The existing transaction tracking system is basic and doesn't support webhook events or comprehensive payment status tracking. This integration will enable users to pay for services, top up wallets, and complete transactions seamlessly through Paystack's payment infrastructure, with full audit trails via webhook events.

### Goals / Outcomes
- Outcome A: Users can initiate payments through Paystack API with proper transaction tracking.
- Outcome B: All Paystack webhook events are captured and stored in `transaction_events` table for audit and reconciliation.
- Outcome C: Transaction statuses are automatically updated based on webhook events (success, failed, pending, etc.).
- Outcome D: Payment verification endpoint allows manual verification of transaction status.
- Outcome E: Secure webhook handling with signature verification to prevent unauthorized access.

### Non‑Goals
- Payment method selection UI (can be added in future iterations).
- Refund processing (to be handled separately).
- Subscription/recurring payments (out of scope for this feature).
- Multi-currency support beyond Paystack's supported currencies.
- Payment gateway switching mechanism (Paystack-specific implementation).

### Assumptions
- Authentication/authorization uses Laravel's standard auth system.
- Paystack API keys (public and secret) will be configured in environment variables.
- Webhook URL will be configured in Paystack dashboard post-deployment.
- Existing `transactions` table structure can be enhanced without breaking existing functionality.
- Users have valid email addresses for payment notifications.
- Paystack account is already set up and verified.

### User Stories
- As a **user**, I want to **initiate a payment through Paystack**, so that **I can pay for services securely online**.
- As a **user**, I want to **receive payment confirmation**, so that **I know my transaction was successful**.
- As a **system administrator**, I want to **track all payment webhook events**, so that **I can audit and reconcile transactions**.
- As a **user**, I want to **verify my payment status**, so that **I can confirm if my payment was processed correctly**.

### Acceptance Criteria
- Given a user is authenticated, when they send a payment initiation request with amount, channel, and relevant information, then the system makes a request to Paystack API, receives an authorization URL, and returns that authorization URL to the user in the response.
- Given a payment initiation request is made, when Paystack API returns an authorization URL, then a transaction record is created in the database with status 'pending' and the authorization URL is stored.
- Given a payment webhook is received, when the signature is valid, then the event is stored in `transaction_events` table, the transaction's `last_webhook_event` field is updated with the webhook payload, and the transaction status is updated accordingly.
- Given a payment webhook is received, when the signature is invalid, then the request is rejected with 401 status.
- Given a user verifies a transaction, when the transaction reference is valid, then the current status is returned from Paystack API.
- Given a transaction is successful, when the webhook is processed, then the related order/service is marked as paid.
- Edge cases: Duplicate webhook events should be idempotent (no duplicate processing), failed payments should be logged, expired payment links should be handled gracefully, Paystack API failures should return appropriate error responses.

### Test-Driven Development (TDD) Approach
**CRITICAL: All implementation MUST follow TDD (Red-Green-Refactor cycle).**

1. **RED**: Write failing tests first that define the desired behavior (feature tests for endpoints, unit tests for services).
2. **GREEN**: Write the minimum implementation needed to make tests pass.
3. **REFACTOR**: Improve code quality while keeping tests green.

**TDD Workflow:**
- Write a test for one small piece of functionality.
- Run the test (it should fail - RED).
- Write the minimum code to make it pass (GREEN).
- Refactor if needed while keeping tests green (REFACTOR).
- Repeat for the next piece of functionality.

**TDD Best Practices:**
- **One test at a time**: Focus on one acceptance criterion or feature at a time.
- **Test behavior, not implementation**: Tests should verify what the code does, not how it does it.
- **Start with the simplest case**: Begin with happy path, then add edge cases and error scenarios.
- **Keep tests fast**: Unit tests should run in milliseconds; feature tests should be under a few seconds.
- **Test isolation**: Each test should be independent and not rely on other tests.
- **Use descriptive test names**: Test names should clearly describe what is being tested and the expected outcome.
- **AAA Pattern**: Arrange (setup), Act (execute), Assert (verify) - makes tests readable and maintainable.

**Benefits:**
- Tests serve as executable documentation.
- Tests drive design decisions.
- Ensures all code is testable and tested.
- Prevents over-engineering.
- Catches bugs early in the development cycle.
- Makes refactoring safe (tests catch regressions).

### High-Level Approach
1. **Write tests first** (TDD): Create failing feature/unit tests that define expected behavior.
2. Design database schema for enhanced `transactions` table and new `transaction_events` table.
3. Create Paystack service class to handle API interactions (initialize, verify, handle webhooks).
4. Design routes and controllers the Laravel way (form requests for validation) - driven by test requirements.
5. Implement models/relationships using Eloquent with explicit return types - make tests pass.
6. Build webhook endpoint with signature verification.
7. Add integration points (events, jobs, notifications) where needed; queue webhook processing if heavy - test with feature tests.
8. Ensure security (webhook signature verification, API key protection) - test with feature tests.
9. Instrument with logs/metrics for observability (minimally structured logs and key counters).
10. **Refactor**: Improve code quality while keeping all tests green.

### Architecture / Design Notes
- Routing: Payment endpoints in `routes/web.php` or `routes/api.php`; webhook endpoint in `routes/api.php` (CSRF exempt).
- Controllers: Thin; delegate validation to Form Requests and logic to `PaystackService`.
- Validation: Dedicated Form Requests with rules and custom messages.
- Data access: Favor Eloquent; eager load relationships to avoid N+1.
- Service Layer: `App\Services\PaystackService` to encapsulate Paystack API interactions.
- **Payment Initiation Flow**:
  1. User sends POST request to `/api/payments/initialize` with: `amount`, `channel`, `email`, and optional `metadata`.
  2. Controller validates request via Form Request.
  3. Controller calls `PaystackService::initializeTransaction()` with validated data.
  4. Service makes HTTP POST request to Paystack API (`https://api.paystack.co/transaction/initialize`).
  5. Paystack returns response containing `authorization_url` and `access_code`.
  6. Service creates Transaction record in database with status 'pending'.
  7. Service returns authorization URL to controller.
  8. Controller returns JSON response with authorization URL to user: `{ "authorization_url": "https://checkout.paystack.com/..." }`.
  9. Frontend receives authorization URL and redirects user to Paystack hosted payment page.
- Webhook Security: Verify Paystack webhook signature using `X-Paystack-Signature` header.
- **Webhook Processing**: When a webhook event is received and processed:
  1. Event is stored in `transaction_events` table for full audit trail.
  2. Transaction's `last_webhook_event` field is updated with the webhook payload for quick access.
  3. Transaction status is updated based on event type (charge.success, charge.failed, etc.).
- Frontend: Payment initiation endpoint returns authorization URL in JSON response; frontend redirects user to Paystack hosted page.

### Database Schema

#### Enhanced `transactions` Table
```php
Schema::create('transactions', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique(); // For external references
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('paystack_reference')->unique()->nullable(); // Paystack transaction reference
    $table->string('client_reference')->unique(); // Our internal reference
    $table->decimal('amount', 15, 2); // Amount in smallest currency unit
    $table->string('currency', 3)->default('GHS'); // GHS, NGN, ZAR, etc.
    $table->string('email');
    $table->string('authorization_url')->nullable(); // Paystack payment URL
    $table->string('access_code')->nullable(); // Paystack access code
    $table->enum('status', ['pending', 'success', 'failed', 'abandoned', 'reversed'])->default('pending');
    $table->string('gateway_response')->nullable(); // Paystack gateway response message
    $table->string('channel')->nullable(); // card, bank, ussd, qr, etc.
    $table->string('payment_type')->nullable(); // one-time, recurring
    $table->timestamp('paid_at')->nullable();
    $table->json('metadata')->nullable(); // Additional data (post_id, service_type, etc.)
    $table->json('customer_data')->nullable(); // Customer information from Paystack
    $table->json('last_webhook_event')->nullable(); // Last webhook event data for quick access
    $table->timestamps();
    $table->softDeletes();
    
    $table->index('paystack_reference');
    $table->index('client_reference');
    $table->index('user_id');
    $table->index('status');
});
```

#### New `transaction_events` Table
```php
Schema::create('transaction_events', function (Blueprint $table) {
    $table->id();
    $table->foreignId('transaction_id')->constrained()->onDelete('cascade');
    $table->string('event_type'); // charge.success, charge.failed, transfer.success, etc.
    $table->string('paystack_event_id')->unique(); // Paystack event ID for idempotency
    $table->json('payload'); // Full webhook payload
    $table->boolean('processed')->default(false); // Whether event was processed
    $table->timestamp('processed_at')->nullable();
    $table->text('processing_error')->nullable(); // Error if processing failed
    $table->timestamps();
    
    $table->index('transaction_id');
    $table->index('paystack_event_id');
    $table->index('event_type');
    $table->index('processed');
});
```

### Small, Independent Tasks

**IMPORTANT: Follow TDD - Write tests BEFORE implementation for each task.**

- **Planning**
  - [x] Create this spec and align on scope with stakeholders.
  - [ ] Confirm data model changes and migration safety plan.
  - [ ] Identify test scenarios based on acceptance criteria.
  - [ ] Review Paystack API documentation for all required endpoints.

- **Testing First (TDD - RED Phase)**
  - [ ] Write failing feature test for payment initiation endpoint.
  - [ ] Write failing feature test for payment verification endpoint.
  - [ ] Write failing feature test for webhook endpoint (signature verification).
  - [ ] Write failing feature test for webhook event processing.
  - [ ] Write failing unit tests for PaystackService methods (initialize, verify, handleWebhook).
  - [ ] Write failing test for duplicate webhook idempotency.
  - [ ] Run tests to confirm they fail (RED).

- **Database (if applicable)**
  - [ ] Create migration to enhance `transactions` table with Paystack-specific fields.
  - [ ] Create migration for `transaction_events` table.
  - [ ] Update Transaction model with casts(), relationships, and factory states.
  - [ ] Create TransactionEvent model with relationships.
  - [ ] Create factories for Transaction and TransactionEvent.
  - [ ] Seed sample data for local/dev if helpful.
  - [ ] **Run tests** - they should still fail (implementation not complete).

- **Paystack Service (TDD - GREEN Phase)**
  - [ ] Create PaystackService class with initializeTransaction() method that:
    - Accepts: amount, channel, email, metadata, and other relevant information.
    - Makes POST request to Paystack API (`https://api.paystack.co/transaction/initialize`).
    - Extracts authorization_url from Paystack response.
    - Creates Transaction record in database with status 'pending'.
    - Returns authorization URL string.
  - [ ] Create PaystackService method verifyTransaction().
  - [ ] Create PaystackService method verifyWebhookSignature().
  - [ ] Create PaystackService method processWebhookEvent().
  - [ ] Add HTTP client configuration for Paystack API (base URL, secret key header).
  - [ ] Handle Paystack API errors and exceptions gracefully.
  - [ ] **Run tests** - implement minimum code to make tests pass (GREEN).

- **HTTP Layer (TDD - GREEN Phase)**
  - [ ] Add route for payment initiation (`POST /api/payments/initialize`).
  - [ ] Add route for payment verification (`GET /api/payments/verify/{reference}`).
  - [ ] Add route for webhook endpoint (`POST /api/webhooks/paystack`).
  - [ ] Create Form Request for payment initialization with rules (amount, channel, email, metadata).
  - [ ] Create PaymentController with initialize() method that:
    - Validates request via Form Request.
    - Calls PaystackService to initialize transaction with Paystack API.
    - Receives authorization URL from service.
    - Returns JSON response with authorization URL: `{ "authorization_url": "..." }`.
  - [ ] Create PaymentController with verify() method.
  - [ ] Create WebhookController with handlePaystack() method.
  - [ ] Add CSRF exemption for webhook route.
  - [ ] **Run tests** - implement minimum code to make tests pass (GREEN).

- **Domain / Services (TDD - GREEN Phase)**
  - [ ] Implement transaction status update logic based on webhook events.
  - [ ] Update `last_webhook_event` field on transaction when processing webhook events.
  - [ ] Implement idempotency check for duplicate webhook events.
  - [ ] Queue webhook processing if needed (for heavy operations).
  - [ ] Add event listeners for successful payments (update related models).
  - [ ] **Run tests** - implement minimum code to make tests pass (GREEN).

- **UI (TDD - GREEN Phase)**
  - [ ] Add payment initiation button/form in relevant views.
  - [ ] Implement redirect to Paystack authorization URL.
  - [ ] Add payment success/failure callback pages.
  - [ ] Display transaction status in user dashboard.
  - [ ] Add Tailwind classes with minimal duplication and logical grouping.
  - [ ] Add AlpineJS interactions if necessary, keeping logic small and declarative.
  - [ ] **Run browser tests** - implement minimum code to make tests pass (GREEN).

- **Refactoring (TDD - REFACTOR Phase)**
  - [ ] Improve code quality, readability, and maintainability.
  - [ ] Extract duplicate code, improve naming, optimize queries.
  - [ ] Add type hints and return types throughout.
  - [ ] **Run all tests** - ensure they remain green after refactoring.

- **Observability**
  - [ ] Add structured logs for payment initiation, verification, and webhook events.
  - [ ] Log webhook signature verification failures.
  - [ ] Add metrics for successful/failed payments.
  - [ ] **Run tests** - ensure logging doesn't break functionality.

- **Documentation & Rollout**
  - [ ] Document Paystack API key configuration in README.
  - [ ] Document webhook URL configuration steps.
  - [ ] Prepare rollout steps and comms; add a quick rollback note.
  - [ ] Update API documentation if applicable.

### Risks & Mitigations
- Risk: Webhook signature verification may fail if Paystack secret key is incorrect.
  - Mitigation: Add comprehensive tests for signature verification; log verification failures; use environment-specific keys.
- Risk: Duplicate webhook events may cause double processing.
  - Mitigation: Implement idempotency check using `paystack_event_id`; test duplicate event handling.
- Risk: Paystack API downtime may affect payment processing.
  - Mitigation: Implement retry logic with exponential backoff; queue failed requests; monitor API status.
- Risk: Database migration may affect existing transaction data.
  - Mitigation: Make new fields nullable where possible; test migration on staging with production data copy.
- Risk: N+1 queries when loading transactions with events.
  - Mitigation: Use `with()`/`load()` for eager loading; add assertions in tests for query counts.

### Dependencies
- Paystack PHP SDK (or Guzzle HTTP client - already in dependencies).
- Paystack API keys (public and secret) - to be configured in environment variables.
- Webhook URL configuration in Paystack dashboard post-deployment.
- SSL certificate for webhook endpoint (Paystack requires HTTPS for webhooks).

### Test Strategy (Pest v4) - TDD Approach

**CRITICAL: Write tests FIRST, then implement to make them pass.**

**Test-Driven Workflow:**
1. Write a failing test for one acceptance criterion.
2. Run the test (confirm it fails - RED).
3. Implement minimum code to pass (GREEN).
4. Refactor if needed (REFACTOR).
5. Move to next acceptance criterion.

**Test Coverage (Write These FIRST):**
- **Feature tests** (write before controller/service implementation):
  - Payment initialization with valid data (amount, channel, email) makes request to Paystack API and returns authorization URL in JSON response.
  - Payment initialization with invalid data (missing amount, invalid channel) returns validation errors.
  - Payment initialization when Paystack API fails returns appropriate error response.
  - Payment initialization creates transaction record in database with status 'pending'.
  - Payment verification returns correct transaction status.
  - Webhook endpoint accepts valid signature and processes event.
  - Webhook endpoint rejects invalid signature with 401.
  - Duplicate webhook events are handled idempotently.
  - Transaction status updates correctly based on webhook event type.
  - Transaction's `last_webhook_event` field is updated when processing webhook events.
- **Unit tests** (write before service implementation):
  - PaystackService.initializeTransaction() accepts amount, channel, email, metadata and makes HTTP POST to Paystack API.
  - PaystackService.initializeTransaction() extracts authorization_url from Paystack response and returns it.
  - PaystackService.initializeTransaction() creates Transaction record in database before returning.
  - PaystackService.initializeTransaction() handles Paystack API errors and throws appropriate exceptions.
  - PaystackService.verifyTransaction() returns correct data.
  - PaystackService.verifyWebhookSignature() validates signatures correctly.
  - PaystackService.processWebhookEvent() handles different event types.
  - PaystackService.processWebhookEvent() updates transaction's `last_webhook_event` field with webhook payload.
  - Error handling for Paystack API failures (network errors, invalid responses, etc.).
- **Browser tests** (write before UI implementation, if critical):
  - User can initiate payment and redirects to Paystack.
  - Payment success callback displays correctly.
  - Payment failure callback displays correctly.

**Test Organization:**
- `tests/Feature/Payment/PaymentInitializationTest.php`
- `tests/Feature/Payment/PaymentVerificationTest.php`
- `tests/Feature/Webhook/PaystackWebhookTest.php`
- `tests/Unit/Services/PaystackServiceTest.php`
- Group related tests using `describe()` blocks.
- Use descriptive test names that explain the scenario.
- Follow AAA pattern: Arrange, Act, Assert.

**Mocking Strategy:**
- Mock Paystack API responses in tests using HTTP client fakes.
- Use Paystack test API keys for integration tests.
- Mock webhook payloads for webhook tests.

### Rollout Plan
- Configure Paystack API keys in environment variables (development and production).
- Run database migrations on staging first; verify data integrity.
- Deploy webhook endpoint; configure webhook URL in Paystack dashboard.
- Test payment flow end-to-end on staging with Paystack test mode.
- Gradually enable for a small cohort; monitor logs/metrics.
- Full rollout after successful staging validation.

### Metrics / Observability
- **Payment Success Rate**: Percentage of successful payments vs total initiated.
- **Webhook Processing Time**: Average time to process webhook events.
- **Payment Failure Rate**: Track failed payment reasons (insufficient funds, card declined, etc.).
- **Webhook Signature Verification Failures**: Monitor for potential security issues.
- Add structured logging:
  - Payment initiation: `[PAYMENT_INIT] user_id, amount, reference`
  - Payment verification: `[PAYMENT_VERIFY] reference, status`
  - Webhook received: `[WEBHOOK_RECEIVED] event_type, paystack_event_id`
  - Webhook processed: `[WEBHOOK_PROCESSED] event_type, transaction_id, status`
  - Webhook errors: `[WEBHOOK_ERROR] event_type, error_message`

### Open Questions
- Should we support multiple payment methods (card, bank transfer, USSD) or start with card only?
- Do we need to store customer payment methods for future use?
- Should failed payments trigger notifications to users?
- Do we need admin dashboard to view/manage transactions?
- Should we implement payment retry logic for failed transactions?
- What is the expected transaction volume? (affects queue/job strategy)

### Payment Initiation Endpoint Details

#### Request Format
**Endpoint**: `POST /api/payments/initialize`

**Request Body**:
```json
{
  "amount": 500000,
  "channel": "card",
  "email": "user@example.com",
  "currency": "GHS",
  "metadata": {
    "post_id": "uuid-here",
    "service_type": "job_payment"
  }
}
```

**Validation Rules**:
- `amount`: required, numeric, min: 1 (amount in kobo/smallest currency unit)
- `channel`: required, string, in: card,bank,ussd,qr,mobile_money
- `email`: required, email
- `currency`: optional, string, default: GHS
- `metadata`: optional, object

#### Response Format
**Success Response** (200):
```json
{
  "status": true,
  "message": "Payment initialized successfully",
  "data": {
    "authorization_url": "https://checkout.paystack.com/xxxxxxxxxxxxx",
    "access_code": "xxxxxxxxxxxxx",
    "reference": "unique_reference_here"
  }
}
```

**Error Response** (422 - Validation Error):
```json
{
  "status": false,
  "message": "Validation failed",
  "errors": {
    "amount": ["The amount field is required."],
    "channel": ["The selected channel is invalid."]
  }
}
```

**Error Response** (500 - Paystack API Error):
```json
{
  "status": false,
  "message": "Failed to initialize payment",
  "error": "Paystack API error message"
}
```

#### Flow Summary
1. User sends POST request with amount, channel, email, and optional metadata.
2. System validates request data.
3. System makes POST request to Paystack API with validated data.
4. Paystack returns authorization URL in response.
5. System creates Transaction record in database.
6. System returns authorization URL to user in JSON response.
7. Frontend receives authorization URL and redirects user to Paystack payment page.

### Paystack API Endpoints Reference
- **Initialize Transaction**: `POST https://api.paystack.co/transaction/initialize`
  - **Request Body**: `{ "email": "user@example.com", "amount": 500000, "currency": "GHS", "channels": ["card", "bank"], "metadata": {...}, "reference": "unique_reference" }`
  - **Response**: `{ "status": true, "message": "Authorization URL created", "data": { "authorization_url": "https://checkout.paystack.com/...", "access_code": "...", "reference": "..." } }`
  - **Headers**: `Authorization: Bearer {secret_key}`, `Content-Type: application/json`
- **Verify Transaction**: `GET https://api.paystack.co/transaction/verify/{reference}`
- **List Transactions**: `GET https://api.paystack.co/transaction`
- **Webhook Events**: Paystack sends POST requests to configured webhook URL
- **Webhook Signature**: Verify using `X-Paystack-Signature` header with HMAC SHA512

### Definition of Done
- [ ] **TDD Process Followed**: All code was written using TDD (tests written first, then implementation).
- [ ] All acceptance criteria pass in feature tests (tests were written before implementation).
- [ ] All unit tests pass (service/domain logic tested before implementation).
- [ ] Database migrations created and tested (transactions enhanced, transaction_events created).
- [ ] PaystackService implemented with all required methods.
- [ ] Webhook endpoint implemented with signature verification.
- [ ] Payment initiation and verification endpoints working.
- [ ] No new linter errors; Pint formatting applied to changed PHP files.
- [ ] Browser tests (if added) pass with no console errors.
- [ ] Test coverage: All new code paths are covered by tests.
- [ ] All existing tests pass (or are updated to reflect new behavior).
- [ ] Webhook URL configured in Paystack dashboard.
- [ ] Environment variables documented.
- [ ] Code review confirms TDD approach was followed.
- [ ] Stakeholder demo reviewed; any noted issues addressed or ticketed.

