@extends("layouts.master")

@section('title')
    Volunteer
@endsection

@section("content")
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title"><em class="icon ni ni-users"></em> Volunteer </h3>
                <div class="nk-block-des text-soft">
                    <p class="hide-mb-sm hide-mb-xs md">
                    <nav>
                        <ul class="breadcrumb breadcrumb-arrow">
                            <li class="breadcrumb-item"><a href="#">Join</a></li>
                            <li class="breadcrumb-item"><a href="#">Upcoming Activities Near You</a></li>
                        </ul>
                    </nav>
                    </p>
                </div>
            </div><!-- .nk-block-head-content -->
            <div class="nk-block-head-content">
                <a href="{{route("user.volunteerism.create")}}"
                   class="btn btn-primary"><span>Post a project</span></a></li>
            </div><!-- .nk-block-head-content -->
        </div><!-- .nk-block-between -->
    </div><!-- .nk-block-head -->
    @if(count($posts) <= 0)
        <div class="row">
            <div class="col-md-12 text-center">
                <img src="{{asset('assets/html-template/src/images/nd.svg')}}" style="height: 250px; width: 250px;" alt="">
                <p style="color: #777;">There are no upcoming activities near you at the moment. Come back later.</p>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-md-5">
                @foreach($posts as $post)
                    <div class="card card-bordered">
                        <div class="card-header bg-white" style="border-bottom: 1px solid #dbdfea;">
                            <b>{{$post->name}}</b></div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <div class="title" style="font-size: 10px;color: #777;">Issuer</div>
                                    <div class="issuer"><b>{{$post->user->name}}</b></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="title" style="font-size: 10px;color: #777;">Date & Time</div>
                                    <b>
                                        <div class="date text-danger">{{$post->date}} {{$post->time}}</div>
                                    </b>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="title" style="font-size: 10px;color: #777;">Location</div>
                                    <div class="issuer"><b>{{$post->location}} ({{$post->distance}}km)</b></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="title" style="font-size: 10px;color: #777;">Volunteer Hours</div>
                                    <b>
                                        <div class="date text-success">{{$post->volunteer_hours}}</div>
                                    </b>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-right bg-white" style="border-top: 1px solid #dbdfea;">
                        <span class="pull-left" style="float: left !important;">
                            @if($post->has_already_applied === "yes") <span>Already applied</span> @endif
                        </span>
                            <a href="{{route('user.volunteerism.show', ['uuid' => $post->id])}}"
                               class="btn btn-outline-secondary">View Details</a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="col-md-7 d-none d-md-block">
                <div class="text-center" style="margin-top: 120px;">
                    <img src="{{asset('assets/html-template/src/images/details.svg')}}" alt=""
                         style="height: 250px; width: 250px;">
                    <p style="color: #777;">Select an activity to view more details.</p>
                </div>
            </div>
        </div>
    @endif
@endsection
