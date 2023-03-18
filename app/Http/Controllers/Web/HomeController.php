<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Skill;
use App\Traits\OpportunitiesAroundMe;

class HomeController extends Controller
{
    use OpportunitiesAroundMe;

    const JOB_SEARCH_RADIUS = 7;
    const VOLUNTEER_SEARCH_RADIUS = 10;

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
            $languages = Language::all();
            return view("onboarding.languages", compact("languages"));
        }

        /**
         * get dashboard values
         */
        $job_history = auth()->user()->job_applications->where("status", "confirmed");
        $average_rating = auth()->user()->rating;
        $volunteer_hours = auth()->user()->volunteer_hours;
        $total_earnings = auth()->user()->total_earnings;

        $jobs_count = 0;
        $volunteer_count = 0;

        foreach ($job_history as $vork) {
            if ($vork->job_post && $vork->job_post->status == "closed") {
                if ($vork->job_post->type === "VOLUNTEER") {
                    $vork->ref_id = "VO" . explode("-", $vork->id)[0];
                    $volunteer_count++;
                } else {
                    $vork->ref_id = "QJ" . explode("-", $vork->id)[0];
                    $jobs_count++;
                }

                $vork->job_post;
                $vork->rating_and_reviews;
            }
        }

        $dashboard_analytics = array(
            "number_of_jobs" => $jobs_count,
            "number_of_activities" => $volunteer_count,
            "average_rating" => number_format($average_rating, 2),
            "location" => auth()->user()->location_name,
            "location_coords" => auth()->user()->location_coords,
            "volunteer_hours" => $volunteer_hours,
            "total_earnings" => number_format($total_earnings, 2),
        );

        /**
         * get available opportunities for user
         */
        $opportunities = self::GetOpportunitiesAroundMe(self::VOLUNTEER_SEARCH_RADIUS);

        return view('home', compact("dashboard_analytics", "job_history", "opportunities"));
    }
}
