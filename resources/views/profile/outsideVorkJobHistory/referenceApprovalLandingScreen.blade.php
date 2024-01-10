@extends("layouts.onboarding")

@section("content")
    <div class="row text-center" style="margin-top: -40px;">
        <div class="col-md-4"></div>
        <div class="col-md-4 text-center">
            <a href="https://myvork.com" style="text-align: center;">
                <img class="" src="{{asset("assets/html-template/src/images/logo_white_bg.png")}}" style="height: 100px;width: auto" alt="logo">
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4 text-center" style="margin-top: 60px;">
            <h1><b>Reference Approval</b></h1>

            @if($status === "success")
                <p>Thank you {{$reference}} for taking time to action {{$requester}}'s job experience endorsement request. Your response has been recorded successfully, feedback will be sent out to the requester. Thank you!</p>

                @if($action === "approve")
                    <h3 class="text-success"><b>You approved the job reference</b></h3>
                @else
                    <h3 class="text-warning"><b>You declined the job reference</b></h3>
                @endif
            @else
                <p>Oops...something went wrong! We encountered an issue while actioning your response. Kindly reload the page or ask the requester to resend a new approval request. Thank you!</p>
            @endif

            <div class="text-center mt-3">
                <p class="text-muted">Not yet a member of the VORK community?</p>
                <a href="https://myvork.com" class="btn btn-full btn-lg btn-primary"><b>Become a VORKer today!</b></a>
            </div>
        </div>

    </div>

@endsection
