@extends("layouts.master")

@section('title')
    Profile
@endsection

@section("content")
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title"><em class="icon ni ni-user"></em> Profile </h3>
                <div class="nk-block-des text-soft">
                    <p class="hide-mb-sm hide-mb-xs md">
                    <nav>
                        <ul class="breadcrumb breadcrumb-arrow">
                            <li class="breadcrumb-item"><a href="#">{{$user->name}}</a></li>
                        </ul>
                    </nav>
                    </p>
                </div>
            </div><!-- .nk-block-head-content -->
        </div>
    </div>

    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-5 text-center">
                    <div class="card card-bordered">
                        <div class="card-body" style="padding: 0px;height:  145px;">
                            @if($user->image_link)
                                <img src="{{$user->image_link}}" alt="" style="height: 160px;">
                            @else
                                <em class="icon ni ni-user" style="font-size: 105px;"></em>
                            @endif
                        </div>
                        <div class="card-footer bg-white border-top" style="padding-left: 0px;padding-right: 0px;;">
                            <div><b>{{$user->name}}</b></div>
                            <hr>
                            <div><em class="icon ni ni-map-pin"></em>{{$user->location_name}}</div>
                        </div>
                    </div>

                </div>
                <div class="col-md-7">
                    <div class="card card-bordered bg-white">
                        <div class="card-header bg-white border-bottom">
                            <b>Skills & Interests</b>
                        </div>
                        <div class="card-body">
                            @foreach($skills as $skill)
                                <span class="badge badge-md badge-dim badge-pill badge-outline-dark">{{$skill->skill->name}}</span>
                            @endforeach
                        </div>
                    </div>
                    <br>
                    <div class="card card-bordered bg-white">
                        <div class="card-header bg-white border-bottom">
                            <b>Languages</b>
                        </div>
                        <div class="card-body">
                            @foreach($languages as $language)
                                <span class="badge badge-md badge-dim badge-pill badge-outline-dark">{{$language->language->name}}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <br>

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-bordered">
                        <div class="card-header bg-white border-bottom"><b>Analytics</b></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-4 text-center">
                                            <div><em class="icon ni ni-briefcase" style="font-size: 80px;"></em></div>
                                        </div>
                                        <div class="col-md-8">
                                            <div style="margin-top: 25px;"><b>Jobs</b> <br> {{$number_of_jobs}}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-4 text-center">
                                            <div><em class="icon ni ni-activity-alt" style="font-size: 80px;"></em></div>
                                        </div>
                                        <div class="col-md-8">
                                            <div style="margin-top: 25px;"><b>Activities</b> <br> {{$number_of_activities}}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                                            <div><em class="icon ni ni-star-half-fill" style="font-size: 80px;"></em></div>
                                        </div>
                                        <div class="col-md-8 col-sm-8 col-xs-8">
                                            <div style="margin-top: 25px;"><b>Average Rating</b> <br> {{$user->rating}}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-4 text-center">
                                            <div><em class="icon ni ni-clock-fill" style="font-size: 80px;"></em></div>
                                        </div>
                                        <div class="col-md-8">
                                            <div style="margin-top: 25px;"><b>Volunteer Hours</b> <br> {{$volunteer_hours}}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-bordered">
                        <div class="card-header bg-white border-bottom"><b>Recent Work History</b></div>
                        <div class="card-body" style="padding: 0px;">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Ref No.</th>
                                    <th>Job</th>
                                    <th>Employer Feedback</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($job_history as $job)
                                    @if($job->type !== "VOLUNTEER" && $job->status === "closed")
                                        <tr>
                                            <td>{{$job->ref_id}}</td>
                                            <td>{{$job->category}}</td>
                                            <td>
                                                <div>starts</div>
                                                <div>{{$job->rating_and_reviews->feedback_message}}</div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2"></div>
    </div>
@endsection
