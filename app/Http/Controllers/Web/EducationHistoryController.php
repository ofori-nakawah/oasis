<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\EducationHistory;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class EducationHistoryController extends Controller
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

        return view("profile.educationHistory.create", compact("user"));
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

        $educationHistory = new EducationHistory();
        $educationHistory->programme = $request->programme;
        $educationHistory->start_date = $request->start_date;
        $educationHistory->end_date = $request->end_date;
        $educationHistory->institution = $request->institution;
        $educationHistory->user_id = $userId;

        if ($request->is_ongoing === "on") {
            $educationHistory->end_date = null;
        }

        try {
            $educationHistory->save();
            return redirect()->route("user.profile", ["user_id" => $userId])->with("success", "Education history has been added to your profile successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR SAVING EDUCATION HISTORY >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return back()->with("danger", "Error saving education history information. Please try again.");
        }
    }

    public function edit($id)
    {
        if (!$id) {
            return redirect()->back()->with("danger", "Invalid request. Kindly try again.");
        }

        $educationHistory = EducationHistory::where("id", $id)->first();

        if (!$educationHistory) {
            return redirect()->back()->with("danger", "Oop..something went wrong. Error retrieving information. Pleas try again.");
        }

        $user = Auth::user();

        return view("profile.educationHistory.edit", compact("educationHistory", "user"));
    }

    public function remove($id)
    {
        if (!$id) {
            return redirect()->back()->with("danger", "Invalid request. Kindly try again.");
        }

        $educationHistory = EducationHistory::where("id", $id)->first();

        if (!$educationHistory) {
            return redirect()->back()->with("danger", "Oop..something went wrong. Error retrieving information. Pleas try again.");
        }

        try {
            $educationHistory->delete();
            return redirect()->back()->with("success", "Education history has been removed successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR DELETING EDUCATION HISTORY >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return back()->with("danger", "Error removing education history information. Please try again.");
        }

    }

    public function update(Request $request, $id)
    {
        if (!$id) {
            return redirect()->back()->with("danger", "Invalid request. Kindly try again.");
        }

        $educationHistory = EducationHistory::where("id", $id)->first();

        if (!$educationHistory) {
            return redirect()->back()->with("danger", "Oop..something went wrong. Error retrieving information. Pleas try again.");
        }

        $educationHistory->programme = $request->programme;
        $educationHistory->start_date = $request->start_date;
        $educationHistory->end_date = $request->end_date;
        $educationHistory->institution = $request->institution;

        if ($request->is_ongoing === "on") {
            $educationHistory->end_date = null;
        }

        try {
            $educationHistory->update();
            return redirect()->route("user.profile", ["user_id" => Auth::id()])->with("success", "Education history has been updated successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR UPDATING EDUCATION HISTORY >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return back()->with("danger", "Error updating education history information. Please try again.");
        }
    }
}
