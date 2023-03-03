<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Skill;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    public function index()
    {
        /**
         * check if location is set else send user to set location
         */
        if (auth()->user()->location_name == "" || auth()->user()->location_name == null) {
            return view("onboarding.location");
        }

        /**
         * check if location is set else send user to set location
         */
        if (auth()->user()->is_core_skills_set == "0") {
            $skills_and_interest = Skill::all();
            return view("onboarding.skills", compact("skills_and_interest"));
        }

        /**
         * check if location is set else send user to set location
         */
        if (auth()->user()->is_languages_set == "0") {
            $skills_and_interest = Language::all();
            return view("onboarding.languages", compact("skills_and_interest"));
        }

        return view('home');
    }
}
