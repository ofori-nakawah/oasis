<?php

namespace App\Helpers;
use App\Notifications\PostActivityNotification;

class Notifications {
    /**
     *setup notification payload
     */
    public static function PushUserNotification($post, $application, $user, $event)
    {
        $message = "";
        $ref_id = "";
        $status = "";
        $category = "";

        switch ($post->type) {
            case "VOLUNTEER":
                break;
            case "QUICK_JOB":
                /**
                 * successful job application notification details
                 */
                if ($event === "SUCCESSFUL_JOB_APPLICATION") {
                    $ref_id = "QJ-" . explode("-", $application->id)[0];
                    $status = "Pending Acceptance By Issuer";
                    $category = $post->category;
                    $message = "Hello ". $user->name .", \n \n Your application for the Quick Job with reference ID (" . $ref_id .") has been received and under review. \n \n You will  be notified when the issuer completes their review. \n \n Thank You \n The VORK Team ";
                }

                if ($event === "APPLICATION_CONFIRMED") {
                    $ref_id = "QJ-" . explode("-", $application->id)[0];
                    $status = "Application Accepted";
                    $category = $post->category;
                    $message = "Congratulations ". $user->name .", \n \n You have been selected for the Quick Job issued by ". $post->user->name . ". \n \n The issuer will contact you for further arrangements. \n \n Thank You \n The VORK Team";
                }
                break;
        }

        $user->notify(new PostActivityNotification($post, $event, $message, $ref_id, $category, $status));
    }
}
