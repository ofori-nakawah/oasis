## WALLET-1 — Wallet Page Enhancement Specification

### Summary
High-level plan and implementation details for enhancing the wallet page with comprehensive balance display, topup functionality, withdrawal capability, and a complete transactions table showing all user transactions (debits and credits). This spec builds upon the existing basic wallet page and integrates with the Paystack payment gateway for topups and withdrawals.

### Context
- Laravel 8+ app with existing wallet infrastructure
- Frontend stack: Blade templates with existing UI components
- Existing `PaystackService` for payment processing
- Existing `Transaction` model and `transactions` table for payment tracking
- Users table has `available_balance`, `total_earnings`, `total_topups`, `total_payouts` fields
- Current wallet page exists at `/wallet` route but lacks full functionality
- Paystack integration already implemented for P2P payments

### Problem Statement
Currently, users have a basic wallet page that displays their balance, but it lacks essential functionality:
1. No way to top up their wallet balance directly
2. No withdrawal functionality to transfer funds to their bank account
3. Transactions table only shows job-related transactions, not all wallet transactions (topups, withdrawals, debits, credits)
4. No clear distinction between different transaction types (topup, withdrawal, payment, earnings)
5. Limited transaction history and filtering capabilities

Users need a comprehensive wallet management system where they can:
- View their current balance prominently
- Top up their wallet using Paystack
- Withdraw funds to their bank account
- View a complete history of all transactions (both debits and credits) with proper categorization
- Filter and search transactions

### Goals / Outcomes
- **Outcome A**: Users can view their current wallet balance prominently on the wallet page
- **Outcome B**: Users can initiate wallet topups through Paystack payment gateway
- **Outcome C**: Users can request withdrawals to their bank account (processed via Paystack transfers)
- **Outcome D**: Users can view all their transactions (topups, withdrawals, payments, earnings) in a comprehensive table
- **Outcome E**: Transactions are properly categorized (credit/debit) and show relevant details (amount, type, status, date)
- **Outcome F**: User balance is automatically updated when transactions are completed
- **Outcome G**: Transaction history supports filtering by type, status, and date range

### Non‑Goals
- Multi-currency wallet support (single currency: GHS)
- Wallet-to-wallet transfers between users (out of scope)
- Cryptocurrency integration
- Wallet balance limits or restrictions (can be added later)
- Automatic withdrawal scheduling
- Withdrawal approval workflow (admin approval)
- Transaction export functionality (can be added later)
- Real-time balance updates via WebSockets (polling is acceptable)

### Assumptions
- Paystack account supports transfers/withdrawals (recipient management)
- Users have valid email addresses and bank account details for withdrawals
- Paystack secret key has transfer/withdrawal permissions
- Existing `Transaction` model can be extended to support wallet transactions
- User balance fields in `users` table are sufficient for tracking
- All amounts are in GHS (Ghana Cedis)
- Minimum topup amount: 1 GHS
- Minimum withdrawal amount: 10 GHS (configurable)
- Withdrawal processing may have delays (1-3 business days)

### User Stories
- As a **user**, I want to **view my current wallet balance**, so that **I know how much money I have available**
- As a **user**, I want to **top up my wallet using Paystack**, so that **I can add funds to my account for future payments**
- As a **user**, I want to **withdraw funds to my bank account**, so that **I can access my earnings**
- As a **user**, I want to **view all my transactions in one place**, so that **I can track my financial activity**
- As a **user**, I want to **see transaction details (type, amount, status, date)**, so that **I understand what each transaction represents**
- As a **user**, I want to **filter transactions by type and date**, so that **I can find specific transactions easily**
- As a **system administrator**, I want to **track all wallet transactions**, so that **I can audit and reconcile user balances**

### Acceptance Criteria

#### Balance Display
- Given a user is authenticated, when they visit the wallet page, then their current `available_balance` is displayed prominently
- Given a user has a balance, when the balance is displayed, then it is formatted as currency (GHS X,XXX.XX)
- Given a user has zero balance, when the balance is displayed, then it shows GHS 0.00

