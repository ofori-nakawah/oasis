<?php

namespace App\Http\Controllers\Web;

use App\Models\Country;
use App\Models\Country as CountryConfig;
use App\Models\User;
use App\Traits\Responses;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use App\Models\OneTimePassword as OTP;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class OnboardingController extends Controller
{
    public function registration_form() {
        $countries = Country::all();
        return view("auth.register", compact("countries"));
    }

    public function register(Request $request){
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'email|unique:users',
            'phone_number' => 'unique:users',
            'country' => 'required',
            'password_confirmation' => 'required|min:6',
            'password' => ['required', Password::min(6)->letters()->mixedCase()->uncompromised()],
            'captcha' => 'required|captcha'
        ]);

        if ($validation->fails()) {return back()->withErrors($validation->errors())->withInput();}

//        /**
//         * Let get country config and check if phone number is required
//         * Get the verification medium based on the users country
//         */
        $country = CountryConfig::GetCountry($request->input("country"));
        if (!$country) {return back()->with("danger", "Error fetching countries list");}

        $errors = new MessageBag();

        $isPhoneNumberTaken = User::where("phone_number", CountryConfig::GetIntPhoneNumber($country["name"], $request->input("phone_number")))->count();

        if ($isPhoneNumberTaken != 0) {
            $errors->add("phone_number", "This phone number is already taken");
            {return back()->withErrors($errors);}
        }


        if (CountryConfig::QueryCountryConfig($country["name"], "is_phone_number_required_during_onboarding") && !$request->has("phone_number")) {
            $errors->add("phone_number", "The phone number field is required");
            {return back()->withErrors($errors);}
        }

        $user = new User();
        $user->uuid = Str::uuid();
        $user->name = $request->input("name");
        $user->email = $request->input("email");
        $user->type = (!$request->input("type")) ? User::APP_USER_TYPE : $request->input("type");
        $user->password = Hash::make($request->input("password"));
        $user->status = User::ONBOARDING_STATUS;
        $user->is_verified = User::UNVERIFIED_STATUS;

        /**
         * Get international phone number
         */
        $international_phone_number = CountryConfig::GetIntPhoneNumber($country["name"], $request->input("phone_number"));
        $user->phone_number = ($international_phone_number === null) ? $request->input("phone_number") : $international_phone_number;

        /**
         * Get country id from the country selected by user
         */
        $user->country_id = $country["id"];

        try {
            $user->save();
        } catch (QueryException $e) {
            Log::error("ERROR SAVING USER >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return back()->with("danger", "Error saving user information. Please try again.");
        }

        //Generate & send otp to user
        if (!OTP::Generate($user)) {
            return back()->with("danger", "Oops. We couldn't send confirmation. Try again");
        }

        //send code to user

        return redirect()->route("onboarding.verify_phone_number", ["uuid" => $user->id]);
    }

    public function reloadCaptcha()
    {
        return response()->json(['captcha'=> captcha_img()]);
    }

    public function resend_otp(User $user)
    {
        if (!OTP::Generate($user)) {
            return back()->with("danger", "Oops. We couldn't send confirmation code. Try again");
        }

        return redirect()->route("onboarding.verify_phone_number", ["uuid" => $user->id]);
    }

    public function verify_onboarding_phone_number($uuid)
    {
        if (!$uuid) {return back()->with("danger", "Invalid request");}
        $user = User::where("id", $uuid)->first();
        if (!$user) {
            return back()->with("danger", "Error fetching account information");
        }

        return view("auth.verify-phone-number", compact("user"));
    }

    public function verify_phone_number(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'phone_number' => 'required',
            'code' => 'required'
        ]);

        if ($validation->fails()) {return back()->withErrors($validation->errors());}

        $verification_status = OTP::Validate("phone_number",  $request->input("phone_number"), $request->input("code"));

        $errors = new MessageBag();

        switch ($verification_status) {
            case OTP::INVALID_STATUS:
                $errors->add("code", "Code is invalid");
                return back()->withErrors($errors);
                break;
            case OTP::ALREADY_USED_STATUS:
                $errors->add("code", "Code has already been used.");
                return back()->withErrors($errors);
                break;
            case OTP::EXPIRED_STATUS:
                $errors->add("code", "Code has expired. Request for another code.");
                return back()->withErrors($errors);
                break;
        }

        $user = User::where("phone_number", $request->input("phone_number"))->first();
        if (!$user) {return back()->with("danger", "Error fetching user information");}

        $user->phone_number_verified_at = Carbon::now();
        $user->status = User::ACTIVE_STATUS;
        try {
            $user->update();
        } catch (QueryException $e) {
            Log::error("ERROR VALIDATION PHONE NUMBER FOR >>>>>>>>>> " . $user->id);
            return back()->with("danger", "Error processing request. Kindly try again.");
        }

        return redirect()->route("login")->with("success", "Phone number verification successful");
    }

//    public function set_password(Request $request) {
//        $validation = Validator::make($request->all(), [
//            'uuid' => 'required',
//            'password' => 'required|confirmed',
//            'password_confirmation' => 'required'
//        ]);
//
//        if ($validation->fails()) {return $this->data_validation_error_response($validation->errors());}
//
//        $user = User::where("uuid", $request->input("uuid"))->first();
//        if (!$user) {return $this->not_found_response([]);}
//
//        $user->password = Hash::make($request->input("password"));
//        try {
//            $user->update();
//        } catch (QueryException $e) {
//            Log::error("ERROR SETTING PASSWORD FOR >>>>>>>>>> " . $user->id);
//            return $this->db_operation_error_response([]);
//        }
//
//        return $this->success_response([], "Password set successful.");
//    }

}
