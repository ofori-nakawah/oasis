<?php

namespace App\Http\Controllers\Mobile;

use App\Models\JobApplication;
use App\Models\Post;
use App\Models\RatingReview;
use App\Models\Skill;
use App\Models\User;
use App\Traits\Responses;
use App\Helpers\Notifications as Notifications;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PostController extends Controller
{
    use Responses;
    const JOB_SEARCH_RADIUS = 5;
    const VOLUNTEER_SEARCH_RADIUS = 10;

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * create a volunteer post
     */
    public function create_volunteer_post(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'date' => 'required',
            'time' => 'required',
            'location' => 'required',
            'coords' => 'required',
            'maximum_number_of_volunteers' => 'required',
            'volunteer_hours' => 'required',
        ]);

        if ($validation->fails()) {
            return $this->data_validation_error_response($validation->errors());
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
            return $this->success_response($post, "Post has been published successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR SAVING USER >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return $this->db_operation_error_response([]);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * Create a quick job post
     */
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
            return $this->data_validation_error_response($validation->errors());
        }

        $category = Skill::where("name", $request->category)->first();
        if (!$category) {
            return $this->not_found_response([], "Error fetching category details");
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
        $post->is_negotiable = $request->is_negotiable;
        $post->is_includes_tax = $request->is_includes_tax;
        $post->other_relevant_information = $request->other_relevant_information;
        $post->user_id = auth()->id();
        $post->type = "QUICK_JOB";

        if ($post->post_image && $post->post_image != "") {
            //save image
            $image = $request->file('post_image');
            $name = $post->user_id . '_' . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/uploads');
            $image->move($destinationPath, $name);

            $post->post_image_link = URL::to('/public/uploads/quick_jobs') . '/' . $name;
        }

        try {
            $post->save();
            return $this->success_response($post, "Post has been published successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR SAVING USER >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return $this->db_operation_error_response([]);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * Get posts created by user
     */
    public function get_user_posts(Request $request)
    {
        $posts = auth()->user()->posts;
        return $this->success_response($posts, "Posts fetched successfully.");
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * Get posts based on user location and category
     */
    public function get_posts_closest_to_me(Request $request)
    {
        $posts = [];

        //get user coordinates
        $user_location = auth()->user()->location_coords;
        if (!$user_location) {
            return $this->not_found_response([], "Could not retrieve user's current location");
        }

        $user_location_lat = explode(',', $user_location)[0];
        $user_location_lng = explode(',', $user_location)[1];

        switch ($request->type) {
            case "VOLUNTEER":
                $volunteer_near_me = collect();
                $posts = Post::where("user_id", "!=", auth()->id())->where("status", "active")->where("type", $request->type)->get();
                $posts->map(function ($post) use ($user_location_lat, $user_location_lng, $volunteer_near_me) {
                    //get post coordinates
                    $post_location_lat = explode(',', $post->coords)[1];
                    $post_location_lng = explode(',', $post->coords)[0];

                    $distance = $this->get_distance($user_location_lat, $user_location_lng, $post_location_lat, $post_location_lng, "K");
                    $post["organiser_name"] = $post->user->name;
                    $post["distance"] = number_format($distance, 2);
                    if ($distance <= self::VOLUNTEER_SEARCH_RADIUS) {
                        Log::debug("on pointoo");
                        $volunteer_near_me->push($post);
                    }
                    return $post;
                });
                $posts = $volunteer_near_me;

                break;
            case "QUICK_JOB":
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
                $posts = Post::where("user_id", "!=", auth()->id())->where("status", "active")->where("type", $request->type)->whereIn("category_id", $user_interests)->get();

                /**
                 * filter using distance
                 */
                $jobs_near_me = collect();
                foreach ($posts as $post) {
                    $post_location_lat = explode(',', $post->coords)[1];
                    $post_location_lng = explode(',', $post->coords)[0];
                    $distance = $this->get_distance($user_location_lat, $user_location_lng, $post_location_lat, $post_location_lng, "K");
                    if ($distance <= self::JOB_SEARCH_RADIUS) {
                        $jobs_near_me->push($post);
                    }
                }
                $posts = $jobs_near_me;
                break;
        }

        return $this->success_response($posts, "Posts fetched successfully.");
    }

    /**
     * @param $lat1
     * @param $lon1
     * @param $lat2
     * @param $lon2
     * @param $unit
     * @return float
     */
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_post_details(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'id' => 'required'
        ]);

        if ($validation->fails()) {
            return $this->data_validation_error_response($validation->errors());
        }

        $post = Post::where("id", $request->id)->first();
        if (!$post) {
            return $this->not_found_response([], "Error fetching post details");
        }
        $has_already_applied = JobApplication::where("user_id", auth()->id())->where("post_id", $post->id)->first();
        if ($has_already_applied) {
            $post->has_already_applied = "yes";
        }
        $post->user;

        //get user coordinates
        $user_location = auth()->user()->location_coords;
        if (!$user_location) {
            return $this->not_found_response([], "Could not retrieve user's current location");
        }

        $user_location_lat = explode(',', $user_location)[0];
        $user_location_lng = explode(',', $user_location)[1];

        //get post coordinates
        $post_location_lat = explode(',', $post->coords)[1];
        $post_location_lng = explode(',', $post->coords)[0];

        $distance = $this->get_distance($user_location_lat, $user_location_lng, $post_location_lat, $post_location_lng, "K");
        $post["distance"] = number_format($distance, 2);

        return $this->success_response($post, "Posts fetched successfully.");
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apply_for_job(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'job_post_id' => 'required'
        ]);

        if ($validation->fails()) {
            return $this->data_validation_error_response($validation->errors());
        }

        $post = Post::where("id", $request->job_post_id)->first();
        if (!$post) {
            return $this->not_found_response([], "Error fetching post details");
        }

        //check if user has applied already
        $has_already_applied = JobApplication::where("user_id", auth()->id())->where("post_id", $request->job_post_id)->first();
        if ($has_already_applied) {
            return $this->general_error_response([], "You have already applied for this job.");
        }

        $job_application = new JobApplication();
        $job_application->user_id = auth()->id();
        $job_application->post_id = $request->job_post_id;
        try {
            $job_application->save();

            /**
             * create notification
             */
            $post->user;
            Notifications::PushUserNotification($post, $job_application, auth()->user(), "SUCCESSFUL_JOB_APPLICATION");
        } catch (QueryException $e) {
            Log::error("ERROR SAVING JOB APPLICATION >>>>>>>>>> " . $job_application . " >>>>>>>>> " . $e);
            return $this->db_operation_error_response([]);
        }

        return $this->success_response([], "Congratulations! Your application was successful");
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_user_post_status(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'uuid' => 'required'
        ]);

        if ($validation->fails()) {
            return $this->data_validation_error_response($validation->errors());
        }

        $post = Post::where("id", $request->uuid)->first();
        if (!$post) {
            return $this->not_found_response([], "Error fetching post details");
        }

        $post->number_of_participants_applied = $post->applications()->count();
        $post->number_of_participants_confirmed = $post->applications()->where("status", "confirmed")->count();

        foreach ($post->applications as $application) {
            $application->user;
            $application->rating_and_reviews;
        }

        return $this->success_response($post, "Posts fetched successfully.");
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * Get the post details for the post creator to make modifications
     */
    public function get_user_post_details(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'uuid' => 'required'
        ]);

        if ($validation->fails()) {
            return $this->data_validation_error_response($validation->errors());
        }

        $post = Post::where("id", $request->uuid)->first();
        if (!$post) {
            return $this->not_found_response([], "Error fetching post details");
        }

        return $this->success_response($post, "Posts fetched successfully.");
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirm_decline_applicant(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'action' => 'required',
            'application_id' => 'required',
        ]);

        if ($validation->fails()) {
            return $this->data_validation_error_response($validation->errors());
        }

        $application = JobApplication::where("id", $request->application_id)->first();
        if (!$application) {
            return $this->not_found_response([], "Error fetching application details");
        }

        $message = "";
        if ($request->action === "confirm") {
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
            if ($application->job_post->type != "VOLUNTEER" || $request->action === "confirm") {
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
            return $this->db_operation_error_response([]);
        }

        return $this->success_response([], $message);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * we are also setting the volunteer hours here
     */
    public function close_post(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'job_post_id' => 'required',
            'job_type' => 'required',
        ]);

        if ($validation->fails()) {
            return $this->data_validation_error_response($validation->errors());
        }

        $post = Post::where("id", $request->job_post_id)->first();
        if (!$post) {
            return $this->not_found_response([], "Error fetching post details");
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
                for ($i = 0; $i < count($volunteer_details); $i++) {
                    $participant = User::where("id", $volunteer_details[$i]["user_id"])->first();
                    if (!$participant) {
                        Log::debug("ERROR FETCHING USER DETAILS FOR USER ID >>>>>>>>>>> " . $volunteer_details[$i]["user_id"]);
                    }

                    $application = JobApplication::where("user_id", $volunteer_details[$i]["user_id"])->where("post_id", $request->job_post_id)->first();
                    if (!$application) {
                        Log::debug("ERROR FETCHING APPLICATION DETAILS FOR USER ID >>>>>>>>>>> " . $volunteer_details[$i]["user_id"] . " AND POST ID >>>>> " . $request->job_post_id);
                    }

                    $participant->volunteer_hours += (float)$volunteer_details[$i]["volunteer_hours"];
                    $application->volunteer_hours = (float)$volunteer_details[$i]["volunteer_hours"];
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
                $ratingReview->rating = ((float) $request->expertise_rating + (float) $request->work_ethic_rating + (float) $request->professionalism_rating + (float) $request->customer_service_rating) / 4;
                $ratingReview->feedback_message = $request->feedback_message;
                try {
                    $ratingReview->save();
                }  catch (QueryException $e) {
                    Log::error("ERROR SAVING RATING REVIEW >>>>>>>>>> " . $ratingReview . " >>>>>>>>> " . $e);
                }

                /**
                 * application with final amount
                 */
                $post->final_payment_amount = $request->final_payment_amount;
                $post->payment_channel = "cash"; // to be update when we start using in-app wallets

                /**
                 * update user earnings
                 */
                $participant->total_earnings = (float) $participant->total_earnings + (float) $request->final_payment_amount;

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
                    Notifications::PushUserNotification($post, $application, $participant, "JOB_CLOSED");
                    $participant->update();
                }  catch (QueryException $e) {
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
            return $this->db_operation_error_response([]);
        }

        return $this->success_response($post, "Post has been closed successfully.");
    }

    public function filter_jobs(Request $request)
    {
        $interests = $request->interests;
        $user_location = auth()->user()->location_coords;
        if (!$user_location) {
            return $this->not_found_response([], "Could not retrieve user's current location");
        }

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
        $posts = Post::where("user_id", "!=", auth()->id())->where("status", "active")->where("type", $request->type)->whereIn("category_id", $user_interests)->get();

        /**
         * filter using distance
         */
        $jobs_near_me = collect();
        foreach ($posts as $post) {
            $post_location_lat = explode(',', $post->coords)[1];
            $post_location_lng = explode(',', $post->coords)[0];

            $user_location_lat = explode(',', $user_location)[0];
            $user_location_lng = explode(',', $user_location)[1];


            $distance = $this->get_distance($user_location_lat, $user_location_lng, $post_location_lat, $post_location_lng, "K");
            if ($distance <= self::JOB_SEARCH_RADIUS) {
                $jobs_near_me->push($post);
            }
        }
        $posts = $jobs_near_me;
        return $this->success_response($posts, "Posts fetched successfully.");
    }
}
