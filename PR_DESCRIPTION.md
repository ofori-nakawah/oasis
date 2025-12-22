# P2P Payment Integration - Split Payment System

## Summary
Implements a split payment system for P2P (peer-to-peer) jobs with configurable payment percentages. Payments are split into two installments: an initial payment at quote approval and a final payment at job completion. Both percentages are configurable via environment variables.

## Features
- ✅ Split payment system (configurable percentages via `.env`)
- ✅ Payment modal with Paystack checkout iframe
- ✅ Automatic quote approval on initial payment success
- ✅ Automatic job closure on final payment success
- ✅ Webhook integration for automatic payment processing
- ✅ Payment status tracking and transaction history
- ✅ Support for 0% payments (skip payment flow)

## Changes

### Database
- Added payment tracking fields to `posts` table (initial/final payment amounts, timestamps, transaction IDs)
- Added quote approval tracking to `job_applications` table

### Backend
- New `P2PPaymentService` for payment calculations and initiation
- New `P2PPaymentController` for payment endpoints
- Updated `PaystackService` to handle P2P payment webhooks
- Enhanced `Post` and `JobApplication` models with payment relationships

### Frontend
- Payment modal component with Paystack iframe integration
- JavaScript payment flow with status polling
- Updated quote approval and job closure views

### Configuration
- New `config/p2p.php` for payment percentage settings
- Environment variable validation (percentages must sum to 100)

## Environment Variables Required

```env
P2P_INITIAL_PAYMENT_PERCENTAGE=10
P2P_FINAL_PAYMENT_PERCENTAGE=90
```

## Payment Flow

### Quote Approval
1. User clicks "Approve Quote" → Payment modal opens
2. User completes payment via Paystack checkout
3. On success: Quote approved, post updated, transaction recorded

### Job Closure
1. User clicks "Close Job & Pay" → Payment modal opens
2. User completes final payment via Paystack checkout
3. On success: Job closed, post updated, transaction recorded

## Testing Checklist
- [ ] Run database migrations
- [ ] Add environment variables to `.env`
- [ ] Test quote approval payment flow
- [ ] Test job closure payment flow
- [ ] Verify webhook processing
- [ ] Test with 0% payment percentages (should skip payment)
- [ ] Verify payment status tracking

## Migration Instructions

1. Run migrations:
   ```bash
   php artisan migrate
   ```

2. Add to `.env`:
   ```env
   P2P_INITIAL_PAYMENT_PERCENTAGE=10
   P2P_FINAL_PAYMENT_PERCENTAGE=90
   ```

3. Configure Paystack webhook (if not already done):
   - URL: `https://oasis.myvork.com/api/v1/webhooks/paystack`

## Related Issues
- Implements payment on P2P jobs with split payments
- Configurable payment percentages (10/90, 50/50, 0/100, etc.)

## Breaking Changes
None - This is a new feature addition.

## Screenshots
- Payment modal with Paystack checkout iframe
- Quote approval with payment flow
- Job closure with payment flow

