<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatingReview extends Model
{
    use HasFactory, Uuids;

    public function user()
    {
        return $this->belongsTo("App\Models\User", "user_id");
    }

    public function job_application()
    {
        return $this->belongsTo("App\Models\JobApplication", "job_application_id");
    }

    public function post()
    {
        return $this->belongsTo("App\Models\Post", "post_id");
    }

    public function setRatingAttribute($value)
    {
        $this->attributes['rating'] = number_format((float)$value, 2, '.', '');
    }
}
