<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    public function get_user_unread_notifications_count(Request $request)
    {
        return $this->success_response(auth()->user()->unreadNotifications->count(), "Notification count fetched");
    }

    /**
     *
     * get all user notifications
     */
    public function get_user_notifications()
    {
        $notifications = auth()->user()->notifications->map(function ($notification) {
            $notification["group_id"] = $notification->data["post"]["id"];
            $notification->update();
            $notification->createdAt = date('d-m-Y H:i:s', strtotime($notification->created_at));
            return $notification;
        })->unique("group_id");

        return view("notifications.index", compact("notifications"));
    }

    /**
     * get single notification details
     * mark the notification as read
     */
    public function get_user_notification_details($notification_group_id)
    {
        if (!$notification_group_id) {return back()->with("danger", "Invalid request.");}

        $notifications = auth()->user()->notifications->where("group_id", $notification_group_id);

        /**
         * mark notification as read
         */
        if (count($notifications) > 0) {
            foreach ($notifications as $notification) {
                $notification->markAsRead();
                $notification->createdAt = date('d-m-Y H:i:s', strtotime($notification->created_at));
            }
        }

        return view("notifications.show", compact("notifications"));
    }
}