#### Topup Functionality
- Given a user is authenticated, when they click "Top Up" button, then a modal/form opens allowing them to enter an amount
- Given a user initiates a topup, when they enter an amount less than 1 GHS, then validation error is shown
- Given a user initiates a topup, when they enter a valid amount and submit, then a Paystack payment is initialized
- Given a Paystack payment is initialized for topup, when the user completes payment, then the transaction is recorded with type "topup" and status "success"
- Given a topup transaction is successful, when the webhook is processed, then the user's `available_balance` and `total_topups` are incremented
- Given a topup transaction fails, when the webhook is processed, then the transaction status is updated to "failed" and balance remains unchanged

#### Withdrawal Functionality
- Given a user is authenticated, when they click "Withdraw" button, then a modal/form opens allowing them to enter amount and bank details
- Given a user initiates a withdrawal, when they enter an amount less than minimum withdrawal (10 GHS), then validation error is shown
- Given a user initiates a withdrawal, when they enter an amount greater than their available balance, then validation error is shown
- Given a user initiates a withdrawal, when they submit valid withdrawal request, then a withdrawal transaction is created with status "pending"
- Given a withdrawal request is created, when Paystack transfer is initiated, then the user's `available_balance` is decremented (held) and `total_payouts` is incremented
- Given a withdrawal transfer succeeds, when the webhook is processed, then the transaction status is updated to "success"
- Given a withdrawal transfer fails, when the webhook is processed, then the transaction status is updated to "failed" and balance is restored

#### Transactions Table
- Given a user is authenticated, when they view the wallet page, then all their transactions are displayed in a table
- Given transactions are displayed, when a transaction is a credit (topup, earnings), then it shows with positive amount and "Credit" label
- Given transactions are displayed, when a transaction is a debit (withdrawal, payment), then it shows with negative amount and "Debit" label
- Given transactions are displayed, when viewing the table, then columns show: Date, Type, Description, Amount, Status
- Given transactions are displayed, when there are no transactions, then an empty state message is shown
- Given transactions are displayed, when there are many transactions, then pagination is available (20 per page)
- Given transactions can be filtered, when user selects a transaction type filter, then only matching transactions are shown
- Given transactions can be filtered, when user selects a date range, then only transactions within that range are shown
- Given transactions can be filtered, when user selects a status filter, then only transactions with that status are shown

#### Transaction Types
- **Topup**: Credit transaction when user adds funds via Paystack
- **Withdrawal**: Debit transaction when user withdraws to bank account
- **Payment**: Debit transaction when user pays for a job/service
- **Earning**: Credit transaction when user receives payment for completed work
- **Refund**: Credit transaction when payment is refunded (if applicable)

#### Balance Updates
- Given a successful topup, when the transaction is processed, then user balance is updated immediately
- Given a successful withdrawal, when the transaction is processed, then user balance is decremented
- Given a payment is made, when the transaction is processed, then user balance is decremented
- Given an earning is received, when the transaction is processed, then user balance is incremented
- Given multiple transactions occur, when balance is calculated, then it matches sum of all successful credit transactions minus sum of all successful debit transactions

### High-Level Approach

1. **Database Enhancements**
   - Add `transaction_type` field to `transactions` table (topup, withdrawal, payment, earning, refund)
   - Add `transaction_category` field (credit/debit) for easier querying
   - Add `bank_account_details` JSON field for withdrawal transactions
   - Add `recipient_code` field for Paystack transfer recipients
   - Ensure `user_id` foreign key exists and is indexed

2. **Service Layer**
   - Extend `PaystackService` with withdrawal/transfer methods
   - Create `WalletService` for balance management and transaction processing
   - Add methods: `topup()`, `withdraw()`, `updateBalance()`, `getUserTransactions()`

