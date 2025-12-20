<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Country as CountryConfig;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->uuid)) {
                $user->uuid = (string) Str::uuid();
            }
        });
    }

    public const ONBOARDING_STATUS = 0;
    public const ACTIVE_STATUS = 1;
    public const INACTIVE_STATUS = 2;
    public const DELETED_STATUS = 3;
    public const APP_USER_TYPE = 1;
    public const ADMIN_USER_TYPE = 0;
    public const UNVERIFIED_STATUS = 0;
    public const VERIFIED_STATUS = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
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

    public function job_applications()
    {
        return $this->hasMany("App\Models\JobApplication", "user_id");
    }

    public function skills()
    {
        return $this->hasMany("App\Models\SkillUser", "user_id");
    }

    public function languages()
    {
        return $this->hasMany("App\Models\LanguageUser", "user_id");
    }

    public function educationHistory()
    {
        return $this->hasMany("App\Models\EducationHistory", "user_id");
    }

    public function trainings()
    {
        return $this->hasMany("App\Models\Training", "user_id");
    }

    public function savedPosts()
    {
        return $this->hasMany("App\Models\UserSavedPosts", "user_id");
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

    public function outsideVorkJobs()
    {
        return $this->hasMany("App\Models\OutsideVorkJob", "user_id");
    }

    public function certificationsAndTrainings()
    {
        return $this->hasMany("App\Models\CertificationAndTraining", "user_id");
    }

    public function rating_and_reviews()
    {
        return $this->hasMany("App\Models\RatingReview", "user_id");
    }
}
