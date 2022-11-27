<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\OneTimePassword as OTP;

class OTPController extends Controller
{
    use Responses;
    /**
     * @param Request $request
     * @return mixed
     */
    public function get_otp(Request $request) {
        $validation = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validation->fails()) {return $this->data_validation_error_response($validation->errors());}

        $user = User::where("id", $request->input("user_id"))->first();
        if (!$user) {return $this->not_found_response([]);}

        return $this->success_response([
            "code" =>  OTP::Get($user)
        ]);
    }
}
