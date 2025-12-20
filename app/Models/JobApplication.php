<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    use HasFactory, Uuids;

    public function user()
    {
        return $this->belongsTo("App\Models\User", "user_id");
    }

    public function job_post()
    {
        return $this->belongsTo("App\Models\Post", "post_id");
    }

    public function rating_and_reviews()
    {
        return $this->hasOne("App\Models\RatingReview");
    }

    public function approvedBy()
    {
        return $this->belongsTo("App\Models\User", "quote_approved_by", "id");
    }

    /**
     * Check if quote has been approved
     */
    public function isQuoteApproved(): bool
    {
        return !empty($this->quote_approved_at);
    }
}
