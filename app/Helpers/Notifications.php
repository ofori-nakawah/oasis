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
                if ($event === "SUCCESSFUL_JOB_APPLICATION") {
                    $ref_id = "VO" . explode("-", $application->id)[0];
                    $status = "Pending Acceptance By Issuer";
                    $category = $post->name;
                    $message = "Hello ". $user->name .", \n \n Your application for the Volunteer work with reference ID (" . $ref_id .") has been received. \n \n You will be notified when the issuer confirms your attendance.  ";
                }
                /**
                 * user confirmed for volunteer work
                 */
                if ($event === "APPLICATION_CONFIRMED") {
                    $ref_id = "VO" . explode("-", $application->id)[0];
                    $status = "Attendance Confirmed";
                    $category = $post->name;
                    $message = "Congratulations ". $user->name .", \n \n Your participation for the volunteering activity with REF ID (". $ref_id .") has been confirmed by the Issuer ". $post->user->name . ". \n \n Please contact the number below for any further enquiries. ";
                }
                /**
                 * user declined for volunteer work
                 */
                if ($event === "APPLICATION_DECLINED") {
                    $ref_id = "VO" . explode("-", $application->id)[0];
                    $status = "Participation Declined";
                    $category = $post->name;
                    $message = "Unfortunately, your application to participate in the volunteering activity with reference ID (". $ref_id .") has been declined by the issuer.. ";
                }
                /**
                 * volunteer activity closed
                 */
                if ($event === "JOB_CLOSED") {
                    $ref_id = "VO" . explode("-", $application->id)[0];
                    $status = "Project Closed";
                    $category = $post->name;
                    $message = "Thank you for participating in the Volunteering activity with REF ID (".$ref_id."). \n \n Your volunteer hours has been credited to your account.. ";
                }
                /**
                 * Job removed
                 */
                if ($event === "JOB_REMOVED") {
                    $ref_id = "VO" . explode("-", $application->id)[0];
                    $status = "Project Cancelled";
                    $category = $post->name;
                    $message = "Hello ". $user->name .", the volunteer activity with REF ID (". $ref_id ."))  has been cancelled by the organiser. Kindly check your feed for other opportunities near you. \n \n Thank You.";
                }
                break;
            case "QUICK_JOB":
                /**
                 * successful job application notification details
                 */
                if ($event === "SUCCESSFUL_JOB_APPLICATION") {
                    $ref_id = "QJ" . explode("-", $application->id)[0];
                    $status = "Pending Acceptance By Issuer";
                    $category = $post->category;
                    $message = "Hello ". $user->name .", \n \n Your application for the Quick Job with reference ID (" . $ref_id .") has been received and under review. \n \n You will  be notified when the issuer completes their review.  ";
                }
                /**
                 * User selected for quick job
                 */
                if ($event === "APPLICATION_CONFIRMED") {
                    $ref_id = "QJ" . explode("-", $application->id)[0];
                    $status = "Application Accepted";
                    $category = $post->category;
                    $message = "Congratulations ". $user->name .", \n \n You have been selected for the Quick Job issued by ". $post->user->name . ". \n \n The issuer will contact you for further arrangements. ";
                }
                /**
                 * User selected for quick job
                 */
                if ($event === "APPLICATION_DECLINED") {
                    $ref_id = "QJ" . explode("-", $application->id)[0];
                    $status = "Application Declined";
                    $category = $post->category;
                    $message = "Unfortunately, your application for the Quick Job with reference ID (". $ref_id .") has been declined by the issuer. \n \n Thank You. \n The VORK Team";
                }
                /**
                 * Job closed
                 */
                if ($event === "JOB_CLOSED") {
                    $ref_id = "QJ" . explode("-", $application->id)[0];
                    $status = "Job Closed";
                    $category = $post->category;
                    $message = "You have successfully completed the Quick Job with the REF ID (". $ref_id ."). \n \n Please see below for the closure details. ";
                }
                /**
                 * Job removed
                 */
                if ($event === "JOB_REMOVED") {
                    $ref_id = "QJ" . explode("-", $application->id)[0];
                    $status = "Job No Longer Required";
                    $category = $post->category;
                    $message = "Hello ". $user->name .", the Quick Job with the REF ID (". $ref_id .") is no longer required by the Issuer. Kindly check your feed for other opportunities near you.  \n \n Thank You.";
                }
                break;
        }

        $user->notify(new PostActivityNotification($post, $event, $message, $ref_id, $category, $status));
    }
}
