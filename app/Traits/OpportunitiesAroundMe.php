<?php
namespace App\Traits;

use App\Models\JobApplication;
use App\Models\Post;
use Illuminate\Support\Facades\Log;

trait OpportunitiesAroundMe
{
    private static function CalculateDistanceBtnUserAndPost($lat1, $lon1, $lat2, $lon2, $unit)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    private static function VolunteerProjects($search_radius)
    {
        $user_location = auth()->user()->location_coords;
        if (!$user_location) {
            Log::error("NO LOCATION COORDS FOUND FOR USER >>>>> " . auth()->user());
            return null;
        }

        $user_location_lat = explode(',', $user_location)[0];
        $user_location_lng = explode(',', $user_location)[1];

        $volunteer_near_me = collect();
        $posts = Post::where("user_id", "!=", auth()->id())->where("status", "active")->where("type", "VOLUNTEER")->whereNull('deleted_at')->get();
        $posts->map(function ($post) use ($user_location_lat, $user_location_lng, $volunteer_near_me, $search_radius) {
            //get post coordinates
            $post_location_lat = explode(',', $post->coords)[0];
            $post_location_lng = explode(',', $post->coords)[1];

            $distance = self::CalculateDistanceBtnUserAndPost($user_location_lat, $user_location_lng, $post_location_lat, $post_location_lng, "K");
            $post["organiser_name"] = $post->user->name;
            $post["distance"] = number_format($distance, 2);
            if ($distance <= $search_radius) {
                $has_already_applied = JobApplication::where("user_id", auth()->id())->where("post_id", $post->id)->first();
                if (!$has_already_applied) {
                    $volunteer_near_me->push($post);
                }
            }

            return $post;
        });
        return $volunteer_near_me->sortBy("distance");;
    }

    private static function QuickJobs($search_radius)
    {
        //get user coordinates
        $user_location = auth()->user()->location_coords;
        if (!$user_location) {
            Log::error("NO LOCATION COORDS FOUND FOR USER >>>>> " . auth()->user());
            return null;
        }

        $user_location_lat = explode(',', $user_location)[0];
        $user_location_lng = explode(',', $user_location)[1];

        $_user_interests = auth()->user()->skills;
        $user_interests = array();
        foreach ($_user_interests as $interest) {
            array_push($user_interests, $interest->skill->id);
        }

        /**
         * filter using:
         * post type
         * interests | skills
         */
        $posts = Post::where("user_id", "!=", auth()->id())->where("status", "active")->where("type", "QUICK_JOB")->whereIn("category_id", $user_interests)->whereNull('deleted_at')->get();

        /**
         * filter using distance
         */
        $jobs_near_me = collect();
        foreach ($posts as $post) {
            $post_location_lat = explode(',', $post->coords)[0];
            $post_location_lng = explode(',', $post->coords)[1];
            $distance = self::CalculateDistanceBtnUserAndPost($user_location_lat, $user_location_lng, $post_location_lat, $post_location_lng, "K");

            if ($distance <= $search_radius) {
                $post["organiser_name"] = $post->user->name;
                $post["distance"] = number_format($distance, 2);
                $has_already_applied = JobApplication::where("user_id", auth()->id())->where("post_id", $post->id)->first();
                if (!$has_already_applied) {
                    $jobs_near_me->push($post);
                }
            }
        }
        return $jobs_near_me->sortBy("distance");;
    }

    public static function GetOpportunitiesAroundMe($search_radius) {
        return [
            "volunteer_activities" => self::VolunteerProjects($search_radius),
            "quick_jobs" => self::QuickJobs($search_radius)
        ];
    }
}
