<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Country as CountryConfig;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ONBOARDING_STATUS = 0;
    public const ACTIVE_STATUS = 0;
    public const INACTIVE_STATUS = 2;
    public const APP_USER_TYPE = 1;
    public const ADMIN_USER_TYPE = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function country() {
        return $this->belongsTo("App\Models\Country", "country_id");
    }

    public static function IsActive(User $user) {
        if ($user->status === self::ACTIVE_STATUS) {
            return true;
        }

        return false;
    }

    public static function IsOnboarding(User $user)
    {
        if ($user->status === self::ONBOARDING_STATUS) {
            return true;
        }

        return false;
    }

    public static function IsPasswordSet(User $user) {
        if ($user->password === "" || $user->password === null) {
            return false;
        }

        return true;
    }

    public static function IsPhoneNumberVerified(User $user) {
        if ($user->phone_number_verified_at === "" || $user->phone_number_verified_at === null) {
            return false;
        }

        return true;
    }

    public static function IsEmailVerified(User $user) {
        if ($user->email_verified_at === "" || $user->email_verified_at === null) {
            return false;
        }

        return true;
    }

    public static function GetUserStatus(User $user) {
        return $user->status;
    }

    public static function IsPhoneNumberVerificationRequired(User $user) {
        if (CountryConfig::QueryCountryConfig($user->country->name, "is_phone_number_default_verification_medium") == true) {
            return true;
        }

        return false;
    }

    public function posts()
    {
        return $this->hasMany("App\Models\Post", "user_id");
    }
}
