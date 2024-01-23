<?php

namespace App\Http\Controllers\Web;

use App\Helpers\Notifications;
use App\Http\Controllers\Controller;
use App\Models\OutsideVorkJob;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OutsideVorkJobController extends Controller
{
    public function create($user)
    {
        if (!$user) {
            return back()->with("danger", "Invalid request");
        }

        $user = User::where("id", $user)->first();
        if (!$user) {
            return back()->with("danger", "Error fetching user information");
        }

        return view("profile.outsideVorkJobHistory.create", compact("user"));
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'role' => 'required',
            'employer' => 'required',
            'start_date' => 'required',
            'responsibilities' => 'required',
            'achievements' => 'required',
            'reference' => 'required',
        ]);

        if ($validation->fails()) {return back()->withErrors($validation->errors())->withInput();}

        $userId = Auth::id();

        $outsideVorkJob = new OutsideVorkJob();
        $outsideVorkJob->role = $request->role;
        $outsideVorkJob->employer = $request->employer;
        $outsideVorkJob->start_date = $request->start_date;
        $outsideVorkJob->end_date = $request->end_date;
        $outsideVorkJob->responsibilities = $request->responsibilities;
        $outsideVorkJob->achievements = $request->achievements;
        $outsideVorkJob->reference = json_encode([
            "name" => $request->reference
        ]);
        $outsideVorkJob->user_id = $userId;

        if ($request->is_ongoing === "on") {
            $outsideVorkJob->end_date = null;
        }

        try {
            $outsideVorkJob->save();
            return redirect()->route("user.profile", ["user_id" => $userId])->with("success", "Outside VORK job history has been added to your profile successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR SAVING OUTSIDE VORK JOB >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return back()->with("danger", "Error saving outside VORK job history information. Please try again.");
        }
    }

    public function edit($id)
    {
        if (!$id) {
            return redirect()->back()->with("danger", "Invalid request. Kindly try again.");
        }

        $outsideVorkJob = OutsideVorkJob::where("id", $id)->first();

        if (!$outsideVorkJob) {
            return redirect()->back()->with("danger", "Oop..something went wrong. Error retrieving information. Pleas try again.");
        }

        $user = Auth::user();

        return view("profile.outsideVorkJobHistory.edit", compact("outsideVorkJob", "user"));
    }

    public function remove($id)
    {
        if (!$id) {
            return redirect()->back()->with("danger", "Invalid request. Kindly try again.");
        }

        $outsideVorkJob = OutsideVorkJob::where("id", $id)->first();

        if (!$outsideVorkJob) {
            return redirect()->back()->with("danger", "Oop..something went wrong. Error retrieving information. Pleas try again.");
        }

        try {
            $outsideVorkJob->delete();
            return redirect()->back()->with("success", "Outside VORK job history has been removed successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR DELETING OUTSIDE VORK JOB >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return back()->with("danger", "Error removing outside VORK job history information. Please try again.");
        }

    }

    public function update(Request $request, $id)
    {
        if (!$id) {
            return redirect()->back()->with("danger", "Invalid request. Kindly try again.");
        }

        $outsideVorkJob = OutsideVorkJob::where("id", $id)->first();

        if (!$outsideVorkJob) {
            return redirect()->back()->with("danger", "Oop..something went wrong. Error retrieving information. Pleas try again.");
        }

        $outsideVorkJob->role = $request->role;
        $outsideVorkJob->employer = $request->employer;
        $outsideVorkJob->start_date = $request->start_date;
        $outsideVorkJob->end_date = $request->end_date;
        $outsideVorkJob->responsibilities = $request->responsibilities;
        $outsideVorkJob->achievements = $request->achievements;
        $outsideVorkJob->reference = json_encode([
            "name" => $request->reference
        ]);

        if ($request->is_ongoing === "on") {
            $outsideVorkJob->end_date = null;
        }

        try {
            $outsideVorkJob->update();
            return redirect()->route("user.profile", ["user_id" => Auth::id()])->with("success", "Outside VORK job history has been updated successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR UPDATING OUTSIDE VORK JOB >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return back()->with("danger", "Error updating outside VORK job history information. Please try again.");
        }
    }

    public function verifyReference($id)
    {
        if (!$id) {
            return redirect()->back()->with("danger", "Invalid request. Kindly try again.");
        }

        $outsideVorkJob = OutsideVorkJob::where("id", $id)->first();

        if (!$outsideVorkJob) {
            return redirect()->back()->with("danger", "Oop..something went wrong. Error retrieving information. Pleas try again.");
        }

        $user = Auth::user();

        return view("profile.outsideVorkJobHistory.verifyReference", compact("outsideVorkJob", "user", "outsideVorkJob"));
    }

    public function getReferenceVerification(Request $request, $id)
    {
        if (!$id) {
            return redirect()->back()->with("danger", "Invalid request. Kindly try again.");
        }

        $outsideVorkJob = OutsideVorkJob::where("id", $id)->first();

        if (!$outsideVorkJob) {
            return redirect()->back()->with("danger", "Oop..something went wrong. Error retrieving information. Pleas try again.");
        }

        $validation = Validator::make($request->all(), [
            'email' => 'required',
            'phone_number' => 'required',
        ]);

        if ($validation->fails()) {return back()->withErrors($validation->errors())->withInput();}

        $reference = json_decode($outsideVorkJob->reference);
        $reference->email = $request->email;
        $reference->phone_number = $request->phone_number;

        $outsideVorkJob->reference = json_encode($reference);
        $outsideVorkJob->reference_verification_sent_at = Carbon::now();


        $to = $reference->email;
        $subject = "Job Experience Reference Approval Request";
        $startDate = date("F Y", strtotime($outsideVorkJob->start_date));
        $endDate = $outsideVorkJob->end_date ? date("F Y", strtotime($outsideVorkJob->end_date)) : "Ongoing";
        $approvalURL = env("BACKEND_URL") ."/external-job-experience/". $outsideVorkJob->id ."/endorsements/approve";
        $imgSrc = asset("assets/html-template/src/images/logo_white_bg.png");

        $message = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="x-apple-disable-message-reformatting">
  <!--[if !mso]><!--><meta http-equiv="X-UA-Compatible" content="IE=edge"><!--<![endif]-->
  <link href="https://fonts.googleapis.com/css2?family=Sen:wght@400;700;800&display=swap" rel="stylesheet">
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <title></title>

    <style type="text/css">
      @media only screen and (min-width: 520px) {
  .u-row {
    width: 500px !important;
  }
  .u-row .u-col {
    vertical-align: top;
  }

  .u-row .u-col-50 {
    width: 250px !important;
  }

  .u-row .u-col-100 {
    width: 500px !important;
  }

}

@media (max-width: 520px) {
  .u-row-container {
    max-width: 100% !important;
    padding-left: 0px !important;
    padding-right: 0px !important;
  }
  .u-row .u-col {
    min-width: 320px !important;
    max-width: 100% !important;
    display: block !important;
  }
  .u-row {
    width: 100% !important;
  }
  .u-col {
    width: 100% !important;
  }
  .u-col > div {
    margin: 0 auto;
  }
}
body {
  margin: 0;
  padding: 0;
}

table,
tr,
td {
  vertical-align: top;
  border-collapse: collapse;
}

p {
  margin: 0;
}

.ie-container table,
.mso-container table {
  table-layout: fixed;
}

* {
  line-height: inherit;
  font-family: "Sen";
}

a[x-apple-data-detectors="true"] {
  color: inherit !important;
  text-decoration: none !important;
}

table, td { color: #000000; } #u_body a { color: #0000ee; text-decoration: underline; }
    </style>



</head>

<body class="clean-body u_body" style="margin: 0;padding: 0;-webkit-text-size-adjust: 100%;background-color: #e7e7e7;color: #000000">
  <table id="u_body" style="border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;min-width: 320px;Margin: 0 auto;background-color: #e7e7e7;width:100%" cellpadding="0" cellspacing="0">
  <tbody>
  <tr style="vertical-align: top">
    <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">



<div class="u-row-container" style="padding: 0px;background-color: transparent">
  <div class="u-row" style="margin: 0 auto;min-width: 320px;max-width: 500px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;">
    <div style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">

<div class="u-col u-col-100" style="max-width: 320px;min-width: 500px;display: table-cell;vertical-align: top;">
  <div style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">

<table style="font-family:Sen,helvetica,sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
  <tbody>
    <tr>
      <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:Sen,helvetica,sans-serif;" align="left">

<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td style="padding-right: 0px;padding-left: 0px;" align="center">

      <img align="center" border="0" src="'. $imgSrc .'" alt="" title="" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: inline-block !important;border: none;height: 200px;float: none;width: 100%;max-width: 480px;" width="480"/>

    </td>
  </tr>
</table>

      </td>
    </tr>
  </tbody>
</table>

  </div>
</div>
    </div>
  </div>
  </div>





<div class="u-row-container" style="padding: 0px;background-color: transparent">
  <div class="u-row" style="margin: 0 auto;min-width: 320px;max-width: 500px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;">
    <div style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">

<div class="u-col u-col-100" style="max-width: 320px;min-width: 500px;display: table-cell;vertical-align: top;">
  <div style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">

<table style="font-family:Sen,helvetica,sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
  <tbody>
    <tr>
      <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:Sen,helvetica,sans-serif;" align="left">

    <h1 style="margin: 0px; line-height: 140%; text-align: center; word-wrap: break-word; font-size: 22px; font-weight: 700;"><span>Job Experience Reference Approval Request</span></h1>

      </td>
    </tr>
  </tbody>
</table>

  </div>
</div>
    </div>
  </div>
  </div>





<div class="u-row-container" style="padding: 0px;background-color: transparent">
  <div class="u-row" style="margin: 0 auto;min-width: 320px;max-width: 500px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;">
    <div style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">

<div class="u-col u-col-100" style="max-width: 320px;min-width: 500px;display: table-cell;vertical-align: top;">
  <div style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">

<table style="font-family:Sen,helvetica,sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
  <tbody>
    <tr>
      <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:Sen,helvetica,sans-serif;" align="left">

  <div style="font-size: 14px; line-height: 140%; text-align: left; word-wrap: break-word;">
    <p style="line-height: 140%;">Dear '. $reference->name .',</p>
<p style="line-height: 140%;"> </p>
<p style="line-height: 140%;">'. Auth::user()->name .' has sent you a request to approve thier job experience on VORK. Kindly find details below:</p>
<p style="line-height: 140%;"> </p>
<p style="line-height: 140%;"><strong>Job Role:</strong></p>
<p style="line-height: 140%;">'. $outsideVorkJob->role .'</p>
<p style="line-height: 140%;"> </p>
<p style="line-height: 140%;"><strong>Company:</strong></p>
<p style="line-height: 140%;">'. $outsideVorkJob->employer .'</p>
<p style="line-height: 140%;"> </p>
<p style="line-height: 140%;"><strong>Period:</strong></p>
<p style="line-height: 140%;">'. $startDate .' - '. $endDate .'</p>
<p style="line-height: 140%;"> </p>
<p style="line-height: 140%;">You are kindly required to approve or decline this request.</p>
  </div>

      </td>
    </tr>
  </tbody>
</table>

  </div>
</div>
    </div>
  </div>
  </div>





<div class="u-row-container" style="padding: 0px;background-color: transparent">
  <div class="u-row" style="margin: 0 auto;min-width: 320px;max-width: 500px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;">
    <div style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">

<div class="u-col u-col-50" style="max-width: 320px;min-width: 250px;display: table-cell;vertical-align: top;">
  <div style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">


<table style="font-family:Sen,helvetica,sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
  <tbody>
    <tr>
      <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:Sen,helvetica,sans-serif;" align="left">

<div align="center">
    <a href="'. $approvalURL .'" target="_blank" class="v-button" style="box-sizing: border-box;display: inline-block;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;color: #FFFFFF; background-color: #353299; border-radius: 4px;-webkit-border-radius: 4px; -moz-border-radius: 4px; width:100%; max-width:100%; overflow-wrap: break-word; word-break: break-word; word-wrap:break-word; mso-border-alt: none;font-size: 14px;">
      <span style="display:block;padding:10px 20px;line-height:120%;"><span style="line-height: 16.8px;">Approve</span></span>
    </a>
</div>

      </td>
    </tr>
  </tbody>
</table>

  </div>
</div>

<div class="u-col u-col-50" style="max-width: 320px;min-width: 250px;display: table-cell;vertical-align: top;">
  <div style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
<table style="font-family:Sen,helvetica,sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
  <tbody>
    <tr>
      <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:Sen,helvetica,sans-serif;" align="left">

<div align="center">
    <a href="" target="_blank" class="v-button" style="box-sizing: border-box;display: inline-block;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;color: #FFFFFF; background-color: #e81e50; border-radius: 4px;-webkit-border-radius: 4px; -moz-border-radius: 4px; width:100%; max-width:100%; overflow-wrap: break-word; word-break: break-word; word-wrap:break-word; mso-border-alt: none;font-size: 14px;">
      <span style="display:block;padding:10px 20px;line-height:120%;"><span style="line-height: 16.8px;">Decline</span></span>
    </a>
</div>

      </td>
    </tr>
  </tbody>
</table>

  </div>
</div>
    </div>
  </div>
  </div>

<div class="u-row-container" style="padding: 0px;background-color: transparent">
  <div class="u-row" style="margin: 0 auto;min-width: 320px;max-width: 500px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;">
    <div style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">

<div class="u-col u-col-100" style="max-width: 320px;min-width: 500px;display: table-cell;vertical-align: top;">
  <div style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">

<table style="font-family:Sen,helvetica,sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
  <tbody>
    <tr>
      <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:Sen,helvetica,sans-serif;" align="left">

  <div style="font-size: 14px; line-height: 140%; text-align: left; word-wrap: break-word;">
    <p style="line-height: 140%;">Regards,</p>
<p style="line-height: 140%;margin-bottom: 20px;">VORK Management</p>
  </div>

      </td>
    </tr>
  </tbody>
</table>

  </div>
</div>
    </div>
  </div>
  </div>

    </td>
  </tr>
  </tbody>
  </table>
</body>

</html>
';

// It is mandatory to set the content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers. From is required, rest other headers are optional
        $headers .= 'From: VORK <no-reply@myvork.com>' . "\r\n";

        mail($to,$subject,$message,$headers);

        try {
            $outsideVorkJob->update();
            return redirect()->route("user.profile", ["user_id" => Auth::id()])->with("success", "Reference approval request has been sent successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR REQUEST OUTSIDE VORK JOB REFERENCE APPROVAL >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return back()->with("danger", "Error requesting reference approval. Please try again.");
        }
    }

    public function approveOrDeclineReferenceRequest($id, $action)
    {
        if (!$id || !$action) {
            return redirect()->back()->with("danger", "Invalid request. Kindly try again.");
        }

        $outsideVorkJob = OutsideVorkJob::where("id", $id)->first();

        if (!$outsideVorkJob) {
            return redirect()->back()->with("danger", "Oop..something went wrong. Error retrieving information. Pleas try again.");
        }

        if ($action === "approve") {
            $outsideVorkJob->reference_verified_at = Carbon::now();
        } else {
            $outsideVorkJob->reference_verified_at = null;
        }

        try {
            $outsideVorkJob->update();
            $requester = $outsideVorkJob->user->name;
            $reference = json_decode($outsideVorkJob->reference)->name;
            $status = "success";

            /**
             * set user notification
             */
            $notificationEvent = $action === "approve" ? "REFERENCE_REQUEST_APPROVED" : "REFERENCE_REQUEST_DECLINED";
            Notifications::FireReferenceRequestNotification($notificationEvent, $outsideVorkJob, Auth::user());

            return view("profile.outsideVorkJobHistory.referenceApprovalLandingScreen", compact("requester", "reference", "outsideVorkJob", "action", "status"));
        } catch (QueryException $e) {
            Log::error("ERROR REQUEST OUTSIDE VORK JOB REFERENCE APPROVAL >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            $status = "error";
            return view("profile.outsideVorkJobHistory.verifyReference", compact("outsideVorkJob", "status"));
        }
    }
}

