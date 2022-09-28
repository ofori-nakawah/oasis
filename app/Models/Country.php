<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Propaganistas\LaravelPhone\PhoneNumber;

class Country extends Model
{
    use HasFactory, Uuids;

    public const ACTIVE_STATUS = 1;
    public const INACTIVE_STATUS = 0;

    public function users()
    {
        return $this->hasMany("App\Model\User");
    }

    public static function QueryCountryConfig($country, $query)
    {
        $country_config = Country::where("id", $country)->orWhere("name", $country)->first();
        if (!$country_config) {
            Log::error("COULD NOT FIND COUNTRY CONFIG FOR >>>>>>>>>>>>>>>>>>>> " . $country);
            return false;
        }

        switch ($query) {
            case "is_phone_number_required_during_onboarding":
                if ($country_config->is_phone_number_required_during_onboarding == true) {return true;}
                return false;
                break;
            case "is_phone_number_default_verification_medium":
                if ($country_config->is_phone_number_default_verification_medium == true) {return true;}
                return false;
                break;
        }
    }

    public static function GetCountry($country){
        $country_config = Country::where("id", $country)->orWhere("name", $country)->first();
        if (!$country_config) {
            Log::error("COULD NOT FIND COUNTRY CONFIG FOR >>>>>>>>>>>>>>>>>>>> " . $country);
            return null;
        }

        return [
            "id" => $country_config->id,
            "name" => $country_config->name
        ];
    }

    public static function GetIntPhoneNumber($country, $phone_number) {
        $country_config = Country::where("id", $country)->orWhere("name", $country)->first();
        if (!$country_config) {
            Log::error(debug_backtrace()[1]['function'] . " COULD NOT FIND COUNTRY CONFIG FOR >>>>>>>>>>>>>>>>>>>> " . $country);
            return null;
        }

        return (string) PhoneNumber::make($phone_number)->ofCountry($country_config->country_code);
    }
}
