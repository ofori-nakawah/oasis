<?php

namespace App\Http\Controllers\Mobile;

use App\Models\Language;
use App\Models\LanguageUser;
use App\Models\Skill;
use App\Models\SkillUser;
use App\Models\Post;
use App\Models\JobApplication;
use App\Models\User;
use App\Services\PushNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    use Responses;

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * update user's location
     */
    public function update_user_current_location(Request $request)
    {
        auth()->user()->location_name = $request->location_name;
        auth()->user()->location_coords = $request->location_coords;
        auth()->user()->update();
        auth()->user()->skills;
        auth()->user()->languages;

        $posts = Post::where("user_id", auth()->id())->where("status", "active")->first();
        $recentlyApplied = JobApplication::where("user_id", auth()->id())->where("status", "confirmed")->limit(3)->get();
        foreach ($recentlyApplied as $item) {
            $item->job_post;
        }
        auth()->user()->listings = $posts;
        auth()->user()->recentlyApplied = $recentlyApplied;
        $payload = [
            "location_coords" => auth()->user()->location_coords,
            "location_name" => auth()->user()->location_name,
            "user"=> auth()->user()
        ];
        return $this->success_response($payload, "Location updated successfully.");
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * update user skills
     */
    public function update_user_core_skills(Request $request)
    {
        if (count($request->skills) > 3) {
            return $this->general_error_response([], "Choose at most three skills or items you have interest in.");
        }

        foreach (auth()->user()->skills as $skill) {
            $skill->delete();
        }

        for ($i = 0; $i < count($request->skills); $i++) {
            $skill = Skill::where("name", $request->skills[$i])->first();
            if ($skill) {
                $skill_user = new SkillUser();
                $skill_user->user_id = auth()->id();
                $skill_user->skill_id = $skill->id;
                $skill_user->save();
            }

        }

        auth()->user()->is_core_skills_set = "1";
        auth()->user()->update();

        return $this->success_response([], "");
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update_user_languages(Request $request)
    {
        foreach (auth()->user()->languages as $language) {
            $language->delete();
        }

        for ($i = 0; $i < count($request->preferredLanguages); $i++) {
            $language = Language::where("name", $request->preferredLanguages[$i])->first();
            if ($language) {
                $language_user = new LanguageUser();
                $language_user->user_id = auth()->id();
                $language_user->language_id = $language->id;
                $language_user->save();
            }
        }

        auth()->user()->is_languages_set = "1";
        auth()->user()->update();

        return $this->success_response([], "");
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * setup user fcm token for push notifications
     */
    public function update_user_fcm_token(Request $request)
    {
        Log::debug("FCM TOKEN >>>>>>>>>>>>> " . $request->user_fcm_token);

        auth()->user()->fcm_token = $request->user_fcm_token;
        auth()->user()->update();

//        PushNotification::FireSingleUserPushNotification("title", "body", "SOME_EVENT", "some details", $request->user_fcm_token);

        return $this->success_response([], "FCM token updated successfully.");
    }

    /**
     * @param Request $request
     * get user unread notifications count to display as badge
     */
    public function get_user_unread_notifications_count(Request $request)
    {
        return $this->success_response(auth()->user()->unreadNotifications->count(), "Notification count fetched");
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * get all user notifications
     */
    public function get_user_notifications(Request $request)
    {
        $notifications = auth()->user()->notifications->map(function ($notification) {
            $notification["group_id"] = $notification->data["post"]["id"];
            $notification->update();
            $notification->createdAt = date('d-m-Y H:i:s', strtotime($notification->created_at));
            return $notification;
        })->unique("group_id");

        return $this->success_response($notifications, "Notifications fetched successfully.");
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * get single notification details
     * mark the notification as read
     */
    public function get_user_notification_details(Request $request)
    {
        $notifications = auth()->user()->notifications->where("group_id", $request->notification_group_id);

        /**
         * mark notification as read
         */
        if (count($notifications) > 0) {
            foreach ($notifications as $notification) {
                $notification->markAsRead();
                $notification->createdAt = date('d-m-Y H:i:s', strtotime($notification->created_at));
            }
        }

        return $this->success_response($notifications, "Notification details fetched successfully.");
    }

    /**
     * @param Request $request
     */
    public function get_user_kyc_status(Request $request)
    {
        $is_kyc_completed = 0;
        if (auth()->user()->is_core_skills_set === "0" || auth()->user()->is_languages_set === "0" || auth()->user()->location_name === null) {
            $is_kyc_completed = 0;
        } else {
            $is_kyc_completed = 1;
        }

        $kyc_status = array(
            "is_kyc_completed" => $is_kyc_completed,
            "is_core_skills_set" => (int) auth()->user()->is_core_skills_set,
            "is_languages_set" => (int) auth()->user()->is_languages_set,
            "is_location_set" => (!auth()->user()->location_name || auth()->user()->location_name == null) ? 0 : 1,
            "is_profile_picture_set" => (!auth()->user()->profile_picture || auth()->user()->profile_picture == null)  ? 0 : 1,
            "is_email_verified" => (!auth()->user()->email_verified_at || auth()->user()->email_verified_at == null)  ? 0 : 1,
            "location_coords" => auth()->user()->location_coords
        );

        return $this->success_response($kyc_status, "KYC status fetched successfully.");
    }

    /**
     * get uer profile details
     */
    public function get_user_full_profile()
    {
        /**
         * Analytics
         * get user number of jobs | average rating | volunteer hours | number of volunteer activities | total earnings
         */
        $core_skills = auth()->user()->skills;
        foreach ($core_skills as $skill) {
            $skill->skill;
        }

        $languages = auth()->user()->languages;
        foreach ($languages as $language) {
            $language->language;
        }

        $job_history = auth()->user()->job_applications->where("status", "confirmed");
        $average_rating = number_format(auth()->user()->rating, 2);
        $volunteer_hours = auth()->user()->volunteer_hours;
        $total_earnings = auth()->user()->total_earnings;

        $jobs_count = 0;
        $volunteer_count = 0;
        foreach ($job_history as $vork) {
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

        $user_profile = array(
            "number_of_jobs" => $jobs_count,
            "number_of_activities" => $volunteer_count,
            "average_rating" => $average_rating,
            "location" => auth()->user()->location_name,
            "location_coords" => auth()->user()->location_coords,
            "volunteer_hours" => $volunteer_hours,
            "total_earnings" => $total_earnings,
            "skills" => $core_skills,
            "languages" => $languages
        );

        return $this->success_response($user_profile, "Profile details fetched successfully.");
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_job_history(Request $request)
    {
        $job_history = auth()->user()->job_applications->where("status", "confirmed");
        $average_rating = number_format(auth()->user()->rating, 2);
        $total_earnings = auth()->user()->total_earnings;

        $jobs_count = 0;
        $volunteer_count = 0;
        $volunteer_hours = 0;
        foreach ($job_history as $vork) {
            if ($vork->job_post->status === "closed") {
                if ($vork->job_post->type === "VOLUNTEER") {
                    $vork->ref_id = "VO" . explode("-", $vork->id)[0];
                    $volunteer_hours += $vork->volunteer_hours;
                    $volunteer_count++;
                } else {
                    $vork->ref_id = "QJ" . explode("-", $vork->id)[0];
                    $jobs_count++;
                }

                $vork->period = $vork->job_post->closed_at->format('M Y');

                $vork->job_post;
                $vork->rating_and_reviews;
            }
        }

        $user_profile = array(
            "number_of_jobs" => $jobs_count,
            "number_of_volunteer_activities" => $volunteer_count,
            "job_history" => $job_history,
            "average_rating" => $average_rating,
            "volunteer_hours" => $volunteer_hours,
            "total_earnings" => $total_earnings
        );

        return $this->success_response($user_profile, "Profile details fetched successfully.");
    }

    /**
     * get uer vork profile details
     */
    public function get_user_vork_profile(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'uuid' => 'required',
        ]);

        if ($validation->fails()) {return $this->data_validation_error_response($validation->errors());}

        /**
         * get user
         */
        $user = User::where("id", $request->uuid)->first();
        if (!$user) {return $this->not_found_response([], "Error fetching user information. Kindly try again");}

        /**
         * Analytics
         * get user number of jobs | average rating | volunteer hours | number of volunteer activities | total earnings
         */
        $core_skills = $user->skills;
        foreach ($core_skills as $skill) {
            $skill->skill;
        }

        $languages = $user->languages;
        foreach ($languages as $language) {
            $language->language;
        }

        $job_history = $user->job_applications->where("status", "confirmed");
        $average_rating = number_format($user->rating, 2);
        $volunteer_hours = $user->volunteer_hours;
        $total_earnings = $user->total_earnings;

        $jobs_count = 0;
        $volunteer_count = 0;
        foreach ($job_history as $vork) {
            if ($vork->job_post->status === "closed") {
                if ($vork->job_post->type === "VOLUNTEER") {
                    $volunteer_count++;
                    $vork->ref_id = "VO" . explode("-", $vork->id)[0];

                } else {
                    $jobs_count++;
                    $vork->ref_id = "QJ" . explode("-", $vork->id)[0];
                }

                $vork->period = $vork->job_post->closed_at->format('M Y');

                $vork->job_post;
                $vork->rating_and_reviews;
            }
        }
        $educationHistories = $user->educationHistory;
        $certificationsAndTrainings = $user->certificationsAndTrainings;
        $userReviews = $user->rating_and_reviews;
        $reviews = array();
        foreach ($userReviews as $userReview) {
            $newReview = [
                "created_at" => $userReview->created_at,
                "message" => $userReview->feedback_message,
                "rating" => $userReview->rating,
                "reviewBy" => $userReview->post->user->name
            ];
            array_push($reviews, $newReview);
        }

        $user_profile = array(
            "number_of_jobs" => $jobs_count,
            "number_of_activities" => $volunteer_count,
            "average_rating" => $average_rating,
            "location" => $user->location_name,
            "location_coords" => $user->location_coords,
            "volunteer_hours" => $volunteer_hours,
            "total_earnings" => $total_earnings,
            "skills" => $core_skills,
            "languages" => $languages,
            "username" => $user->name,
            "work_history" => $job_history,
            "profile_picture" => $user->profile_picture,
            "educationHistory" => $educationHistories,
            "certificationsAndTrainings" => $certificationsAndTrainings,
            "workExperience" => $user->outsideVorkJobs,
            "reviews" => $reviews,
            "email" => $user->email,
            "phoneNumber" => $user->phone_number
    );

        return $this->success_response($user_profile, "Profile details fetched successfully.");
    }

    public function updateProfileInformation(Request $request)
    {
        if (!$request->module) {
            return back()->with("danger", "Invalid request");
        }
        $errors = new MessageBag();

        switch($request->module) {
            case "display-name":
                if (!$request->name) {
                    $errors->add("name", "The name field is required.");
                    return $this->data_validation_error_response($errors);
                }
                auth()->user()->name = $request->name;
                auth()->user()->update();

//                PushNotification::notify("title", "body", "PROFILE_UPDATE", "details", auth()->user()->fcm_token);
                break;
            case "profile-picture":
                if ($request->profile_picture && $request->profile_picture != "") {
                    //save image
                    $image = $request->profile_picture;
                    $name = auth()->user()->name . '_' . time() . '.png';
                    $destinationPath = public_path('/uploads/profile_pics/');

                    $image_parts = explode(";base64,", $image);
                    $image_base64 = base64_decode($image_parts[1]);
                    $file = $destinationPath . $name;
                    file_put_contents($file, $image_base64);

                    auth()->user()->profile_picture = URL::to('/public/uploads/profile_pics') . '/' . $name;
                    auth()->user()->update();

//                    PushNotification::notify("title", "body", "PROFILE_UPDATE", "details", auth()->user()->fcm_token);

                    return $this->success_response(["image" => auth()->user()->profile_picture], "Profile picture updated successfully.");
                }
                break;
            case "update-password":
                if (!Hash::check($request->old_password, auth()->user()->password)){
                    $errors->add("old_password", "The old password you entered does not match.");
                    return $this->data_validation_error_response($errors);
                }

                $validation = Validator::make($request->all(), [
                    'old_password' => 'required',
                    'password' => ['required', Password::min(6)->letters()->mixedCase()->uncompromised()]
                ]);

                if ($validation->fails()) {
                    return $this->data_validation_error_response($validation->errors());
                }

                auth()->user()->password = Hash::make($request->password);
                auth()->user()->update();

                return $this->success_response([], "Your password has been changed successfully.");
                break;
        }

        auth()->user()->skills;
        auth()->user()->languages;

        $posts = Post::where("user_id", auth()->id())->where("status", "active")->first();
        $recentlyApplied = JobApplication::where("user_id", auth()->id())->where("status", "confirmed")->limit(3)->get();
        foreach ($recentlyApplied as $item) {
            $item->job_post;
        }
        auth()->user()->listings = $posts;
        auth()->user()->recentlyApplied = $recentlyApplied;

        return $this->success_response(auth()->user(), "Profile updated successfully.");
    }


    public function deleteAccount(Request $request)
    {
        $errors = new MessageBag();
        if (!Hash::check($request->password, auth()->user()->password)){
            $errors->add("password", "Password confirmation failed. Kindly try again");
            return $this->data_validation_error_response($errors);
        }

        auth()->user()->reason_for_leaving = $request->reasonForLeaving;
        auth()->user()->deleted_at = Carbon::now();
        auth()->user()->status = User::DELETED_STATUS;
        auth()->user()->update();

        return $this->success_response([], "Account deleted successfully.");
    }

    public function isToolboxUser(Request $request)
    {
        return $this->success_response(["is_toolbox_user" => auth()->user()->is_toolbox_user], "successful request.");
    }
}
