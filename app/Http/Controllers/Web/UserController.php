<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\LanguageUser;
use App\Models\Skill;
use App\Models\SkillUser;
use App\Models\User;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;


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
        $notifications = auth()->user()->notifications->sortByDesc("created_at")->map(function ($notification) {
            if (array_key_exists("post", $notification->data)) {
                $notification["group_id"] = $notification->data["post"]["id"];
            } else {
                $notification["group_id"] = $notification->data["job"]["id"];
            }
            $notification->createdAt = date('d-m-Y H:i:s', strtotime($notification->created_at));
            $notification->update();
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
        if (!$notification_group_id) {
            return back()->with("danger", "Invalid request.");
        }

        /**
         * refresh notification and ensure new notifiations
         * have been added to existing notifications with the same
         * group_id
         */
        auth()->user()->notifications->sortByDesc("created_at")->map(function ($notification) {
            if (array_key_exists("post", $notification->data)) {
                $notification["group_id"] = $notification->data["post"]["id"];
            } else {
                $notification["group_id"] = $notification->data["job"]["id"];
            }
            $notification->createdAt = date('d-m-Y H:i:s', strtotime($notification->created_at));
            $notification->update();
            return $notification;
        })->unique("group_id");

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
                $notification->created_at = date('d-m-Y H:i:s', strtotime($notification->created_at));
                if (!$location_coordinates && array_key_exists("post", $notification->data)) {
                    $location_coordinates = $notification->data["post"]["coords"];
                }
            }
        }

        $notifications = auth()->user()->notifications->sortByDesc("created_at")->map(function ($notification) {
            return $notification;
        })->unique("group_id");


        return view("notifications.show", compact("notifications", "group_notifications", "location_coordinates"));
    }

    public function my_wallet()
    {
        $jobCount = 0;
        $job_history = auth()->user()->job_applications->where("status", "confirmed");
        foreach ($job_history as $vork) {
            $vork->hasTransaction = false;
            if ($vork->job_post->type !== "VOLUNTEER" && $vork->status === "closed") {
                $vork->ref_id = "QJ" . explode("-", $vork->id)[0];
                $vork->hasTransaction = true;
                $jobCount++;
            }

            $vork->job_post;
        }
        return view("wallet.index", compact("job_history", "jobCount"));
    }

    public function user_postings()
    {
        $posts = auth()->user()->posts->sortByDesc("created_at")->whereNull('deleted_at');
        return view("postings.index", compact("posts"));
    }

    public function user_profile($user_id)
    {
        if (!$user_id) {
            return back()->with("danger", "Invalid request");
        }

        $user = User::where("id", $user_id)->first();
        if (!$user) {
            return back()->with("danger", "Error fetching user information");
        }

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
            if ($vork->job_post->status === "closed") {
                if ($vork->job_post->type === "VOLUNTEER") {
                    $vork->ref_id = "VO" . explode("-", $vork->id)[0];
                    $volunteer_count++;
                } else {
                    $vork->ref_id = "QJ" . explode("-", $vork->id)[0];
                    $jobs_count++;
                }

                $vork->job_post;
                $vork->rating_and_reviews;
                $vork->period = $vork->job_post->closed_at->format('M Y');
            }
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
        $errors = new MessageBag();

        if ($request->skills_and_interest == null || count($request->skills_and_interest) <= 0) {
            $errors->add("skills_and_interest", "Choose at least one skill or an item you have interest in.");
            return back()->withErrors($errors)->with("danger", "Choose at least one skill or an item you have interest in.");
        }

        if (count($request->skills_and_interest) > 3) {
            $errors->add("skills_and_interest", "Choose at most three skills or items you have interest in.");
            return back()->withErrors($errors)->with("danger", "Choose at most three skills or items you have interest in.");
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

    public function set_location()
    {
        return view("profile.updateLocation");
    }

    public function setSkillsAndInterest()
    {
        $userSkills = array();
        $_userSkills = auth()->user()->skills;
        foreach ($_userSkills as $skill) {
            array_push($userSkills, $skill->skill->name);
        }
        $skills_and_interest = Skill::all()->sortBy('name');
        return view("profile.updateSkills", compact("skills_and_interest", "userSkills"));
    }

    public function setLanguages()
    {
        $userLanguages = array();
        $_userLanguages = auth()->user()->languages;
        foreach ($_userLanguages as $language) {
            array_push($userLanguages, $language->language->name);
        }
        $languages = Language::all()->sortBy('name');
        return view("profile.updateLanguages", compact("languages", "userLanguages"));
    }

    public function updatePassword()
    {
        return view("profile.updatePassword");
    }

    public function updateProfileInformation(Request $request, $module)
    {
        if (!$module) {
            return back()->with("danger", "Invalid request");
        }

        switch ($module) {
            case "display-name":
                auth()->user()->name = $request->name;
                auth()->user()->bio = $request->bio;
                auth()->user()->update();
                break;
            case "profile-picture":
                if ($request->profile_picture && $request->profile_picture != "") {
                    //save image
                    $image = $request->file('profile_picture');
                    $name = auth()->user()->name . '_' . time() . '.' . $image->getClientOriginalExtension();
                    $destinationPath = public_path('/uploads/profile_pics');
                    $image->move($destinationPath, $name);

                    auth()->user()->profile_picture = URL::to('/public/uploads/profile_pics') . '/' . $name;
                    auth()->user()->update();
                }
                break;
            case "update-password":
                $errors = new MessageBag();
                if (!Hash::check($request->old_password, auth()->user()->password)) {
                    $errors->add("old_password", "The old password you entered does not match.");
                    return back()->withErrors($errors)->with("danger", "Oops...something went wrong.");
                }

                $validation = Validator::make($request->all(), [
                    'old_password' => 'required',
                    'password_confirmation' => 'required|min:6',
                    'password' => ['required', Password::min(6)->letters()->mixedCase()->uncompromised()]
                ]);

                if ($validation->fails()) {
                    return back()->withErrors($validation->errors())->with("danger", "Oops. We encountered an issue updating your password. Kindly try again.");
                }

                auth()->user()->password = Hash::make($request->password);
                auth()->user()->update();

                return redirect()->route("home")->with("success", "Your password has been changed successfully");

                break;
        }

        return redirect()->route("user.profile", ["user_id" => auth()->id()])->with("success", "Your profile has been updated successfully");
    }

    public function resume($id)
    {
        $user = User::where("id", $id)->first();
        if (!$user) {
            return back()->with("danger", "Invalid request");
        }

        $competencies = array();
        foreach ($user->skills as $skill) {
            array_push($competencies, $skill->skill->name);
        }

        $name = '';
        $parts = explode(" ", $user->name);
        switch (count($parts)) {
            case 2:
                $name .= '<h2 style="font-family: Rockwell;">' . $parts[0] . '<div style="margin-top: -15px;font-family: Rockwell">' . $parts[1] . '</div></h2>';
                break;
            case 3:
                $name .= '<h2 style="font-family: Rockwell;">' . $parts[0] . " " . $parts[1] . ' <div style="margin-top: -15px;font-family: Rockwell">' . $parts[2] . '</div></h2>';
                break;
            case 4:
                $name .= '<h2 style="font-family: Rockwell;">' . $parts[0] . " " . $parts[1] . ' <div style="margin-top: -15px;font-family: Rockwell">' . $parts[2] . " " . $parts[3] . '</div></h2>';
                break;
            default:
                $name .= $user->name;
        }

        $email = $user->email;
        $phoneNumber = $user->phone_number;
        $location = $user->location_name;
        $outsideVorkJobs = $user->outsideVorkJobs;
        $educationHistories = $user->educationHistory;
        $certificationsAndTrainings = $user->certificationsAndTrainings;

        $data = [
            "name" => $name,
            "email" => $email,
            "phoneNumber" => $phoneNumber,
            "location" => $location,
            "competencies" => $competencies,
            "outsideVorkJobs" => $outsideVorkJobs,
            "educationHistories" => $educationHistories,
            "certificationsAndTrainings" => $certificationsAndTrainings
        ];

        $pdf = PDF::loadView('profile.resume', $data);
        return $pdf->download('resume.pdf');
        //        return view("profile.resume", compact("name", "email", "phoneNumber", "competencies", "location", "outsideVorkJobs", "educationHistories", "certificationsAndTrainings"));
    }

    public function searchVorkers(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'module' => 'required',
            'target' => 'required'
        ]);

        $target = $request->target;

        if ($validation->fails()) {
            return back()->withErrors($validation->errors())->with("danger", "Please ensure all required fields are completed.")->withInput();
        }


        switch ($request->module) {
            case "NAME_SEARCH":
                $users = User::where('name', 'LIKE', "%{$request->target}%")->get();
                foreach ($users as $user) {
                    $skills = $user->skills;
                    foreach ($skills as $skill) {
                        $skill->skill;
                    }
                }
                $users = $this->filterUsersBasedOnDistance($users);

                // Store search results in session
                session(['search_users' => $users, 'search_target' => $target]);

                // Redirect to search results page
                return redirect()->route('p2p.searchResults');
                break;
            case "CATEGORY_SEARCH":
                $category = Skill::where("name", $request->target)->first();
                if (!$category) {
                    return redirect()->back()->with("danger", "Something went wrong. Please try again");
                }

                $userSkills = SkillUser::where("skill_id", $category->id)->get();
                if (!$userSkills) {
                    return redirect()->back()->with("danger", "Something went wrong. Please try again");
                }

                $users = collect();
                foreach ($userSkills as $record) {
                    $skills = $record->user->skills;
                    foreach ($skills as $skill) {
                        $skill->skill;
                    }
                    $users->push($record->user);
                }
                $users = $this->filterUsersBasedOnDistance($users);

                // Store search results in session
                session(['search_users' => $users, 'search_target' => $target]);

                // Redirect to search results page
                return redirect()->route('p2p.searchResults');
                break;
        }

        return $this->success_response([]);
    }

    private function getUserLocationCoords($locationCoords)
    {
        if (empty($locationCoords)) {
            return null;
        }

        // Try JSON format first
        $jsonCoords = json_decode($locationCoords);
        if ($jsonCoords && isset($jsonCoords->latitude) && isset($jsonCoords->longitude)) {
            return [
                "latitude" => (float) $jsonCoords->latitude,
                "longitude" => (float) $jsonCoords->longitude
            ];
        }

        // Try comma-separated string format
        $coordsArray = array_map('trim', explode(',', $locationCoords));
        if (count($coordsArray) === 2 && is_numeric($coordsArray[0]) && is_numeric($coordsArray[1])) {
            return [
                "latitude" => (float) $coordsArray[0],
                "longitude" => (float) $coordsArray[1]
            ];
        }

        return null;
    }

    private function getDistance($lat1, $lon1, $lat2, $lon2, $unit)
    {
        $theta = (float)$lon1 - (float)$lon2;
        if ($theta == 0) {
            return 0;
        }
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
     * Display search results page
     *
     * @return \Illuminate\View\View
     */
    public function searchResults(Request $request)
    {
        $users = session('search_users', collect());
        $target = session('search_target', '');

        return view("work.p2p.searchResults", ['users' => $users, 'target' => $target]);
    }

    private function filterUsersBasedOnDistance($users)
    {
        $auth_user = Auth::user();
        $auth_user_location = $this->getUserLocationCoords($auth_user->location_coords);

        // If auth user doesn't have location, return users without distance filtering
        if (!$auth_user_location) {
            return null;
        }

        $auth_user_location_lat = $auth_user_location["latitude"];
        $auth_user_location_lng = $auth_user_location["longitude"];

        // Calculate distances and store them in an array
        $usersWithDistance = $users->map(function ($user) use ($auth_user_location_lat, $auth_user_location_lng) {
            // If user doesn't have location coords, return user without distance
            if (!$user->location_coords || !$auth_user_location_lat || !$auth_user_location_lng) {
                $user->distance = null;
                return $user;
            }

            $userLocations = $this->getUserLocationCoords($user->location_coords);
            if (!$userLocations) {
                $user->distance = null;
                return $user;
            }

            $user_location_lat = $userLocations["latitude"];
            $user_location_lng = $userLocations["longitude"];

            $distance = $this->getDistance(
                $auth_user_location_lat,
                $auth_user_location_lng,
                $user_location_lat,
                $user_location_lng,
                "K"
            );

            $user->distance = number_format($distance, 2);
            return $user;
        });

        // Filter out any null values that might have been returned
        $filteredUsers = $usersWithDistance->filter();

        // Sort users by distance in ascending order (nulls last)
        $sortedUsers = $filteredUsers->sortBy(function ($user) {
            return $user->distance === null ? PHP_FLOAT_MAX : $user->distance;
        });

        return $sortedUsers;
    }
}
