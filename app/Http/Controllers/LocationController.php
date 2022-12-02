<?php

namespace App\Http\Controllers;

use App\Traits\Responses;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    use Responses;

    public function update_user_current_location(Request $request)
    {
        auth()->user()->location = $request->location;
        auth()->user()->update();
        return $this->success_response([], "Location updated successfully.");
    }
}
