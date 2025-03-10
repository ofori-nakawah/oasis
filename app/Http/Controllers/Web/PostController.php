<?php

namespace App\Http\Controllers\Web;

use App\Helpers\Notifications as Notifications;
use App\Http\Controllers\Controller;
use App\Models\Industry;
use App\Models\JobApplication;
use App\Models\Post;
use App\Models\RatingReview;
use App\Models\Skill;
use App\Models\User;
use App\Models\UserSavedPost;
use App\Services\PushNotification;
use App\Traits\Responses;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

/**
 * Class PostController
 * @package App\Http\Controllers\Web
 */
class PostController extends Controller
{
    const JOB_SEARCH_RADIUS = 5;
    const VOLUNTEER_SEARCH_RADIUS = 10;

    use Responses;

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
        if (!$type_of_user) {
            return back()->with("danger", "Invalid request");
        }

        if ($type_of_user != "seeker" && $type_of_user != "employer") {
            return back()->with("danger", "Invalid request");
        }

        return view("work.type", compact("type_of_user"));
    }

    public function list_jobs($type_of_user, $type_of_work)
    {
        Log::info("type_of_work: " . $type_of_work);


        if (!$type_of_user || !$type_of_work) {
            return back()->with("danger", "Invalid request");
        }

        $valid_work_types = ["quick-job", "fixed-term", "permanent", "p2p"];
        if (!in_array($type_of_work, $valid_work_types)) {
            return back()->with("danger", "Invalid request");
        }


        switch ($type_of_work) {
            case "quick-job":
                if ($type_of_user == "seeker") {
                    return $this->list_quick_jobs();
                } else {
                    $categories = Skill::orderBy('name')->get();
                    return view("work.quick_jobs.create", compact("categories"));
                }
            case "fixed-term":
                if ($type_of_user == "seeker") {
                    return $this->list_part_time_jobs();
                } else {
                    $categories = Skill::orderBy('name')->get();
                    return view("work.part_time_jobs.create", compact("categories"));
                }
                break;
            case "permanent":
                if ($type_of_user == "seeker") {
                    return $this->list_permanent_jobs();
                } else {
                    $categories = Skill::orderBy('name')->get();
                    $industries = Industry::orderBy('name')->get();
                    return view("work.permanent.create", compact("categories", "industries"));
                }
                break;
            case "p2p":
                if ($type_of_user != "seeker") {
                    $skills_and_interest = Skill::orderBy('name')->get();
                    return view("work.p2p.search", compact("skills_and_interest"));
                }
        }

        return back()->with("danger", "Invalid request");
    }

    public function list_volunteer_activities()
    {
        //get user coordinates
        $user_location = auth()->user()->location_coords;
        if (!$user_location) {
            return back()->with("danger", "Could not retrieve user's current location");
        }

        $user_location_lat = json_decode($user_location)->latitude ??  explode(',', $user_location)[0];
        $user_location_lng = json_decode($user_location)->longitude ?? explode(',', $user_location)[1];

        $volunteer_near_me = collect();
        $posts = Post::where("user_id", "!=", auth()->id())->where("status", "active")->where("type", "VOLUNTEER")->whereNull('deleted_at')->orderByDesc("created_at")->get();
        $posts->map(function ($post) use ($user_location_lat, $user_location_lng, $volunteer_near_me) {
            //get post coordinates
            $post_location_lat = json_decode($post->coords)->latitude ?? explode(',', $post->coords)[0];
            $post_location_lng = json_decode($post->coords)->longitude ?? explode(',', $post->coords)[1];

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
        //get user coordinates
        $user_location = auth()->user()->location_coords;

        if (!$user_location) {
            return back()->with("danger", "Could not retrieve user's current location");
        }

        $user_location_lat = json_decode($user_location)->latitude ??  explode(',', $user_location)[0];
        $user_location_lng = json_decode($user_location)->longitude ?? explode(',', $user_location)[1];

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
        $posts = Post::where("user_id", "!=", auth()->id())->where("status", "active")->where("type", "QUICK_JOB")->whereIn("category_id", $user_interests)->whereNull('deleted_at')->orderByDesc("created_at")->get();

        /**
         * filter using distance
         */
        $jobs_near_me = collect();
        foreach ($posts as $post) {
            $post_location_lat = json_decode($post->coords)->latitude ?? explode(',', $post->coords)[0];
            $post_location_lng = json_decode($post->coords)->longitude ?? explode(',', $post->coords)[1];
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

    public function list_part_time_jobs()
    {
        $skills = Skill::all();
        $posts = $this->getFixedTermOpportunities();
        return view("work.part_time_jobs.index-o", compact("skills", "posts"));
    }

    public function list_permanent_jobs()
    {
        $skills = Skill::all();
        $posts = $this->getPermanentOpportunities();
        return view("work.permanent.index", compact("skills", "posts"));
    }

    public function getFixedTermOpportunities()
    {
        $user_location = auth()->user()->location_coords;
        if (!$user_location) {
            return $this->data_validation_error_response();
        }

        $user_location_lat = json_decode($user_location)->latitude ??  explode(',', $user_location)[0];
        $user_location_lng = json_decode($user_location)->longitude ?? explode(',', $user_location)[1];

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
        $posts = Post::where("user_id", "!=", auth()->id())->where("status", "active")->where("type", "FIXED_TERM_JOB")->whereNull('deleted_at')->orderByDesc("created_at")->get();

        /**
         * filter using distance
         */
        $jobs_near_me = collect();
        foreach ($posts as $post) {
            $post_location_lat = json_decode($post->coords)->latitude ?? explode(',', $post->coords)[0];
            $post_location_lng = json_decode($post->coords)->longitude ?? explode(',', $post->coords)[1];

            $distance = $this->get_distance($user_location_lat, $user_location_lng, $post_location_lat, $post_location_lng, "K");

            $toDate = Carbon::parse($post->end_date);
            $fromDate = Carbon::parse($post->start_date);
            $post["duration"] = $toDate->diffInMonths($fromDate);
            $post["createdOn"] = $post->created_at->diffForHumans();

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
        return $posts;
    }

    public function getPermanentOpportunities()
    {
        $user_location = auth()->user()->location_coords;
        if (!$user_location) {
            return $this->data_validation_error_response();
        }

        $user_location_lat = json_decode($user_location)->latitude ??  explode(',', $user_location)[0];
        $user_location_lng = json_decode($user_location)->longitude ?? explode(',', $user_location)[1];

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
        $posts = Post::where("user_id", "!=", auth()->id())->where("status", "active")->where("type", "PERMANENT_JOB")->whereNull('deleted_at')->orderByDesc("created_at")->get();

        /**
         * filter using distance
         */
        $jobs_near_me = collect();
        foreach ($posts as $post) {
            $post_location_lat = json_decode($post->coords)->latitude ?? explode(',', $post->coords)[0];
            $post_location_lng = json_decode($post->coords)->longitude ?? explode(',', $post->coords)[1];

            $distance = $this->get_distance($user_location_lat, $user_location_lng, $post_location_lat, $post_location_lng, "K");

            $toDate = Carbon::parse($post->end_date);
            $fromDate = Carbon::parse($post->start_date);
            $post["duration"] = $toDate->diffInMonths($fromDate);
            $post["createdOn"] = $post->created_at->diffForHumans();

            if ($distance <= self::JOB_SEARCH_RADIUS) {
                $post["organiser_name"] = $post->user->name;
                $post["distance"] = number_format($distance, 2);
                $jobs_near_me->push($post);
            }

            $post->industry;

            $has_already_applied = JobApplication::where("user_id", auth()->id())->where("post_id", $post->id)->first();
            if ($has_already_applied) {
                $post->has_already_applied = "yes";
            }
        }
        $posts = $jobs_near_me->sortBy("distance");
        return $posts;
    }

    private function get_distance($lat1, $lon1, $lat2, $lon2, $unit)
    {
        $theta = (float)$lon1 - (float)$lon2;
        $dist = sin(deg2rad((float)$lat1)) * sin(deg2rad((float)$lat2)) + cos(deg2rad((float)$lat1)) * cos(deg2rad((float)$lat2)) * cos(deg2rad($theta));
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
        if (!$uuid) {
            return back()->with("danger", "Invalid request. Kindly try again.");
        }

        $original_post = Post::where("id", $uuid)->first();
        if (!$original_post) {
            return back()->with("danger", "Oops...something went wrong. We could not retrieve post details.");
        }

        /**
         * 404
         */
        if ($original_post->deleted_at) {
            return back()->with("info", "This post has been removed by the issuer.");
        }

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

        $user_location_lat = json_decode($user_location)->latitude ??  explode(',', $user_location)[0];
        $user_location_lng = json_decode($user_location)->longitude ?? explode(',', $user_location)[1];

        /**
         * filter using distance
         */
        $volunteer_near_me = collect();
        $posts = Post::where("user_id", "!=", auth()->id())->where("status", "active")->where("type", "VOLUNTEER")->whereNull('deleted_at')->get();
        $posts->map(function ($post) use ($user_location_lat, $user_location_lng, $volunteer_near_me) {
            //get post coordinates
            $post_location_lat = json_decode($post->coords)->latitude ?? explode(',', $post->coords)[0];
            $post_location_lng = json_decode($post->coords)->longitude ?? explode(',', $post->coords)[1];

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
        $posts = $volunteer_near_me->sortBy("distance");;

        return view("volunteerism.show", compact("original_post", "posts"));
    }

    public function show_quick_job($uuid)
    {
        if (!$uuid) {
            return back()->with("danger", "Invalid request. Kindly try again.");
        }

        $original_post = Post::where("id", $uuid)->first();
        if (!$original_post) {
            return back()->with("danger", "Oops...something went wrong. We could not retrieve post details.");
        }

        /**
         * 404@alias
         */
        if ($original_post->deleted_at) {
            return back()->with("info", "This post has been removed by the issuer.");
        }

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

        $user_location_lat = json_decode($user_location)->latitude ??  explode(',', $user_location)[0];
        $user_location_lng = json_decode($user_location)->longitude ?? explode(',', $user_location)[1];

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
            $post_location_lat = json_decode($post->coords)->latitude ?? explode(',', $post->coords)[0];
            $post_location_lng = json_decode($post->coords)->longitude ?? explode(',', $post->coords)[1];
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
        $posts = $jobs_near_me->sortBy("distance");;

        return view("work.quick_jobs.show", compact("original_post", "posts"));
    }

    public function show_fixed_term_job_details($uuid)
    {
        if (!$uuid) {
            return back()->with("danger", "Invalid request. Kindly try again.");
        }

        $original_post = Post::where("id", $uuid)->first();
        if (!$original_post) {
            return back()->with("danger", "Oops...something went wrong. We could not retrieve post details.");
        }

        /**
         * 404@alias
         */
        if ($original_post->deleted_at) {
            return back()->with("info", "This post has been removed by the issuer.");
        }

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

        $user_location_lat = json_decode($user_location)->latitude ??  explode(',', $user_location)[0];
        $user_location_lng = json_decode($user_location)->longitude ?? explode(',', $user_location)[1];

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
        $posts = Post::where("user_id", "!=", auth()->id())->where("status", "active")->where("type", "FIXED_TERM_JOB")->whereNull('deleted_at')->get();

        /**
         * filter using distance
         */
        $jobs_near_me = collect();
        foreach ($posts as $post) {
            $post_location_lat = json_decode($post->coords)->latitude ?? explode(',', $post->coords)[0];
            $post_location_lng = json_decode($post->coords)->longitude ?? explode(',', $post->coords)[1];
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
        $posts = $jobs_near_me->sortBy("distance");;
        $uuid = $original_post->id;

        return view("work.part_time_jobs.show", compact("original_post", "posts", "uuid"));
    }

    public function show_permanent_job_details($uuid)
    {
        if (!$uuid) {
            return back()->with("danger", "Invalid request. Kindly try again.");
        }

        $original_post = Post::where("id", $uuid)->first();
        if (!$original_post) {
            return back()->with("danger", "Oops...something went wrong. We could not retrieve post details.");
        }

        /**
         * 404@alias
         */
        if ($original_post->deleted_at) {
            return back()->with("info", "This post has been removed by the issuer.");
        }

        $has_already_applied = JobApplication::where("user_id", auth()->id())->where("post_id", $original_post->id)->first();
        if ($has_already_applied) {
            $original_post->has_already_applied = "yes";
        }
        $original_post->user;
        $original_post->industry;

        $posts = [];

        //get user coordinates
        $user_location = auth()->user()->location_coords;
        if (!$user_location) {
            return back()->with("danger", "Could not retrieve user's current location");
        }

        $user_location_lat = json_decode($user_location)->latitude ??  explode(',', $user_location)[0];
        $user_location_lng = json_decode($user_location)->longitude ?? explode(',', $user_location)[1];

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
        $posts = Post::where("user_id", "!=", auth()->id())->where("status", "active")->where("type", "FIXED_TERM_JOB")->whereNull('deleted_at')->get();

        /**
         * filter using distance
         */
        $jobs_near_me = collect();
        foreach ($posts as $post) {
            $post_location_lat = json_decode($post->coords)->latitude ?? explode(',', $post->coords)[0];
            $post_location_lng = json_decode($post->coords)->longitude ?? explode(',', $post->coords)[1];
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
        $posts = $jobs_near_me->sortBy("distance");;
        $uuid = $original_post->id;

        return view("work.permanent.show", compact("original_post", "posts", "uuid"));
    }

    public function getFixedTermOpportunityDetails($uuid)
    {
        if (!$uuid) {
            return $this->not_found_response([],  "Invalid request. Kindly try again.");
        }

        $original_post = Post::where("id", $uuid)->first();
        if (!$original_post) {
            return $this->not_found_response([], "Oops...something went wrong. We could not retrieve post details.");
        }

        /**
         * 404
         */
        if ($original_post->deleted_at) {
            return $this->general_error_response([], "This post has been removed by the issuer.");
        }

        $has_already_applied = JobApplication::where("user_id", auth()->id())->where("post_id", $original_post->id)->first();
        if ($has_already_applied) {
            $original_post->has_already_applied = "yes";
        }
        $original_post->user;
        $toDate = Carbon::parse($original_post->end_date);
        $fromDate = Carbon::parse($original_post->start_date);
        $original_post["duration"] = $toDate->diffInMonths($fromDate);
        $original_post["postedOn"] = $original_post->created_at->diffForHumans();
        $original_post["createdOn"] = $original_post->created_at->diffForHumans();

        $posts = [];

        //get user coordinates
        $user_location = auth()->user()->location_coords;
        if (!$user_location) {
            return $this->not_found_response([], "Could not retrieve user's current location");
        }

        $user_location_lat = json_decode($user_location)->latitude ??  explode(',', $user_location)[0];
        $user_location_lng = json_decode($user_location)->longitude ?? explode(',', $user_location)[1];

        $_post_location_lat = json_decode($original_post->coords)->latitude ?? explode(',', $original_post->coords)[0];
        $_post_location_lng = json_decode($original_post->coords)->longitude ?? explode(',', $original_post->coords)[1];
        $distance = $this->get_distance($user_location_lat, $user_location_lng, $_post_location_lat, $_post_location_lng, "K");
        $original_post["distance"] = number_format($distance, 2);

        /**
         * filter using distance
         */
        $volunteer_near_me = collect();
        $posts = Post::where("user_id", "!=", auth()->id())->where("status", "active")->where("type", "FIXED_TERM_JOB")->whereNull('deleted_at')->where("id", "!=", $original_post->id)->get();
        $posts->map(function ($post) use ($user_location_lat, $user_location_lng, $volunteer_near_me) {
            //get post coordinates
            $post_location_lat = json_decode($post->coords)->latitude ?? explode(',', $post->coords)[0];
            $post_location_lng = json_decode($post->coords)->longitude ?? explode(',', $post->coords)[1];


            $distance = $this->get_distance($user_location_lat, $user_location_lng, $post_location_lat, $post_location_lng, "K");
            $post["organiser_name"] = $post->user->name;
            $post["distance"] = number_format($distance, 2);
            $toDate = Carbon::parse($post->end_date);
            $fromDate = Carbon::parse($post->start_date);
            $post["duration"] = $toDate->diffInMonths($fromDate);
            if ($distance <= self::VOLUNTEER_SEARCH_RADIUS) {
                $volunteer_near_me->push($post);
            }

            $has_already_applied = JobApplication::where("user_id", auth()->id())->where("post_id", $post->id)->first();
            if ($has_already_applied) {
                $post->has_already_applied = "yes";
            }

            return $post;
        });
        $posts = $volunteer_near_me->sortBy("distance")->take(3);

        return $this->success_response([
            "opportunity" => $original_post,
            "otherOpportunities" => $posts
        ], "Post details fetched successfully");
    }

    public function getPermanentOpportunityDetails($uuid)
    {
        if (!$uuid) {
            return $this->not_found_response([],  "Invalid request. Kindly try again.");
        }

        $original_post = Post::where("id", $uuid)->first();
        if (!$original_post) {
            return $this->not_found_response([], "Oops...something went wrong. We could not retrieve post details.");
        }

        /**
         * 404
         */
        if ($original_post->deleted_at) {
            return $this->general_error_response([], "This post has been removed by the issuer.");
        }

        $has_already_applied = JobApplication::where("user_id", auth()->id())->where("post_id", $original_post->id)->first();
        if ($has_already_applied) {
            $original_post->has_already_applied = "yes";
        }
        $original_post->user;
        $original_post->industry;
        $toDate = Carbon::parse($original_post->end_date);
        $fromDate = Carbon::parse($original_post->start_date);
        //        $original_post["duration"] = $toDate->diffInMonths($fromDate);
        $original_post["postedOn"] = $original_post->created_at->diffForHumans();
        $original_post["createdOn"] = $original_post->created_at->diffForHumans();

        $posts = [];

        //get user coordinates
        $user_location = auth()->user()->location_coords;
        if (!$user_location) {
            return $this->not_found_response([], "Could not retrieve user's current location");
        }

        $user_location_lat = json_decode($user_location)->latitude ??  explode(',', $user_location)[0];
        $user_location_lng = json_decode($user_location)->longitude ?? explode(',', $user_location)[1];

        $_post_location_lat = json_decode($original_post->coords)->latitude ?? explode(',', $original_post->coords)[0];
        $_post_location_lng = json_decode($original_post->coords)->longitude ?? explode(',', $original_post->coords)[1];
        $distance = $this->get_distance($user_location_lat, $user_location_lng, $_post_location_lat, $_post_location_lng, "K");
        $original_post["distance"] = number_format($distance, 2);

        /**
         * filter using distance
         */
        $volunteer_near_me = collect();
        $posts = Post::where("user_id", "!=", auth()->id())->where("status", "active")->where("type", "PERMANET_JOB")->whereNull('deleted_at')->where("id", "!=", $original_post->id)->get();
        $posts->map(function ($post) use ($user_location_lat, $user_location_lng, $volunteer_near_me) {
            //get post coordinates
            $post_location_lat = json_decode($post->coords)->latitude ?? explode(',', $post->coords)[0];
            $post_location_lng = json_decode($post->coords)->longitude ?? explode(',', $post->coords)[1];

            Log::debug($post_location_lng . " >>>> " . $post_location_lat);


            $distance = $this->get_distance($user_location_lat, $user_location_lng, $post_location_lat, $post_location_lng, "K");
            $post["organiser_name"] = $post->user->name;
            $post["distance"] = number_format($distance, 2);
            $toDate = Carbon::parse($post->end_date);
            $fromDate = Carbon::parse($post->start_date);
            //            $post["duration"] = $toDate->diffInMonths($fromDate);
            if ($distance <= self::VOLUNTEER_SEARCH_RADIUS) {
                $volunteer_near_me->push($post);
            }

            $has_already_applied = JobApplication::where("user_id", auth()->id())->where("post_id", $post->id)->first();
            if ($has_already_applied) {
                $post->has_already_applied = "yes";
            }

            return $post;
        });
        $posts = $volunteer_near_me->sortBy("distance")->take(3);

        return $this->success_response([
            "opportunity" => $original_post,
            "otherOpportunities" => $posts
        ], "Post details fetched successfully");
    }

    public function getFixedTermOpportunitiesBySearchRadius($radius)
    {
        if (!$radius) {
            return $this->not_found_response([], "Invalid request.");
        }

        $posts = [];

        //get user coordinates
        $user_location = auth()->user()->location_coords;
        if (!$user_location) {
            return $this->not_found_response([], "Could not retrieve user's current location");
        }

        $user_location_lat = json_decode($user_location)->latitude ??  explode(',', $user_location)[0];
        $user_location_lng = json_decode($user_location)->longitude ?? explode(',', $user_location)[1];

        /**
         * filter using distance
         */
        $volunteer_near_me = collect();
        $posts = Post::where("user_id", "!=", auth()->id())->where("status", "active")->where("type", "FIXED_TERM_JOB")->whereNull('deleted_at')->get();
        $posts->map(function ($post) use ($user_location_lat, $user_location_lng, $volunteer_near_me, $radius) {
            //get post coordinates
            $post_location_lat = json_decode($post->coords)->latitude ?? explode(',', $post->coords)[0];
            $post_location_lng = json_decode($post->coords)->longitude ?? explode(',', $post->coords)[1];

            $distance = $this->get_distance($user_location_lat, $user_location_lng, $post_location_lat, $post_location_lng, "K");
            $post["organiser_name"] = $post->user->name;
            $post["distance"] = number_format($distance, 2);
            $toDate = Carbon::parse($post->end_date);
            $fromDate = Carbon::parse($post->start_date);
            $post["duration"] = $toDate->diffInMonths($fromDate);
            Log::debug($distance . " >>>>>>>>>>>>>>> " . $radius);
            if ($distance <= $radius) {
                $volunteer_near_me->push($post);
            }

            $has_already_applied = JobApplication::where("user_id", auth()->id())->where("post_id", $post->id)->first();
            if ($has_already_applied) {
                $post->has_already_applied = "yes";
            }

            return $post;
        });
        $posts = $volunteer_near_me->sortBy("distance");

        return $this->success_response([
            "opportunities" => $posts
        ], "Post details fetched successfully");
    }

    public function getPermanentOpportunitiesBySearchRadius($radius)
    {
        if (!$radius) {
            return $this->not_found_response([], "Invalid request.");
        }

        $posts = [];

        //get user coordinates
        $user_location = auth()->user()->location_coords;
        if (!$user_location) {
            return $this->not_found_response([], "Could not retrieve user's current location");
        }

        $user_location_lat = json_decode($user_location)->latitude ??  explode(',', $user_location)[0];
        $user_location_lng = json_decode($user_location)->longitude ?? explode(',', $user_location)[1];

        /**
         * filter using distance
         */
        $volunteer_near_me = collect();
        $posts = Post::where("user_id", "!=", auth()->id())->where("status", "active")->where("type", "FIXED_TERM_JOB")->whereNull('deleted_at')->get();
        $posts->map(function ($post) use ($user_location_lat, $user_location_lng, $volunteer_near_me, $radius) {
            //get post coordinates
            $post_location_lat = json_decode($post->coords)->latitude ?? explode(',', $post->coords)[0];
            $post_location_lng = json_decode($post->coords)->longitude ?? explode(',', $post->coords)[1];

            $distance = $this->get_distance($user_location_lat, $user_location_lng, $post_location_lat, $post_location_lng, "K");
            $post["organiser_name"] = $post->user->name;
            $post["distance"] = number_format($distance, 2);
            $toDate = Carbon::parse($post->end_date);
            $fromDate = Carbon::parse($post->start_date);
            //            $post["duration"] = $toDate->diffInMonths($fromDate);
            Log::debug($distance . " >>>>>>>>>>>>>>> " . $radius);
            if ($distance <= $radius) {
                $volunteer_near_me->push($post);
            }

            $has_already_applied = JobApplication::where("user_id", auth()->id())->where("post_id", $post->id)->first();
            if ($has_already_applied) {
                $post->has_already_applied = "yes";
            }

            return $post;
        });
        $posts = $volunteer_near_me->sortBy("distance");

        return $this->success_response([
            "opportunities" => $posts
        ], "Post details fetched successfully");
    }

    /**
     * @param $uuid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function apply_for_job($uuid)
    {
        if (!$uuid) {
            return back()->with("danger", "Invalid request. Kindly try again.");
        }

        $post = Post::where("id", $uuid)->first();
        if (!$post) {
            return back()->with("danger", "Oops...something went wrong. We could not retrieve post details.");
        }

        /**
         * 404
         */
        if ($post->deleted_at) {
            return back()->with("info", "This post has been removed by the issuer.");
        }

        //check if user has applied already
        $has_already_applied = JobApplication::where("user_id", auth()->id())->where("post_id", $uuid)->first();
        if ($has_already_applied) {
            return back()->with("warning", "You have already applied for this job.");
        }

        //        /**
        //         * validate the number of volunteers required
        //        */
        //        if ($post->type === "VOLUNTEER") {
        //            $countOfConfirmedVolunteers = $post->applications->where("status", "confirmed")->count();
        //            if ($countOfConfirmedVolunteers >= $post->maximum_number_of_volunteers) {
        //                return back()->with("danger", "Maximum number of volunteers reached.");
        //            }
        //        }

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

    /**
     * @param $uuid
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show_user_post_details($uuid)
    {
        if (!$uuid) {
            return back()->with("danger", "Invalid request.");
        }

        $post = Post::where("id", $uuid)->first();
        if (!$post) {
            return back()->with("danger", "Error fetching post details");
        }

        /**
         * 404
         */
        if ($post->deleted_at) {
            return back()->with("info", "This post has been removed by the issuer.");
        }

        $post->number_of_participants_applied = $post->applications()->count();
        $post->number_of_participants_confirmed = $post->applications()->where("status", "confirmed")->count();

        foreach ($post->applications as $application) {
            $application->user;
            $application->rating_and_reviews;
        }

        $toDate = Carbon::parse($post->final_end_date);
        $fromDate = Carbon::parse($post->final_start_date);
        $post["duration"] = $toDate->diffInMonths($fromDate);

        $posts = auth()->user()->posts;
        $shortListedApplicants = self::GetShortlistCandidates($post->applications, $post->tags);
        $shortListedApplicants = $shortListedApplicants->sortByDesc('points');

        return view("postings.show", compact("post", "posts", "shortListedApplicants"));
    }

    private static function GetShortlistCandidates($applications, $categories)
    {
        $categories = json_decode($categories);
        $_applicants = collect();
        foreach ($applications as $application) {
            $applicant = $application->user;

            /**
             * let's get the number of hits for categories
             */
            $skills = "";
            foreach ($applicant->skills as $skill) {
                $skills .= " " . $skill->skill->name;
            }
            $string2 = "";
            for ($i = 0; $i < count((array) $categories); $i++) {
                $string2 .= " " . $categories[$i];
            }

            $skillWords = str_word_count($skills, 1);
            $words2 = str_word_count($string2, 1);

            $similarWords = [];
            foreach ($skillWords as $skillWord) {
                foreach ($words2 as $word2) {
                    if (levenshtein($skillWord, $word2) <= 2) {
                        $similarWords[] = $skillWord;
                        break;
                    }
                }
            }

            $applicant["points"] = count($similarWords) + $applicant->rating;
            $applicant->user;
            $_applicants->push($applicant);
        }
        return $_applicants;
    }

    /**
     * @param $application_id
     * @param $action
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirm_decline_applicant($application_id, $action)
    {
        if (!$application_id || !$action) {
            return back()->with("danger", "Invalid request");
        }

        $application = JobApplication::where("id", $application_id)->first();
        if (!$application) {
            return back()->with("danger", "Error fetching application details");
        }

        /**
         * validate the number of volunteers required
         */
        if ($application->job_post->type === "VOLUNTEER") {
            $countOfConfirmedVolunteers = $application->job_post->applications->where("status", "confirmed")->count();
            if ($countOfConfirmedVolunteers >= $application->job_post->maximum_number_of_volunteers) {
                return back()->with("danger", "Maximum number of volunteers reached.");
            }
        }

        $message = "";
        if ($action === "confirm") {
            $application->status = "confirmed";
            $message = "Applicant has been confirmed successfully";
        } else {
            $application->status = "declined";
            $message = "Applicant has been declined successfully";
        }
        try {
            $application->update();

            /**
             * job applicant confirmed
             */
            if ($application->job_post->type != "VOLUNTEER" || $action === "confirm") {
                $application->job_post->is_job_applicant_confirmed = "1";
                $application->job_post->confirmed_applicant_id = $application->user->id;
                $application->job_post->update();
                /**
                 * create notification
                 */
                Notifications::PushUserNotification($application->job_post, $application, $application->user, "APPLICATION_CONFIRMED");
            } else {
                /**
                 * create notification
                 */
                Notifications::PushUserNotification($application->job_post, $application, $application->user, "APPLICATION_DECLINED");
            }
        } catch (QueryException $e) {
            Log::error("ERROR confirming user for JOB APPLICATION >>>>>>>>>> " . $application . " >>>>>>>>> " . $e);
            return back()->with("danger", "Error confirming applicant. Kindly try again.");
        }

        return back()->with("success", $message);
    }

    public function create_volunteer_post(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'date' => 'required',
            'time' => 'required',
            'location' => 'required',
            'coords' => 'required',
            'maximum_number_of_volunteers' => 'required|numeric',
            'volunteer_hours' => 'required',
        ]);

        if ($validation->fails()) {
            return back()->withErrors($validation->errors())->with("danger", "Please ensure all required fields are completed.")->withInput();
        }

        $post = new Post();
        $post->name = $request->name;
        $post->description = $request->description;
        $post->date = $request->date;
        $post->time = $request->time;
        $post->location = $request->location;
        $post->coords = $request->coords;
        $post->maximum_number_of_volunteers = $request->maximum_number_of_volunteers;
        $post->volunteer_hours = $request->volunteer_hours;
        $post->other_relevant_information = $request->other_relevant_information;
        $post->user_id = auth()->id();
        $post->type = "VOLUNTEER";
        $post->source = "WEB";

        try {
            $post->save();
            return redirect()->route("home")->with("success", "Post has been published successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR SAVING USER >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return back()->with("danger", "Oops. We encountered an issue while publishing your post. Kindly try again.");
        }
    }

    public function create_quick_job_post(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'category' => 'required',
            'description' => 'required',
            'date' => 'required',
            'time' => 'required',
            'location' => 'required',
            'coords' => 'required',
            'min_budget' => 'required',
            'max_budget' => 'required',
        ]);

        if ($validation->fails()) {
            return back()->withErrors($validation->errors())->with("danger", "Please ensure all required fields are completed.")->withInput();
        }

        $category = Skill::where("name", $request->category)->first();
        if (!$category) {
            return back()->with("danger", "Error fetching category details");
        }

        $post = new Post();
        $post->category = $request->category;
        $post->category_id = $category->id;
        $post->description = $request->description;
        $post->date = $request->date;
        $post->time = $request->time;
        $post->location = $request->location;
        $post->coords = $request->coords;
        $post->max_budget = $request->max_budget;
        $post->min_budget = $request->min_budget;
        $post->is_negotiable = $request->negotiable;
        $post->is_includes_tax = $request->includes_tax;
        $post->other_relevant_information = $request->other_relevant_information;
        $post->user_id = auth()->id();
        $post->type = "QUICK_JOB";
        $post->source = "WEB";

        if ($request->post_image && $request->post_image != "") {
            //save image
            $image = $request->file('post_image');
            $name = $post->user->name . '_' . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/quick_jobs');
            $image->move($destinationPath, $name);

            $post->post_image_link = URL::to('/public/uploads/quick_jobs') . '/' . $name;
        }

        try {
            $post->save();
            return redirect()->route("home")->with("success", "Post has been published successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR SAVING USER >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return back()->with("danger", "Oops. We encountered an issue while publishing your post. Kindly try again.");
        }
    }

    public function submitQuoteRequest(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'category' => 'required',
            'description' => 'required',
        ]);

        if ($validation->fails()) {
            return back()->withErrors($validation->errors())->with("danger", "Please ensure all required fields are completed.")->withInput();
        }

        $category = Skill::where("name", $request->category)->first();
        if (!$category) {
            return back()->with("danger", "Error fetching category details");
        }

        DB::beginTransaction();

        $post = new Post();
        $post->category = $request->category;
        $post->category_id = $category->id;
        $post->description = $request->description;
        $post->date = date('Y-m-d');
        $post->time = date('H:i');
        $post->location = auth()->user()->location_name;
        $post->coords = auth()->user()->location_coords;
        $post->user_id = auth()->id();
        $post->type = "P2P";
        $post->source = "WEB";

        if ($request->post_image && $request->post_image != "") {
            //save image
            $image = $request->file('post_image');
            $name = $post->user->name . '_' . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/P2P');
            $image->move($destinationPath, $name);

            $post->post_image_link = URL::to('/public/uploads/P2P') . '/' . $name;
        }

        $post->save();

        /**
         * create application | placeholder quotation
         */
        $vorkers = $request->vorkers;
        $vorkers = json_decode($vorkers);

        foreach ($vorkers as $vorker) {
            // dd($vorker);

            /**
             * get vorker details
             */
            $vorkerDetails = User::where("id", $vorker->userId)->first();
            if (!$vorkerDetails) {
                Log::error("ERROR FETCHING VORKER " . $vorker->userId);
                continue;
            }

            /**
             * these are the users we sent the job request to
             * for quotation
             */
            $application = new JobApplication();
            $application->user_id = $vorkerDetails->id;
            $application->post_id = $post->id;
            if (!$application->save()) {
                Log::error("ERROR SAVING APPLICATION FOR " . $vorker->userId . " FOR P2P POST " . $post->id);
            }

            $post->user;

            /**
             * create notification
             */
            Notifications::PushUserNotification($post, $post, $vorkerDetails, "SUCCESSFUL_JOB_APPLICATION");
            //PushNotification::notify("title", "body", "PROFILE_UPDATE", "details", auth()->user()->fcm_token);
        }

        try {
            DB::commit();
            return redirect()->route("home")->with("success", "Post has been published successfully.");
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error("ERROR SAVING USER >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return back()->with("danger", "Oops. We encountered an issue while publishing your post. Kindly try again.");
        }
    }

    public function requestQuote($uuid)
    {
        if (!$uuid) {
            return back()->with("danger", "Invalid request");
        }

        $user = User::where("uuid", $uuid)->first();
        if (!$user) {
            return back()->with("danger", "Error fetching vorker details. Please try again");
        }

        $categories = collect($user->skills)->map(function ($category) {
            return $category->skill->name;
        });
        return view("work.p2p.create", compact("user", "categories"));
    }

    public function editQuoteRequest($uuid)
    {
        if (!$uuid) {
            return back()->with("danger", "Invalid request");
        }

        $post = Post::where("id", $uuid)->first();
        if (!$post) {
            return back()->with("danger", "Error fetching job details. Please try again");
        }

        // Get the vorker from the job application
        $application = JobApplication::where("post_id", $post->id)->first();
        if (!$application) {
            return back()->with("danger", "Error fetching application details. Please try again");
        }

        $vorker = User::where("id", $application->user_id)->first();
        if (!$vorker) {
            return back()->with("danger", "Error fetching vorker details. Please try again");
        }

        $categories = collect($vorker->skills)->map(function ($category) {
            return $category->skill->name;
        });

        // Pass the application user ID directly to avoid variable issues in the view
        $workerId = $application->user_id;

        return view("work.p2p.edit", compact("post", "categories", "workerId"));
    }

    public function updateQuoteRequest(Request $request, $uuid)
    {
        $validation = Validator::make($request->all(), [
            'category' => 'required',
            'description' => 'required',
        ]);

        if ($validation->fails()) {
            return back()->withErrors($validation->errors())->with("danger", "Please ensure all required fields are completed.")->withInput();
        }

        $post = Post::where("id", $uuid)->first();
        if (!$post) {
            return back()->with("danger", "Error fetching job details. Please try again");
        }

        $category = Skill::where("name", $request->category)->first();
        if (!$category) {
            return back()->with("danger", "Error fetching category details");
        }

        DB::beginTransaction();

        try {
            $post->category = $request->category;
            $post->category_id = $category->id;
            $post->description = $request->description;

            if ($request->post_image && $request->post_image != "") {
                // Save image
                $image = $request->file('post_image');
                $name = $post->user->name . '_' . time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('/uploads/P2P');
                $image->move($destinationPath, $name);

                $post->post_image_link = URL::to('/public/uploads/P2P') . '/' . $name;
            }

            $post->save();
            DB::commit();

            return redirect()->route("home")->with("success", "Job request has been updated successfully.");
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error("ERROR UPDATING P2P JOB >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return back()->with("danger", "Oops. We encountered an issue while updating your job request. Kindly try again.");
        }
    }

    public function removeQuoteRequest($uuid)
    {
        $post = Post::where("id", $uuid)->first();
        if (!$post) {
            return back()->with("danger", "Error fetching job details. Please try again");
        }

        // Check if the post belongs to the authenticated user
        if ($post->user_id != auth()->id()) {
            return back()->with("danger", "You are not authorized to remove this job request.");
        }

        DB::beginTransaction();

        try {
            // Delete related job applications
            JobApplication::where("post_id", $post->id)->delete();

            // Delete the post
            $post->delete();

            DB::commit();
            return redirect()->route("home")->with("success", "Job request has been removed successfully.");
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error("ERROR REMOVING P2P JOB >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return back()->with("danger", "Oops. We encountered an issue while removing your job request. Kindly try again.");
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * create a fixed term job post
     */
    public function create_fixed_term_job_post(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'title' => 'required',
            'employer' => 'required',
            'description' => 'required',
            'qualifications' => 'required',
            'date' => 'required',
            'time' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'location' => 'required',
            'coords' => 'required',
            'min_budget' => 'required',
            'max_budget' => 'required',
            'tags' => 'required',
        ]);

        if ($validation->fails()) {
            return back()->withErrors($validation->errors())->with("danger", "Please ensure all required fields are completed.")->withInput();
        }

        $tags = array();
        foreach ($request->tags as $tag) {
            $category = Skill::where("name", $tag)->first();
            if ($category) {
                array_push($tags, $tag);
            }
        }

        $post = new Post();
        $post->title = $request->title;
        $post->description = $request->description;
        $post->qualifications = $request->qualifications;
        $post->date = $request->date;
        $post->time = $request->time;
        $post->location = $request->location;
        $post->employer = $request->employer;
        $post->coords = $request->coords;
        $post->start_date = $request->start_date;
        $post->end_date = $request->end_date;
        $post->max_budget = $request->max_budget;
        $post->min_budget = $request->min_budget;
        $post->other_relevant_information = $request->other_relevant_information;

        if ($request->negotiable === "on") {
            $post->is_negotiable = "yes";
        } else {
            $post->is_negotiable = "no";
        }

        if ($request->renewable === "on") {
            $post->is_renewable = "yes";
        } else {
            $post->is_renewable = "no";
        }

        if ($request->is_internship === "on") {
            $post->is_internship = "yes";
        } else {
            $post->is_internship = "no";
        }

        $post->tags = json_encode($tags);
        $post->user_id = auth()->id();
        $post->type = "FIXED_TERM_JOB";
        $post->source = "WEB";

        try {
            $post->save();
            return redirect()->route("home")->with("success", "Post has been published successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR SAVING POST >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return back()->with("danger", "Oops. We encountered an issue while publishing your post. Kindly try again.");
        }
    }

    public function create_permanent_job_post(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'title' => 'required',
            'employer' => 'required',
            'description' => 'required',
            'qualifications' => 'required',
            'date' => 'required',
            'time' => 'required',
            'location' => 'required',
            'coords' => 'required',
            'min_budget' => 'required',
            'max_budget' => 'required',
            'tags' => 'required',
            'industry' => 'required',
        ]);

        if ($validation->fails()) {
            return back()->withErrors($validation->errors())->with("danger", "Please ensure all required fields are completed.")->withInput();
        }

        $tags = array();
        foreach ($request->tags as $tag) {
            $category = Skill::where("name", $tag)->first();
            if ($category) {
                array_push($tags, $tag);
            }
        }

        $industry = Industry::where("name", $request->industry)->first();
        if (!$industry) {
            return redirect()->back()->with("danger", "Invalid request");
        }

        $post = new Post();
        $post->title = $request->title;
        $post->description = $request->description;
        $post->qualifications = $request->qualifications;
        $post->date = $request->date;
        $post->time = $request->time;
        $post->location = $request->location;
        $post->employer = $request->employer;
        $post->coords = $request->coords;
        $post->start_date = $request->start_date;
        $post->max_budget = $request->max_budget;
        $post->min_budget = $request->min_budget;
        $post->other_relevant_information = $request->other_relevant_information;

        if ($request->negotiable === "on") {
            $post->is_negotiable = "yes";
        } else {
            $post->is_negotiable = "no";
        }

        if ($request->renewable === "on") {
            $post->is_renewable = "yes";
        } else {
            $post->is_renewable = "no";
        }

        if ($request->is_internship === "on") {
            $post->is_internship = "yes";
        } else {
            $post->is_internship = "no";
        }

        $post->tags = json_encode($tags);
        $post->user_id = auth()->id();
        $post->industry_id = $industry->id;
        $post->type = "PERMANENT_JOB";
        $post->source = "WEB";

        try {
            $post->save();

            return redirect()->route("home")->with("success", "Post has been published successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR SAVING POST >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return back()->with("danger", "Oops. We encountered an issue while publishing your post. Kindly try again.");
        }
    }

    public function close_post(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'job_post_id' => 'required',
            'job_type' => 'required',
        ]);

        if ($validation->fails()) {
            return back()->withErrors($validation->errors())->with("danger", "Oops. We encountered an issue while closing job. Kindly try again.");
        }

        $post = Post::where("id", $request->job_post_id)->first();
        if (!$post) {
            return back()->with("danger", "Error fetching post details");
        }

        /**
         * seperate activity or job closing logic by switching on the
         * type of post
         */
        switch ($request->job_type) {
            case "VOLUNTEER":
                /**
                 * assign volunteer hours to participants
                 */
                $volunteer_details = $request->volunteer_details;
                $user_id = $request->user_id;
                for ($i = 0; $i < count($user_id); $i++) {
                    $participant = User::where("id", $user_id[$i])->first();
                    if (!$participant) {
                        Log::debug("ERROR FETCHING USER DETAILS FOR USER ID >>>>>>>>>>> " . $user_id[$i]);
                    }

                    $application = JobApplication::where("user_id", $user_id[$i])->where("post_id", $request->job_post_id)->first();
                    if (!$application) {
                        Log::debug("ERROR FETCHING APPLICATION DETAILS FOR USER ID >>>>>>>>>>> " . $volunteer_details[$i]["user_id"] . " AND POST ID >>>>> " . $request->job_post_id);
                    }

                    $participant->volunteer_hours += (float)$volunteer_details[$i];
                    $application->volunteer_hours = (float)$volunteer_details[$i];
                    try {
                        $participant->update();
                        $application->update();

                        /**
                         * create notification
                         */
                        $post->user;
                        Notifications::PushUserNotification($post, $application, $participant, "JOB_CLOSED");
                    } catch (QueryException $e) {
                        Log::error("ERROR UPDATING VOLUNTEER HOURS FOR >>>>>>>>>> " . $participant->id . " >>>>>>>>> " . $e);
                        continue;
                    }
                }
                break;
            case "QUICK_JOB":
                $participant = User::where("id", $request->user_id)->first();
                if (!$participant) {
                    Log::debug("ERROR FETCHING USER DETAILS FOR USER ID >>>>>>>>>>> " . $request->user_id);
                }

                $application = JobApplication::where("user_id", $request->user_id)->where("post_id", $request->job_post_id)->first();
                if (!$application) {
                    Log::debug("ERROR FETCHING APPLICATION DETAILS FOR USER ID >>>>>>>>>>> " . $request->user_id . " AND POST ID >>>>> " . $request->job_post_id);
                }

                /**
                 * create feedback and rating for vorker
                 */
                $ratingReview = new RatingReview();
                $ratingReview->user_id = $participant->id;
                $ratingReview->job_application_id = $application->id;
                $ratingReview->post_id = $post->id;
                $ratingReview->expertise_rating = $request->expertise_rating;
                $ratingReview->work_ethic_rating = $request->work_ethic_rating;
                $ratingReview->professionalism_rating = $request->professionalism_rating;
                $ratingReview->customer_service_rating = $request->customer_service_rating;
                $ratingReview->rating = ((float)$request->expertise_rating + (float)$request->work_ethic_rating + (float)$request->professionalism_rating + (float)$request->customer_service_rating) / 4;
                $ratingReview->feedback_message = $request->feedback_message;
                try {
                    $ratingReview->save();
                } catch (QueryException $e) {
                    Log::error("ERROR SAVING RATING REVIEW >>>>>>>>>> " . $ratingReview . " >>>>>>>>> " . $e);
                }

                /**
                 * application with final amount
                 */
                if (!$request->final_payment_amount || $request->final_payment_amount == 0) {
                    return back()->with("danger", "Invalid final payment amount. Kindly check and try again.");
                }
                $post->final_payment_amount = $request->final_payment_amount;
                $post->payment_channel = "cash"; // to be update when we start using in-app wallets

                /**
                 * update user earnings
                 */
                $participant->total_earnings = (float)$participant->total_earnings + (float)$request->final_payment_amount;

                /**
                 * update vorker rating
                 */
                $user_review_rating = 0;
                if ($participant->rating_and_reviews->count() >= 1) {
                    $user_review_rating = $participant->rating_and_reviews->sum("rating") / $participant->rating_and_reviews->count();
                }

                $participant->rating = $user_review_rating;
                try {
                    /**
                     * create notification
                     */
                    $post->user;
                    $post->job_done_overall_rating = $ratingReview->rating;

                    $participant->update();
                    $post->update();

                    Notifications::PushUserNotification($post, $application, $participant, "JOB_CLOSED");
                } catch (QueryException $e) {
                    Log::error("ERROR UPDATING USER RATING >>>>>>>>>> " . $participant . " >>>>>>>>> " . $e);
                }
                break;
            case "FIXED_TERM_JOB" || "PERMANENT_JOB":
                $participant = User::where("id", $request->user_id)->first();
                if (!$participant) {
                    Log::debug("ERROR FETCHING USER DETAILS FOR USER ID >>>>>>>>>>> " . $request->user_id);
                }

                $application = JobApplication::where("user_id", $request->user_id)->where("post_id", $request->job_post_id)->first();
                if (!$application) {
                    Log::debug("ERROR FETCHING APPLICATION DETAILS FOR USER ID >>>>>>>>>>> " . $request->user_id . " AND POST ID >>>>> " . $request->job_post_id);
                }

                $post->final_start_date = $request->start_date;
                if ($post->final_end_date === "FIXED_TERM_JOB") {
                    $post->final_end_date = $request->end_date;
                }
                $post->final_payment_amount = $request->monthly_payment;

                try {
                    /**
                     * create notification
                     */
                    $post->user;
                    $application->job_post;
                    foreach ($post->applications as $submittedApplication) {
                        Notifications::PushUserNotification($post, $submittedApplication, $submittedApplication->user, "JOB_CLOSED");
                    }


                    /**
                     * push notification
                     */
                    PushNotification::Notify("JOB_CLOSED", $application, null);

                    $participant->update();
                } catch (QueryException $e) {
                    Log::error("ERROR UPDATING USER RATING >>>>>>>>>> " . $participant . " >>>>>>>>> " . $e);
                }
                break;
        }

        /**
         * close the post
         */
        $post->status = "closed";
        $post->closed_at = Carbon::now();
        try {
            $post->update();
        } catch (QueryException $e) {
            Log::error("ERROR closing JOB post >>>>>>>>>> " . $post . " >>>>>>>>> " . $e);
            return back()->with("danger", "Ooops...we encountered an error while closing job. Kindly try again later.");
        }

        return redirect()->route("user.posts.list")->with("success", "Post has been closed successfully.");
    }

    /**
     * @param $uuid
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function updateUserPost($uuid)
    {
        if (!$uuid) {
            return back()->with("danger", "Invalid request");
        }

        $post = Post::where("id", $uuid)->first();
        if (!$post) {
            return back()->with("danger", "Error fetching post details");
        }

        /**
         * 404
         */
        if ($post->deleted_at) {
            return back()->with("info", "This post has been removed by the issuer.");
        }

        switch ($post->type) {
            case "VOLUNTEER":
                return view("volunteerism.edit", compact("post"));
            case "QUICK_JOB":
                $categories = Skill::orderBy('name')->get();
                return view("work.quick_jobs.edit", compact("post", "categories"));
            case "P2P":
                return $this->editQuoteRequest($uuid);
            case "FIXED_TERM_JOB":
                $categories = Skill::orderBy('name')->get();
                return view("work.part_time_jobs.edit", compact("post", "categories"));
            case "PERMANENT_JOB":
                $categories = Skill::orderBy('name')->get();
                $industries = Industry::orderBy('name')->get();
                return view("work.permanent.edit", compact("post", "categories", "industries"));
        }

        return back()->with("danger", "Invalid request");
    }

    public function updatePostInformation(Request $request, $uuid)
    {
        if (!$uuid) {
            return back()->with("danger", "Invalid request");
        }

        $post = Post::where("id", $uuid)->first();
        if (!$post) {
            return back()->with("danger", "Error fetching post details");
        }

        /**
         * 404
         */
        if ($post->deleted_at) {
            return back()->with("info", "This post has been removed by the issuer.");
        }

        if ($post->user->id !== auth()->id() || $post->status === "closed") {
            return back()->with("danger", "Unauthorized operation");
        }

        switch ($post->type) {
            case "VOLUNTEER":
                $post->name = $request->name;
                $post->description = $request->description;
                $post->date = $request->date;
                $post->time = $request->time;
                $post->location = $request->location;
                $post->coords = $request->coords;
                $post->maximum_number_of_volunteers = $request->maximum_number_of_volunteers;
                $post->volunteer_hours = $request->volunteer_hours;
                $post->other_relevant_information = $request->other_relevant_information;
                break;
            case "QUICK_JOB":
                $category = Skill::where("name", $request->category)->first();
                if (!$category) {
                    return back()->with("danger", "Error fetching category details");
                }

                $post->category = $request->category;
                $post->category_id = $category->id;
                $post->description = $request->description;
                $post->date = $request->date;
                $post->time = $request->time;
                $post->location = $request->location;
                $post->coords = $request->coords;
                $post->max_budget = $request->max_budget;
                $post->min_budget = $request->min_budget;
                $post->is_negotiable = $request->negotiable;
                $post->is_includes_tax = $request->includes_tax;
                $post->other_relevant_information = $request->other_relevant_information;

                if ($post->post_image && $post->post_image != "") {
                    //save image
                    $image = $request->file('post_image');
                    $name = $post->user_id . '_' . time() . '.' . $image->getClientOriginalExtension();
                    $destinationPath = public_path('/uploads');
                    $image->move($destinationPath, $name);

                    $post->post_image_link = URL::to('/public/uploads/quick_jobs') . '/' . $name;
                }
                break;
            case "FIXED_TERM_JOB":
                $tags = array();
                foreach ($request->tags as $tag) {
                    $category = Skill::where("name", $tag)->first();
                    if ($category) {
                        array_push($tags, $tag);
                    }
                }

                $post->title = $request->title;
                $post->description = $request->description;
                $post->qualifications = $request->qualifications;
                $post->date = $request->date;
                $post->time = $request->time;
                $post->location = $request->location;
                $post->employer = $request->employer;
                $post->coords = $request->coords;
                $post->start_date = $request->start_date;
                $post->end_date = $request->end_date;
                $post->max_budget = $request->max_budget;
                $post->min_budget = $request->min_budget;
                $post->other_relevant_information = $request->other_relevant_information;

                if ($request->negotiable === "on") {
                    $post->is_negotiable = "yes";
                } else {
                    $post->is_negotiable = "no";
                }

                if ($request->renewable === "on") {
                    $post->is_renewable = "yes";
                } else {
                    $post->is_renewable = "no";
                }

                if ($request->is_internship === "on") {
                    $post->is_internship = "yes";
                } else {
                    $post->is_internship = "no";
                }

                $post->tags = json_encode($tags);
                break;
            case "PERMANENT_JOB":
                $tags = array();
                foreach ($request->tags as $tag) {
                    $category = Skill::where("name", $tag)->first();
                    if ($category) {
                        array_push($tags, $tag);
                    }
                }

                $industry = Industry::where("name", $request->industry)->first();
                if (!$industry) {
                    return redirect()->back()->with("danger", "Invalid request");
                }

                $post->title = $request->title;
                $post->description = $request->description;
                $post->qualifications = $request->qualifications;
                $post->other_relevant_information = $request->other_relevant_information;
                $post->date = $request->date;
                $post->time = $request->time;
                $post->location = $request->location;
                $post->employer = $request->employer;
                $post->coords = $request->coords;
                $post->industry_id = $industry->id;
                $post->start_date = $request->start_date;
                $post->max_budget = $request->max_budget;
                $post->min_budget = $request->min_budget;

                if ($request->negotiable === "on") {
                    $post->is_negotiable = "yes";
                } else {
                    $post->is_negotiable = "no";
                }

                if ($request->renewable === "on") {
                    $post->is_renewable = "yes";
                } else {
                    $post->is_renewable = "no";
                }

                $post->tags = json_encode($tags);
                break;
        }

        try {
            $post->update();
            return redirect()->route("user.posts.list")->with("success", "Post has been updated successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR UPDATING POST >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return back()->with("danger", "Oops. We encountered an issue while updating your post. Kindly try again.");
        }
    }

    /**
     * @param $uuid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removePost($uuid)
    {
        if (!$uuid) {
            return back()->with("danger", "Invalid request");
        }

        $post = Post::where("id", $uuid)->first();
        if (!$post) {
            return back()->with("danger", "Error fetching post details");
        }

        /**
         * 404
         */
        if ($post->deleted_at) {
            return back()->with("info", "This post has been removed by the issuer.");
        }

        if ($post->user->id !== auth()->id() || $post->status === "closed") {
            return back()->with("danger", "Unauthorized operation");
        }

        /**
         * update only the deleted_at so if it's restored the user can get the previous status
         */
        $post->deleted_at = Carbon::now();

        /**
         * get applicants and send them notifications
         */
        $confirmedApplicants = $post->applications->where("status", "confirmed");
        foreach ($confirmedApplicants as $application) {
            Notifications::PushUserNotification($post, $application, $application->user, "JOB_REMOVED");
        }

        try {
            $post->update();
            return redirect()->route("user.posts.list")->with("success", "Post has been removed successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR UPDATING POST >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return back()->with("danger", "Oops. We encountered an issue while removing your post. Kindly try again.");
        }
    }

    public function getCategories()
    {
        $categories = Skill::all();
        return $this->success_response($categories, "success");
    }

    /**
     * get all user saved posts
     */
    public function savedOpportunities(Request $request)
    {
        switch ($request->action) {
            case "get":
                return $this->success_response(["code" => "0000"], "Removed successfully.");
                break;
            case "save":
                if (!$request->uuid) {
                    return $this->data_validation_error_response([]);
                }

                $isValidOpportunity = Post::where("id", $request->uuid)->get();
                if (!$isValidOpportunity) {
                    return $this->not_found_response([]);
                }

                $savedOpportunity = new UserSavedPost();
                $savedOpportunity->user_id = auth()->id();
                $savedOpportunity->post_id = $request->uuid;
                $savedOpportunity->save();
                Log::debug("Saving opportunity");
                return $this->success_response(["code" => "0000"], "Saved successfully.");
                break;
            case "remove":
                if (!$request->uuid) {
                    return $this->data_validation_error_response([]);
                }

                $isValidOpportunity = Post::where("id", $request->uuid)->get();
                if (!$isValidOpportunity) {
                    return $this->not_found_response([], "Oops...something went wrong");
                }

                $isValidSavedOpportunity = UserSavedPost::where("post_id", $request->uuid)->get();
                if (!$isValidSavedOpportunity) {
                    return $this->not_found_response([], "Oops...something went wrong");
                }

                $isValidSavedOpportunity->delete();

                return $this->success_response(["code" => "0000"], "Removed successfully.");
                break;
        }
    }

    public function declinedJob(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'post_id' => 'required'
        ]);

        if ($validation->fails()) {
            return back()->withErrors($validation->errors())->with("danger", "Please ensure all required fields are completed.")->withInput();
        }

        $post = Post::where("id", $request->post_id)->first();
        if (!$post) {
            return back()->with("danger", "Error fetching post details");
        }

        $application = JobApplication::where("user_id", auth()->id())->where("post_id", $post->id)->first();
        if (!$application) {
            Log::info("DECLINING JOB >>>>>>>>>>>>>>>>>>>>>>>> " . json_encode($request->all()));

            return $this->not_found_response([], "Error fetching application details");
        }

        $application->status = "declined";


        try {
            $application->update();

            /**
             * Notify the issuer of a quote
             */
            $post->applications;
            Notifications::PushUserNotification($post, $post, $post->user, "JOB_DECLINED");
            Notifications::PushUserNotification($post, $post, auth()->user(), "JOB_DECLINED");

            return back()->with("success", "Job has been declined successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR DECLINING JOB >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return back()->with("danger", "Oops. We encountered an issue while declining your job. Kindly try again.");
        }
    }

    /**
     * submit quote
     */
    public function submitQuote(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'quote' => 'required',
            'post_id' => 'required',
        ]);

        if ($validation->fails()) {
            return back()->withErrors($validation->errors())->with("danger", "Please ensure all required fields are completed.")->withInput();
        }

        $post = Post::where("id", $request->post_id)->first();
        if (!$post) {
            return back()->with("danger", "Error fetching post details");
        }

        $application = JobApplication::where("user_id", auth()->id())->where("post_id", $post->id)->first();
        if (!$application) {
            return back()->with("danger", "Error fetching application details");
        }

        $application->quote = $request->quote;
        $application->comments = $request->comments;

        try {
            $application->update();

            /**
             * Notify the issuer of a quote
             */
            $post->applications;
            Notifications::PushUserNotification($post, $post, $post->user, "QUOTE_RECEIVED");
            Notifications::PushUserNotification($post, $post, auth()->user(), "QUOTE_SUBMITTED");

            return back()->with("success", "Quote has been submitted successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR SAVING USER >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return back()->with("danger", "Oops. We encountered an issue while submitting your quote. Kindly try again.");
        }
    }
}
