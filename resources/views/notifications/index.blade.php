@extends("layouts.master")

@section('title')
    Notifications
@endsection

@section("content")
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title"><em class="icon ni ni-bell"></em> Notifications </h3>
                <div class="nk-block-des text-soft">
                    <p class="hide-mb-sm hide-mb-xs md">
                    <nav>
                        <ul class="breadcrumb breadcrumb-arrow">
                            <li class="breadcrumb-item"><a href="#">All Notifications</a></li>
                        </ul>
                    </nav>
                    </p>
                </div>
            </div><!-- .nk-block-head-content -->
            <div class="nk-block-head-content">
                <a href="{{URL::previous()}}"
                   class="btn btn-outline-primary"><span>Back</span></a></li>
            </div><!-- .nk-block-head-content -->
        </div><!-- .nk-block-between -->
    </div><!-- .nk-block-head -->
    @if(count($notifications) <= 0)
        <div class="row">
            <div class="col-md-12 text-center">
                <img src="{{asset('assets/html-template/src/images/nd.svg')}}" style="height: 250px; width: 250px;" alt="">
                <p style="color: #777;">You don't have any notifications at the moment. Come back later.</p>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-md-5">
                @foreach($notifications as $notification)
                        <div class="card card-bordered">
                            <div class="card-header bg-white border-bottom">
                                <b>
                                    @if(array_key_exists("post", $notification->data)) {{$notification->data["post"]["type"]}} @endif
                                    @if(array_key_exists("job", $notification->data)) Reference Verification @endif
                                    <span style="float: right">{{$notification->created_at}}</span>
                                </b></div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="title" style="font-size: 10px;color: #777;">
                                            @if(array_key_exists("job", $notification->data)) Job Title @endif
                                            @if(array_key_exists("post", $notification->data)) Ref ID @endif
                                        </div>

                                        <div class="issuer"><b>
                                            @if(array_key_exists("post", $notification->data)) {{$notification->data["ref_id"]}}@endif
                                                @if(array_key_exists("job", $notification->data)) {{$notification->data["job"]["role"]}} @endif
                                            </b></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="title" style="font-size: 10px;color: #777;">
                                            @if(array_key_exists("post", $notification->data))
                                                @if(($notification->data["post"]["type"] === "VOLUNTEER"))
                                                    Activity Name
                                                @endif

                                                @if(($notification->data["post"]["type"] === "QUICK_JOB") || ($notification->data["post"]["type"] === "P2P"))
                                                    Category
                                                @endif

                                                @if(($notification->data["post"]["type"] === "FIXED_TERM_JOB") || ($notification->data["post"]["type"] === "PERMANENT_JOB"))
                                                    Title
                                                @endif
                                            @endif
                                                @if(array_key_exists("job", $notification->data)) Reference @endif
                                        </div>
                                        <b>
                                            <div class="date ">
                                                @if(array_key_exists("post", $notification->data))
                                                    @if(($notification->data["post"]["type"] === "VOLUNTEER"))
                                                        {{$notification->data["post"]["name"]}}
                                                    @endif

                                                    @if(($notification->data["post"]["type"] === "QUICK_JOB") || ($notification->data["post"]["type"] === "P2P"))
                                                        {{$notification->data["post"]["category"]}}
                                                    @endif

                                                    @if(($notification->data["post"]["type"] === "FIXED_TERM_JOB"))
                                                        {{$notification->data["post"]["title"]}}
                                                    @endif

                                                    @if(($notification->data["post"]["type"] === "FIXED_TERM_JOB") || ($notification->data["post"]["type"] === "PERMANENT_JOB"))
                                                        {{$notification->data["post"]["title"]}}
                                                    @endif
                                                @endif
                                                    @if(array_key_exists("job", $notification->data)) {{json_decode($notification->data["job"]["reference"])->name}} @endif
                                            </div>
                                        </b>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <div class="title" style="font-size: 10px;color: #777;">Status</div>
                                        <div class="issuer"><b>
                                                @if(array_key_exists("post", $notification->data)) {{$notification->data["status"]}}@endif
                                                @if(array_key_exists("job", $notification->data)) {{$notification->data["event"] === "REFERENCE_REQUEST_APPROVED" ? "Approved" : "Declined"}}@endif
                                        </b></div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right bg-white" style="border-top: 1px solid #dbdfea;">
                                <a href="{{route('user.notifications.show', ["notification_group_id" => $notification->group_id])}}"
                                   class="btn btn-outline-secondary">View Details</a>
                            </div>
                        </div>
                @endforeach
            </div>
            <div class="col-md-7 d-none d-md-block">
                <div class="text-center" style="margin-top: 120px;">
                    <img src="{{asset('assets/html-template/src/images/details.svg')}}" alt=""
                         style="height: 250px; width: 250px;">
                    <p style="color: #777;">Select a notification to view details.</p>
                </div>
            </div>
        </div>
    @endif
@endsection
