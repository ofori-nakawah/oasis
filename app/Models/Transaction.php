<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transactions';

    protected $fillable = [
        'user_id',
        'uuid',
        'paystack_reference',
        'client_reference',
        'amount',
        'currency',
        'email',
        'pay_link_url', // Legacy field from original migration
        'authorization_url',
        'access_code',
        'status',
        'gateway_response',
        'channel',
        'payment_type',
        'paid_at',
        'metadata',
        'customer_data',
        'last_webhook_event',
        'transaction_type',
        'transaction_category',
        'bank_account_details',
        'recipient_code',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'metadata' => 'array',
        'customer_data' => 'array',
        'last_webhook_event' => 'array',
        'bank_account_details' => 'array',
    ];

    // Status constants
    public const STATUS_PENDING = 'pending';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';
    public const STATUS_ABANDONED = 'abandoned';
    public const STATUS_REVERSED = 'reversed';

    // Transaction type constants
    public const TYPE_TOPUP = 'topup';
    public const TYPE_WITHDRAWAL = 'withdrawal';
    public const TYPE_PAYMENT = 'payment';
    public const TYPE_EARNING = 'earning';
    public const TYPE_REFUND = 'refund';

    // Transaction category constants
    public const CATEGORY_CREDIT = 'credit';
    public const CATEGORY_DEBIT = 'debit';

    /**
     * Get the user that owns the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transaction events for the transaction.
     */
    public function events()
    {
        return $this->hasMany(TransactionEvent::class);
    }

    /**
     * Check if transaction is successful.
     */
    public function isSuccessful(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    /**
     * Check if transaction is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if transaction is failed.
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Scope a query to only include credit transactions.
     */
    public function scopeCredits($query)
    {
        return $query->where('transaction_category', self::CATEGORY_CREDIT);
    }

    /**
     * Scope a query to only include debit transactions.
     */
    public function scopeDebits($query)
    {
        return $query->where('transaction_category', self::CATEGORY_DEBIT);
    }

    /**
     * Scope a query to only include topup transactions.
     */
    public function scopeTopups($query)
    {
        return $query->where('transaction_type', self::TYPE_TOPUP);
    }

    /**
     * Scope a query to only include withdrawal transactions.
     */
    public function scopeWithdrawals($query)
    {
        return $query->where('transaction_type', self::TYPE_WITHDRAWAL);
    }

    /**
     * Scope a query to only include payment transactions.
     */
    public function scopePayments($query)
    {
        return $query->where('transaction_type', self::TYPE_PAYMENT);
    }

    /**
     * Scope a query to only include earning transactions.
     */
    public function scopeEarnings($query)
    {
        return $query->where('transaction_type', self::TYPE_EARNING);
    }

    /**
     * Get formatted amount with sign based on category.
     */
    public function getFormattedAmountAttribute(): string
    {
        $sign = $this->transaction_category === self::CATEGORY_CREDIT ? '+' : '-';
        return $sign . number_format(abs($this->amount), 2);
    }

    /**
     * Check if transaction is a credit.
     */
    public function isCredit(): bool
    {
        return $this->transaction_category === self::CATEGORY_CREDIT;
    }

    /**
     * Check if transaction is a debit.
     */
    public function isDebit(): bool
    {
        return $this->transaction_category === self::CATEGORY_DEBIT;
    }
}
