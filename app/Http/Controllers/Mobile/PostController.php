<?php

namespace App\Http\Controllers\Mobile;

use App\Constants\EventsList;
use App\Helpers\DateFormatter;
use App\Helpers\Notifications as Notifications;
use App\Models\JobApplication;
use App\Models\Post;
use App\Models\RatingReview;
use App\Models\Skill;
use App\Models\User;
use App\Services\PushNotification;
use App\Traits\Responses;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    use Responses;

    const JOB_SEARCH_RADIUS = 5;
    const VOLUNTEER_SEARCH_RADIUS = 10;

    /**
     * @param Request $request
     * @return JsonResponse
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
        $post->source = "MOBILE";

        try {
            $post->save();
            return $this->success_response($post, "Post has been published successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR SAVING USER >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return $this->db_operation_error_response([]);
        }
    }

    public function updatePost(Request $request)
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

        switch ($request->type) {
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
                    return $this->not_found_response([], "Error fetching category details");
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
                $post->is_negotiable = $request->is_negotiable;
                $post->is_includes_tax = $request->is_includes_tax;
                $post->other_relevant_information = $request->other_relevant_information;

                if ($request->post_image && $request->post_image != "") {
                    $image = $request->post_image;
                    $name = $post->id . '_' . time() . '.png';
                    $destinationPath = public_path('/uploads/quick_jobs/');

                    $image_parts = explode(";base64,", $image);
                    $image_base64 = base64_decode($image_parts[1]);
                    $file = $destinationPath . $name;
                    file_put_contents($file, $image_base64);

                    $post->post_image_link = URL::to('/public/uploads/quick_jobs') . '/' . $name;
                }
                break;
            case "FIXED_TERM_JOB":
                $tags = array();
                foreach (explode(",", $request->tags) as $tag) {
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
        }

        try {
            $post->update();
            return $this->success_response($post, "Post has been updated successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR UPDATING POST >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return $this->db_operation_error_response([]);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
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
        $post->coords = json_encode($request->coords);
        $post->max_budget = $request->max_budget;
        $post->min_budget = $request->min_budget;
        $post->is_negotiable = $request->is_negotiable;
        $post->is_includes_tax = $request->is_includes_tax;
        $post->other_relevant_information = $request->other_relevant_information;
        $post->user_id = auth()->id();
        $post->type = "QUICK_JOB";
        $post->source = "MOBILE";

        if ($request->post_image && $request->post_image["_j"] !== []) {
            $image = $request->post_image;
            $name = $post->id . '_' . $post->type . '_' . time() . '.png';
            $destinationPath = public_path('/uploads/listings/quick-job/');

            $image_parts = explode(";base64,", $image);
            $image_base64 = base64_decode($image_parts[1]);
            $file = $destinationPath . $name;
            file_put_contents($file, $image_base64);

            $post->post_image_link = URL::to('/public/uploads/listings/quick-job') . '/' . $name;
            if (!$post->update()) {
                Log::error("ERROR UPDATING IMAGE FOR P2P POST " . $post->id);
            }
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
     * request a quote for a P2P job
     */
    public function requestP2PQuote(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'category' => 'required',
            'description' => 'required',
            'date' => 'required',
            'time' => 'required',
            'location' => 'required',
            'coords' => 'required',
        ]);

        if ($validation->fails()) {
            return $this->data_validation_error_response($validation->errors());
        }

        $category = Skill::where("name", $request->category)->first();
        if (!$category) {
            return $this->not_found_response([], "Error fetching category details");
        }

        DB::beginTransaction();

        $post = new Post();
        $post->category = $request->category;
        $post->category_id = $category->id;
        $post->description = $request->description;
        $post->date = $request->date;
        $post->time = $request->time;
        $post->location = $request->location;
        $post->coords = json_encode($request->coords);
        $post->user_id = auth()->id();
        $post->type = "P2P";
        $post->source = "MOBILE";

        try {
            if (!$post->save()) {
                Log::error("ERROR CREATING P2P POST " . json_encode($request));
            }

            /**
             * if the request included an image this
             * is when we save the image and add to the post
             */
            if ($request->post_image) {
                $image = $request->post_image;
                $name = $post->id . '_' . $post->type . '_' . time() . '.png';
                $destinationPath = public_path('/uploads/listings/p2p/');

                $image_parts = explode(";base64,", $image);
                $image_base64 = base64_decode($image_parts[1]);
                $file = $destinationPath . $name;
                file_put_contents($file, $image_base64);

                $post->post_image_link = URL::to('/public/uploads/listings/p2p') . '/' . $name;
                if (!$post->update()) {
                    Log::error("ERROR UPDATING IMAGE FOR P2P POST " . $post->id);
                }
            }

            /**
             * create application | placeholder quotation
             */
            $vorkers = $request->vorkers;
            foreach ($vorkers as $vorker) {
                /**
                 * get vorker details
                 */
                $vorkerDetails = User::where("id", $vorker["userId"])->first();
                if (!$vorkerDetails) {
                    Log::error("ERROR FETCHING VORKER " . $vorker["userId"]);
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
                    Log::error("ERROR SAVING APPLICATION FOR " . $vorker["userId"] . " FOR P2P POST " . $post->id);
                }

                $post->user;

                /**
                 * create notification
                 */
                Notifications::PushUserNotification($post, $post, $vorkerDetails, "SUCCESSFUL_JOB_APPLICATION");
                //PushNotification::notify("title", "body", "PROFILE_UPDATE", "details", auth()->user()->fcm_token);
            }

            DB::commit();

            return $this->success_response($post, "Job request has been sent to vorker successfully.");
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error("ERROR SAVING USER >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return $this->db_operation_error_response([]);
        }
    }

    public function declined_job(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'post_id' => 'required'
        ]);

        if ($validation->fails()) {
            return $this->data_validation_error_response($validation->errors());
        }

        $post = Post::where("id", $request->post_id)->first();
        if (!$post) {
            return $this->not_found_response([], "Error fetching post details");
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

            return $this->success_response($application, "Job has been declined successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR DECLINING JOB >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return $this->db_operation_error_response([]);
        }
    }

    /**
     * submit quote
     */
    public function submit_quote(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'quote' => 'required',
            'post_id' => 'required',
        ]);

        if ($validation->fails()) {
            return $this->data_validation_error_response($validation->errors());
        }

        $post = Post::where("id", $request->post_id)->first();
        if (!$post) {
            return $this->not_found_response([], "Error fetching post details");
        }

        $application = JobApplication::where("user_id", auth()->id())->where("post_id", $post->id)->first();
        if (!$application) {
            return $this->not_found_response([], "Error fetching application details");
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

            return $this->success_response($application, "Quote has been submitted successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR SAVING USER >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return $this->db_operation_error_response([]);
        }
    }


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
            return $this->data_validation_error_response($validation->errors());
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
        $post->coords = json_encode($request->coords);;
        $post->start_date = date('Y-m-d', strtotime(str_replace('/', '-', $request->start_date)));
        $post->end_date = date('Y-m-d', strtotime(str_replace('/', '-', $request->end_date)));
        $post->max_budget = $request->max_budget;
        $post->min_budget = $request->min_budget;
        $post->is_negotiable = $request->is_negotiable;
        $post->is_renewable = $request->is_renewable;
        $post->is_internship = $request->is_internship;


        $post->tags = json_encode($tags);
        $post->user_id = auth()->id();
        $post->type = "FIXED_TERM_JOB";
        $post->source = "MOBILE";

        try {
            $post->save();
            return $this->success_response($post, "Post has been published successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR SAVING post >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return $this->db_operation_error_response([], "Oops. We encountered an issue while publishing your post. Kindly try again.");
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
            'location' => 'required',
            'coords' => 'required',
            'min_budget' => 'required',
            'max_budget' => 'required',
            'tags' => 'required',
        ]);

        if ($validation->fails()) {
            return $this->data_validation_error_response($validation->errors());
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
        $post->coords = json_encode($request->coords);;
        $post->max_budget = $request->max_budget;
        $post->min_budget = $request->min_budget;
        $post->is_negotiable = $request->is_negotiable;
        $post->is_renewable = $request->is_renewable ?? 'No';
        $post->is_internship = $request->is_internship;


        $post->tags = json_encode($tags);
        $post->user_id = auth()->id();
        $post->type = "PERMANENT_JOB";
        $post->source = "MOBILE";

        try {
            $post->save();
            return $this->success_response($post, "Post has been published successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR SAVING post >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return $this->db_operation_error_response([], "Oops. We encountered an issue while publishing your post. Kindly try again.");
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * Get posts created by user
     */
    public function get_user_posts(Request $request)
    {
        $posts = auth()->user()->posts->sortBy("created_at")->whereNull('deleted_at');
        foreach ($posts as $post) {
            $post->createdAt = $post->created_at->diffForHumans();
            $time = strtotime($post->date . ' ' . $post->time);
            $newformat = date('d-m-Y H:i', $time);
            $post->postedDateTime = Carbon::parse($newformat)->toDayDateTimeString();
            $has_already_applied = JobApplication::where("user_id", auth()->id())->where("post_id", $post->id)->first();
            if ($has_already_applied) {
                $post->has_already_applied = "yes";
            } else {
                $post->has_already_applied = "no";
            }

            $jobApplications = $post->applications;
            foreach ($jobApplications as $jobApplication) {
                $jobApplication->user;
                $jobApplication->rating_and_reviews;
            }
            $post->applications;
            $post->user;
        }
        return $this->success_response($posts, "Posts fetched successfully.");
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * Get posts based on user location and category
     */
    public function get_posts_closest_to_me(Request $request)
    {
        Log::debug("GET POSTS CLOSEST TO ME >>>>>>>>>>>>>>>>>>>>>>>> " . json_encode($request->all()));
        $posts = [];

        //get user coordinates
        $user_location = auth()->user()->location_coords;
        if (!$user_location) {
            return $this->not_found_response([], "Could not retrieve user's current location");
        }

        $user_location_lat = json_decode($user_location)->latitude ??  explode(',', $user_location)[0];
        $user_location_lng = json_decode($user_location)->longitude ?? explode(',', $user_location)[1];

        switch ($request->type) {
            case "VOLUNTEER":
                $volunteer_near_me = collect();
                $posts = Post::where("user_id", "!=", auth()->id())->where("status", "active")->where("type", $request->type)->orderByDesc("created_at")->get();
                $posts->map(function ($post) use ($user_location_lat, $user_location_lng, $volunteer_near_me) {
                    //get post coordinates
                    $post_location_lat = json_decode($post->coords)->latitude ?? explode(',', $post->coords)[0];
                    $post_location_lng = json_decode($post->coords)->longitude ?? explode(',', $post->coords)[1];

                    $distance = $this->get_distance($user_location_lat, $user_location_lng, $post_location_lat, $post_location_lng, "K");
                    $post["organiser_name"] = $post->user->name;
                    $post["distance"] = number_format($distance, 2);
                    $post["postedOn"] = $post->created_at->diffForHumans();
                    $post["postedDateTime"] = date("jS \of F, Y g:i A", strtotime(DateFormatter::Parse($post->date) . ' ' . $post->time));
                    if ($distance <= self::VOLUNTEER_SEARCH_RADIUS) {
                        $volunteer_near_me->push($post);
                    }
                    return $post;
                });
                $posts = $volunteer_near_me->sortBy("distance");
                foreach ($posts as $post) {
                    $has_already_applied = JobApplication::where("user_id", auth()->id())->where("post_id", $post->id)->first();
                    if ($has_already_applied) {
                        $post->has_already_applied = "yes";
                    } else {
                        $post->has_already_applied = "no";
                    }
                }

                break;
            case "QUICK_JOB":
                $searchCategories = $request->categories;
                $_user_interests = auth()->user()->skills;
                $user_interests = array();
                foreach ($_user_interests as $interest) {
                    array_push($user_interests, $interest->skill->id);
                }

                /**
                 * filter using search categories
                 */
                if ($searchCategories && count($searchCategories) > 0) {
                    $_searchCategories = array();
                    foreach ($searchCategories as $interest) {
                        array_push($_searchCategories, $interest);
                    }

                    $posts = Post::where("user_id", "!=", auth()->id())->where("status", "active")->where("type", $request->type)->whereIn("category_id", $_searchCategories)->orderByDesc("created_at")->get();
                } else {
                    /**
                     * default search categories
                     */
                    $posts = Post::where("user_id", "!=", auth()->id())->where("status", "active")->where("type", $request->type)->whereIn("category_id", $user_interests)->orderByDesc("created_at")->get();
                }

                $jobs_near_me = collect();
                foreach ($posts as $post) {
                    $post_location_lat = json_decode($post->coords)->latitude ?? explode(',', $post->coords)[0];
                    $post_location_lng = json_decode($post->coords)->longitude ?? explode(',', $post->coords)[1];
                    $distance = $this->get_distance($user_location_lat, $user_location_lng, $post_location_lat, $post_location_lng, "K");
                    $post["distance"] = number_format($distance, 2);
                    $post["postedOn"] = $post->created_at->diffForHumans();
                    $post["postedDateTime"] = date("jS \of F, Y g:i A", strtotime(DateFormatter::Parse($post->date) . ' ' . $post->time));

                    $post->user;

                    /**
                     * filter using distance
                     */
                    if ($request->searchRadius && $request->searchRadius != "") {
                        if ($distance <= $request->searchRadius) {
                            $jobs_near_me->push($post);
                        }
                    } elseif ($request->filterOptions["distance"]) {
                        if ($distance <= $request->filterOptions["distance"]) {
                            $jobs_near_me->push($post);
                        }
                    } else {
                        if ($distance <= self::JOB_SEARCH_RADIUS) {
                            $jobs_near_me->push($post);
                        }
                    }
                }

                if ($request->filterOptions) {
                    /**
                     * filter by isNegotiable
                     */
                    if ($request->filterOptions["isSalaryNegotiable"]) {
                        $jobs_near_me = $jobs_near_me->filter(function ($post) {
                            $matchingItems = $post->is_negotiable === "yes";
                            return !empty($matchingItems);
                        });
                    }

                    /**
                     * filter by minBudget from filterOptions
                     */
                    if (isset($request->filterOptions['minBudget']) && $request->filterOptions['minBudget'] !== '') {
                        $minBudget = floatval($request->filterOptions['minBudget']);
                        $posts = $posts->filter(function ($post) use ($minBudget) {
                            return floatval($post->min_budget) >= $minBudget;
                        });
                    }

                    /**
                     * filter by maxBudget from filterOptions
                     */
                    if (isset($request->filterOptions['maxBudget']) && $request->filterOptions['maxBudget'] !== '') {
                        $maxBudget = floatval($request->filterOptions['maxBudget']);
                        $posts = $posts->filter(function ($post) use ($maxBudget) {
                            return floatval($post->max_budget) <= $maxBudget;
                        });
                    }

                    /**
                     * fiter by categories
                     */
                    $searchCategories = $request->filterOptions["selectedCategories"];
                    if ($searchCategories && count($searchCategories) > 0) {
                        $_searchCategories = array();
                        foreach ($searchCategories as $interest) {
                            array_push($_searchCategories, $interest);
                        }

                        $jobs_near_me = $jobs_near_me->filter(function ($post) use ($_searchCategories) {
                            $matchingItems = in_array($post->category, $_searchCategories);
                            return !empty($matchingItems);
                        });
                    }
                }


                $posts = $jobs_near_me->sortBy("distance");
                break;
            case "FIXED_TERM_JOB":
                $searchCategories = $request->categories;
                $_user_interests = auth()->user()->skills;
                $user_interests = array();
                foreach ($_user_interests as $interest) {
                    array_push($user_interests, $interest->skill->id);
                }

                $posts = Post::where("user_id", "!=", auth()->id())->where("status", "active")->where("type", "FIXED_TERM_JOB")->whereNull('deleted_at')->orderByDesc("created_at")->get();

                $jobs_near_me = collect();
                foreach ($posts as $post) {
                    $post_location_lat = json_decode($post->coords)->latitude ?? explode(',', $post->coords)[0];
                    $post_location_lng = json_decode($post->coords)->longitude ?? explode(',', $post->coords)[1];
                    $distance = $this->get_distance($user_location_lat, $user_location_lng, $post_location_lat, $post_location_lng, "K");
                    $post["distance"] = number_format($distance, 2);
                    $post["organiser_name"] = $post->user->name;
                    $post["postedOn"] = $post->created_at->diffForHumans();
                    $post["postedDateTime"] = date("jS \of F, Y g:i A", strtotime($post->date . ' ' . $post->time));

                    $post->user;
                    /**
                     * filter using distance
                     */
                    if ($request->searchRadius && $request->searchRadius != "") {
                        if ($distance <= $request->searchRadius) {
                            $jobs_near_me->push($post);
                        }
                    } else {
                        if ($distance <= self::JOB_SEARCH_RADIUS) {
                            $jobs_near_me->push($post);
                        }
                    }

                    /**
                     * filter using search categories
                     */
                    if ($searchCategories && count($searchCategories) > 0) {
                        $_searchCategories = array();
                        foreach ($searchCategories as $interest) {
                            array_push($_searchCategories, $interest);
                        }

                        $matchingItems = array_intersect($_searchCategories, json_decode($post->tags));
                        if (!empty($matchingItems)) {
                            $jobs_near_me->push($post);
                        }
                    } else {
                        /**
                         * default search categories
                         */
                        $matchingItems = array_intersect($user_interests, json_decode($post->tags));
                        if (!empty($matchingItems)) {
                            $jobs_near_me->push($post);
                        }
                    }

                    if ($request->filterOptions) {
                        /**
                         * fiter by category
                         */
                        $searchCategories = $request->filterOptions["selectedCategories"];
                        if ($searchCategories && count($searchCategories) > 0) {
                            $_searchCategories = array();
                            foreach ($searchCategories as $interest) {
                                array_push($_searchCategories, $interest);
                            }

                            $matchingItems = array_intersect($_searchCategories, json_decode($post->tags));
                            if (!empty($matchingItems)) {
                                $jobs_near_me->push($post);
                            }
                        } else {
                            /**
                             * default search categories
                             */
                            $matchingItems = array_intersect($user_interests, json_decode($post->tags));
                            if (!empty($matchingItems)) {
                                $jobs_near_me->push($post);
                            }
                        }
                    }
                }


                /**
                 * filter by minBudget
                 */
                $minBudget = $request->minBudget;
                if ($minBudget && $minBudget != "") {
                    $jobs_near_me = $jobs_near_me->filter(function ($value, $key) use ($minBudget) {
                        return $value['min_budget'] >= $minBudget;
                    });
                }

                /**
                 * filter by maxBudget
                 */
                $maxBudget = $request->maxBudget;
                if ($maxBudget && $maxBudget != "") {
                    $jobs_near_me = $jobs_near_me->filter(function ($value, $key) use ($maxBudget) {
                        return $value['max_budget'] <= $maxBudget;
                    });
                }

                $posts = $jobs_near_me->sortBy("distance");
                break;
            case "PERMANENT_JOB":
                $searchCategories = $request->categories;
                $_user_interests = auth()->user()->skills;
                $user_interests = array();
                foreach ($_user_interests as $interest) {
                    array_push($user_interests, $interest->skill->id);
                }

                $posts = Post::where("user_id", "!=", auth()->id())->where("status", "active")->where("type", "PERMANENT_JOB")->whereNull('deleted_at')->orderByDesc("created_at")->get();

                $jobs_near_me = collect();
                foreach ($posts as $post) {
                    $post_location_lat = json_decode($post->coords)->latitude ?? explode(',', $post->coords)[0];
                    $post_location_lng = json_decode($post->coords)->longitude ?? explode(',', $post->coords)[1];
                    $distance = $this->get_distance($user_location_lat, $user_location_lng, $post_location_lat, $post_location_lng, "K");
                    $post["distance"] = number_format($distance, 2);
                    $post["organiser_name"] = $post->user->name;
                    $post["postedOn"] = $post->created_at->diffForHumans();
                    $post["postedDateTime"] = date("jS \of F, Y g:i A", strtotime($post->date . ' ' . $post->time));

                    $post->user;
                    $post->industry;

                    /**
                     * filter using distance
                     */
                    if ($request->searchRadius && $request->searchRadius != "") {
                        if ($distance <= $request->searchRadius) {
                            $jobs_near_me->push($post);
                        }
                    } else {
                        if ($distance <= self::JOB_SEARCH_RADIUS) {
                            $jobs_near_me->push($post);
                        }
                    }

                    /**
                     * filter using search categories
                     */
                    if ($searchCategories && count($searchCategories) > 0) {
                        $_searchCategories = array();
                        foreach ($searchCategories as $interest) {
                            array_push($_searchCategories, $interest);
                        }

                        $matchingItems = array_intersect($_searchCategories, json_decode($post->tags));
                        if (!empty($matchingItems)) {
                            $jobs_near_me->push($post);
                        }
                    } else {
                        /**
                         * default search categories
                         */
                        $matchingItems = array_intersect($user_interests, json_decode($post->tags));
                        if (!empty($matchingItems)) {
                            $jobs_near_me->push($post);
                        }
                    }

                    if ($request->filterOptions) {
                        /**
                         * fiter by category
                         */
                        $searchCategories = $request->filterOptions["selectedCategories"];
                        if ($searchCategories && count($searchCategories) > 0) {
                            $_searchCategories = array();
                            foreach ($searchCategories as $interest) {
                                array_push($_searchCategories, $interest);
                            }

                            $matchingItems = array_intersect($_searchCategories, json_decode($post->tags));
                            if (!empty($matchingItems)) {
                                $jobs_near_me->push($post);
                            }
                        } else {
                            /**
                             * default search categories
                             */
                            $matchingItems = array_intersect($user_interests, json_decode($post->tags));
                            if (!empty($matchingItems)) {
                                $jobs_near_me->push($post);
                            }
                        }
                    }
                }

                /**
                 * filter by minBudget
                 */
                $minBudget = $request->minBudget;
                if ($minBudget && $minBudget != "") {
                    $jobs_near_me = $jobs_near_me->filter(function ($value, $key) use ($minBudget) {
                        return $value['min_budget'] >= $minBudget;
                    });
                }

                /**
                 * filter by maxBudget
                 */
                $maxBudget = $request->maxBudget;
                if ($maxBudget && $maxBudget != "") {
                    $jobs_near_me = $jobs_near_me->filter(function ($value, $key) use ($maxBudget) {
                        return $value['max_budget'] <= $maxBudget;
                    });
                }

                $posts = $jobs_near_me->sortBy("distance");
                break;
        }


        return $this->success_response($posts, "Posts fetched successfully.");
    }



    /**
     * @param Request $request
     * @return JsonResponse
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
        $post->industry;

        //get user coordinates
        $user_location = auth()->user()->location_coords;
        if (!$user_location) {
            return $this->not_found_response([], "Could not retrieve user's current location");
        }

        $user_location_lat = json_decode($user_location)->latitude ??  explode(',', $user_location)[0];
        $user_location_lng = json_decode($user_location)->longitude ?? explode(',', $user_location)[1];


        //get post coordinates
        $post_location_lat = json_decode($post->coords)->latitude ?? explode(',', $post->coords)[0];
        $post_location_lng = json_decode($post->coords)->longitude ?? explode(',', $post->coords)[1];

        $distance = $this->get_distance($user_location_lat, $user_location_lng, $post_location_lat, $post_location_lng, "K");
        $post["distance"] = number_format($distance, 2);
        $post["postedOn"] = $post->created_at->diffForHumans();
        $toDate = Carbon::parse($post->end_date);
        $fromDate = Carbon::parse($post->start_date);
        $post["duration"] = $toDate->diffInMonths($fromDate);
        $post["deadline"] =  date("jS \of F, Y g:i A", strtotime($post->date . ' ' . $post->time));
        $post["postedDateTime"] = date("jS \of F, Y g:i A", strtotime(DateFormatter::Parse($post->date) . ' ' . $post->time));

        return $this->success_response($post, "Posts fetched successfully.");
    }

    /**
     * @param Request $request
     * @return JsonResponse
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
     * @return JsonResponse
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

        $post->createdAt = $post->created_at->diffForHumans();
        $post["postedDateTime"] = date("jS \of F, Y g:i A", strtotime(DateFormatter::Parse($post->date) . ' ' . $post->time));

        $toDate = Carbon::parse($post->final_end_date);
        $fromDate = Carbon::parse($post->final_start_date);
        $post["duration"] = $toDate->diffInMonths($fromDate);

        return $this->success_response($post, "Posts fetched successfully.");
    }

    /**
     * @param Request $request
     * @return JsonResponse
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

    public function get_distance($lat1, $lon1, $lat2, $lon2, $unit)
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
     * @param Request $request
     * @return JsonResponse
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

        /**
         * validate the number of volunteers required
         */
        if ($application->job_post->type === "VOLUNTEER") {
            $countOfConfirmedVolunteers = $application->job_post->applications->where("status", "confirmed")->count();
            if ($countOfConfirmedVolunteers >= $application->job_post->maximum_number_of_volunteers) {
                return $this->general_error_response([], "Maximum number of volunteers reached");
            }
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

            $application->user;
            $application->job_post;

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
                PushNotification::Notify("APPLICATION_CONFIRMED", $application, $application->user->fcm_token);
            } else {
                /**
                 * create notification
                 */
                Notifications::PushUserNotification($application->job_post, $application, $application->user, "APPLICATION_DECLINED");
                PushNotification::Notify("APPLICATION_DECLINED", $application, $application->user->fcm_token);
            }
        } catch (QueryException $e) {
            Log::error("ERROR confirming user for JOB APPLICATION >>>>>>>>>> " . $application . " >>>>>>>>> " . $e);
            return $this->db_operation_error_response([]);
        }

        $listing = $application->job_post;
        $jobApplications = $listing->applications;
        foreach ($jobApplications as $jobApplication) {
            $jobApplication->user;
            $jobApplication->rating_and_reviews;
        }
        $listing->applications;
        $listing->user;

        return $this->success_response($listing, $message);
    }

    /**
     * @param Request $request
     * @return JsonResponse
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
                        $application->job_post;

                        Notifications::PushUserNotification($post, $application, $participant, "JOB_CLOSED");
                        PushNotification::Notify("JOB_CLOSED", $application, null);
                    } catch (QueryException $e) {
                        Log::error("ERROR UPDATING VOLUNTEER HOURS FOR >>>>>>>>>> " . $participant->id . " >>>>>>>>> " . $e);
                        continue;
                    }
                }
                break;
            case "P2P":
            case "QUICK_JOB":
                $participant = User::where("id", $request->user_id)->first();
                if (!$participant) {
                    Log::debug("ERROR FETCHING USER DETAILS FOR USER ID >>>>>>>>>>> " . $request->user_id);
                }

                $application = JobApplication::where("user_id", $request->user_id)->where("post_id", $request->job_post_id)->first();
                if (!$application) {
                    Log::debug("ERROR FETCHING APPLICATION DETAILS FOR USER ID >>>>>>>>>>> " . $request->user_id . " AND POST ID >>>>> " . $request->job_post_id);
                    $post->status = "closed";
                    $post->closed_at = Carbon::now();
                    $post->update();
                    return $this->success_response($post, "Post has been closed successfully.");
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
                $ratingReview->rating = number_format(((float)$request->expertise_rating + (float)$request->work_ethic_rating + (float)$request->professionalism_rating + (float)$request->customer_service_rating) / 4, 2);
                $ratingReview->feedback_message = $request->feedback_message;
                try {
                    $ratingReview->save();
                } catch (QueryException $e) {
                    Log::error("ERROR SAVING RATING REVIEW >>>>>>>>>> " . $ratingReview . " >>>>>>>>> " . $e);
                }

                /**
                 * application with final amount
                 */
                $post->final_payment_amount = $request->final_payment_amount;
                $post->payment_channel = "cash"; // to be update when we start using in-app wallets
                $post->job_done_overall_rating = $ratingReview->rating;

                /**
                 * update user earnings
                 */
                $participant->total_earnings = (float)$participant->total_earnings + (float)$request->final_payment_amount;

                /**
                 * update vorker rating
                 */
                $user_review_rating = 0;
                if ($participant->rating_and_reviews->count() >= 1) {
                    $user_review_rating = number_format($participant->rating_and_reviews->sum("rating") / $participant->rating_and_reviews->count(), 2);
                }

                $participant->rating = $user_review_rating;
                try {
                    /**
                     * create notifications
                     */
                    $post->user;
                    $application->job_post;
                    $application->job_post->user;
                    $application->job_post->rating_and_reviews;
                    $post->rating_and_reviews;

                    /**
                     * selected vorker notification
                     */
                    Notifications::PushUserNotification($post, $application, $participant, "JOB_CLOSED");
                    // PushNotification::Notify("JOB_CLOSED", $application, null);

                    /**
                     * issuer notification
                     */
                    Notifications::PushUserNotification($post, $post, $post->user, "JOB_CLOSED");
                    // PushNotification::Notify("JOB_CLOSED", $post, null);


                    /**
                     * unselected vorker notification
                     */
                    $allJobApplications = $post->applications;
                    foreach ($allJobApplications as $jobApplication) {
                        if ((int) $jobApplication->user_id !== (int) $participant->id) {
                            Notifications::PushUserNotification($post, $application, $jobApplication->user, "JOB_CLOSED");
                            // PushNotification::Notify("JOB_CLOSED", $application, null);
                        }
                    }

                    $participant->update();
                } catch (QueryException $e) {
                    Log::error("ERROR UPDATING USER RATING >>>>>>>>>> " . $participant . " >>>>>>>>> " . $e);
                }
                break;
            case "FIXED_TERM_JOB":
                $participant = User::where("id", $request->user_id)->first();
                if (!$participant) {
                    Log::debug("ERROR FETCHING USER DETAILS FOR USER ID >>>>>>>>>>> " . $request->user_id);
                }

                $application = JobApplication::where("user_id", $request->user_id)->where("post_id", $request->job_post_id)->first();
                if (!$application) {
                    Log::debug("ERROR FETCHING APPLICATION DETAILS FOR USER ID >>>>>>>>>>> " . $request->user_id . " AND POST ID >>>>> " . $request->job_post_id);
                    $post->status = "closed";
                    $post->closed_at = Carbon::now();
                    $post->update();
                    return $this->success_response($post, "Post has been closed successfully.");
                }

                $post->final_start_date = date('Y-m-d', strtotime(str_replace('/', '-', $request->start_date)));
                $post->final_end_date = date('Y-m-d', strtotime(str_replace('/', '-', $request->end_date)));;
                $post->final_payment_amount = $request->monthly_payment;

                try {
                    /**
                     * create notification
                     */
                    $post->user;
                    $post->applications;
                    $application->job_post;
                    Notifications::PushUserNotification($post, $application, $participant, "JOB_CLOSED");
                    PushNotification::Notify("JOB_CLOSED", $application, null);

                    $participant->update();
                } catch (QueryException $e) {
                    Log::error("ERROR UPDATING USER RATING >>>>>>>>>> " . $participant . " >>>>>>>>> " . $e);
                }
                break;
            case "PERMANENT_JOB":
                $participant = User::where("id", $request->user_id)->first();
                if (!$participant) {
                    Log::debug("ERROR FETCHING USER DETAILS FOR USER ID >>>>>>>>>>> " . $request->user_id);
                }

                $application = JobApplication::where("user_id", $request->user_id)->where("post_id", $request->job_post_id)->first();
                if (!$application) {
                    Log::debug("ERROR FETCHING APPLICATION DETAILS FOR USER ID >>>>>>>>>>> " . $request->user_id . " AND POST ID >>>>> " . $request->job_post_id);
                    $post->status = "closed";
                    $post->closed_at = Carbon::now();
                    $post->update();
                    return $this->success_response($post, "Post has been closed successfully.");
                }

                $post->final_start_date = date('Y-m-d', strtotime(str_replace('/', '-', $request->start_date)));
                $post->final_payment_amount = $request->monthly_payment;

                try {
                    /**
                     * create notification
                     */
                    $post->user;
                    $post->applications;
                    $application->job_post;
                    Notifications::PushUserNotification($post, $application, $participant, "JOB_CLOSED");
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
            return $this->db_operation_error_response([]);
        }

        return $this->success_response($post, "Post has been closed successfully.");
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * filterng is going to be done by
     * distance
     * category
     * budget range
     */
    public function filter_jobs(Request $request)
    {
        $searchCategories = $request->categories;
        $user_location = auth()->user()->location_coords;
        if (!$user_location) {
            return $this->not_found_response([], "Could not retrieve user's current location");
        }

        $_user_interests = auth()->user()->skills;
        $user_interests = array();
        foreach ($_user_interests as $interest) {
            array_push($user_interests, $interest->skill->id);
        }

        $posts = null;
        /**
         * filter using search categories
         */
        if (count($searchCategories) > 1) {
            $_searchCategories = array();
            foreach ($searchCategories as $category) {
                array_push($_searchCategories, $category->id);
            }

            $posts = Post::where("user_id", "!=", auth()->id())->where("status", "active")->where("type", $request->type)->whereIn("category_id", $_searchCategories)->get();
        } else {
            /**
             * default search categories
             */
            $posts = Post::where("user_id", "!=", auth()->id())->where("status", "active")->where("type", $request->type)->whereIn("category_id", $user_interests)->get();
        }

        /**
         * filter using distance
         */
        $jobs_near_me = collect();
        foreach ($posts as $post) {
            $post_location_lat = explode(',', $post->coords)[0];
            $post_location_lng = explode(',', $post->coords)[1];

            $user_location_lat = explode(',', $user_location)[0];
            $user_location_lng = explode(',', $user_location)[1];


            $distance = $this->get_distance($user_location_lat, $user_location_lng, $post_location_lat, $post_location_lng, "K");
            $post["distance"] = number_format($distance, 2);

            if ($request->searchRadius && $request->searchRadius != "") {
                if ($distance <= $request->searchRadius) {
                    $jobs_near_me->push($post);
                }
            } else {
                if ($distance <= self::JOB_SEARCH_RADIUS) {
                    $jobs_near_me->push($post);
                }
            }
        }
        $posts = $jobs_near_me->sortBy("distance");
        return $this->success_response($posts, "Posts fetched successfully.");
    }
}