3. **Controller Layer**
   - Create/update `WalletController` with methods:
     - `index()` - Display wallet page with balance and transactions
     - `topup()` - Initiate topup payment
     - `withdraw()` - Process withdrawal request
     - `transactions()` - API endpoint for filtered transactions (AJAX)

4. **Frontend**
   - Update wallet page view with:
     - Prominent balance display
     - Top Up button/modal
     - Withdraw button/modal
     - Comprehensive transactions table
     - Filter controls (type, status, date range)
     - Pagination for transactions

5. **Webhook Integration**
   - Extend webhook handler to process wallet transactions
   - Update balance on successful topup/withdrawal
   - Handle failed transactions appropriately

### Architecture / Design Notes

#### Database Schema Changes
```php
// Migration: add_wallet_fields_to_transactions_table
Schema::table('transactions', function (Blueprint $table) {
    $table->enum('transaction_type', ['topup', 'withdrawal', 'payment', 'earning', 'refund'])
          ->default('payment')
          ->after('payment_type');
    $table->enum('transaction_category', ['credit', 'debit'])
          ->after('transaction_type');
    $table->json('bank_account_details')->nullable()->after('metadata');
    $table->string('recipient_code')->nullable()->after('bank_account_details');
    
    $table->index(['user_id', 'transaction_type']);
    $table->index(['user_id', 'transaction_category']);
    $table->index(['user_id', 'status']);
});
```

#### Transaction Model Updates
- Add constants for transaction types and categories
- Add scope methods: `credits()`, `debits()`, `topups()`, `withdrawals()`
- Add relationship: `user()` (already exists)
- Add accessor for formatted amount with sign

#### WalletService Methods
```php
class WalletService
{
    public function topup(User $user, float $amount): array
    public function withdraw(User $user, float $amount, array $bankDetails): array
    public function updateBalance(User $user, float $amount, string $type): void
    public function getUserTransactions(User $user, array $filters = []): Collection
    public function getBalance(User $user): float
}
```

#### PaystackService Extensions
```php
// Add to PaystackService
public function createTransferRecipient(array $data): array
public function initiateTransfer(array $data): array
public function verifyTransfer(string $transferCode): array
```

### Small, Independent Tasks

**IMPORTANT: Follow TDD - Write tests BEFORE implementation for each task.**

#### Planning
- [ ] Create this spec and align on scope with stakeholders
- [ ] Review Paystack transfer API documentation
- [ ] Confirm bank account details collection requirements
- [ ] Identify test scenarios based on acceptance criteria

#### Testing First (TDD - RED Phase)
- [ ] Write failing feature tests for wallet page display
- [ ] Write failing feature tests for topup initiation
- [ ] Write failing feature tests for withdrawal request
- [ ] Write failing feature tests for transactions listing
- [ ] Write failing feature tests for transaction filtering
- [ ] Write failing unit tests for WalletService methods
- [ ] Write failing unit tests for PaystackService transfer methods
- [ ] Write failing browser tests for wallet UI flows
- [ ] Run tests to confirm they fail (RED)

#### Database
- [ ] Create migration to add wallet fields to transactions table
- [ ] Update Transaction model with new fields, constants, and scopes
- [ ] Add factory states for different transaction types
- [ ] Seed sample wallet transactions for testing
- [ ] **Run tests** - they should still fail (implementation not complete)

#### Service Layer (TDD - GREEN Phase)
- [ ] Implement WalletService with topup method
- [ ] Implement WalletService with withdraw method
- [ ] Implement WalletService with updateBalance method
- [ ] Implement WalletService with getUserTransactions method
- [ ] Extend PaystackService with createTransferRecipient method
- [ ] Extend PaystackService with initiateTransfer method
- [ ] Extend PaystackService with verifyTransfer method
- [ ] **Run tests** - implement minimum code to make tests pass (GREEN)

#### HTTP Layer (TDD - GREEN Phase)
- [ ] Update wallet route to use WalletController
- [ ] Create WalletController with index method
- [ ] Create WalletController with topup method
- [ ] Create WalletController with withdraw method
- [ ] Create WalletController with transactions method (AJAX)
- [ ] Create Form Requests for topup and withdrawal validation
- [ ] **Run tests** - implement minimum code to make tests pass (GREEN)

