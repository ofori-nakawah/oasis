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
                        <div class="card-header bg-white" style="border-bottom: 1px solid #dbdfea;">
                            <b>{{$notification->data["post"]["type"]}} <span
                                    style="float: right">{{$notification->created_at}}</span></b></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="title" style="font-size: 10px;color: #777;">Ref ID</div>
                                    <div class="issuer"><b>{{$notification->data["ref_id"]}}</b></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="title" style="font-size: 10px;color: #777;">{{ ($notification->data["post"]["type"] != "VOLUNTEER") ? 'Category' : 'Activity Name' }}</div>
                                    <b>
                                        <div class="date text-danger">{{($notification->data["post"]["type"] != "VOLUNTEER") ? $notification->data["post"]["category"] : $notification->data["post"]["name"]}}</div>
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
