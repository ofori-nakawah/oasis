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
                    <div class="card-header bg-white border-bottom">
                        <b>
                            @if(array_key_exists("post", $notification->data))
                                {{$notification->data["post"]["type"]}}
                            @endif
                            @if(array_key_exists("job", $notification->data))
                                Reference Verification
                            @endif
                            <span style="float: right">{{$notification->created_at}}</span>
                        </b>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="title" style="font-size: 10px;color: #777;">
                                    @if(array_key_exists("job", $notification->data))
                                        Job Title
                                    @endif
                                    @if(array_key_exists("post", $notification->data))
                                        Ref ID
                                    @endif
                                </div>

                                <div class="issuer"><b>
                                        @if(array_key_exists("post", $notification->data))
                                            {{$notification->data["ref_id"]}}
                                        @endif
                                        @if(array_key_exists("job", $notification->data))
                                            {{$notification->data["job"]["role"]}}
                                        @endif
                                    </b></div>
                            </div>
                            <div class="col-md-8">
                                <div class="title"
                                     style="font-size: 10px;color: #777;">
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
                                    @if(array_key_exists("job", $notification->data))
                                        Reference
                                    @endif
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
                                        @if(array_key_exists("job", $notification->data))
                                            {{json_decode($notification->data["job"]["reference"])->name}}
                                        @endif
                                    </div>
                                </b>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <div class="title" style="font-size: 10px;color: #777;">Status</div>
                                <div class="issuer"><b>
                                        @if(array_key_exists("post", $notification->data))
                                            @if(array_key_exists("status", $notification->data))
                                                {{$notification->data["status"]}}
                                            @elseif($notification->data["post"]["type"] === "P2P")
                                                Quote Request
                                            @else
                                                Pending
                                            @endif
                                        @endif
                                        @if(array_key_exists("job", $notification->data))
                                            {{$notification->data["event"] === "REFERENCE_REQUEST_APPROVED" ? "Approved" : "Declined"}}
                                        @endif
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
        <div class="col-md-7">
            @include("utilities.alerts.alerts")

            @php
                $hasDeclinedOrSubmitted = false;
                foreach ($group_notifications as $notification) {
                    if (isset($notification->data["event"]) && in_array($notification->data["event"], ["JOB_DECLINED", "QUOTE_SUBMITTED"])) {
                        $hasDeclinedOrSubmitted = true;
                        break;
                    }
                }

                $isConfirmed = false;
                foreach ($group_notifications as $notification) {
                    if (isset($notification->data["event"]) && $notification->data["event"] === "APPLICATION_CONFIRMED") {
                        $isConfirmed = true;
                        break;
                    }
                }
            @endphp
            
            <!-- Special handling for QUOTE_RECEIVED events -->
            @foreach($group_notifications as $notify)
                @if($notify->data["event"] === "QUOTE_RECEIVED" && auth()->id() == $notify->data['post']['user_id'])
                    <div class="card card-bordered mb-4">
                        <div class="card-header  bg-white " style="border-bottom: 1px solid #dbdfea;">
                            @if(array_key_exists("post", $notify->data))
                                {{$notify->data["post"]["type"]}} | <b>{{$notify->data["ref_id"]}}</b>
                            @endif
                            <span
                                style="float: right; font-weight: bold">{{$notify->created_at}}</span>
                        </div>
                        <div class="card-body">
                            @if(array_key_exists("post", $notify->data))
                              @if(isset($notify->data["status"]))
                                <div class="issuer mb-2" style="font-weight: bold;">{{$notify->data["status"]}}</div>
                              @endif
                              @if(isset($notify->data["message"]))
                                <div class="issuer">{{$notify->data["message"]}}</div>
                              @endif
                            @endif
                            <a class="btn btn-primary mt-3 text-white" href="{{ route('user.posts.show', ['uuid' => $notify->data['post']['id']]) }}">Review Quote</a>
                        </div>
                    </div>
                @endif
            @endforeach

            @foreach($group_notifications as $notify)
                @if(!($notify->data["event"] === "QUOTE_RECEIVED" && auth()->id() == $notify->data['post']['user_id']))
                <div class="card card-bordered">
                    <div class="card-header bg-white" style="border-bottom: 1px solid #dbdfea;">
                        <b>
                            @if(array_key_exists("post", $notify->data))
                                {{$notify->data["post"]["type"]}} | <b>{{$notify->data["ref_id"]}}</b>
                            @endif
                            @if(array_key_exists("job", $notify->data))
                                Reference Verification
                            @endif
                            <span
                                style="float: right">{{$notify->created_at}}</span></b>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                @if(array_key_exists("post", $notify->data))
                                    <div class="issuer">{{$notify->data["message"]}}</div>
                                @endif
                                @if(array_key_exists("job", $notify->data))
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="title" style="font-size: 10px;color: #777;">
                                                @if(array_key_exists("job", $notify->data))
                                                    Job Title
                                                @endif
                                            </div>

                                            <div class="issuer"><b>
                                                    @if(array_key_exists("job", $notify->data))
                                                        {{$notify->data["job"]["role"]}}
                                                    @endif
                                                </b></div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="title"
                                                 style="font-size: 10px;color: #777;">
                                                @if(array_key_exists("job", $notify->data))
                                                    Reference
                                                @endif
                                            </div>
                                            <b>
                                                <div class="date ">
                                                    @if(array_key_exists("job", $notify->data))
                                                        {{json_decode($notify->data["job"]["reference"])->name}}
                                                    @endif
                                                </div>
                                            </b>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-4">
                                            <div class="title" style="font-size: 10px;color: #777;">
                                                @if(array_key_exists("job", $notify->data))
                                                    Company
                                                @endif
                                            </div>

                                            <div class="issuer"><b>
                                                    @if(array_key_exists("job", $notify->data))
                                                        {{$notify->data["job"]["employer"]}}
                                                    @endif
                                                </b></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-footer border-top bg-white">
                        @if(array_key_exists("post", $notify->data))

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        @if($notify->data["event"] != "QUOTE_RECEIVED")
                                        <div class="col-md-6">
                                            <div class="title"
                                                 style="font-size: 10px;color: #777;">@if($notify->data["post"]["type"] === "VOLUNTEER")
                                                    Activity Name
                                                @endif @if($notify->data["post"]["type"] === "QUICK_JOB")
                                                    Category
                                                @endif @if($notify->data["post"]["type"] === "P2P")
                                                    Job Type
                                                @endif @if($notify->data["post"]["type"] === "FIXED_TERM_JOB" || $notify->data["post"]["type"] === "PERMANENT_JOB")
                                                    Title
                                                @endif</div>
                                            <div
                                                class="date text-danger">@if($notify->data["post"]["type"] === "VOLUNTEER")
                                                    {{$notify->data["post"]["name"]}}
                                                @endif @if($notify->data["post"]["type"] === "QUICK_JOB" || $notify->data["post"]["type"] === "P2P")
                                                    {{$notify->data["post"]["category"]}}
                                                @endif @if($notify->data["post"]["type"] === "FIXED_TERM_JOB" || $notify->data["post"]["type"] === "PERMANENT_JOB")
                                                    {{$notify->data["post"]["title"]}}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            @if(isset($notify->data["post"]["status"]) && $notify->data["post"]["status"] == "closed")
                                                <div class="title" style="font-size: 10px;color: #777;">Closure Date
                                                </div>
                                                <div>
                                                    <b>{{$notify->data["post"]["closed_at"] ?? $notify->data["post"]["updated_at"]}}</b>
                                                </div>
                                            @elseif(isset($notify->data["post"]["deleted_at"]))
                                                <div class="title" style="font-size: 10px;color: #777;">Removal Date
                                                </div>
                                                <div>
                                                    <b>{{$notify->data["post"]["deleted_at"]}}</b>
                                                </div>
                                            @else
                                                <div class="title" style="font-size: 10px;color: #777;">Date & Time
                                                </div>
                                                <div>
                                                    <b>{{$notify->data["post"]["date"]}} {{$notify->data["post"]["time"]}}</b>
                                                </div>
                                            @endif
                                        </div>

                                        @endif                                    </div>

                                    @php
                                        $userApplication = null;
                                        if (isset($notify->data["post"]["applications"])) {
                                            foreach ($notify->data["post"]["applications"] as $application) {
                                                if ($application["user_id"] == auth()->id()) {
                                                    $userApplication = $application;
                                                    break;
                                                }
                                            }
                                        }
                                    @endphp

                                    @if($notify->data["event"] != "SUCCESSFUL_JOB_APPLICATION" && $notify->data["event"] != "JOB_REMOVED" && $notify->data["event"] != "APPLICATION_DECLINED" && $notify->data["event"] != "JOB_CLOSED" && $notify->data["event"] != "QUOTE_RECEIVED")
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="title" style="font-size: 10px;color: #777;">Location</div>
                                                <div><b>{{$notify->data["post"]["location"]}}</b> | <a href="#"
                                                                                                       data-toggle="modal"
                                                                                                       data-target="#locationModal"
                                                                                                       class="text-primary">View
                                                        on map</a></div>
                                            </div>
                                            @if ($userApplication && $userApplication['status'] === 'confirmed')
                                                <div class="col-md-6">
                                                    <div class="title" style="font-size: 10px;color: #777;">Issuer Tel</div>
                                                    <div><b>{{$notify->data["post"]["user"]["phone_number"]}}</b></div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif


                                    @if($notify->data["event"] === "SUCCESSFUL_JOB_APPLICATION" || $notify->data["post"]["type"] === "P2P" )
                                        @if($notify->data["event"] !== "QUOTE_RECEIVED" && (!isset($notify->data["post"]["status"]) || $notify->data["post"]["status"] !== "closed") )
                                        <div class="row mt-1">
                                            <div class="col-md-12">
                                                <div class="title" style="font-size: 10px;color: #777;">Job
                                                    Description
                                                </div>
                                                <div><b>{{$notify->data["post"]["description"]}}</b></div>
                                            </div>
                                        </div>
                                        @endif


                                        @if ($notify->data["post"]["type"] === "P2P")
                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    @if (!$hasDeclinedOrSubmitted && !$userApplication && auth()->user()->id != $notify->data['post']['user_id'])
                                                        <!-- No application yet, show both buttons if user is not the post creator -->
                                                        <button class="btn btn-outline-secondary" onclick="confirmDecline(event)">Not Interested</button>
                                                        <form id="declineForm" method="POST" action="{{ route('job.decline') }}" style="display: none;">
                                                            @csrf
                                                            <input type="hidden" name="post_id" value="{{ $notify->data['post']['id'] }}">
                                                        </form>
                                                        <button class="btn btn-primary" data-toggle="modal" data-target="#quoteModal">Apply with quote</button>
                                                    @elseif ($userApplication && $userApplication['status'] !== 'declined')
                                                  
                                                        <!-- Application exists and not declined -->
                                                        @if (isset($userApplication['quote']) && !empty($userApplication['quote']))
                                                            <!-- Quote already submitted -->
                                                            <div class="p-3 border bg-gray-100" style="border-radius: 18px;">
                                                                <div>
                                                                    <div class="title" style="font-size: 10px;color: #777;">Quote (GHS)
                                                </div>
                                                                    <div class="mb-1"><b>{{ is_numeric($userApplication['quote']) ? $userApplication['quote'] : 'N/A' }}</b></div>
                                                                </div>
                                                                @if (isset($userApplication['comments']) && !empty($userApplication['comments']))
                                                                    <div>
                                                                        <div class="title" style="font-size: 10px;color: #777;">Comments</div>
                                                                        <div class="mb-1"><b>{{ $userApplication['comments'] }}</b></div>
                                                                    </div>
                                                                @endif

                                                                @if ($notify->data["event"] === "QUOTE_RECEIVED")
                                                                    @if (auth()->id() == $notify->data['post']['user_id'])
                                                                        <div class="alert alert-info mb-2">You have received a quote for your post. You can review and respond to it.</div>
                                                                    @endif
                                                                    
                                                                    <div style="margin-top: 10px; margin-bottom: 10px;">
                                                                        @if (auth()->id() == $notify->data['post']['user_id'])
                                                                            <a class="btn btn-primary btn-lg btn-block text-white" href="{{ route('user.posts.show', ['uuid' => $notify->data['post']['id']]) }}">Review Quote</a>
                                                                        @else
                                                                            <a class="btn btn-primary btn-lg btn-block text-white" href="{{ route('user.posts.show', ['uuid' => $notify->data['post']['id']]) }}">View Post</a>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @else
                                                            @if (!$hasDeclinedOrSubmitted)
                                                                <!-- Applied but no quote submitted yet -->
                                                                <button class="btn btn-primary" data-toggle="modal" data-target="#quoteModal">Apply with quote</button>
                                                                <button class="btn btn-outline-secondary" onclick="confirmDecline(event)">Not interested</button>
                                                            @endif
                                                        @endif
                                                    @elseif ($userApplication && $userApplication['status'] === 'declined')
                                                        <!-- Application was declined by user -->
                                                        <div class="alert alert-warning">{{auth()->id() != $userApplication['user_id']  ? auth()->user()->name : "You"}} have declined this job.</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @endif

                                    
                                    @if($notify->data["event"] == "JOB_CLOSED")
                                        @if($notify->data["post"]["type"] === "QUICK_JOB" || $notify->data["post"]["type"] === "P2P")
                                            <br>
                                            <p><b style="color: #777;">Payment (GHS)</b></p>
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
                                                    <td>{{$notify->data["post"]["final_payment_amount"] - ((5/ 100) * $notify->data["post"]["final_payment_amount"]) + ((1/ 100) * $notify->data["post"]["final_payment_amount"]) }}</td>
                                                </tr>
                                                </body>
                                            </table>
                                        @endif

                                        @if(($notify->data["post"]["type"] === "FIXED_TERM_JOB" || $notify->data["post"]["type"] === "PERMANENT_JOB") && auth()->id() == $notify->data["post"]["confirmed_applicant_id"])
                                            <br>
                                            <div class="text-muted">Income:
                                                GHS {{$notify->data["post"]["final_payment_amount"]}}/Month
                                            </div>
                                            @if($notify->data["post"]["type"] === "FIXED_TERM_JOB" )
                                                <div class="text-muted">
                                                    Term: {{\Carbon\Carbon::parse($notify->data["post"]["final_end_date"])->diffInMonths(\Carbon\Carbon::parse($notify->data["post"]["final_start_date"])) }}
                                                    months
                                                </div>
                                            @else
                                                <div class="text-muted"> Term: Permanent</div>
                                            @endif

                                            <br>

                                            <div class="card bg-lighter">
                                                <div class="card-body">
                                                    <div class="title" style="font-size: 10px;color: #777;">Start Date
                                                    </div>
                                                    <div class="issuer">
                                                        <b>{{($notify->data["post"]["final_start_date"]) ? date ("l jS F Y", strtotime($notify->data["post"]["final_start_date"])) : 'N/A'}}</b>
                                                    </div>

                                                    @if($notify->data["post"]["type"] === "FIXED_TERM_JOB" )
                                                        <br>

                                                        <div class="title" style="font-size: 10px;color: #777;">End
                                                            Date
                                                        </div>
                                                        <div class="issuer">
                                                            <b>{{($notify->data["post"]["final_end_date"]) ? date ("l jS F Y", strtotime($notify->data["post"]["final_end_date"])) : 'N/A'}}</b>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @endif


                                </div>
                            </div>
                        @endif
                        @if(array_key_exists("job", $notify->data))
                            <div class="title"
                                 style="font-size: 10px;color: #777;">Verification Status
                            </div>
                            <ul class="timeline" style="margin-left: -20px;">
                                <li>
                                    <div style="margin-left: 30px;padding-bottom: 30px;">
                                        <div>{{date("Y-m-d H:i:s", strtotime($notify->data["job"]["reference_verified_at"]))}}</div>
                                        @if($notify->data["event"] === "REFERENCE_REQUEST_APPROVED")
                                            <div class="text-success"><b>Approved</b></div>
                                        @endif

                                        @if($notify->data["event"] === "REFERENCE_REQUEST_DECLINED")
                                            <div class="text-danger"><b>Declined</b></div>
                                        @endif
                                    </div>
                                </li>
                                <li>
                                    <div style="margin-left: 30px;padding-bottom: 30px;">
                                        <div>{{$notify->data["job"]["reference_verification_sent_at"]}}</div>
                                        <div class="text">Verification Request Issued</div>
                                    </div>
                                </li>
                            </ul>
                        @endif
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Quote Modal -->
    <div class="modal fade" tabindex="-1" id="quoteModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Submit Your Quote</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross" aria-hidden="true"></em>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="quoteForm" method="POST" action="{{ route('job.submit.quote') }}">
                        @csrf
                        <input type="hidden" name="post_id" value="{{ $notify->data['post']['id'] }}">
                        <div class="form-group">
                            <label for="quote_amount">Quote Amount</label>
                            <input type="number" class="form-control" id="quote_amount" name="quote" placeholder="Enter your quote amount" required>
                        </div>
                        <div class="form-group">
                            <label for="quote_description">Comment</label>
                            <textarea class="form-control" id="quote_description" name="comments" rows="4" placeholder="Add a comment" required></textarea>
                        </div>
                        <div class="form-group text-right">
                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Location Modal -->
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
        function confirmDecline(event) {
            event.preventDefault();
            if (confirm('Are you sure you want to decline this job?')) {
                document.getElementById('declineForm').submit();
            }
        }
        
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
                componentRestrictions: {
                    country: "gh"
                }
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
