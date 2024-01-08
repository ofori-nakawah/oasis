<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\OutsideVorkJob;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
        $outsideVorkJob->reference = $request->reference;
        $outsideVorkJob->user_id = $userId;

        if ($request->is_ongoing === "on") {
            $outsideVorkJob->end_date = null;
        }

        try {
            $outsideVorkJob->save();
            return redirect()->route("user.profile", ["user_id" => $userId])->with("success", "Outside VORK job history has been added to your profile successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR SAVING USER >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return back()->with("danger", "Error saving outside VORK job history information. Please try again.");
        }
    }

    public function edit($id)
    {

    }

    public function update(Request $request, $id)
    {

    }

    public function verifyReference($id)
    {

    }

    public function getReferenceVerification($id)
    {

    }
}
