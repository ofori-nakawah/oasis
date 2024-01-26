<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndustryPost extends Model
{
    use HasFactory, Uuids;

    public function industry()
    {
        return $this->belongsTo("App\Models\Industry", "industry_id");
    }

    public function post()
    {
        return $this->belongsTo("App\Models\Post", "post_id");
    }
}
