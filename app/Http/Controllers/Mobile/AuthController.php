<?php

namespace App\Http\Controllers\Mobile;

use App\Models\OneTimePassword as OTP;
use App\Traits\Responses;
use App\Models\User;
use App\Models\Country as Country;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Rules\Password;

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
            if ($user->status != User::DELETED_STATUS) {
                Auth::login($user);
            } else {
                return $this->not_found_response([], "Invalid account credentials.");
            }
        } else {
            return $this->not_found_response([], "Invalid account credentials.");
        }

        return $this->success_response([
            "user" => auth()->user(),
            "country" => Country::GetCountry($user->country_id),
            "token" => auth()->user()->createToken('auth_token')->plainTextToken
        ]);
    }

    public function passwordResetPhoneNumberVerification(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'phoneNumber' => 'required',
        ]);

        if ($validation->fails()) {return $this->data_validation_error_response($validation->errors());}

        $user = User::where("email", $request->phoneNumber)->orWhere("phone_number", $request->phoneNumber)->first();
        if (!$user) {return $this->not_found_response([], "Oops. We could not find any records");}

        if (!OTP::Get($user)) {return $this->general_error_response([], "Oops. We couldn't send confirmation code. Try again");}

        return $this->success_response([
            "phoneNumber" => $user->phone_number
        ], "An SMS with confirmation code has been sent to your phone number");
    }

    public function verifyOTPForPasswordReset(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'phone_number' => 'required',
            'code' => 'required'
        ]);

        if ($validation->fails()) {return $this->data_validation_error_response($validation->errors());}

        $verification_status = OTP::Validate("phone_number", $request->input("phone_number"), $request->input("code"));

        $errors = new MessageBag();

        switch ($verification_status) {
            case OTP::INVALID_STATUS:
                $errors->add("code", "Code is invalid");
                return $this->data_validation_error_response($errors, "Code is invalid");
                break;
            case OTP::ALREADY_USED_STATUS:
                $errors->add("code", "Code has already been used.");
                return $this->data_validation_error_response($errors, "Code has already been used.");
                break;
            case OTP::EXPIRED_STATUS:
                $errors->add("code", "Code has expired. Go back and try again.");
                return $this->data_validation_error_response($errors, "Code has expired. Go back and try again.");
                break;
        }

        $user = User::where("phone_number", $request->input("phone_number"))->first();
        if (!$user) {return $this->not_found_response([], "Oops. Something went wrong. Error fetching data."); }

        $isLatePhoneNumberVerification = "no";
        if (!$user->phone_number_verified_at) {
            $user->phone_number_verified_at = Carbon::now();
            $user->status = User::ACTIVE_STATUS;
            $user->update();
            $isLatePhoneNumberVerification = "yes";
        }

        return $this->success_response(["isLatePhoneNumberVerification" => $isLatePhoneNumberVerification], "Code verification successful");
    }

    public function executePasswordReset(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'password' => ['required', Password::min(6)->letters()->mixedCase()->uncompromised()],
            'phoneNumber' => 'required'
        ]);

        if ($validation->fails()) {return $this->data_validation_error_response($validation->errors());}

        $user = User::where("phone_number", $request->input("phoneNumber"))->first();
        if (!$user) {return $this->not_found_response([], "Oops..Something went wrong. Error fetching data.");}

        $user->password = Hash::make($request->password);
        $user->update();

        return $this->success_response([], "Password reset successful");
    }
}
