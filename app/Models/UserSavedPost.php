<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSavedPost extends Model
{
    use HasFactory;

    public function posts()
    {
        return $this->hasMany("App/Models/Posts", "user_id");
    }
}
