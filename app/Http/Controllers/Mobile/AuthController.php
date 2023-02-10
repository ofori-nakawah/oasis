<?php

namespace App\Http\Controllers\Mobile;

use App\Traits\Responses;
use App\Models\User;
use App\Models\Country as Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use Responses;

    public function login (Request  $request) {
        $validation = Validator::make($request->all(), [
            'email_phone_number' => 'required',
            'password' => 'required'
        ]);

        if ($validation->fails()) {return $this->data_validation_error_response($validation->errors());}

        $user = User::where("phone_number", $request->input("email_phone_number"))->orWhere("email", $request->input("email_phone_number"))->first();
        if (!$user) {return $this->not_found_response([], "Invalid account credentials.");}
        //fetch user tokens

//        create new token for user
//        if (!Auth::attempt([$user->email, $request->input("password")])) {
//            Log::error("ERROR GENERATING USER API ACCESS TOKEN FOR >>>>>>>>>>>>>>>>>>>> " . $user->email);
//            return $this->not_found_response([], "Invalid account credentials.");
//        }

        if (Hash::check($request->input("password"), $user->password)) {
            Auth::login($user);
        } else {
            return $this->not_found_response([], "Invalid account credentials.");
        }

        return $this->success_response([
            "user" => auth()->user(),
            "country" => Country::GetCountry($user->country_id),
            "token" => auth()->user()->createToken('auth_token')->plainTextToken
        ]);
    }

}
