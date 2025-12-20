<?php

namespace App\Models;

use App\Traits\Uuids;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Post extends Model
{
    use HasFactory, Uuids, Notifiable;

    protected $dates = ['closed_at', 'start_date', 'end_date', 'initial_payment_paid_at', 'final_payment_paid_at'];

    public function user()
    {
        return $this->belongsTo("App\Models\User", "user_id");
    }

    public function industry()
    {
        return $this->belongsTo("App\Models\Industry", "industry_id");
    }

    public function applications()
    {
        return $this->hasMany("App\Models\JobApplication");
    }

    public function initialPaymentTransaction()
    {
        return $this->belongsTo(Transaction::class, 'initial_payment_transaction_id');
    }

    public function finalPaymentTransaction()
    {
        return $this->belongsTo(Transaction::class, 'final_payment_transaction_id');
    }

    public function formattedCreatedAt()
    {
        return $this->created_at->diffForHumans();
    }

    public function rating_and_reviews()
    {
        return $this->hasMany("App\Models\RatingReview", "post_id");
    }

    /**
     * Check if initial payment has been made
     */
    public function hasInitialPayment(): bool
    {
        return !empty($this->initial_payment_paid_at) && !empty($this->initial_payment_transaction_id);
    }

    /**
     * Check if final payment has been made
     */
    public function hasFinalPayment(): bool
    {
        return !empty($this->final_payment_paid_at) && !empty($this->final_payment_transaction_id);
    }

    /**
     * Check if all payments are complete
     */
    public function isFullyPaid(): bool
    {
        return $this->payment_status === 'fully_paid' || ($this->hasInitialPayment() && $this->hasFinalPayment());
    }
}
