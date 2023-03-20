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
        <div class="col-md-4 text-center">
            <div class="card card-bordered">
                <div class="card-body" style="padding: 0px;height:  240px;">
                    @if($user->image_link)
                        <img src="{{$user->image_link}}" alt="" style="height: 240px;">
                    @else
                        <div style="margin-top: 60px;">
                            <em class="icon ni ni-user" style="font-size: 105px"></em>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-white border-top" style="padding-left: 0px;padding-right: 0px;;">
                    <div><b>{{$user->name}}</b></div>
                    <hr>
                    <div><em class="icon ni ni-map-pin"></em>{{$user->location_name}}</div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div style="margin-top: 10px;margin-bottom: 18px;">
                <span
                    class="@if($user->status == 1) text-success @elseif($user->status == 2) text-warning @else text-secondary @endif"><em
                        class="icon ni ni-circle-fill @if($user->status == 1) text-success @elseif($user->status == 2) text-warning @else text-secondary @endif"></em> {{($user->status == 1) ? 'active' : 'inactive'}}</span>
                <span class="text-primary"><em class="icon ni ni-live text-primary"></em> Member since {{$user->created_at}}</span>
                <span><em class="icon ni ni-eye"></em> Last seen was {{$user->created_at->diffForHumans()}}</span>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div id="languagesInterestsBox">
                        <div class="card card-bordered1 bg-white">
                            <div class="card-header bg-white border-bottom">
                                <b>Skills & Interests</b>
                            </div>
                            <div class="card-body">
                                @foreach($skills as $skill)
                                    <span
                                        class="badge badge-md badge-dim badge-pill badge-outline-dark">{{$skill->skill->name}}</span>
                                @endforeach
                            </div>
                        </div>
                        <div class="card card-bordered1 bg-white" style="margin-top:-15px !important;">
                            <div class="card-header bg-white border-bottom">
                                <b>Languages</b>
                            </div>
                            <div class="card-body">
                                @foreach($languages as $language)
                                    <span
                                        class="badge badge-md badge-dim badge-pill badge-outline-dark">{{$language->language->name}}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-bordered">
                        <div class="card-header bg-white border-bottom"><b>Analytics</b></div>
                        <div class="card-body" style="height: 245px;">
                            <div class="row" style="margin-top: 30px;">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-4 text-center">
                                            <div><em class="icon ni ni-briefcase" style="font-size: 30px;"></em></div>
                                        </div>
                                        <div class="col-md-8">
                                            <div style="margin-top: 0px;;"><b>Jobs</b> <br> {{$number_of_jobs}}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-4 text-center">
                                            <div><em class="icon ni ni-activity-alt" style="font-size: 30px;"></em>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div style="margin-top: 0px;"><b>Activities</b>
                                                <br> {{$number_of_activities}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row" style="margin-top: 20px;">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                                            <div><em class="icon ni ni-star-half-fill" style="font-size: 30px;"></em>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-sm-8 col-xs-8">
                                            <div style="margin-top: 0px;;"><b>Average Rating</b> <br> {{$user->rating}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-4 text-center">
                                            <div><em class="icon ni ni-clock-fill" style="font-size: 30px;"></em></div>
                                        </div>
                                        <div class="col-md-8">
                                            <div style="margin-top: 0px;;"><b>Volunteer Hours</b>
                                                <br> {{$volunteer_hours}}
                                            </div>
                                        </div>
                                    </div>
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
        <div class="col-md-4">
            <div class="card card-bordered undelineLinks" style="text-align: left !important;">
                <div class="card-header bg-white"><b>User Profile Information</b></div>
                <div class="card-body border-top" style="padding-top: 10px;padding-bottom: 5px;">
                    <a id="vorkHistoryLink" href="#" class="text-muted">Recent VORK History <span
                            style="float: right;"><em class="icon ni ni-chevron-right"
                                                      style="font-size: 22px;"></em></span></a>
                </div>
                <div class="card-body border-top" style="padding-top: 10px;padding-bottom: 5px;">
                    <a id="jobExperienceLink" href="#" class="text-muted">Job Experience Outside VORK <span
                            style="float: right;"><em class="icon ni ni-chevron-right"
                                                      style="font-size: 22px;"></em></span></a>
                </div>
                <div class="card-body border-top" style="padding-top: 10px;padding-bottom: 5px;">
                    <a id="certificationsLink" href="#" class="text-muted">Certifications <span
                            style="float: right;"><em class="icon ni ni-chevron-right"
                                                      style="font-size: 22px;"></em></span></a>
                </div>
                <div class="card-body border-top" style="padding-top: 10px;padding-bottom: 5px;">
                    <a id="educationLink" href="#" class="text-muted">Education <span style="float: right;"><em
                                class="icon ni ni-chevron-right" style="font-size: 22px;"></em></span></a>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div id="infoContentBox">
                <div id="emptyState">
                    <div class="text-center" style="margin-top: 0px;">
                        <img src="{{asset('assets/html-template/src/images/details.svg')}}" alt=""
                             style="height: 200px; width: 200px;">
                        <p style="color: #777;">Click on link you get information.</p>
                    </div>
                </div>
                <div id="loadingState">
                    <p class="text-center" style="color: #777;margin-top: 25px;">Loading...</p>
                </div>
                <div id="educationBox">
                    <div class="text-center" style="margin-top: 0px;">
                        <img src="{{asset('assets/html-template/src/images/wip.svg')}}"
                             style="height: 200px; width: 200px" alt="">
                        <p style="color: #777;">This feature is in maintenance mode. Come back later</p>
                    </div>
                </div>
                <div id="jobExperienceBox">
                    <div class="text-center" style="margin-top: 0px;">
                        <img src="{{asset('assets/html-template/src/images/wip.svg')}}"
                             style="height: 200px; width: 200px" alt="">
                        <p style="color: #777;">This feature is in maintenance mode. Come back later</p>
                    </div>
                </div>
                <div id="certificationsBox">
                    <div class="text-center" style="margin-top: 0px;">
                        <img src="{{asset('assets/html-template/src/images/wip.svg')}}"
                             style="height: 200px; width: 200px" alt="">
                        <p style="color: #777;">This feature is in maintenance mode. Come back later</p>
                    </div>
                </div>
                <div id="vorkHistoryBox">
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
                                                    <td>{{$job->created_at->diffForHumans()}}</td>
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
            </div>
        </div>
    </div>
@endsection

@section("scripts")
    <script>
        $("#emptyState").show()
        $("#loadingState").hide()
        // $("#languagesInterestsBox").hide()
        $("#vorkHistoryBox").hide()
        $("#jobExperienceBox").hide()
        $("#educationBox").hide()
        $("#certificationsBox").hide()

        // $("#skillsInterestLanguageLink").on("click", function () {
        //     clearContentBox()
        //     $("#loadingState").show()
        //     $("#emptyState").hide()
        //     $("#vorkHistoryBox").hide()
        //     $("#jobExperienceBox").hide()
        //     $("#educationBox").hide()
        //     $("#certificationsBox").hide()
        //     setTimeout(function () {
        //         $("#loadingState").hide()
        //         $("#languagesInterestsBox").show()
        //     }, 2000)
        // })

        $("#educationLink").on("click", function () {
            clearContentBox()
            $("#loadingState").show()
            $("#emptyState").hide()
            $("#vorkHistoryBox").hide()
            $("#jobExperienceBox").hide()
            // $("#languagesInterestsBox").hide()
            $("#certificationsBox").hide()
            setTimeout(function () {
                $("#loadingState").hide()
                $("#educationBox").show()
            }, 2000)
        })

        $("#jobExperienceLink").on("click", function () {
            clearContentBox()
            $("#loadingState").show()
            $("#emptyState").hide()
            $("#vorkHistoryBox").hide()
            $("#jobExperienceBox").hide()
            // $("#languagesInterestsBox").hide()
            $("#certificationsBox").hide()
            setTimeout(function () {
                $("#loadingState").hide()
                $("#jobExperienceBox").show()
            }, 2000)
        })

        $("#vorkHistoryLink").on("click", function () {
            clearContentBox()
            $("#loadingState").show()
            $("#emptyState").hide()
            $("#vorkHistoryBox").hide()
            $("#jobExperienceBox").hide()
            // $("#languagesInterestsBox").hide()
            $("#certificationsBox").hide()
            setTimeout(function () {
                $("#loadingState").hide()
                $("#vorkHistoryBox").show()
            }, 2000)
        })

        $("#certificationsLink").on("click", function () {
            clearContentBox()
            $("#loadingState").show()
            $("#emptyState").hide()
            $("#vorkHistoryBox").hide()
            $("#jobExperienceBox").hide()
            // $("#languagesInterestsBox").hide()
            $("#certificationsBox").hide()
            setTimeout(function () {
                $("#loadingState").hide()
                $("#certificationsBox").show()
            }, 2000)
        })

        function clearContentBox() {
            $("#emptyState").hide()
            $("#loadingState").hide()
            // $("#languagesInterestsBox").hide()
            $("#vorkHistoryBox").hide()
            $("#jobExperienceBox").hide()
            $("#educationBox").hide()
            $("#certificationsBox").hide()
        }
    </script>
@endsection
