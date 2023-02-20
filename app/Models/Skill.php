<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory, Uuids;

    public function users()
    {
        return $this->hasMany("App\Models\SkillUser", "skill_id");
    }
}
