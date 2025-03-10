<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    use HasFactory, Uuids;

    public function posts()
    {
        return $this->hasMany("App\Models\IndustryPost", "post_id");
    }
}
