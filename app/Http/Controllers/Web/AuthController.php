<?php

namespace App\Http\Controllers\Web;

use App\Models\OneTimePassword as OTP;
use App\Models\User;
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
    public function __construct()
    {
        $this->middleware('guest:web');
    }

    public function show_login_form()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email_or_phone_number' => 'required',
            'password' => 'required|min:4'
        ]);

        $user = User::where("email", $request->email_or_phone_number)->orWhere("phone_number", $request->email_or_phone_number)->first();
        if (!$user) {
            return redirect()->back()->withInput($request->only('email', 'remember'))->with("danger", "Error. Invalid login credentials.");
        }

        if (Hash::check($request->password, $user->password)) {
            if ($user->phone_number_verified_at === null) {
                return redirect()->route('onboarding.verify_phone_number', ['uuid' => $user->id])->with("info", "We noticed your phone number has not been verified. A confirmation code has been sent to your phone number.");
            }

            if ($user->status == User::DELETED_STATUS) {
                return redirect()->back()->withInput($request->only('email', 'remember'))->with("danger", "Error. Invalid login credentials.");
            }
        }

        if (Auth::attempt(['email' => $request->email_or_phone_number, 'password' => $request->password], $request->remember) || Auth::attempt(['phone_number' => $request->email_or_phone_number, 'password' => $request->password], true)) {
            /**
             * update users last seen and online status
             */
            Auth::user()->last_seen = Carbon::now();
            Auth::user()->is_online = true;
            Auth::user()->update();

            if (auth()->user()->location_name === "" || auth()->user()->location_name === null) {
                return view("onboarding.location");
            }

            return redirect()->intended(route('home'));
        }

        return redirect()->back()->withInput($request->only('email', 'remember'))->with("danger", "Error. Invalid login credentials.");
    }

    public function logout(Request $request)
    {
        /**
         * update online status
         */
        Auth::user()->is_online = false;
        Auth::user()->update();

        /**
         * logout user
         */
        Auth::guard('user')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function forgotPassword()
    {
        return view("auth.forgotPassword.email-phone-number");
    }

    public function verifyAccount(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email_phone_number' => 'required',
        ]);

        if ($validation->fails()) {
            return back()->withErrors($validation->errors())->with("danger", "Please ensure all required fields are completed.");
        }

        $user = User::where("email", $request->email_phone_number)->orWhere("phone_number", $request->email_phone_number)->first();
        if (!$user) {
            $validation->errors()->add("email_phone_number", "Invalid email or phone number entered");
            return redirect()->back()->withErrors($validation->errors())->withInput($request->only('email_phone_number',))->with("danger", "No account found for entered information.");
        }

        if (!OTP::Get($user)) {
            return back()->with("danger", "Oops. We couldn't send confirmation code. Try again");
        }

        return redirect()->route("auth.password_reset.verify_phone_number", ["uuid" => $user->id]);
    }

    public function resendCode(User $user)
    {
        if (!OTP::Get($user)) {
            return back()->with("danger", "Oops. We couldn't send confirmation code. Try again");
        }

        return redirect()->route("auth.password_reset.verify_phone_number", ["uuid" => $user->id]);
    }

    public function confirmPasswordReset($uuid)
    {
        if (!$uuid) {
            return back()->with("danger", "Invalid request");
        }
        $user = User::where("id", $uuid)->first();
        if (!$user) {
            return back()->with("danger", "Error fetching account information");
        }

        return view("auth.forgotPassword.otp", compact("user"));
    }

    public function confirmPasswordResetViaCode(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'phone_number' => 'required',
            'code' => 'required'
        ]);

        if ($validation->fails()) {
            return back()->withErrors($validation->errors());
        }

        $verification_status = OTP::Validate("phone_number", $request->input("phone_number"), $request->input("code"));

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
        if (!$user) {
            return back()->with("danger", "Error fetching user information");
        }

        return redirect()->route("auth.passwordReset.ready", ["user" => $user]);
    }

    public function readyPasswordReset(User $user)
    {
        if (!$user) {
            return back()->with("danger", "Error fetching account information");
        }

        return view("auth.forgotPassword.resetPassword", compact("user"));
    }

    public function executePasswordReset(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'password_confirmation' => 'required|min:6',
            'password' => ['required', 'confirmed', Password::min(6)->letters()->mixedCase()->uncompromised()]
        ]);

        if ($validation->fails()) {
            return back()->withErrors($validation->errors())->with("danger", "Oops. Ensure all required data are completed. Kindly try again.");
        }

        $user = User::where("id", $request->input("user_id"))->first();
        if (!$user) {
            return back()->with("danger", "Error fetching user information");
        }

        $user->password = Hash::make($request->password);
        $user->update();

        return redirect()->route("login")->with("success", "Your password has been reset successfully");
    }
}
