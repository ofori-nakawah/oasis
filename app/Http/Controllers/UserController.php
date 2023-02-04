<?php

namespace App\Http\Controllers;

use App\Models\LanguageUser;
use App\Models\SkillUser;
use App\Models\User;
use App\Services\PushNotification;
use Illuminate\Support\Facades\Validator;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        $payload = [
            "location_coords" => auth()->user()->location_coords,
            "location_name" => auth()->user()->location_name,
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
        foreach (auth()->user()->skills as $skill) {
            $skill->delete();
        }

        for ($i = 0; $i < count($request->skills); $i++) {
            $skill_user = new SkillUser();
            $skill_user->user_id = auth()->id();
            $skill_user->skill_id = $request->skills[$i];
            $skill_user->save();
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

        for ($i = 0; $i < count($request->languages); $i++) {
            $language_user = new LanguageUser();
            $language_user->user_id = auth()->id();
            $language_user->language_id = $request->languages[$i];
            $language_user->save();
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
        $average_rating = auth()->user()->rating;
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
        $average_rating = auth()->user()->rating;

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
            "job_history" => $job_history,
            "average_rating" => $average_rating,
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
        $average_rating = $user->rating;
        $volunteer_hours = $user->volunteer_hours;
        $total_earnings = $user->total_earnings;

        $jobs_count = 0;
        $volunteer_count = 0;
        foreach ($job_history as $vork) {
            if ($vork->job_post->type === "VOLUNTEER") {
                $volunteer_count++;
                $vork->ref_id = "VO" . explode("-", $vork->id)[0];

            } else {
                $jobs_count++;
                $vork->ref_id = "QJ" . explode("-", $vork->id)[0];
            }

            $vork->job_post;
            $vork->rating_and_reviews;
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
        );

        return $this->success_response($user_profile, "Profile details fetched successfully.");
    }
}
