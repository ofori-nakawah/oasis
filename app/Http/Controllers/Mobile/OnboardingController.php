<?php

namespace App\Http\Controllers\Mobile;

use App\Models\User;
use App\Traits\Responses;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use App\Models\Country as CountryConfig;
use App\Models\OneTimePassword as OTP;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class OnboardingController extends Controller
{
    use Responses;

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * return user uuid needed to set password
     */
    public function register(Request $request){
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'email',
            'phone_number' => 'required',
            'country' => 'required',
            'password' => ['required', Password::min(6)->letters()->mixedCase()->uncompromised()]        ]);

        if ($validation->fails()) {return $this->data_validation_error_response($validation->errors());}
//        /**
//         * Let get country config and check if phone number is required
//         * Get the verification medium based on the users country
//         */
        $country = $request->input("country");
        $country = CountryConfig::GetCountry($country);
        if (!$country) {return $this->general_error_response([]);}

        $errors = new MessageBag();

        $isPhoneNumberTaken = User::where("phone_number", CountryConfig::GetIntPhoneNumber($country["name"], $request->input("phone_number")))->first();

        if ($isPhoneNumberTaken) {
            if ($isPhoneNumberTaken->status != User::DELETED_STATUS) {
                $errors->add("phone_number", "This phone number is already taken");
                return $this->data_validation_error_response($errors->getMessages());
            } else {
                $isPhoneNumberTaken->phone_number .= "-000";
                $isPhoneNumberTaken->update();
            }
        }

         $isEmailTaken = User::where("email", $request->input("email"))->first();

         if ($isEmailTaken && $isEmailTaken !== null && $isEmailTaken !== "") {
             if ($isEmailTaken->status != User::DELETED_STATUS) {
                 $errors->add("email", "This email is already taken");
                 return $this->data_validation_error_response($errors->getMessages());
             } else {
                 $isEmailTaken->email .= "-000";
                 $isEmailTaken->update();
             }
         }

        if (CountryConfig::QueryCountryConfig($country["name"], "is_phone_number_required_during_onboarding") && !$request->has("phone_number")) {
            $errors = new MessageBag();
            $errors->add("phone_number", "The phone number field is required");
            return $this->data_validation_error_response($errors->getMessages());
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
            return $this->db_operation_error_response([]);
        }

        //Generate & send otp to user
        if (!OTP::Get($user)) {
            return $this->success_response([], "Oops. We couldn't send confirmation. Try again");
        }

        //send code to user

        $data = [
            "uuid" => $user->uuid,
            "is_phone_number_default_verification_medium" => User::IsPhoneNumberVerificationRequired($user),
            "phone_number" => $user->phone_number
        ];
        $default_verification_medium = (User::IsPhoneNumberVerificationRequired($user)) ? 'phone number' : 'email';
        $message = "A verification code has been sent to your " . $default_verification_medium ;

        return $this->success_response($data, $message);
    }

    public function verify_phone_number(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'phone_number' => 'required',
            'code' => 'required'
        ]);

        if ($validation->fails()) {return $this->data_validation_error_response($validation->errors());}

        $verification_status = OTP::Validate("phone_number",  $request->input("phone_number"), $request->input("code"));

        switch ($verification_status) {
            case OTP::INVALID_STATUS:
                return $this->data_validation_error_response([], "Code is invalid.");
                break;
            case OTP::ALREADY_USED_STATUS:
                return $this->data_validation_error_response([], "Code has already been used.");
                break;
            case OTP::EXPIRED_STATUS:
                return $this->data_validation_error_response([], "Code has expired. Request for another code.");
                break;
        }

        $user = User::where("phone_number", $request->input("phone_number"))->first();
        if (!$user) {return $this->not_found_response([]);}

        $user->phone_number_verified_at = Carbon::now();
        $user->status = User::ACTIVE_STATUS;
        try {
            $user->update();
        } catch (QueryException $e) {
            Log::error("ERROR VALIDATION PHONE NUMBER FOR >>>>>>>>>> " . $user->id);
            return $this->db_operation_error_response([]);
        }

        return $this->success_response([], "Phone number verification successful");
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
