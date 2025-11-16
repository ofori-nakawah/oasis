<?php

namespace App\Http\Controllers\Mobile;

use App\Models\Country as Country;
use App\Models\Language;
use App\Models\LanguageUser;
use App\Models\Skill;
use App\Models\SkillUser;
use App\Models\CertificationAndTraining;
use App\Models\Post;
use App\Models\EducationHistory;
use App\Models\OutsideVorkJob;
use App\Models\JobApplication;
use App\Models\User;
use App\Models\RatingReview;
use App\Services\PushNotification;
use App\Traits\Utils;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use App\Traits\Responses;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    use Responses;
    use Utils;

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
            "user" => auth()->user()
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
            if (array_key_exists("post", $notification->data)) {
                $notification["group_id"] = $notification->data["post"]["id"];
                $notification->update();
                $notification->createdAt = date('d-m-Y H:i:s', strtotime($notification->created_at));
                return $notification;
            }
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
            "is_core_skills_set" => (int)auth()->user()->is_core_skills_set,
            "is_languages_set" => (int)auth()->user()->is_languages_set,
            "is_location_set" => (!auth()->user()->location_name || auth()->user()->location_name == null) ? 0 : 1,
            "is_profile_picture_set" => (!auth()->user()->profile_picture || auth()->user()->profile_picture == null) ? 0 : 1,
            "is_email_verified" => (!auth()->user()->email_verified_at || auth()->user()->email_verified_at == null) ? 0 : 1,
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

        return $this->success_response([
            "number_of_jobs" => $jobs_count,
            "number_of_activities" => $volunteer_count,
            "average_rating" => $average_rating,
            "location" => auth()->user()->location_name,
            "location_coords" => auth()->user()->location_coords,
            "volunteer_hours" => $volunteer_hours,
            "total_earnings" => $total_earnings,
            "skills" => $core_skills,
            "languages" => $languages
        ], "Profile details fetched successfully.");
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

        if ($validation->fails()) {
            return $this->data_validation_error_response($validation->errors());
        }

        /**
         * get user
         */
        $user = User::where("id", $request->uuid)->first();
        if (!$user) {
            return $this->not_found_response([], "Error fetching user information. Kindly try again");
        }

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
        $userReviews = $user->rating_and_reviews()->latest()->take(2)->get();
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
        auth()->user()->skills;
        auth()->user()->languages;

        $posts = Post::where("user_id", auth()->id())->where("status", "active")->first();
        $recentlyApplied = JobApplication::where("user_id", auth()->id())->where("status", "confirmed")->limit(3)->get();
        foreach ($recentlyApplied as $item) {
            $item->job_post;
        }
        auth()->user()->listings = $posts;
        auth()->user()->recentlyApplied = $recentlyApplied;

        $averageRatingsForReviewCategories = [
            'expertise' => 5,
            'work_ethic' => 5,
            'professionalism' => 5,
            'customer_service' => 5
        ];

        $ratings = RatingReview::where('user_id', auth()->id())->get();
        if ($ratings->count() > 0) {
            $expertiseAvg = round($ratings->avg('expertise_rating'), 1);
            $workEthicAvg = round($ratings->avg('work_ethic_rating'), 1);
            $professionalismAvg = round($ratings->avg('professionalism_rating'), 1);
            $customerServiceAvg = round($ratings->avg('customer_service_rating'), 1);
            
            $averageRatingsForReviewCategories = [
                'expertise' => $expertiseAvg > 0 ? $expertiseAvg : 5,
                'work_ethic' => $workEthicAvg > 0 ? $workEthicAvg : 5,
                'professionalism' => $professionalismAvg > 0 ? $professionalismAvg : 5,
                'customer_service' => $customerServiceAvg > 0 ? $customerServiceAvg : 5
            ];
        }

        $volunteerHistory = [];
        $volunteerApplications = JobApplication::where('user_id', auth()->id())
            ->where('status', 'confirmed')
            ->whereNotNull('volunteer_hours')
            ->with(['job_post' => function ($query) {
                $query->where('type', 'VOLUNTEER')
                    ->where('status', 'closed');
            }])
            ->get();

        foreach ($volunteerApplications as $application) {
            if ($application->job_post && $application->job_post->type === 'VOLUNTEER') {
                $volunteerHistory[] = [
                    'date' => $application->job_post->date ?? $application->job_post->created_at->format('Y-m-d'),
                    'name' => $application->job_post->name,
                    'volunteer_hours_awarded' => $application->volunteer_hours
                ];
            }
        }

        $references = [];

        $outsideVorkJobs = OutsideVorkJob::where('user_id', auth()->id())
            ->whereNotNull('reference')
            ->get();
        foreach ($outsideVorkJobs as $job) {
            $referenceData = json_decode($job->reference, true);
            if (is_array($referenceData)) {
                $references[] = [
                    'name' => $referenceData['name'] ?? '',
                    'company' => $referenceData['company'] ?? $job->employer ?? '',
                    'email' => $referenceData['email'] ?? '',
                    'phone_number' => $referenceData['phone_number'] ?? ''
                ];
            }
        }

        $user_profile = array(
            "number_of_jobs" => $jobs_count,
            "number_of_activities" => $volunteer_count,
            "average_rating" => $average_rating,
            "average_ratings_by_category" => $averageRatingsForReviewCategories,
            "volunteer_history" => $volunteerHistory,
            "references" => $references,
            "location" => $user->location_name,
            "location_coords" => $user->location_coords,
            "volunteer_hours" => $volunteer_hours,
            "total_earnings" => $total_earnings,
            "skills" => $core_skills,
            "languages" => $languages,
            "username" => $user->name,
            "bio" => $user->bio,
            "work_history" => $job_history,
            "profile_picture" => $user->profile_picture,
            "educationHistory" => $educationHistories,
            "certificationsAndTrainings" => $certificationsAndTrainings,
            "workExperience" => $user->outsideVorkJobs,
            "reviews" => $reviews,
            "email" => $user->email,
            "phoneNumber" => $user->phone_number,
            "user" => auth()->user(),
            "country" => Country::GetCountry($user->country_id),
        );

        return $this->success_response([
            "user" => auth()->user(),
            "country" => Country::GetCountry($user->country_id),
            $user_profile
        ], "Profile details fetched successfully.");
    }

    public function updateProfileInformation(Request $request)
    {
        if (!$request->module) {
            return back()->with("danger", "Invalid request");
        }
        $errors = new MessageBag();

        switch ($request->module) {
            case "basic-information":
                if (!$request->name) {
                    $errors->add("name", "The name field is required.");
                    return $this->data_validation_error_response($errors);
                }
                auth()->user()->name = $request->name;
                auth()->user()->email = $request->email;
                auth()->user()->bio = $request->bio;
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

                /**
                 * new implementaiont
                 */
                if ($request->image) {
                    //save image
                    $image = $request->image;
                    $name = auth()->user()->name . '_' . time() . '.png';
                    $destinationPath = public_path('/uploads/profile_pics/');

                    $image_parts = explode(";base64,", $image);
                    $image_base64 = base64_decode($image_parts[1]);
                    $file = $destinationPath . $name;
                    file_put_contents($file, $image_base64);

                    if (file_exists(auth()->user()->profile_picture)) {
                        unlink(auth()->user()->profile_picture);
                    }

                    auth()->user()->profile_picture = URL::to('/public/uploads/profile_pics') . '/' . $name;
                    auth()->user()->update();

                    //                    PushNotification::notify("title", "body", "PROFILE_UPDATE", "details", auth()->user()->fcm_token);

                    return $this->success_response(["image" => auth()->user()->profile_picture], "Profile picture updated successfully.");
                } else {
                    Log::debug("No file");
                }
                break;
            case "update-password":
                $validation = Validator::make($request->all(), [
                    'old_password' => 'required',
                    'password' => ['required', Password::min(6)->letters()->mixedCase()->uncompromised()],
                    'password_confirmation' => 'required|same:password',
                ]);

                if (!Hash::check($request->old_password, auth()->user()->password)) {
                    $errors->add("old_password", "The old password you entered does not match.");
                    return $this->data_validation_error_response($errors);
                }

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
        if (!Hash::check($request->password, auth()->user()->password)) {
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

    public function addEducationHistory(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'startDate' => 'required',
            'institution' => 'required',
            'programme' => 'required',
        ]);

        if ($validation->fails()) {
            return $this->data_validation_error_response($validation->errors());
        }

        $userId = auth()->id();

        $educationHistory = new EducationHistory();
        $educationHistory->programme = $request->programme;
        $educationHistory->start_date = $this->convertDateStringToDateTime($request->startDate);
        $educationHistory->end_date = $this->convertDateStringToDateTime($request->endDate);
        $educationHistory->institution = $request->institution;
        $educationHistory->specialty = $request->specialty;
        $educationHistory->user_id = $userId;

        if ($request->is_ongoing === "on") {
            $educationHistory->endDate = null;
        }

        try {

            if ($educationHistory->save() && $request->image !== null) {
                $image = $request->image;
                $name = auth()->user()->name . '-education-' . uniqid() . '.png';
                $destinationPath = public_path('/uploads/education-history/');

                $image_parts = explode(";base64,", $image);
                $image_base64 = base64_decode($image_parts[1]);
                $file = $destinationPath . $name;
                file_put_contents($file, $image_base64);

                $educationHistory->certificate_link = URL::to('/uploads/education-history') . '/' . $name;
                if (!$educationHistory->update()) {
                    Log::error("ERROR SAVING IMAGE FOR EDUCATION HISTORY POST " . $educationHistory->id);
                }
            }
            return $this->success_response(auth()->user(), "Education history has been added to your profile successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR SAVING EDUCATION HISTORY >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return $this->error_response(auth()->user(), "Error saving education history information. Please try again.");
        }
    }

    public function updateEducationHistory(Request $request)
    {
        $educationHistory = EducationHistory::where("id", $request->id)->first();
        if (!$educationHistory) {
            return $this->not_found_response([], "Error fetching information. Kindly try again");
        }

        $educationHistory->programme = $request->programme;
        $educationHistory->start_date = $this->convertDateStringToDateTime($request->startDate);
        $educationHistory->end_date = $this->convertDateStringToDateTime($request->endDate);
        $educationHistory->institution = $request->institution;
        $educationHistory->specialty = $request->specialty;

        if ($request->isOngoing === "on" || $request->isOngoing === "true" || $request->isOngoing === true) {
            $educationHistory->end_date = null;
        }

        try {
            if ($educationHistory->update() && $request->image !== null) {
                $image = $request->image;
                $name = auth()->user()->name . '-education-' . uniqid() . '.png';
                $destinationPath = public_path('/uploads/education-history/');

                $image_parts = explode(";base64,", $image);
                $image_base64 = base64_decode($image_parts[1]);
                $file = $destinationPath . $name;
                file_put_contents($file, $image_base64);

                $educationHistory->certificate_link = URL::to('/uploads/education-history') . '/' . $name;
                if (!$educationHistory->update()) {
                    Log::error("ERROR UPDATING IMAGE FOR EDUCATION HISTORY POST " . $educationHistory->id);
                }
            }
            return $this->success_response(auth()->user(), "Education history updated successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR UPDATING EDUCATION HISTORY >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return $this->error_response(auth()->user(), "Error updating education history.");
        }
    }

    public function addCertificationAndTraining(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'trainingHours' => 'required',
            'startDate' => 'required',
            'institution' => 'required',
            'programme' => 'required',
        ]);

        if ($validation->fails()) {
            return $this->data_validation_error_response($validation->errors());
        }

        $userId = auth()->id();

        $certificateAndTraining = new CertificationAndTraining();
        $certificateAndTraining->programme = $request->programme;
        $certificateAndTraining->start_date = $this->convertDateStringToDateTime($request->startDate);
        $certificateAndTraining->end_date = $this->convertDateStringToDateTime($request->endDate);
        $certificateAndTraining->institution = $request->institution;
        $certificateAndTraining->training_hours = $request->trainingHours;
        $certificateAndTraining->user_id = $userId;

        if ($request->is_ongoing === "on") {
            $certificateAndTraining->end_date = null;
        }

        try {
            if ($certificateAndTraining->save() && $request->image !== null) {
                $image = $request->image;
                $name = auth()->user()->name . '-education-' . uniqid() . '.png';
                $destinationPath = public_path('/uploads/certifications/');

                $image_parts = explode(";base64,", $image);
                $image_base64 = base64_decode($image_parts[1]);
                $file = $destinationPath . $name;
                file_put_contents($file, $image_base64);

                $certificateAndTraining->certificate_link = URL::to('/uploads/certifications') . '/' . $name;
                if (!$certificateAndTraining->update()) {
                    Log::error("ERROR SAVING IMAGE FOR CERTIFICATION POST " . $certificateAndTraining->id);
                }
            }
            return $this->success_response(auth()->user(), "Certificate and training history has been added to your profile successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR SAVING CERTIFICATE AND TRAINING HISTORY >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return $this->error_response(auth()->user(), "Error saving education history information. Please try again.");
        }
    }

    public function updateCertificationsAndTrainings(Request $request)
    {

        $certificateAndTraining = CertificationAndTraining::where("id", $request->id)->first();
        if (!$certificateAndTraining) {
            return $this->not_found_response([], "Error fetching information. Kindly try again");
        }

        $certificateAndTraining->programme = $request->programme;
        $certificateAndTraining->start_date = $this->convertDateStringToDateTime($request->startDate);
        $certificateAndTraining->end_date = $this->convertDateStringToDateTime($request->endDate);
        $certificateAndTraining->institution = $request->institution;
        $certificateAndTraining->training_hours = $request->trainingHours;

        if ($request->isOngoing === "on" || $request->isOngoing === "true" || $request->isOngoing === true) {
            $certificateAndTraining->end_date = null;
        }

        try {
            if ($certificateAndTraining->update() && $request->image !== null) {
                $image = $request->image;
                $name = auth()->user()->name . '-education-' . uniqid() . '.png';
                $destinationPath = public_path('/uploads/certifications/');

                $image_parts = explode(";base64,", $image);
                $image_base64 = base64_decode($image_parts[1]);
                $file = $destinationPath . $name;
                file_put_contents($file, $image_base64);

                $certificateAndTraining->certificate_link = URL::to('/uploads/certifications') . '/' . $name;
                if (!$certificateAndTraining->update()) {
                    Log::error("ERROR SAVING IMAGE FOR EDUCATION HISTORY POST " . $certificateAndTraining->id);
                }
            }
            return $this->success_response(auth()->user(), "Certifications and training history updated successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR UPDATING EDUCATION HISTORY >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return $this->error_response(auth()->user(), "Error updating certifications and training history.");
        }
    }

    public function deleteCertificationsAndTrainings(Request $request)
    {
        if (!$request->id) {
            return $this->not_found_response([], "Error fetching information. Kindly try again");
        }

        $certificateAndTraining = CertificationAndTraining::where("id", $request->id)->first();
        if (!$certificateAndTraining) {
            return $this->not_found_response([], "Error fetching information. Kindly try again");
        }

        try {
            $certificateAndTraining->delete();

            if ($certificateAndTraining->certificate_link !== null) {
                $path = $certificateAndTraining->certificate_link;
                if (preg_match('/^https?:\/\//', $path)) {
                    // Extract the path starting from /uploads
                    $path = substr($path, strpos($path, '/uploads'));
                }
                if (file_exists(public_path($path))) {
                    unlink(public_path($path));
                }
            }

            return $this->success_response([], "Certifications and training history deleted successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR DELETING CERTIFICATION AND TRAINING >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return $this->error_response([], "Error deleting certifications and training history.");
        }
    }

    public function deleteEducationHistory(Request $request)
    {
        if (!$request->id) {
            return $this->not_found_response([], "Error fetching information. Kindly try again");
        }

        $educationHistory = EducationHistory::where("id", $request->id)->first();
        if (!$educationHistory) {
            return $this->not_found_response([], "Error fetching information. Kindly try again");
        }

        try {
            $educationHistory->delete();

            if ($educationHistory->certificate_link !== null) {
                $path = $educationHistory->certificate_link;
                if (preg_match('/^https?:\/\//', $path)) {
                    // Extract the path starting from /uploads
                    $path = substr($path, strpos($path, '/uploads'));
                }
                if (file_exists(public_path($path))) {
                    unlink(public_path($path));
                }
            }

            return $this->success_response([], "Education history deleted successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR DELETING EDUCATION HISTORY >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return $this->error_response([], "Error deleting education history.");
        }
    }

    public function deleteExternalJobHistory(Request $request)
    {
        if (!$request->id) {
            return $this->not_found_response([], "Error fetching information. Kindly try again");
        }

        $outsideVorkJob = OutsideVorkJob::where("id", $request->id)->first();
        if (!$outsideVorkJob) {
            return $this->not_found_response([], "Error fetching information. Kindly try again");
        }

        try {
            $outsideVorkJob->delete();
            return $this->success_response([], "Outside VORK job history deleted successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR DELETING OUTSIDE VORK JOB >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return $this->error_response([], "Error deleting outside VORK job history.");
        }
    }

    public function addExternalJobHistory(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'role' => 'required',
            'employer' => 'required',
            'startDate' => 'required',
            'responsibilities' => 'required',
            'achievements' => 'required',
            'reference' => 'required',
        ]);

        if ($validation->fails()) {
            return $this->data_validation_error_response($validation->errors());
        }

        $userId = auth()->id();

        $outsideVorkJob = new OutsideVorkJob();
        $outsideVorkJob->role = $request->role;
        $outsideVorkJob->employer = $request->employer;
        $outsideVorkJob->start_date = $this->convertDateStringToDateTime($request->startDate);
        $outsideVorkJob->end_date = $this->convertDateStringToDateTime($request->endDate);
        $outsideVorkJob->responsibilities = $request->responsibilities;
        $outsideVorkJob->achievements = $request->achievements;
        $outsideVorkJob->reference = json_encode([
            "name" => $request->reference
        ]);
        $outsideVorkJob->user_id = $userId;

        if ($request->isOngoing === "on" || $request->isOngoing === true || $request->isOngoing === "true" || $request->isOngoing === "1" || $request->isOngoing === 1 || $request->isOngoing === "yes") {
            $outsideVorkJob->end_date = null;
        }

        try {
            $outsideVorkJob->save();
            return $this->success_response(auth()->user(), "Outside VORK job history has been added to your profile successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR SAVING OUTSIDE VORK JOB >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return $this->error_response(auth()->user(), "Error adding outside VORK job history information. Please try again.");
        }
    }

    public function updateExternalJobHistory(Request $request)
    {
        $outsideVorkJob = OutsideVorkJob::where("id", $request->id)->first();
        if (!$outsideVorkJob) {
            return $this->not_found_response([], "Error fetching information. Kindly try again");
        }

        $outsideVorkJob->role = $request->role;
        $outsideVorkJob->employer = $request->employer;
        $outsideVorkJob->start_date = $this->convertDateStringToDateTime($request->startDate);
        $outsideVorkJob->end_date = $this->convertDateStringToDateTime($request->endDate);
        $outsideVorkJob->responsibilities = $request->responsibilities;
        $outsideVorkJob->achievements = $request->achievements;
        $outsideVorkJob->reference = json_encode([
            "name" => $request->reference
        ]);

        if ($request->isOngoing === "on" || $request->isOngoing === true || $request->isOngoing === "true" || $request->isOngoing === "1" || $request->isOngoing === 1 || $request->isOngoing === "yes") {
            $outsideVorkJob->end_date = null;
        }

        try {
            $outsideVorkJob->update();
            return $this->success_response(auth()->user(), "Outside VORK job history has been updated successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR UPDATING OUTSIDE VORK JOB >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return $this->error_response(auth()->user(), "Error updating outside VORK job history information. Please try again.");
        }
    }

    public function getAllUserData()
    {
        auth()->user()->skills;
        auth()->user()->languages;

        $posts = Post::where("user_id", auth()->id())->where("status", "active")->whereNull('deleted_at')->get();
        $recentlyApplied = JobApplication::where("user_id", auth()->id())->where("status", "confirmed")->limit(3)->get();
        foreach ($recentlyApplied as $item) {
            $item->job_post;
        }
        auth()->user()->listings = $posts;
        auth()->user()->recentlyApplied = $recentlyApplied;

        return [
            "user" => auth()->user(),
            "country" => Country::GetCountry(auth()->user()->country_id),
        ];
    }

    /**
     * V2 CONTROLLER
     */
    public function get_user_home_data()
    {
        $user = auth()->user();

        $latestActiveListing = $user->posts->where("status", "active")->whereNull('deleted_at')->sortByDesc("created_at")->first();
        $latestOngoingApplication = $user->job_applications->whereIn('status', array("applied", "confirmed"))->sortByDesc("created_at")->first();
        if ($latestOngoingApplication) {
            $latestOngoingApplication->job_post;
            if ($latestOngoingApplication->job_post) {
                $latestOngoingApplication->job_post->user;
            }
        }

        return $this->success_response([
            "user" => [
                "id" => $user->id,
                "name" => $user->name,
                "profile_picture" => $user->profile_picture
            ],
            "latest_active_listing" => $latestActiveListing,
            "latest_ongoing_application" => $latestOngoingApplication,
            "kyc_status" => [
                "is_skills_and_interests_set" => count($user->skills) > 0 ? 1 : 0,
                "is_languages_set" => count($user->languages) > 0 ? 1 : 0,
                "is_location_set" => $user->location_coords ? 1 : 0,
                "is_profile_picture_set" => $user->profile_picture ? 1 : 0
            ]
        ], "");
    }

    public function search_vorkers(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'module' => 'required',
            'target' => 'required'
        ]);

        if ($validation->fails()) {
            return $this->data_validation_error_response($validation->errors());
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
                return $this->success_response($this->filterUsersBasedOnDistance($users), "Search successful");
                break;
            case "CATEGORY_SEARCH":
                $category = Skill::where("name", $request->target)->first();
                if (!$category) {
                    return $this->not_found_response([], "Error fetching information. Kindly try again");
                }

                $userSkills = SkillUser::where("skill_id", $category->id)->get();
                if (!$userSkills) {
                    return $this->success_response([]);
                }

                $users = collect();
                foreach ($userSkills as $record) {
                    $skills = $record->user->skills;
                    foreach ($skills as $skill) {
                        $skill->skill;
                    }
                    $users->push($record->user);
                }

                return $this->success_response($this->filterUsersBasedOnDistance($users), "Search successful");
                break;
        }

        return $this->success_response([]);
    }

    /**
     * updates user expo push token
     */
    public function updateUserExpoPushToken(Request $request)
    {
        Log::debug("REQUEST FOR TOKEN UPDATE >>>>>>>>> " . $request->token);

        $validation = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validation->fails()) {
            return $this->data_validation_error_response($validation->errors());
        }

        if (!str_contains($request->token, "Error:")) {
            auth()->user()->expo_push_token = $request->token;
            auth()->user()->update();
        }

        return $this->success_response([]);
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

    private function filterUsersBasedOnDistance($users)
    {
        $auth_user = Auth::user();
        $auth_user_location = $this->getUserLocationCoords($auth_user->location_coords);
        $auth_user_location_lat = $auth_user_location["latitude"];
        $auth_user_location_lng = $auth_user_location["longitude"];

        // Calculate distances and store them in an array
        $usersWithDistance = $users->map(function ($user) use ($auth_user_location_lat, $auth_user_location_lng) {

            if (!$user->location_coords || !$auth_user_location_lat || !$auth_user_location_lng) {
                return;
            }

            $userLocations = $this->getUserLocationCoords($user->location_coords);
            if (!$userLocations) {
                return;
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

        // Sort users by distance in ascending order
        $sortedUsers = $usersWithDistance->sortBy('distance');

        return $sortedUsers;
    }


    public function getResumeData($id, $forPdf = false)
    {
        $user = User::where("id", $id)->first();
        if (!$user) {
            return back()->with("danger", "Invalid request");
        }

        $competencies = array();
        foreach ($user->skills as $skill) {
            array_push($competencies, $skill->skill->name);
        }

        $name = $user->name;
        $parts = explode(" ", $user->name);
        $nameHtml = '';
        $nameText = $user->name;

        switch (count($parts)) {
            case 2:
                $nameHtml = '<h2 style="font-family: Rockwell;">' . $parts[0] . '<div style="margin-top: -15px;font-family: Rockwell">' . $parts[1] . '</div></h2>';
                break;
            case 3:
                $nameHtml = '<h2 style="font-family: Rockwell;">' . $parts[0] . " " . $parts[1] . ' <div style="margin-top: -15px;font-family: Rockwell">' . $parts[2] . '</div></h2>';
                break;
            case 4:
                $nameHtml = '<h2 style="font-family: Rockwell;">' . $parts[0] . " " . $parts[1] . ' <div style="margin-top: -15px;font-family: Rockwell">' . $parts[2] . " " . $parts[3] . '</div></h2>';
                break;
            default:
                $nameHtml = $user->name;
        }

        $averageRatingsForReviewCategories = [
            'expertise' => 5,
            'work_ethic' => 5,
            'professionalism' => 5,
            'customer_service' => 5
        ];

        $ratings = RatingReview::where('user_id', $user->id)->get();
        if ($ratings->count() > 0) {
            $expertiseAvg = round($ratings->avg('expertise_rating'), 1);
            $workEthicAvg = round($ratings->avg('work_ethic_rating'), 1);
            $professionalismAvg = round($ratings->avg('professionalism_rating'), 1);
            $customerServiceAvg = round($ratings->avg('customer_service_rating'), 1);
            
            $averageRatingsForReviewCategories = [
                'expertise' => $expertiseAvg > 0 ? $expertiseAvg : 5,
                'work_ethic' => $workEthicAvg > 0 ? $workEthicAvg : 5,
                'professionalism' => $professionalismAvg > 0 ? $professionalismAvg : 5,
                'customer_service' => $customerServiceAvg > 0 ? $customerServiceAvg : 5
            ];
        }

        $volunteerHistory = [];
        $volunteerApplications = JobApplication::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->whereNotNull('volunteer_hours')
            ->with(['job_post' => function ($query) {
                $query->where('type', 'VOLUNTEER')
                    ->where('status', 'closed');
            }])
            ->get();

        foreach ($volunteerApplications as $application) {
            if ($application->job_post && $application->job_post->type === 'VOLUNTEER') {
                $volunteerHistory[] = [
                    'name' => $application->job_post->name,
                    'date' => $application->job_post->date ?? $application->job_post->created_at->format('Y-m-d'),
                    'volunteer_hours_awarded' => $application->volunteer_hours,
                    'description' => $application->job_post->description ?? 'Volunteer work'
                ];
            }
        }

        $email = $user->email;
        $phoneNumber = $user->phone_number;
        $location = $user->location_name;
        $outsideVorkJobs = $user->outsideVorkJobs;
        $educationHistories = $user->educationHistory;
        $certificationsAndTrainings = $user->certificationsAndTrainings;
        $languages = $user->languages;

        // Initialize references array
        $references = [];
        $otherSkills = [];

        // Get references from outside jobs
        $externalJobs = OutsideVorkJob::where('user_id', $user->id)
            ->whereNotNull('reference')
            ->get();

        foreach ($externalJobs as $job) {
            $referenceData = json_decode($job->reference, true);
            if (is_array($referenceData)) {
                $references[] = [
                    'name' => $referenceData['name'] ?? '',
                    'company' => $referenceData['company'] ?? $job->employer ?? '',
                    'email' => $referenceData['email'] ?? '',
                    'phone_number' => $referenceData['phone_number'] ?? ''
                ];
            }
        }

        foreach($languages as $language) {
            $otherSkills[] = [
                'name' => $language->language->name,
            ];
        }

        $otherSkillsString = implode(', ', array_column($otherSkills, 'name'));

        $data = [
            "name" => $name,
            "bio" => $user->bio,
            "nameString" => $user->name,
            "email" => $email,
            "phoneNumber" => $phoneNumber,
            "location" => $location,
            "competencies" => $competencies,
            "outsideVorkJobs" => $outsideVorkJobs,
            "educationHistories" => $educationHistories,
            "certificationsAndTrainings" => $certificationsAndTrainings,
            "references" => $references,
            "volunteerHistory" => $volunteerHistory,
            "ratings" => $averageRatingsForReviewCategories,
            "userId" => $user->id,
            "otherSkills" => $otherSkillsString,
        ];
        return $data;
    }

    public function downloadUserResume(Request $request) {

        $validation = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        $userId = $request->id;

        if ($validation->fails()) {
            return $this->data_validation_error_response($validation->errors());
        }

        $data = $this->getResumeData($userId);

        // Add user object to data for the PDF view with the exact property names expected by the view
        $pdfData = array_merge($data, [
            'user' => (object)[
                'name' => $data['name'],
                'bio' => $data['bio'] ?? null,
                'email' => $data['email'] ?? null,
                'phone' => $data['phoneNumber'] ?? null, // Changed from phone_number to phone
                'location_name' => $data['location'] ?? null, // Changed from location to location_name
                'competencies' => $data['competencies'] ?? [],
                'outsideVorkJobs' => $data['outsideVorkJobs'] ?? [],
                'educationHistories' => $data['educationHistories'] ?? [],
                'certificationsAndTrainings' => $data['certificationsAndTrainings'] ?? [],
                'references' => $data['references'] ?? [],
                'volunteerHistory' => $data['volunteerHistory'] ?? [],
                'ratings' => $data['ratings'] ?? [],
                'userId' => $data['userId'] ?? null,
                'otherSkills' => $data['otherSkills'] ?? null,
            ]
        ]);

        $pdf = PDF::loadView('profile.resume-template', ["data" => $pdfData]);

        $pdf->getDomPDF()->set_option('defaultFont', 'Rockwell');
        $pdf->getDomPDF()->set_option('fontDir', base_path('assets/assets/fonts/'));
        $pdf->getDomPDF()->set_option('fontCache', base_path('assets/assets/fonts/'));
        $pdf->getDomPDF()->set_option('isRemoteEnabled', true);

        // Set the filename
        $filename = strtolower(str_replace(' ', '_', $data['name'])) . '_resume.pdf';

        // Save the PDF to the storage
        $storagePath = 'public/resumes/' . $filename;
        Storage::put($storagePath, $pdf->output());

        // Generate the public URL
        $publicUrl = "public" . Storage::url('resumes/' . $filename);
        $fullUrl = url($publicUrl);

        // Return the public URL
       return $this->success_response([
            'url' => $fullUrl,
            'filename' => $filename
        ]);
    }
}
