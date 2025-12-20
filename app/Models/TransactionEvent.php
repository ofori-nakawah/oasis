<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'event_type',
        'paystack_event_id',
        'payload',
        'processed',
        'processed_at',
        'processing_error',
    ];

    protected $casts = [
        'payload' => 'array',
        'processed' => 'boolean',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the transaction that owns the event.
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Mark event as processed.
     */
    public function markAsProcessed(): void
    {
        $this->update([
            'processed' => true,
            'processed_at' => now(),
        ]);
    }

    /**
     * Mark event as failed with error message.
     */
    public function markAsFailed(string $error): void
    {
        $this->update([
            'processed' => true,
            'processed_at' => now(),
            'processing_error' => $error,
        ]);
    }
}
