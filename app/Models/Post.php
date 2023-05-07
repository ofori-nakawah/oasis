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

    protected $dates = ['closed_at', 'date'];

    public function getDateAttribute($date)
    {
        return $this->attributes['date'] = Carbon::parse(str_replace("/", "-", $date))->format('Y-m-d');
    }

    public function user()
    {
        return $this->belongsTo("App\Models\User", "user_id");
    }

    public function applications()
    {
        return $this->hasMany("App\Models\JobApplication");
    }

    public function formattedCreatedAt()
    {
        return $this->created_at->diffForHumans();
    }

}
