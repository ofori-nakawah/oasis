<?php

namespace App\Services;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PushNotification {
    public static function notify($title,$body,$event,$details,$user_fcm_tokens){
        Log::debug($user_fcm_tokens);
        Log::debug("checking ......");
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
            'to' => $user_fcm_tokens,
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

    /**
     * @return array
     * get users based on post category and distance
     */
    private static function GetFCMTokenOfOtherUsers()
    {
        $users = User::where('account_status', 1)->get();
        $fcm_tokens = array();
        foreach ($users as $user) {
            if ($user->fcm_token) {
                array_push($fcm_tokens, $user->fcm_token);
            }
        }
        return $fcm_tokens;
    }

    /**
     * @param $post
     * @return array
     * Push notification for selected applicants on Job changes
     * Job Closed | Job Cancelled |
     */
    private static function GetFCMTokenOfParticipants($post)
    {
        $selectedApplications = $post->applicacations->where("status", "confirmed");
        $fcm_tokens = array();
        foreach ($selectedApplications as $application) {
            if ($application->user->fcm_token) {
                array_push($fcm_tokens, $application->user->fcm_token);
            }
        }
        return $fcm_tokens;
    }
}
