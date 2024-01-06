<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Request;

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
