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

    protected $dates = ['closed_at', 'start_date', 'end_date'];

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

    public function formattedCreatedAt()
    {
        return $this->created_at->diffForHumans();
    }

    public function rating_and_reviews()
    {
        return $this->hasMany("App\Models\RatingReview", "post_id");
    }
}
