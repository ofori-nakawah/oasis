<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CertificationAndTraining;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CertificationAndTrainingController extends Controller
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

        return view("profile.certificationAndTraining.create", compact("user"));
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'end_date' => 'required',
            'start_date' => 'required',
            'institution' => 'required',
            'programme' => 'required',
        ]);

        if ($validation->fails()) {return back()->withErrors($validation->errors())->withInput();}

        $userId = Auth::id();

        $certificateAndTraining = new CertificationAndTraining();
        $certificateAndTraining->programme = $request->programme;
        $certificateAndTraining->start_date = $request->start_date;
        $certificateAndTraining->end_date = $request->end_date;
        $certificateAndTraining->institution = $request->institution;
        $certificateAndTraining->user_id = $userId;

        if ($request->is_ongoing === "on") {
            $certificateAndTraining->end_date = null;
        }

        try {
            $certificateAndTraining->save();
            return redirect()->route("user.profile", ["user_id" => $userId])->with("success", "Certificate and training history has been added to your profile successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR SAVING CERTIFICATE AND TRAINING HISTORY >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return back()->with("danger", "Error saving education history information. Please try again.");
        }
    }

    public function edit($id)
    {
        if (!$id) {
            return redirect()->back()->with("danger", "Invalid request. Kindly try again.");
        }

        $certificationAndTraining = CertificationAndTraining::where("id", $id)->first();

        if (!$certificationAndTraining) {
            return redirect()->back()->with("danger", "Oop..something went wrong. Error retrieving information. Pleas try again.");
        }

        $user = Auth::user();

        return view("profile.certificationAndTraining.edit", compact("certificationAndTraining", "user"));
    }

    public function remove($id)
    {
        if (!$id) {
            return redirect()->back()->with("danger", "Invalid request. Kindly try again.");
        }

        $certificateAndTraining = CertificationAndTraining::where("id", $id)->first();

        if (!$certificateAndTraining) {
            return redirect()->back()->with("danger", "Oop..something went wrong. Error retrieving information. Pleas try again.");
        }

        try {
            $certificateAndTraining->delete();
            return redirect()->back()->with("success", "Certificate and training history has been removed successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR DELETING CERTIFICATE AND TRAINING HISTORY >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return back()->with("danger", "Error removing education history information. Please try again.");
        }

    }

    public function update(Request $request, $id)
    {
        if (!$id) {
            return redirect()->back()->with("danger", "Invalid request. Kindly try again.");
        }

        $certificateAndTraining = CertificationAndTraining::where("id", $id)->first();

        if (!$certificateAndTraining) {
            return redirect()->back()->with("danger", "Oop..something went wrong. Error retrieving information. Pleas try again.");
        }

        $certificateAndTraining->programme = $request->programme;
        $certificateAndTraining->start_date = $request->start_date;
        $certificateAndTraining->end_date = $request->end_date;
        $certificateAndTraining->institution = $request->institution;

        if ($request->is_ongoing === "on") {
            $certificateAndTraining->end_date = null;
        }

        try {
            $certificateAndTraining->update();
            return redirect()->route("user.profile", ["user_id" => Auth::id()])->with("success", "Certificate and training history has been updated successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR UPDATING CERTIFICATE AND TRAINING HISTORY >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return back()->with("danger", "Error updating education history information. Please try again.");
        }
    }
}
