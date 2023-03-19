<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Post extends Model
{
    use HasFactory, Uuids, Notifiable;

    protected $dates = ['closed_at'];

    public function user()
    {
        return $this->belongsTo("App\Models\User", "user_id");
    }

    public function applications()
    {
        return $this->hasMany("App\Models\JobApplication");
    }

}
