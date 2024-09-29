<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class PushNotification
{
    public static function Notify($event, $details, $user_fcm_token)
    {
        $server_api_key = env("FIREBASE_SERVER_API_KEY");
        if (!$server_api_key) {
            Log::debug("MISSING FIREBASE_SERVER_API_KEY IN ENV FILE");
        }

        $headers = [
            'Authorization: key =' . $server_api_key,
            'Content-Type: application/json'
        ];

        $notification_data = null;
        $notification_payload = null;
        $notification_body = null;

        switch ($event) {
            case "APPLICATION_CONFIRMED" || "APPLICATION_DECLINED":
                $title = ($event === 'APPLICATION_CONFIRMED') ? 'Your application has been confirmed!' : 'Your application has been declined';
                $body = ($event === 'APPLICATION_CONFIRMED') ? 'The issuer for a job you applied to has confirmed you for the position. Go to your notifications to view more details.' : 'ok';

                $notification_data = [
                    'title' => $title,
                    'body' => $body,
                    'icon' => '',
                    'image' => '',
                    'sound' => 'default'
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
                break;
            case "JOB_CANCELLED || JOB_CLOSED":
                $notification_data = [
                    'title' => 'New delivery request!',
                    'body' => 'A new delivery request has been made. Dive into your Delivery Request Pool and accept delivery. Remember, the more deliveries you make the more you earn!',
                    'icon' => '',
                    'image' => '',
                    'sound' => 'default'
                ];

                $notification_payload = [
                    'title' => 'New delivery request!',
                    'details' => 'A new delivery request has been made. Dive into your Delivery Request Pool and accept delivery. Remember, the more deliveries you make the more you earn!',
                    'type' => 'new-open-delivery-request',
                    'is_open_delivery_request_pool' => "true",
                    'app_route' => "/delivery-request-pool/",
                ];

                $notification_body = [
                    'notification' => $notification_data,
                    'data' => $notification_payload,
                    'registration_ids' => self::GetFCMTokenOfParticipants($details),
                    'android' => [
                        'notification' => [
                            'sound' => 'default'
                        ]
                    ]
                ];
                break;
        }

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
        $selectedApplications = $post->applications->where("status", "confirmed");
        $fcm_tokens = array();
        foreach ($selectedApplications as $application) {
            if ($application->user->fcm_token) {
                array_push($fcm_tokens, $application->user->fcm_token);
            }
        }
        return $fcm_tokens;
    }

    public static function NotifyViaExpo($tokens, $notificationData, $event)
    {
        Log::debug("TOKEN >>>>>>> " . $tokens . " EVENT >>>>>> " . $event);

        $headers = [
            'Content-Type: application/json'
        ];

        $notificationPayload = [
            "to" => $tokens,
            "title" => $notificationData["title"],
            "body" => [
                "event" => $event,
                "notificationDetails" => $notificationData["content"]
            ]
        ];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://exp.host/--/api/v2/push/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($notificationPayload),
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        Log::debug("CUSTOMER PUSH NOTIFICATION RESPONSE VIA EXPO >>>>>>>> " . $response);
    }
}
