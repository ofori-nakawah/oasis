<?php

namespace App\Services;
use Illuminate\Support\Facades\Log;

class PushNotification {
    public static function notify($title,$body,$event,$details,$user_fcm_token){
        $server_api_key = env("FIREBASE_SERVER_API_KEY");
        if (!$server_api_key) {
            Log::debug("MISSING FIREBASE_SERVER_API_KEY IN ENV FILE");
        }

        $headers = [
            'Authorization: key =' . $server_api_key,
            'Content-Type: application/json'
        ];
        $notification_data = [
            'title' => $title,
            'body' => $body,
            'icon' => '',
            'image' => '',
            'event' => $event
        ];

        $notification_payload = [
            'event' => $event,
            'details' => $details,
            'title' => $title,
            'body' => $body
        ];

        $notification_body = [
            'notification' => $notification_data,
            'data' => $notification_payload,
            'to' => $user_fcm_token,
        ];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($notification_body),
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        Log::debug("CUSTOMER PUSH NOTIFICATION RESPONSE >>>>>>>> " . $response);
    }
}
