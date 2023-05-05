<?php


namespace App\Services;


use Illuminate\Support\Facades\Log;

class SMS
{
    public static function notify($phone,$message){
        $message = urlencode($message);
        $phone = self::format_msisdn($phone);

        Log::debug("SENDING SMS TO >>>>>>>> $phone");

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://smsbolt.com/sms/api?action=send-sms&api_key=dm9yay10ZWNoOkBDMGQzTjFuamE=&to=$phone&from=VORK&sms=$message",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "postman-token: 4134a3ec-0a36-18f3-f24a-7c91f27f78e9"
            ),
        ));

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        if ($error) {
            Log::debug("ERROR SENDING SMS >>>>>>>>>>> " . $error);
        } else {
            Log::info('SENT SMS RESPONSE LOG >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>' . $response);
            return $response;
        }
    }

    private static function format_msisdn($msisdn)
    {
        $msisdn = str_replace("+", "", $msisdn);
        $msisdn = str_replace(" ", "", $msisdn);
        if (self::startsWith($msisdn, '0')) {
            return "233" . substr($msisdn, 1);
        } else {
            if (!self::startsWith($msisdn, '233')) {
                return "233" . $msisdn;
            } else {
                return $msisdn;
            }
        }
    }

    private static function startsWith($haystack, $needle)
    {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }
}
