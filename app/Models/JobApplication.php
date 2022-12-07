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
}
