<?php

namespace App\Helpers;

use App\Notifications\PostActivityNotification;
use App\Notifications\ReferenceActivityNotification;
use App\Services\PushNotification;
use Illuminate\Support\Facades\Log;


class Notifications
{
    /**
     *setup notification payload
     */
    public static function PushUserNotification($post, $application, $user, $event, $tokens = null)
    {
        $message = "";
        $ref_id = "";
        $status = "";
        $category = "";
        $title = "";

        switch ($post->type) {
            case "VOLUNTEER":
                if ($event === "SUCCESSFUL_JOB_APPLICATION") {
                    $ref_id = "VO" . explode("-", $application->id)[0];
                    $status = "Pending Acceptance By Issuer";
                    $category = $post->name;
                    $message = "Hello " . $user->name . ",\nYour application for the Volunteer work with reference ID (" . $ref_id . ") has been received. \n \n You will be notified when the issuer confirms your attendance.  ";
                    $title = "Successful job application";
                }
                /**
                 * user confirmed for volunteer work
                 */
                if ($event === "APPLICATION_CONFIRMED") {
                    $ref_id = "VO" . explode("-", $application->id)[0];
                    $status = "Attendance Confirmed";
                    $category = $post->name;
                    $message = "Congratulations " . $user->name . ",\nYour participation for the volunteering activity with REF ID (" . $ref_id . ") has been confirmed by the Issuer " . $post->user->name . ".\nPlease contact the number below for any further enquiries. ";
                    $title = "Attendance confirmed";
                }
                /**
                 * user declined for volunteer work
                 */
                if ($event === "APPLICATION_DECLINED") {
                    $ref_id = "VO" . explode("-", $application->id)[0];
                    $status = "Participation Declined";
                    $category = $post->name;
                    $message = "Unfortunately, your application to participate in the volunteering activity with reference ID (" . $ref_id . ") has been declined by the issuer.. ";
                    $title = "Application declined";
                }
                /**
                 * volunteer activity closed
                 */
                if ($event === "JOB_CLOSED") {
                    $ref_id = "VO" . explode("-", $application->id)[0];
                    $status = "Project Closed";
                    $category = $post->name;
                    $message = "Thank you for participating in the Volunteering activity with REF ID (" . $ref_id . "). \n \n Your volunteer hours has been credited to your account.. ";
                    $title = "Project closed";
                }
                /**
                 * Job removed
                 */
                if ($event === "JOB_REMOVED") {
                    $ref_id = "VO" . explode("-", $application->id)[0];
                    $status = "Project Cancelled";
                    $category = $post->name;
                    $message = "Hello " . $user->name . ", the volunteer activity with REF ID (" . $ref_id . "))  has been cancelled by the organiser. Kindly check your feed for other opportunities near you. \n \n Thank You.";
                    $title = "Project cancelled";
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
                    $message = "Hello " . $user->name . ",\nYour application for the Quick Job with reference ID (" . $ref_id . ") has been received and under review.\nYou will  be notified when the issuer completes their review.  ";
                    $title = "Successful job application";
                }
                /**
                 * User selected for quick job
                 */
                if ($event === "APPLICATION_CONFIRMED") {
                    $ref_id = "QJ" . explode("-", $application->id)[0];
                    $title = "Application accepted";
                    $status = "Application Accepted";
                    $category = $post->category;
                    $message = "Congratulations " . $user->name . ",\nYou have been selected for the Quick Job issued by " . $post->user->name . ".\nThe issuer will contact you for further arrangements. ";
                }
                /**
                 * User selected for quick job
                 */
                if ($event === "APPLICATION_DECLINED") {
                    $ref_id = "QJ" . explode("-", $application->id)[0];
                    $status = "Application Declined";
                    $title = "Application declined";
                    $category = $post->category;
                    $message = "Unfortunately, your application for the Quick Job with reference ID (" . $ref_id . ") has been declined by the issuer.\n Thank You.\nThe VORK Team";
                }
                /**
                 * Job closed
                 */
                $isIssuer = (int) $post->user_id === (int) auth()->user()->id && (int) $application->user_id === (int) auth()->user()->id;
                if ($event === "JOB_CLOSED" && !$isIssuer) {
                    $ref_id = "QJ" . explode("-", $application->id)[0];
                    $status = "Job Closed";
                    $title = "Job closed";
                    $category = $post->category;
                    $message = "You have successfully completed the Quick Job with the REF ID (" . $ref_id . "). \n \n Please see below for the closure details. ";

                    $post->status = "closed";
                }
                /**
                 * Job removed
                 */
                if ($event === "JOB_REMOVED") {
                    $ref_id = "QJ" . explode("-", $application->id)[0];
                    $status = "Job No Longer Required";
                    $title = "Job removed";
                    $category = $post->category;
                    $message = "Hello " . $user->name . ", the Quick Job with the REF ID (" . $ref_id . ") is no longer required by the Issuer. Kindly check your feed for other opportunities near you.\nThank You.";
                }
                break;
            case "FIXED_TERM_JOB":
                /**
                 * successful job application notification details
                 */
                if ($event === "SUCCESSFUL_JOB_APPLICATION") {
                    $ref_id = "FT" . explode("-", $application->id)[0];
                    $status = "Pending Acceptance By Issuer";
                    $category = $post->category;
                    $message = "Hello " . $user->name . ",\nYour application for the Fixed Term Job with reference ID (" . $ref_id . ") has been received and under review.\nYou will  be notified when the issuer completes their review.  ";
                    $title = "Successful job application";
                }
                /**
                 * User selected for quick job
                 */
                if ($event === "APPLICATION_CONFIRMED") {
                    $ref_id = "FT" . explode("-", $application->id)[0];
                    $status = "Application Accepted";
                    $title = "Application accepted";
                    $category = $post->category;
                    $message = "Congratulations " . $user->name . ", \n \n You have been selected for the Fixed Term job issued by " . $post->user->name . " on behalf of " . $post->employer . ". \n \n  A representative of the employer will contact you for further arrangements";
                }
                /**
                 * User selected for quick job
                 */
                if ($event === "APPLICATION_DECLINED") {
                    $ref_id = "FT" . explode("-", $application->id)[0];
                    $status = "Application Declined";
                    $title = "Application declined";
                    $category = $post->category;
                    $message = "Unfortunately, your application for the Fixed Term Job with reference ID (" . $ref_id . ") has been declined by the issuer. \nThank You.\n The VORK Team";
                }
                /**
                 * Job closed
                 */
                if ($event === "JOB_CLOSED") {
                    $ref_id = "FT" . explode("-", $application->id)[0];
                    $status = "Job Closed";
                    $title = "Job closeed";
                    $category = $post->category;
                    if ($application->status === "confirmed") {
                        $message = "Applications for the Fixed Term Job with the REF ID (" . $ref_id . ") has been closed.\nYour new employer will be responsible for providing you with further information and assistance. We wish you the best in your New Chapter!. ";
                    } else {
                        $message = "Applications and Review for the Fixed Term Job with the REF ID (" . $ref_id . ") has been closed.  Kindly explore other opportunities available";
                    }
                    $post->status = "closed";
                }
                /**
                 * Job removed
                 */
                if ($event === "JOB_REMOVED") {
                    $ref_id = "FT" . explode("-", $application->id)[0];
                    $status = "Job No Longer Required";
                    $title = "Job removed";
                    $category = $post->category;
                    $message = "Hello " . $user->name . ", the Fixed Term Job with the REF ID (" . $ref_id . ") is no longer required by the Issuer. Kindly check your feed for other opportunities near you.\nThank You.";
                }
                break;
            case "PERMANENT_JOB":
                /**
                 * successful job application notification details
                 */
                if ($event === "SUCCESSFUL_JOB_APPLICATION") {
                    $ref_id = "PJ" . explode("-", $application->id)[0];
                    $status = "Pending Acceptance By Issuer";
                    $category = $post->category;
                    $message = "Hello " . $user->name . ",\nYour application for the Permanent Job with reference ID (" . $ref_id . ") has been received and under review.\nYou will  be notified when the issuer completes their review.  ";
                    $title = "Successful job application";
                }
                /**
                 * User selected for quick job
                 */
                if ($event === "APPLICATION_CONFIRMED") {
                    $ref_id = "PJ" . explode("-", $application->id)[0];
                    $status = "Application Accepted";
                    $title = "Application accepted";
                    $category = $post->category;
                    $message = "Congratulations " . $user->name . ",\nYou have been selected for the Permanent job issued by " . $post->user->name . " on behalf of " . $post->employer . ".\nA representative of the employer will contact you for further arrangements";
                }
                /**
                 * User selected for quick job
                 */
                if ($event === "APPLICATION_DECLINED") {
                    $ref_id = "PJ" . explode("-", $application->id)[0];
                    $status = "Application Declined";
                    $title = "Application declined";
                    $category = $post->category;
                    $message = "Unfortunately, your application for the Permanent with reference ID (" . $ref_id . ") has been declined by the issuer. \n \n Thank You. \n The VORK Team";
                }
                /**
                 * Job closed
                 */
                if ($event === "JOB_CLOSED") {
                    $ref_id = "PJ" . explode("-", $application->id)[0];
                    $status = "Job Closed";
                    $title = "Job closed";
                    $category = $post->category;
                    if ($application->status === "confirmed") {
                        $message = "Applications for the Permanent Job with the REF ID (" . $ref_id . ") has been closed.\nYour new employer will be responsible for providing you with further information and assistance. We wish you the best in your New Chapter! ";
                    } else {
                        $message = "Applications and Review for the Permanent Job with the REF ID (" . $ref_id . ") has been closed.  Kindly explore other opportunities available";
                    }
                    $post->status = "closed";
                }
                /**
                 * Job removed
                 */
                if ($event === "JOB_REMOVED") {
                    $ref_id = "PJ" . explode("-", $application->id)[0];
                    $status = "Job No Longer Required";
                    $title = "Job removed";
                    $category = $post->category;
                    $message = "Hello " . $user->name . ", the Permanent Job with the REF ID (" . $ref_id . ") is no longer required by the Issuer. Kindly check your feed for other opportunities near you.\nThank You.";
                }
                break;
            case "P2P":
                /**
                 * successful job application notification details
                 */
                if ($event === "SUCCESSFUL_JOB_APPLICATION") {
                    $ref_id = "PP" . explode("-", $post->id)[0];
                    $status = "New P2P job request";
                    $title = "New P2P job request";
                    $category = $post->category;
                    $message = "Hello " . $user->name . ",\nYou have a new P2P job request with reference ID (" . $ref_id . ").\nKindly review the details and provide quote or let the issuer know if you are not interested.  ";
                }
                /**
                 * Quote submitted
                 */
                if ($event === "QUOTE_RECEIVED") {
                    $ref_id = "PP" . explode("-", $post->id)[0];
                    $status = "New quote recieved";
                    $title = "New quote received";
                    $category = $post->category;
                    $message = "Hello " . $user->name . ",\nYou have a new quote from a vorker for your job request with reference ID (" . $ref_id . ").  ";
                }

                if ($event === "JOB_DECLINED") {
                    $ref_id = "PP" . explode("-", $post->id)[0];
                    $status = "Job Declined";
                    $title = "Job declined";
                    $category = $post->category;

                    $isIssuer = (int) $post->user_id === (int) auth()->user()->id && (int) $application->user_id === (int) auth()->user()->id;

                    if ($isIssuer) {
                        $message =  $user->name . " has declined the job opportunity REF ID (" . $ref_id . ").";
                    } else {
                        $message = "P2P Job with the REF ID (" . $ref_id . ") has been declined.";
                    }
                }

                /**
                 * Quote submitted
                 */
                if ($event === "QUOTE_SUBMITTED") {
                    $ref_id = "PP" . explode("-", $post->id)[0];
                    $status = "Quote submitted successfully";
                    $title = "Quote submitted successfully";
                    $category = $post->category;
                    $message = "Hello " . $user->name . ",\nYour quote for your job request with reference ID (" . $ref_id . ") has been submitted successfully. We will let you know when the issuer makes a decision.";
                }

                if ($event === "APPLICATION_CONFIRMED") {
                    $ref_id = "PP" . explode("-", $post->id)[0];
                    $status = "Quote Accepted";
                    $category = $post->category;
                    $message = "Congratulations " . $user->name . ",\nYou have been selected for the job issued by " . $post->user->name . ".\nThe issuer will contact you for further arrangements";
                    $title = "Quote accepted";
                }

                /**
                 * vorkers
                 */
                if ($event === "JOB_CLOSED") {
                    $ref_id = "PP" . explode("-", $post->id)[0];
                    $status = "Job Closed";
                    $category = $post->category;
                    $title = "Job closed";

                    $isIssuer = (int) $post->user_id === (int) auth()->user()->id && (int) $application->user_id === (int) auth()->user()->id;

                    if ($isIssuer) {
                        $message = "Your P2P job with the REF ID (" . $ref_id . ") has been closed. Ensure all the necessary payments have been made. Thank you for choosing Vork!";
                    } else {
                        if ($application->status === "confirmed") {
                            $message = "P2P Job with the REF ID (" . $ref_id . ") has been closed.";
                        } else {
                            $message = "Applications and review for the P2P job with the REF ID (" . $ref_id . ") has been closed.  Kindly explore other opportunities available";
                        }
                    }

                    $post->status = "closed";
                }
                break;
        }

        $user->notify(new PostActivityNotification($post, $event, $message, $ref_id, $category, $status));
        PushNotification::NotifyViaExpo($tokens ?? $user->expo_push_token, ["body" => $message, "title" => $title, "content" => $post], $event);
    }

    public static function FireReferenceRequestNotification($event, $outsideVorkJob, $user)
    {
        if ($event === "REFERENCE_REQUEST_APPROVED") {
            $status = "Approved";
            $job = $outsideVorkJob;
        }

        if ($event === "REFERENCE_REQUEST_DECLINED") {
            $status = "Declined";
            $job = $outsideVorkJob;
        }

        $user->notify(new ReferenceActivityNotification($event, $job));
    }
}
