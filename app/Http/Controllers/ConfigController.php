<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Skill;
use App\Traits\Responses;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    use Responses;

    public function get_skills()
    {
        $skills = Skill::all();
        return $this->success_response($skills, "");
    }

    public function get_languages()
    {
        $skills = Language::all();
        return $this->success_response($skills, "");
    }
}
