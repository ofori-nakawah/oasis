<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\ExternalJobReferenceApprovalRequestEmail;
use App\Models\OutsideVorkJob;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class OutsideVorkJobController extends Controller
{
    public function create($user)
    {
        if (!$user) {
            return back()->with("danger", "Invalid request");
        }

        $user = User::where("id", $user)->first();
        if (!$user) {
            return back()->with("danger", "Error fetching user information");
        }

        return view("profile.outsideVorkJobHistory.create", compact("user"));
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'role' => 'required',
            'employer' => 'required',
            'start_date' => 'required',
            'responsibilities' => 'required',
            'achievements' => 'required',
            'reference' => 'required',
        ]);

        if ($validation->fails()) {return back()->withErrors($validation->errors())->withInput();}

        $userId = Auth::id();

        $outsideVorkJob = new OutsideVorkJob();
        $outsideVorkJob->role = $request->role;
        $outsideVorkJob->employer = $request->employer;
        $outsideVorkJob->start_date = $request->start_date;
        $outsideVorkJob->end_date = $request->end_date;
        $outsideVorkJob->responsibilities = $request->responsibilities;
        $outsideVorkJob->achievements = $request->achievements;
        $outsideVorkJob->reference = json_encode([
            "name" => $request->reference
        ]);
        $outsideVorkJob->user_id = $userId;

        if ($request->is_ongoing === "on") {
            $outsideVorkJob->end_date = null;
        }

        try {
            $outsideVorkJob->save();
            return redirect()->route("user.profile", ["user_id" => $userId])->with("success", "Outside VORK job history has been added to your profile successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR SAVING OUTSIDE VORK JOB >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return back()->with("danger", "Error saving outside VORK job history information. Please try again.");
        }
    }

    public function edit($id)
    {
        if (!$id) {
            return redirect()->back()->with("danger", "Invalid request. Kindly try again.");
        }

        $outsideVorkJob = OutsideVorkJob::where("id", $id)->first();

        if (!$outsideVorkJob) {
            return redirect()->back()->with("danger", "Oop..something went wrong. Error retrieving information. Pleas try again.");
        }

        $user = Auth::user();

        return view("profile.outsideVorkJobHistory.edit", compact("outsideVorkJob", "user"));
    }

    public function remove($id)
    {
        if (!$id) {
            return redirect()->back()->with("danger", "Invalid request. Kindly try again.");
        }

        $outsideVorkJob = OutsideVorkJob::where("id", $id)->first();

        if (!$outsideVorkJob) {
            return redirect()->back()->with("danger", "Oop..something went wrong. Error retrieving information. Pleas try again.");
        }

        try {
            $outsideVorkJob->delete();
            return redirect()->back()->with("success", "Outside VORK job history has been removed successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR DELETING OUTSIDE VORK JOB >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return back()->with("danger", "Error removing outside VORK job history information. Please try again.");
        }

    }

    public function update(Request $request, $id)
    {
        if (!$id) {
            return redirect()->back()->with("danger", "Invalid request. Kindly try again.");
        }

        $outsideVorkJob = OutsideVorkJob::where("id", $id)->first();

        if (!$outsideVorkJob) {
            return redirect()->back()->with("danger", "Oop..something went wrong. Error retrieving information. Pleas try again.");
        }

        $outsideVorkJob->role = $request->role;
        $outsideVorkJob->employer = $request->employer;
        $outsideVorkJob->start_date = $request->start_date;
        $outsideVorkJob->end_date = $request->end_date;
        $outsideVorkJob->responsibilities = $request->responsibilities;
        $outsideVorkJob->achievements = $request->achievements;
        $outsideVorkJob->reference = json_encode([
            "name" => $request->reference
        ]);

        if ($request->is_ongoing === "on") {
            $outsideVorkJob->end_date = null;
        }

        try {
            $outsideVorkJob->update();
            return redirect()->route("user.profile", ["user_id" => Auth::id()])->with("success", "Outside VORK job history has been updated successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR UPDATING OUTSIDE VORK JOB >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return back()->with("danger", "Error updating outside VORK job history information. Please try again.");
        }
    }

    public function verifyReference($id)
    {
        if (!$id) {
            return redirect()->back()->with("danger", "Invalid request. Kindly try again.");
        }

        $outsideVorkJob = OutsideVorkJob::where("id", $id)->first();

        if (!$outsideVorkJob) {
            return redirect()->back()->with("danger", "Oop..something went wrong. Error retrieving information. Pleas try again.");
        }

        $user = Auth::user();

        return view("profile.outsideVorkJobHistory.verifyReference", compact("outsideVorkJob", "user", "outsideVorkJob"));
    }

    public function getReferenceVerification(Request $request, $id)
    {
        if (!$id) {
            return redirect()->back()->with("danger", "Invalid request. Kindly try again.");
        }

        $outsideVorkJob = OutsideVorkJob::where("id", $id)->first();

        if (!$outsideVorkJob) {
            return redirect()->back()->with("danger", "Oop..something went wrong. Error retrieving information. Pleas try again.");
        }

        $validation = Validator::make($request->all(), [
            'email' => 'required',
            'phone_number' => 'required',
        ]);

        if ($validation->fails()) {return back()->withErrors($validation->errors())->withInput();}

        $reference = json_decode($outsideVorkJob->reference);
        $reference->email = $request->email;
        $reference->phone_number = $request->phone_number;

        $outsideVorkJob->reference = json_encode($reference);
        $outsideVorkJob->reference_verification_sent_at = Carbon::now();

        /**
         * send email with approval and decline link
         */
        Mail::to($reference->email)->send(new ExternalJobReferenceApprovalRequestEmail($outsideVorkJob));

        try {
            $outsideVorkJob->update();
            return redirect()->route("user.profile", ["user_id" => Auth::id()])->with("success", "Reference approval request has been sent successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR REQUEST OUTSIDE VORK JOB REFERENCE APPROVAL >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return back()->with("danger", "Error requesting reference approval. Please try again.");
        }
    }
}
