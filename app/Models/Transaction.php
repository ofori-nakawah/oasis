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
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'metadata' => 'array',
        'customer_data' => 'array',
        'last_webhook_event' => 'array',
    ];

    // Status constants
    public const STATUS_PENDING = 'pending';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';
    public const STATUS_ABANDONED = 'abandoned';
    public const STATUS_REVERSED = 'reversed';

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
}
