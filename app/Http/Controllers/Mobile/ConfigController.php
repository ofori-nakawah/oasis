<?php

namespace App\Http\Controllers\Mobile;

use App\Models\Industry;
use App\Models\Language;
use App\Models\Skill;
use App\Traits\Responses;


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

    public function get_industries()
    {
        $industries = Industry::orderBy('name')->get();
        return $this->success_response($industries, "");
    }
}
