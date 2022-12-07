<?php

namespace App\Http\Controllers;

use App\Models\SkillUser;
use App\Traits\Responses;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use Responses;

    public function update_user_current_location(Request $request)
    {
        auth()->user()->location = $request->location;
        auth()->user()->update();
        return $this->success_response([], "Location updated successfully.");
    }

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
}
