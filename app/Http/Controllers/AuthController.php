<?php

namespace App\Http\Controllers;

use App\Traits\Responses;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class AuthController extends Controller
{
    use Responses;

    private function generate_user_api_access_token(User $user) {
        if (!auth()->attempt([$user->email, $user->password])) {
            Log::error("ERROR GENERATING USER API ACCESS TOKEN FOR >>>>>>>>>>>>>>>>>>>> " . $user->email);
            return null;
        }
        return auth()->user()->createToken('auth_token', ['services:bills', 'services:airtime', 'services:data-bundle'])->plainTextToken;
    }

    public function set_password(Request $request) {
        $user = auth()->user();

        if ($this->generate_user_api_access_token($user) === null) {
            return $this->db_operation_error_response([], "Oops. An error occurred. Kindly try again later.");
        }
    }
}
