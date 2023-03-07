<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LanguageUser;
use App\Models\Post;
use App\Models\Skill;
use App\Models\SkillUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

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

        $group_notifications = auth()->user()->notifications->where("group_id", $notification_group_id);
        if (!$group_notifications || count($group_notifications) <= 0) {
            return back()->with("danger", "Invalid request.");
        }

        $location_coordinates = null;

        /**
         * mark notification as read
         */
        if (count($group_notifications) > 0) {
            foreach ($group_notifications as $notification) {
                $notification->markAsRead();
                $notification->created_at= date('d-m-Y H:i:s', strtotime($notification->created_at));
                if (!$location_coordinates) {
                    $location_coordinates = $notification->data["post"]["coords"];
                }
            }
        }

        $notifications = auth()->user()->notifications->map(function ($notification) {
            return $notification;
        })->unique("group_id");

        return view("notifications.show", compact("notifications", "group_notifications", "location_coordinates"));
    }

    public function my_wallet()
    {
        return view("wallet.index");
    }

    public function user_postings()
    {
        $posts = auth()->user()->posts;
        return view("postings.index", compact("posts"));
    }

    public function user_profile($user_id)
    {
        if (!$user_id) {return back()->with("danger", "Invalid request");}

        $user = User::where("id", $user_id)->first();
        if (!$user) {return back()->with("danger", "Error fetching user information");}

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
                $vork->ref_id = "VO" . explode("-", $vork->id)[0];
                $volunteer_count++;
            } else {
                $vork->ref_id = "QJ" . explode("-", $vork->id)[0];
                $jobs_count++;
            }

            $vork->job_post;
            $vork->rating_and_reviews;
        }

            $number_of_jobs = $jobs_count;
            $number_of_activities = $volunteer_count;
            $location = $user->location_name;
            $location_coords = $user->location_coords;
            $skills = $core_skills;

        return view("profile.show", compact("number_of_activities", "number_of_jobs", "average_rating", "volunteer_hours", "total_earnings", "languages", "skills", "location", "user", "job_history"));
    }

    public function update_user_location(Request $request)
    {
        auth()->user()->location_name = $request->location_name;
        auth()->user()->location_coords = $request->location_coords;
        auth()->user()->update();
        return redirect()->route("home")->with("success", "Your location has been updated successfully");
    }

    public function update_user_core_skills(Request $request)
    {
        if ($request->skills_and_interest == null || count($request->skills_and_interest) <= 0) {
            $errors = new MessageBag();
            $errors->add("skills_and_interest", "Choose at least one skill or an item you have interest in.");
            return back()->withErrors($errors)->with("danger", "Choose at least one skill or an item you have interest in.");
        }

        foreach (auth()->user()->skills as $skill) {
            $skill->delete();
        }

        for ($i = 0; $i < count($request->skills_and_interest); $i++) {
            $skill_user = new SkillUser();
            $skill_user->user_id = auth()->id();
            $skill_user->skill_id = $request->skills_and_interest[$i];
            $skill_user->save();
        }

        auth()->user()->is_core_skills_set = "1";
        auth()->user()->update();

        return redirect()->route("home")->with("success", "Your skills and interests have been updated successfully");
    }

    public function update_user_languages(Request $request)
    {
        $request = $request->all();
        if (!array_key_exists('languages', $request) || $request["languages"] == null || count($request["languages"]) <= 0) {
            $errors = new MessageBag();
            $errors->add("languages", "Choose at least one language you are comfortable to conduct business in.");
            return back()->withErrors($errors)->with("danger", "Choose at least one language you are comfortable to conduct business in.");
        }

        foreach (auth()->user()->languages as $language) {
            $language->delete();
        }

        for ($i = 0; $i < count($request["languages"]); $i++) {
            $language_user = new LanguageUser();
            $language_user->user_id = auth()->id();
            $language_user->language_id = $request["languages"][$i];
            $language_user->save();
        }

        auth()->user()->is_languages_set = "1";
        auth()->user()->update();

        return redirect()->route("home")->with("success", "Your languages have been updated successfully");
    }
}
