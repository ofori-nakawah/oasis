<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:web');
    }

    public function show_login_form(){
        return view('auth.login');
    }

    public function login(Request $request){
        $this->validate($request, [
            'email_or_phone_number' => 'required',
            'password' => 'required|min:4'
        ]);

        if (Auth::attempt(['email' => $request->email_or_phone_number, 'password' => $request->password], $request->remember) || Auth::attempt(['phone_number' => $request->email_or_phone_number, 'password' => $request->password], $request->remember)){
            return redirect()->intended(route('home'));
        }

        return redirect()->back()->withInput($request->only('email', 'remember'))->with("danger","Error. Invalid login credentials.");
    }

    public function logout(Request $request)
    {
        Auth::guard('user')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
