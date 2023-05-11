<?php

namespace App\Models;

use App\Services\SMS as SMS;
use App\Traits\Uuids;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OneTimePassword extends Model
{
    use HasFactory, Uuids;

    public const VALID_STATUS = 0;
    public const ALREADY_USED_STATUS = 1;
    public const EXPIRED_STATUS = 2;
    public const INVALID_STATUS = 3;
    private const PERMITTED_CHARS = '0123456789';

    /**
     * @param User $user
     * @return false|mixed|string|null
     * Generate new code for user
     */
    public static function Generate(User $user) {
        $otp = new OneTimePassword();
        $otp->user_id = $user->id;
        $otp->email = $user->email;
        $otp->phone_number = $user->phone_number;
        $otp->code = substr(str_shuffle(self::PERMITTED_CHARS), 0, 6);
        $otp->status = self::VALID_STATUS;
        if (!$otp->save()) {
            return null;
        }

        return $otp->code;
    }

    /**
     * @param User $user
     * @return false|mixed|string|null
     * Fetch and resend code to user or generate new code for user based on the status
     */
    public static function Get(User $user) {
        //check if otp exists with asset info provided
        $otp = OneTimePassword::where("user_id", $user->id)->where("status", self::VALID_STATUS)->latest()->first();
        if (!$otp) {
            //generate a new code for user
            $code = self::Generate($user);
            $message = $code . " is your VORK verification code.";
            SMS::notify($user->phone_number, $message);
            return $code;
        }

        //check if it has not expired
        if (self::Validate("user_id", $user->id, $otp->code) != self::VALID_STATUS) {
            //generate a new code for user if it has expired
            $code = self::Generate($user);
            $message = $code . " is your VORK verification code.";
            SMS::notify($user->phone_number, $message);
            return $code;
        }

        //return existing code
        //send code to user
        $message = $otp->code . " is your VORK verification code.";
        SMS::notify($user->phone_number, $message);
        return $otp->code;
    }

    /**
     * @param $asset_type
     * @param $asset
     * @param $code
     * @return int
     * validate code based on asset information provided
     */
    public static function Validate($asset_type, $asset, $code) {
        $otp = OneTimePassword::where($asset_type, $asset)->where("code", $code)->latest()->first();
        //invalid
        if (!$otp) {return self::INVALID_STATUS;}

        $code_created_at = $otp->created_at;
        $validating_code_at = Carbon::now()->toDateTimeString();
        $diff = $code_created_at->diffInSeconds($validating_code_at);

        //expired
        //if its more than 300 seconds (5 minutes) it has expired
        if($diff > 300){
            //update status to expired
            $otp->status = self::EXPIRED_STATUS;
            $otp->update();
            return self::EXPIRED_STATUS;
        }

        //already used
        if ($otp->status === self::ALREADY_USED_STATUS) {return self::ALREADY_USED_STATUS;}

        //valid
        if ($otp->status === self::VALID_STATUS) {
            //update status to already used
            $otp->status = self::ALREADY_USED_STATUS;
            $otp->update();
            return self::VALID_STATUS;
        }

        return self::INVALID_STATUS;
    }
}