#### Webhook Integration
- [ ] Extend webhook handler to process topup transactions
- [ ] Extend webhook handler to process withdrawal transactions
- [ ] Add balance update logic for successful transactions
- [ ] Add balance restoration logic for failed withdrawals
- [ ] **Run tests** - implement minimum code to make tests pass (GREEN)

#### UI (TDD - GREEN Phase)
- [ ] Update wallet index view with prominent balance display
- [ ] Add Top Up button and modal
- [ ] Add Withdraw button and modal
- [ ] Create transactions table component
- [ ] Add transaction type badges (credit/debit)
- [ ] Add transaction status badges
- [ ] Add filter controls (type, status, date range)
- [ ] Add pagination for transactions table
- [ ] Add empty state for no transactions
- [ ] Integrate Paystack payment iframe for topup
- [ ] Add JavaScript for AJAX transaction loading
- [ ] **Run browser tests** - implement minimum code to make tests pass (GREEN)

#### Refactoring (TDD - REFACTOR Phase)
- [ ] Improve code quality, readability, and maintainability
- [ ] Extract duplicate code, improve naming, optimize queries
- [ ] Add eager loading to prevent N+1 queries
- [ ] Optimize transaction queries with proper indexes
- [ ] **Run all tests** - ensure they remain green after refactoring

#### Observability
- [ ] Add structured logs for topup initiation
- [ ] Add structured logs for withdrawal requests
- [ ] Add structured logs for balance updates
- [ ] Add metrics for topup success/failure rates
- [ ] Add metrics for withdrawal success/failure rates
- [ ] **Run tests** - ensure logging doesn't break functionality

#### Documentation & Rollout
- [ ] Update API documentation if applicable
- [ ] Document withdrawal process and requirements
- [ ] Prepare rollout steps and comms
- [ ] Add rollback plan for database changes

### Risks & Mitigations

- **Risk**: Paystack transfer API may require recipient verification before transfers
  - **Mitigation**: Implement recipient creation and verification flow; store recipient_code for reuse

- **Risk**: Balance inconsistencies if webhooks fail or are delayed
  - **Mitigation**: Implement balance reconciliation job; add manual balance correction endpoint (admin only)

- **Risk**: N+1 queries when loading transactions with relationships
  - **Mitigation**: Use eager loading (`with()`); add query count assertions in tests

- **Risk**: Concurrent transactions causing balance race conditions
  - **Mitigation**: Use database transactions with row-level locking; implement optimistic locking

- **Risk**: Withdrawal fraud or insufficient balance
  - **Mitigation**: Validate balance before withdrawal; implement minimum withdrawal amount; add withdrawal limits

- **Risk**: Paystack transfer failures leaving balance in inconsistent state
  - **Mitigation**: Implement idempotent withdrawal processing; restore balance on transfer failure

### Dependencies

- Paystack API for transfers/withdrawals (requires account verification)
- Existing PaystackService must support transfer endpoints
- User must have valid bank account details for withdrawals
- Webhook endpoint must be configured in Paystack dashboard

### Test Strategy (Pest v4) - TDD Approach

**CRITICAL: Write tests FIRST, then implement to make them pass.**

#### Test-Driven Workflow
1. Write a failing test for one acceptance criterion
2. Run the test (confirm it fails - RED)
3. Implement minimum code to pass (GREEN)
4. Refactor if needed (REFACTOR)
5. Move to next acceptance criterion

#### Test Coverage (Write These FIRST)

**Feature Tests (write before controller/service implementation):**
- `tests/Feature/WalletTest.php`
  - User can view wallet page with balance
  - User can initiate topup
  - User can complete topup payment
  - User can request withdrawal
  - User can view transactions
  - User can filter transactions
  - Balance updates correctly on successful topup
  - Balance updates correctly on successful withdrawal
  - Validation errors for invalid amounts
  - Validation errors for insufficient balance

