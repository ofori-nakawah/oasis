<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Skill;
use App\Services\PushNotification;
use App\Traits\OpportunitiesAroundMe;

class HomeController extends Controller
{
    use OpportunitiesAroundMe;

    const JOB_SEARCH_RADIUS = 10;
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
            $skills_and_interest = Skill::orderBy("name", "asc")->all();
            return view("onboarding.skills", compact("skills_and_interest"));
        }

        /**
         * check if location is set else send user to set location
         */
        if (auth()->user()->is_languages_set == "0") {
            $languages = Language::orderBy("name", "asc")->all();
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
        $estIncomeTax = 0;

        foreach ($job_history as $vork) {
            if ($vork->job_post && $vork->job_post->status == "closed") {
                if ($vork->job_post->type === "VOLUNTEER") {
                    $vork->ref_id = "VO" . explode("-", $vork->id)[0];
                    $volunteer_count++;
                }

                if ($vork->job_post->type === "QUICK_JOB") {
                    $vork->ref_id = "QJ" . explode("-", $vork->id)[0];
                    $jobs_count++;

                    /**
                     * calculate income tax
                     */
                    $estIncomeTax += (5/100) * $vork->job_post->final_payment_amount;
                }

                $vork->period = $vork->job_post->closed_at->format('M Y');
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
            "estIncomeTax" => number_format($estIncomeTax, 2),
        );

        /**
         * get available opportunities for user
         */
        $opportunities = self::GetOpportunitiesAroundMe(self::VOLUNTEER_SEARCH_RADIUS);
        $incomeData = self::GetIncomeTrendData()["income"];
        $taxData = self::GetIncomeTrendData()["tax"];

        return view('home', compact("dashboard_analytics", "job_history", "opportunities", "incomeData", "taxData"));
    }

    private static function GetIncomeTrendData()
    {
        $trendYear = date('Y');
        $janIncome = 0;
        $janTax = 0;
        $febIncome = 0;
        $febTax = 0;
        $marIncome = 0;
        $marTax = 0;
        $aprIncome = 0;
        $aprTax = 0;
        $mayIncome = 0;
        $mayTax = 0;
        $junIncome = 0;
        $junTax = 0;
        $julIncome = 0;
        $julTax = 0;
        $augIncome = 0;
        $augTax = 0;
        $sepIncome = 0;
        $sepTax = 0;
        $octIncome = 0;
        $octTax = 0;
        $novIncome = 0;
        $novTax = 0;
        $decIncome = 0;
        $decTax = 0;

        $job_history = auth()->user()->job_applications->where("status", "confirmed");
        foreach ($job_history as $vork) {
            /**
             * make sure job is closed and is actually a quick job not volunteer activity or other
             * job types
             */
            if ($vork->job_post && $vork->job_post->status == "closed" && $vork->job_post->type === "QUICK_JOB") {
                /**
                 * let's get monthly income for the current year
                 */
                $amount = $vork->job_post->final_payment_amount;
                $jobMonth = $vork->job_post->closed_at->format('M');
                $jobYear = $vork->job_post->closed_at->format('Y');
                if ($trendYear === $jobYear) {
                    switch ($jobMonth) {
                        case "Jan":
                            $janIncome += $amount;
                            $janTax += $amount * (5 / 100);
                            break;
                        case "Feb":
                            $febIncome += $amount;
                            $febTax += $amount * (5 / 100);
                            break;
                        case "Mar":
                            $marIncome += $amount;
                            $marTax += $amount * (5 / 100);
                            break;
                        case "Apr":
                            $aprIncome += $amount;
                            $aprTax += $amount * (5 / 100);
                            break;
                        case "May":
                            $mayIncome += $amount;
                            $mayTax += $amount * (5 / 100);
                            break;
                        case "Jun":
                            $junIncome += $amount;
                            $junTax += $amount * (5 / 100);
                            break;
                        case "Jul":
                            $julIncome += $amount;
                            $julTax += $amount * (5 / 100);
                            break;
                        case "Aug":
                            $augIncome += $amount;
                            $augTax += $amount * (5 / 100);
                            break;
                        case "Sep":
                            $sepIncome += $amount;
                            $sepTax += $amount * (5 / 100);
                            break;
                        case "Oct":
                            $octIncome += $amount;
                            $octTax += $amount * (5 / 100);
                            break;
                        case "Nov":
                            $novIncome += $amount;
                            $novTax += $amount * (5 / 100);
                            break;
                        case "Dec":
                            $decIncome += $amount;
                            $decTax += $amount * (5 / 100);
                            break;
                    }
                }
            }
        }
        $incomeTrendData = array(
            "income" => ["janIncome" => $janIncome, "febIncome" => $febIncome, "marIncome" => $marIncome, "aprIncome" =>$aprIncome, "mayIncome" => $mayIncome, "junIncome" => $junIncome, "julIncome" => $julIncome, "augIncome" => $augIncome, "sepIncome" => $sepIncome, "octIncome" => $octIncome, "novIncome" => $novIncome, "decIncome" => $decIncome],
            "tax" => ["janTax" => $janTax, "febTax" => $febTax, "marTax" => $marTax, "aprTax" =>$aprTax, "mayTax" => $mayTax, "junTax" => $junTax, "julTax" => $julTax, "augTax" => $augTax, "sepTax" => $sepTax, "octTax" => $octTax, "novTax" => $novTax, "decTax" => $decTax],
        );
        return $incomeTrendData;
    }
}
