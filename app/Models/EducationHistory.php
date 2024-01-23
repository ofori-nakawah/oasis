<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationHistory extends Model
{
    use HasFactory, Uuids;

    protected $table = "education_history";

    public function user()
    {
        return $this->belongsTo("App\Models\User", "user_id");
    }
}
