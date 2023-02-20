<?php

namespace App\Http\Controllers\Web;

use App\Helpers\Notifications as Notifications;
use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;


class PostController extends Controller
{
    const JOB_SEARCH_RADIUS = 7;
    const VOLUNTEER_SEARCH_RADIUS = 10;

    public function volunteerism()
    {
        return view("volunteerism.index");
    }

    public function work()
    {
        return view("work.index");
    }

    public function type_of_work($type_of_user)
    {
        if (!$type_of_user) {return back()->with("danger", "Invalid request");}

        if ($type_of_user != "seeker" && $type_of_user != "employer") {
            return back()->with("danger", "Invalid request");
        }

        return view("work.type", compact("type_of_user"));
    }

    public function list_jobs($type_of_user, $type_of_work)
    {
        if (!$type_of_user || !$type_of_work) {return back()->with("danger", "Invalid request");}

        if ($type_of_user != "seeker" && $type_of_user != "employer") {return back()->with("danger", "Invalid request");}

        if ($type_of_work != "quick-job" && $type_of_work != "fixed-term" && $type_of_work != "permanent") {return back()->with("danger", "Invalid request");}

        switch ($type_of_work) {
            case "quick-job":
                if ($type_of_work == "quick-job") {
                    return $this->list_quick_jobs();
                } else {
                    return view("quick_jobs.create");
                }
                break;
        }

        return back()->with("danger", "Invalid request");
    }

    public function list_volunteer_activities()
    {
        $posts = [];

        //get user coordinates
        $user_location = auth()->user()->location_coords;
        if (!$user_location) {
            return back()->with("danger", "Could not retrieve user's current location");
        }

        $user_location_lat = explode(',', $user_location)[0];
        $user_location_lng = explode(',', $user_location)[1];

        $volunteer_near_me = collect();
        $posts = Post::where("user_id", "!=", auth()->id())->where("status", "active")->where("type", "VOLUNTEER")->get();
        $posts->map(function ($post) use ($user_location_lat, $user_location_lng, $volunteer_near_me) {
            //get post coordinates
            $post_location_lat = explode(',', $post->coords)[1];
            $post_location_lng = explode(',', $post->coords)[0];

            $distance = $this->get_distance($user_location_lat, $user_location_lng, $post_location_lat, $post_location_lng, "K");
            $post["organiser_name"] = $post->user->name;
            $post["distance"] = number_format($distance, 2);
            if ($distance <= self::VOLUNTEER_SEARCH_RADIUS) {
                $volunteer_near_me->push($post);
            }

            $has_already_applied = JobApplication::where("user_id", auth()->id())->where("post_id", $post->id)->first();
            if ($has_already_applied) {
                $post->has_already_applied = "yes";
            }

            return $post;
        });
        $posts = $volunteer_near_me;

        return view("volunteerism.list", compact("posts"));
    }

    public function list_quick_jobs()
    {
        $posts = [];

        //get user coordinates
        $user_location = auth()->user()->location_coords;
        if (!$user_location) {
            return back()->with("danger", "Could not retrieve user's current location");
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
        $posts = Post::where("user_id", "!=", auth()->id())->where("status", "active")->where("type", "QUICK_JOB")->whereIn("category_id", $user_interests)->get();

        /**
         * filter using distance
         */
        $jobs_near_me = collect();
        foreach ($posts as $post) {
            $post_location_lat = explode(',', $post->coords)[1];
            $post_location_lng = explode(',', $post->coords)[0];
            $distance = $this->get_distance($user_location_lat, $user_location_lng, $post_location_lat, $post_location_lng, "K");

            if ($distance <= self::JOB_SEARCH_RADIUS) {
                $post["organiser_name"] = $post->user->name;
                $post["distance"] = number_format($distance, 2);
                $jobs_near_me->push($post);
            }

            $has_already_applied = JobApplication::where("user_id", auth()->id())->where("post_id", $post->id)->first();
            if ($has_already_applied) {
                $post->has_already_applied = "yes";
            }
        }
        $posts = $jobs_near_me;

        return view("work.quick_jobs.index", compact("posts"));
    }

    private function get_distance($lat1, $lon1, $lat2, $lon2, $unit)
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

    /**
     * @param $uuid
     * @return mixed
     */
    public function show_volunteer_activity($uuid)
    {
        if (!$uuid) {return back()->with("danger", "Invalid request. Kindly try again.");}

        $original_post = Post::where("id", $uuid)->first();
        if (!$original_post) {return back()->with("danger", "Oops...something went wrong. We could not retrieve post details.");}
        $has_already_applied = JobApplication::where("user_id", auth()->id())->where("post_id", $original_post->id)->first();
        if ($has_already_applied) {
            $original_post->has_already_applied = "yes";
        }
        $original_post->user;

        $posts = [];

        //get user coordinates
        $user_location = auth()->user()->location_coords;
        if (!$user_location) {
            return back()->with("danger", "Could not retrieve user's current location");
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
        $posts = Post::where("user_id", "!=", auth()->id())->where("status", "active")->where("type", "QUICK_JOB")->whereIn("category_id", $user_interests)->get();

        /**
         * filter using distance
         */
        $volunteer_near_me = collect();
        $posts = Post::where("user_id", "!=", auth()->id())->where("status", "active")->where("type", "VOLUNTEER")->get();
        $posts->map(function ($post) use ($user_location_lat, $user_location_lng, $volunteer_near_me) {
            //get post coordinates
            $post_location_lat = explode(',', $post->coords)[1];
            $post_location_lng = explode(',', $post->coords)[0];

            $distance = $this->get_distance($user_location_lat, $user_location_lng, $post_location_lat, $post_location_lng, "K");
            $post["organiser_name"] = $post->user->name;
            $post["distance"] = number_format($distance, 2);
            if ($distance <= self::VOLUNTEER_SEARCH_RADIUS) {
                $volunteer_near_me->push($post);
            }

            $has_already_applied = JobApplication::where("user_id", auth()->id())->where("post_id", $post->id)->first();
            if ($has_already_applied) {
                $post->has_already_applied = "yes";
            }

            return $post;
        });
        $posts = $volunteer_near_me;

        return view("volunteerism.show", compact("original_post", "posts"));
    }

    public function apply_for_job($uuid)
    {
        if (!$uuid) {return back()->with("danger", "Invalid request. Kindly try again.");}

        $post = Post::where("id", $uuid)->first();
        if (!$post) {return back()->with("danger", "Oops...something went wrong. We could not retrieve post details.");}

        //check if user has applied already
        $has_already_applied = JobApplication::where("user_id", auth()->id())->where("post_id", $uuid)->first();
        if ($has_already_applied) {
            return back()->with("warning", "You have already applied for this job.");
        }

        $job_application = new JobApplication();
        $job_application->user_id = auth()->id();
        $job_application->post_id = $uuid;
        try {
            $job_application->save();

            /**
             * create notification
             */
            $post->user;
            Notifications::PushUserNotification($post, $job_application, auth()->user(), "SUCCESSFUL_JOB_APPLICATION");
        } catch (QueryException $e) {
            Log::error("ERROR SAVING JOB APPLICATION >>>>>>>>>> " . $job_application . " >>>>>>>>> " . $e);
            return back()->with("danger", "We encountered an error processing your request. Kindly try again.");
        }

        return back()->with("success", "Congratulations! Your application was successful");
    }

    public function create_volunteer_activity()
    {
        return view("volunteerism.create");
    }
}
