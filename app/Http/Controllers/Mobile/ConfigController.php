<?php

namespace App\Http\Controllers\Mobile;

use App\Models\Language;
use App\Models\Skill;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConfigController extends Controller
{
    use Responses;

    public function get_skills()
    {
        $skills = Skill::orderBy('name')->get();
        return $this->success_response($skills, "");
    }

    public function get_languages()
    {
        $skills = Language::orderBy('name')->get();
        return $this->success_response($skills, "");
    }
}
