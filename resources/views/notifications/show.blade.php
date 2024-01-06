@extends("layouts.master")

@section('title')
    Volunteer
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
                            <li class="breadcrumb-item"><a href="#">My Notifications</a></li>
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

    <div class="row">
        <div class="col-md-5 d-none d-md-block">
            @foreach($notifications as $notification)
                    <div class="card card-bordered">
                        <div class="card-header bg-white" style="border-bottom: 1px solid #dbdfea;">
                            <b>{{$notification->data["post"]["type"]}} <span
                                    style="float: right">{{$notification->created_at}}</span></b></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="title" style="font-size: 10px;color: #777;">Ref ID</div>
                                    <div class="issuer"><b>{{$notification->data["ref_id"]}}</b></div>
                                </div>
                                <div class="col-md-8">
                                    <div class="title"
                                         style="font-size: 10px;color: #777;">@if($notification->data["post"]["type"] === "VOLUNTEER") Activity Name @endif @if($notification->data["post"]["type"] === "QUICK_JOB") Category @endif @if($notification->data["post"]["type"] === "FIXED_TERM_JOB") Title @endif</div>
                                    <b>
                                        <div
                                            class="date text-danger">@if($notification->data["post"]["type"] === "VOLUNTEER")
                                            {{$notification->data["post"]["name"]}} @endif @if($notification->data["post"]["type"] === "QUICK_JOB")
                                            {{$notification->data["post"]["category"]}} @endif @if($notification->data["post"]["type"] === "FIXED_TERM_JOB")
                                            {{$notification->data["post"]["title"]}} @endif</div>
                                    </b>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="title" style="font-size: 10px;color: #777;">Status</div>
                                    <div class="issuer"><b>{{$notification->data["status"]}}</b></div>
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
        <div class="col-md-7">
            @include("utilities.alerts.alerts")
            @foreach($group_notifications as $notify)
                <div class="card card-bordered">
                    <div class="card-header bg-white" style="border-bottom: 1px solid #dbdfea;">
                        <b>{{$notify->data["post"]["type"]}} | <b>{{$notify->data["ref_id"]}}</b> <span
                                style="float: right">{{$notify->created_at}}</span></b></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="issuer">{{$notify->data["message"]}}</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer border-top bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="title"
                                             style="font-size: 10px;color: #777;">@if($notify->data["post"]["type"] === "VOLUNTEER") Activity Name @endif @if($notify->data["post"]["type"] === "QUICK_JOB") Category @endif @if($notify->data["post"]["type"] === "FIXED_TERM_JOB" || $notify->data["post"]["type"] === "PERMANENT_JOB") Title @endif</div>
                                        <div
                                            class="date text-danger">@if($notify->data["post"]["type"] === "VOLUNTEER")
                                                {{$notify->data["post"]["name"]}} @endif @if($notify->data["post"]["type"] === "QUICK_JOB")
                                                {{$notify->data["post"]["category"]}} @endif @if($notify->data["post"]["type"] === "FIXED_TERM_JOB" || $notify->data["post"]["type"] === "PERMANENT_JOB")
                                                {{$notify->data["post"]["title"]}} @endif</div>
                                    </div>
                                    <div class="col-md-6">
                                        @if($notify->data["post"]["status"] == "closed")
                                            <div class="title" style="font-size: 10px;color: #777;">Closure Date</div>
                                            <div>
                                                <b>{{$notify->data["post"]["closed_at"]}}</b>
                                            </div>
                                        @elseif($notify->data["post"]["deleted_at"])
                                            <div class="title" style="font-size: 10px;color: #777;">Removal Date</div>
                                            <div>
                                                <b>{{$notify->data["post"]["deleted_at"]}}</b>
                                            </div>
                                        @else
                                            <div class="title" style="font-size: 10px;color: #777;">Date & Time</div>
                                            <div>
                                                <b>{{$notify->data["post"]["date"]}} {{$notify->data["post"]["time"]}}</b>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if($notify->data["event"] != "SUCCESSFUL_JOB_APPLICATION" && $notify->data["event"] != "JOB_REMOVED" && $notify->data["event"] != "APPLICATION_DECLINED" && $notify->data["event"] != "JOB_CLOSED")
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="title" style="font-size: 10px;color: #777;">Location</div>
                                            <div><b>{{$notify->data["post"]["location"]}}</b> | <a href="#" data-toggle="modal" data-target="#locationModal" class="text-primary">View on map</a></div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="title" style="font-size: 10px;color: #777;">Issuer Tel</div>
                                            <div><b>{{$notify->data["post"]["user"]["phone_number"]}}</b></div>
                                        </div>
                                    </div>
                                @endif

                                @if($notify->data["event"] == "JOB_CLOSED")
                                    @if($notify->data["post"]["type"] === "QUICK_JOB")
                                        <br>
                                        <p><b  style="color: #777;">Payment (GHS)</b></p>
                                        <table class="table table-striped">
                                            <body>
                                            <tr>
                                                <td class="text-muted">Gross Amount</td>
                                                <td>{{$notify->data["post"]["final_payment_amount"]}}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">WHT Allocation (5%)</td>
                                                <td>{{number_format((5/ 100) * $notify->data["post"]["final_payment_amount"], 2)}}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Vork Charge (1%)</td>
                                                <td>{{number_format((1/ 100) * $notify->data["post"]["final_payment_amount"], 2)}}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Net Amount</b></td>
                                                <td>{{$notify->data["post"]["final_payment_amount"] - number_format((5/ 100) * $notify->data["post"]["final_payment_amount"], 2) - number_format((1/ 100) * $notify->data["post"]["final_payment_amount"], 2) }}</td>
                                            </tr>
                                            </body>
                                        </table>
                                    @endif

                                    @if(($notify->data["post"]["type"] === "FIXED_TERM_JOB" || $notify->data["post"]["type"] === "PERMANENT_JOB") && auth()->id() == $notify->data["post"]["confirmed_applicant_id"])
                                        <br>
                                            <div class="text-muted">Income: GHS {{$notify->data["post"]["final_payment_amount"]}}/Month
                                            </div>
                                            @if($notify->data["post"]["type"] === "FIXED_TERM_JOB" )
                                            <div class="text-muted"> Term: {{\Carbon\Carbon::parse($notify->data["post"]["final_end_date"])->diffInMonths(\Carbon\Carbon::parse($notify->data["post"]["final_start_date"])) }} months
                                            </div>
                                            @else
                                            <div class="text-muted"> Term: Permanent </div>
                                            @endif

                                            <br>

                                            <div class="card bg-lighter">
                                                <div class="card-body">
                                                    <div class="title" style="font-size: 10px;color: #777;">Start Date </div>
                                                    <div class="issuer"><b>{{($notify->data["post"]["final_start_date"]) ? date ("l jS F Y", strtotime($notify->data["post"]["final_start_date"])) : 'N/A'}}</b></div>

                                                    @if($notify->data["post"]["type"] === "FIXED_TERM_JOB" )
                                                        <br>

                                                        <div class="title" style="font-size: 10px;color: #777;">End Date </div>
                                                        <div class="issuer"><b>{{($notify->data["post"]["final_end_date"]) ? date ("l jS F Y", strtotime($notify->data["post"]["final_end_date"])) : 'N/A'}}</b></div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @endif

                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="modal modal-lg fade" tabindex="-1" id="locationModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title">Location</h5>
                </div>
                <div class="modal-body">
                    <div class="card card-bordered" style="padding: 0px;">
                        <div id="locationMap" style="height: 350px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section("scripts")
    <script>
        const coords = '{{$location_coordinates}}'
        const lat = parseFloat(coords.split(',')[0])
        const lng = parseFloat(coords.split(',')[1])
        console.log(coords)
    </script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{env("GOOGLE_MAPS_KEY")}}&libraries=places&callback=mountMap"
        async defer></script>
    <script>
        function mountMap() {
            var mapOptions = {
                center: {
                    lat: parseFloat(coords.split(',')[0]),
                    lng: parseFloat(coords.split(',')[1])
                },
                zoom: 14,
                componentRestrictions: {country: "gh"}
            };
            var map = new google.maps.Map(document.getElementById("locationMap"), mapOptions);

            /**
             * show marker of user's current location
             * @type {google.maps.Marker}
             */
            const _latlng = new google.maps.LatLng(lat, lng);
            map.panTo(_latlng)
            const marker = new google.maps.Marker({
                position: _latlng,
                map: map,
            });
        }
    </script>
@endsection