**Unit Tests (write before service implementation):**
- `tests/Unit/WalletServiceTest.php`
  - `topup()` method creates transaction and updates balance
  - `withdraw()` method creates transaction and decrements balance
  - `updateBalance()` method correctly increments/decrements balance
  - `getUserTransactions()` method returns filtered transactions
  - Balance calculation is accurate

- `tests/Unit/PaystackServiceTransferTest.php`
  - `createTransferRecipient()` creates recipient successfully
  - `initiateTransfer()` initiates transfer successfully
  - `verifyTransfer()` verifies transfer status

**Browser Tests (write before UI implementation):**
- `tests/Browser/WalletPageTest.php`
  - Wallet page loads with balance displayed
  - Top Up modal opens and allows amount entry
  - Withdraw modal opens and allows amount/bank details entry
  - Transactions table displays correctly
  - Filters work correctly
  - Pagination works correctly

#### Test Organization
- One test file per controller/service
- Group related tests using `describe()` blocks
- Use descriptive test names that explain the scenario
- Follow AAA pattern: Arrange, Act, Assert

### Rollout Plan

1. **Phase 1: Database Migration**
   - Run migration to add wallet fields
   - Verify existing transactions are not affected
   - Test on staging environment

2. **Phase 2: Backend Implementation**
   - Deploy WalletService and PaystackService extensions
   - Deploy WalletController
   - Test topup flow end-to-end

3. **Phase 3: Frontend Implementation**
   - Deploy updated wallet page
   - Test UI flows with real Paystack test mode
   - Verify transaction display

4. **Phase 4: Withdrawal Feature**
   - Deploy withdrawal functionality
   - Test with Paystack test transfers
   - Verify balance updates

5. **Phase 5: Production**
   - Switch to Paystack live mode
   - Monitor webhook processing
   - Monitor balance updates
   - Monitor error rates

### Metrics / Observability

**Key Metrics:**
- Topup success rate (target: >95%)
- Withdrawal success rate (target: >90%)
- Average time to process withdrawal (target: <24 hours)
- Balance accuracy (reconciliation job should show 0 discrepancies)
- Transaction query performance (target: <200ms for 1000 transactions)

**Logging:**
- Log all topup initiations with amount and user_id
- Log all withdrawal requests with amount and user_id
- Log all balance updates with before/after values
- Log Paystack API errors with full response
- Log webhook processing with transaction reference

### Open Questions

- Should withdrawals require admin approval before processing?
- What is the minimum withdrawal amount? (Suggested: 10 GHS)
- What is the maximum withdrawal amount per transaction? (Suggested: 50,000 GHS)
- Should there be daily/weekly withdrawal limits?
- How should we handle bank account verification?
- Should users be able to save multiple bank accounts?
- What happens if Paystack transfer fails after balance is decremented?
- Should we implement automatic retry for failed transfers?

### Definition of Done

- [ ] **TDD Process Followed**: All code was written using TDD (tests written first, then implementation)
- [ ] All acceptance criteria pass in feature tests (tests were written before implementation)
- [ ] All unit tests pass (service/domain logic tested before implementation)
- [ ] No new linter errors; Pint formatting applied to changed PHP files
- [ ] Browser tests (if added) pass with no console errors
- [ ] Test coverage: All new code paths are covered by tests
- [ ] All existing tests pass (or are updated to reflect new behavior)
- [ ] Database migration runs successfully on staging
- [ ] Topup flow works end-to-end with Paystack test mode
- [ ] Withdrawal flow works end-to-end with Paystack test mode
- [ ] Balance updates correctly for all transaction types
- [ ] Transactions table displays all transaction types correctly
- [ ] Filters and pagination work correctly
- [ ] Webhook processing updates balances correctly
- [ ] Code review confirms TDD approach was followed
- [ ] Stakeholder demo reviewed; any noted issues addressed or ticketed
- [ ] Documentation updated (if applicable)

