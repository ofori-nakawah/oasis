<?php

namespace App\Http\Controllers;

use App\Models\SkillUser;
use App\Services\PushNotification;
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
}
