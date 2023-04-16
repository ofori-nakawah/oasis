<?php

namespace App\Http\Controllers\Web;

use App\Helpers\Notifications as Notifications;
use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\Post;
use App\Models\RatingReview;
use App\Models\Skill;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;


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
        if (!$type_of_user || !$type_of_work) {
            return back()->with("danger", "Invalid request");
        }

        if ($type_of_user != "seeker" && $type_of_user != "employer") {
            return back()->with("danger", "Invalid request");
        }

        if ($type_of_work != "quick-job" && $type_of_work != "fixed-term" && $type_of_work != "permanent") {
            return back()->with("danger", "Invalid request");
        }

        switch ($type_of_work) {
            case "quick-job":
                if ($type_of_user == "seeker") {
                    return $this->list_quick_jobs();
                } else {
                    $categories = Skill::all();
                    return view("work.quick_jobs.create", compact("categories"));
                }
                break;
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

        $user_location_lat = explode(',', $user_location)[0];
        $user_location_lng = explode(',', $user_location)[1];

        $volunteer_near_me = collect();
        $posts = Post::where("user_id", "!=", auth()->id())->where("status", "active")->where("type", "VOLUNTEER")->whereNull('deleted_at')->get();
        $posts->map(function ($post) use ($user_location_lat, $user_location_lng, $volunteer_near_me) {
            //get post coordinates
            $post_location_lat = explode(',', $post->coords)[0];
            $post_location_lng = explode(',', $post->coords)[1];

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
        $posts = $volunteer_near_me->sortBy("distance");

        return view("volunteerism.list", compact("posts"));
    }

    public function list_quick_jobs()
    {
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
        $posts = Post::where("user_id", "!=", auth()->id())->where("status", "active")->where("type", "QUICK_JOB")->whereIn("category_id", $user_interests)->whereNull('deleted_at')->get();

        /**
         * filter using distance
         */
        $jobs_near_me = collect();
        foreach ($posts as $post) {
            $post_location_lat = explode(',', $post->coords)[0];
            $post_location_lng = explode(',', $post->coords)[1];
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
        $posts = $jobs_near_me->sortBy("distance");;;

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

        $user_location_lat = explode(',', $user_location)[0];
        $user_location_lng = explode(',', $user_location)[1];

        /**
         * filter using distance
         */
        $volunteer_near_me = collect();
        $posts = Post::where("user_id", "!=", auth()->id())->where("status", "active")->where("type", "VOLUNTEER")->whereNull('deleted_at')->get();
        $posts->map(function ($post) use ($user_location_lat, $user_location_lng, $volunteer_near_me) {
            //get post coordinates
            $post_location_lat = explode(',', $post->coords)[0];
            $post_location_lng = explode(',', $post->coords)[1];

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

        $posts = auth()->user()->posts;

        return view("postings.show", compact("post", "posts"));
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
            return back()->withErrors($validation->errors())->with("danger", "Please ensure all required fields are completed.");
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
            return back()->withErrors($validation->errors())->with("danger", "Please ensure all required fields are completed.");
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
                $categories = Skill::all();
                return view("work.quick_jobs.edit", compact("post", "categories"));
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

        if ($post->user->id !== auth()->id()) {
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
        }

        try {
            $post->update();
            return redirect()->route("user.posts.show", ["uuid" => $post->id])->with("success", "Post has been updated successfully.");
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
}
