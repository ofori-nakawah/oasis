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
        <div class="nk-block-head-content">
            <div class="flex flex-row justify-center items-center">
                {{-- <a href="{{route('user.profile.resume', ["id" => $user->id])}}"--}}
                {{-- class=" ml-2 mt-1"><em class="ni ni-download" style="font-size: 28px;"></em></a>--}}
                {{-- <a href="{{URL::previous()}}"--}}
                {{-- class="btn btn-outline-primary ml-2"><span>Gallery</span></a>--}}
                @if(Auth::id() !== $user->id)
                <a href="{{URL::previous()}}"
                    class="btn btn-primary ml-2"><span>Hire Vorker</span></a>
                @endif
                <a href="{{URL::previous()}}"
                    class="btn btn-outline-light ml-2"><span>Back</span></a>
            </div>
        </div><!-- .nk-block-head-content -->
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div style="margin-top: 10px;margin-bottom: 18px;">
            <span
                class="@if($user->status == 1) text-success @elseif($user->status == 2) text-warning @else text-secondary @endif"><em
                    class="icon ni ni-circle-fill @if($user->status == 1) text-success @elseif($user->status == 2) text-warning @else text-secondary @endif"></em> {{($user->status == 1) ? 'active' : 'inactive'}}</span>
            <span class="text-primary"><em class="icon ni ni-live text-primary"></em> Member since {{$user->created_at}}</span>
            <span><em class="icon ni ni-eye"></em> @if($user->is_online)
                <span
                    class="text-success">online</span>
                @else
                Last seen
                was {{($user->last_seen) ? $user->last_seen->diffForHumans() : ' a while back'}}
                @endif</span>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 text-center">
        <div class="card card-bordered">
            <div class="card-body" style="padding: 0px;height:  270px;">
                @if($user->profile_picture)
                <div class="text-center">
                    <img src="{{$user->profile_picture}}" alt=""
                        style="height: 220px;width: 220px;border: 1px solid #ccc;border-radius: 50%;margin-top: 25px;">
                    <div>@if($user->id == auth()->user()->id)
                        <a href="#" data-toggle="modal"
                            data-target="#editProfilePicModal"><em
                                class="icon ni ni-pen"></em> <b>Edit</b></a>
                        @endif
                    </div>

                </div>
                @else
                <div style="margin-top: 80px;">
                    <em class="icon ni ni-user" style="font-size: 105px"></em>
                    <div>@if($user->id == auth()->user()->id)
                        <a href="#" data-toggle="modal"
                            data-target="#editProfilePicModal"><em
                                class="icon ni ni-pen"></em> <b>Edit</b></a>
                        @endif
                    </div>
                </div>
                @endif
            </div>
            <div class="card-footer bg-white border-top" style="padding-left: 0px;padding-right: 0px;;">
                <div><b>{{$user->name}} @if($user->id == auth()->user()->id)
                        <a href="#" data-toggle="modal"
                            data-target="#editNameModal"><em
                                class="icon ni ni-pen"></em> <b>Edit</b></a>
                        @endif</b></div>
                        <div>{{$user->bio}}</div>
                <hr>
                <div><em
                        class="icon ni ni-map-pin"></em>{{$user->location_name}} @if($user->id == auth()->user()->id)
                    <a href="{{route("user.profile.updateLocation")}}"><em class="icon ni ni-pen"></em>
                        <b>Edit</b></a>
                    @endif
                </div>
            </div>
        </div>

        <br>

        <form action="{{route("user.updateProfileInformation", ["module" => "display-name"])}}" method="POST"
            enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="modal modal-lg fade" tabindex="-1" id="editNameModal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                            <em class="icon ni ni-cross"></em>
                        </a>
                        <div class="modal-header">
                            <h5 class="modal-title">Update your name and bio</h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-control-wrajhgp">
                                        <label for="name" style="float: left !important;">Name</label>
                                        <input type="text" class="form-control form-control-lg"
                                            id="name" name="name"
                                            placeholder="Enter your new name" value="{{$user->name}}">
                                    </div>
                                    <div class="form-control-wrajhgp mt-3">
                                        <label for="bio" style="float: left !important;">Bio</label>
                                        <textarea class="form-control form-control-lg"
                                            id="bio" name="bio"
                                            placeholder="Enter your new bio" >{{$user->bio}}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-white text-right" style="float: right !important;">
                            <button style="float: right;" class="btn btn-outline-primary"><b>Save Changes</b>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <form action="{{route("user.updateProfileInformation", ["module" => "profile-picture"])}}" method="POST"
            enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="modal modal-lg fade" tabindex="-1" id="editProfilePicModal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                            <em class="icon ni ni-cross"></em>
                        </a>
                        <div class="modal-header">
                            <h5 class="modal-title">Update your profile picture</h5>
                        </div>
                        <div class="modal-body">
                            <p style="text-align: left;">Choose a picture you want to use as your profile
                                picture</p>
                            <div class="row">
                                <div class="col-md-12">
                                    @if($user->profile_picture)
                                    <div class="text-center">
                                        <img src="{{$user->profile_picture}}" alt="" style="height: 220px;">
                                    </div>
                                    @else
                                    <div style="margin-top: 20px;margin-bottom: 20px;">
                                        <em class="icon ni ni-user" style="font-size: 145px"></em>
                                    </div>
                                    @endif

                                    <div class="form-control-wrajhgp">
                                        <input type="file" class="form-control form-control-lg"
                                            id="profile_picture" name="profile_picture"
                                            accept="image/png, image/gif, image/jpeg, image/jpg"
                                            placeholder="Choose a picture">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-white text-right" style="text-align: right !important;">
                            <button style="float: right;" class="btn btn-outline-primary"><b>Save Changes</b>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-8">
        <div class="row">
            <div class="col-md-6">
                <div id="languagesInterestsBox">
                    <div class="card card-bordered bg-white">
                        <div class="card-header bg-white border-bottom">
                            <b>Skills & Interests @if($user->id == auth()->user()->id)
                                <a
                                    href="{{route("user.profile.skills_and_interest")}}"><em
                                        class="icon ni ni-pen"></em> <b>Edit</b></a>
                                @endif</b>
                        </div>
                        <div class="card-body" style="height: 125px;padding-top: 10px;padding-bottom: 10px;">
                            @foreach($skills as $skill)
                            <span
                                class="badge badge-md badge-dim badge-pill badge-outline-dark"
                                style="margin-bottom: 5px;">{{$skill->skill->name}}</span>
                            @endforeach
                        </div>
                    </div>
                    <br>
                    <div class="card card-bordered bg-white" style="margin-top: 5px;">
                        <div class="card-header bg-white border-bottom">
                            <b>Languages @if($user->id == auth()->user()->id)
                                <a
                                    href="{{route("user.profile.languages")}}"><em class="icon ni ni-pen"></em>
                                    <b>Edit</b></a>
                                @endif
                            </b>
                        </div>
                        <div class="card-body" style="height: 125px;padding-top: 10px;padding-bottom: 10px;">
                            @foreach($languages as $language)
                            <span
                                class="badge badge-md badge-dim badge-pill badge-outline-dark"
                                style="margin-bottom: 5px;">{{$language->language->name}}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
                <br>
            </div>
            <div class="col-md-6">
                <div class="card card-bordered">
                    <div class="card-header bg-white border-bottom"><b>Analytics</b></div>
                    <div class="card-body" style="min-height: 325px;">
                        <div class="row" style="margin-top: 65px;">
                            <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6">
                                <div class="text-center"><small><b>Number of Jobs</b></small></div>
                                <div class="text-center text-primary tex"
                                    style="font-size: 28px;">{{$number_of_jobs}}</div>
                            </div>
                            <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6">
                                <div class="text-center"><small><b>Number of Activities</b></small></div>
                                <div class="text-center text-success"
                                    style="font-size: 28px;">{{$number_of_activities}}</div>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 30px;">
                            <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6">
                                <div class="text-center"><small><b>Average Rating</b></small></div>
                                <div class="text-center text-primary tex"
                                    style="font-size: 28px;">{{$user->rating}}</div>
                            </div>
                            <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6">
                                <div class="text-center"><small><b>Volunteer Hours</b></small></div>
                                <div class="text-center text-success"
                                    style="font-size: 28px;">{{$volunteer_hours}}</div>
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
            <div class="card-body border-top" style="padding-top: 15px;padding-bottom: 10px;">
                <a id="vorkHistoryLink" href="javascript:void(0)" class="text-muted">VORK History <span
                        style="float: right;"><em class="icon ni ni-chevron-right"
                            style="font-size: 22px;"></em></span></a>
            </div>
            <div class="card-body border-top" style="padding-top: 15px;padding-bottom: 10px;">
                <a id="jobExperienceLink" href="javascript:void(0)" class="text-muted">Job Experience
                    <span
                        style="float: right;"><em class="icon ni ni-chevron-right"
                            style="font-size: 22px;"></em></span></a>
            </div>
            <div class="card-body border-top" style="padding-top: 15px;padding-bottom: 10px;">
                <a id="certificationsLink" href="javascript:void(0)" class="text-muted">Certification & Training <span
                        style="float: right;"><em class="icon ni ni-chevron-right"
                            style="font-size: 22px;"></em></span></a>
            </div>
            <div class="card-body border-top" style="padding-top: 15px;padding-bottom: 10px;">
                <a id="educationLink" href="javascript:void(0)" class="text-muted">Education <span
                        style="float: right;"><em
                            class="icon ni ni-chevron-right" style="font-size: 22px;"></em></span></a>
            </div>
        </div>
        <br>
    </div>
    <div class="col-md-8">
        <div id="infoContentBox">
            <div id="emptyState">
                <div class="text-center" style="margin-top: 10px;">
                    <img src="{{asset('assets/html-template/src/images/details.svg')}}" alt=""
                        style="height: 200px; width: 200px;">
                    <p style="color: #777;">Click on link you get information.</p>
                </div>
            </div>
            <div id="loadingState">
                <p class="text-center" style="color: #777;margin-top: 25px;">Loading...</p>
            </div>
            <div id="educationBox">
                <div class="card card-bordered">
                    <div class="card-header bg-white border-bottom flex flex-row justify-between items-center">
                        <div class="font-weight-bolder mt-1">Education</div>
                        @if($user->id == auth()->user()->id)
                        <div><a href="{{route("user.educationHistory.create", ["user" => auth()->id()])}}"
                                style="float: right"><em style="font-size: 28px;"
                                    class="ni ni-plus-circle"></em></a></div>
                        @endif
                    </div>
                    <div class="card-body">
                        @if(count($user->educationHistory) < 1)
                            <div class="text-center m-5">
                            <img src="{{asset('assets/html-template/src/images/nd.svg')}}"
                                style="height: 200px; width: 200px" alt="">
                            <p style="color: #777;">No data found.</p>
                    </div>
                    @endif
                    <ul class="timeline" style="margin-left: -20px;">
                        @foreach($user->educationHistory->sortByDesc("start_date") as $educationHistory)
                        <li>
                            <div style="margin-left: 30px;padding-bottom: 30px;">
                                <div>{{date("F Y", strtotime($educationHistory->start_date))}}
                                    - {{$educationHistory->end_date ? date("F Y", strtotime($educationHistory->end_date)) : "Ongoing"}}</div>
                                <div class="text-dark">{{$educationHistory->programme}}</div>
                                @if($educationHistory->specialty)
                                <div class="text-primary">Specialty: {{$educationHistory->specialty}}</div>
                                @endif
                                <div>{{$educationHistory->institution}}</div>
                                <hr>
                                <div style="float: right;">
                                    @if($educationHistory->certificate_link)
                                    <a target="_blank" href="{{$educationHistory->certificate_link}}" onclick="return confirm('Are you sure?')"
                                        class="btn btn-outline-primary">View Certificate</a>
                                    @endif
                                    @if($user->id == auth()->user()->id)
                                    <a href="{{route("user.educationHistory.remove", ["id" => $educationHistory->id])}}"
                                        onclick="return confirm('Are you sure?')"
                                        class="btn btn-outline-danger"><em class="ni ni-trash"></em>
                                        Remove</a>
                                    <a href="{{route("user.educationHistory.edit", ["id" => $educationHistory->id])}}"
                                        class="btn btn-outline-primary">Make Changes</a>
                                    @endif
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div id="jobExperienceBox">
            <div class="card card-bordered">
                <div class="card-header bg-white border-bottom flex flex-row justify-between items-center">
                    <div class="font-weight-bolder mt-1">Job Experience</div>
                    @if($user->id == auth()->user()->id)
                    <div><a href="{{route("user.outsideVorkJobHistory.create", ["user" => auth()->id()])}}"
                            style="float: right"><em style="font-size: 28px;"
                                class="ni ni-plus-circle"></em></a></div>
                    @endif
                </div>
                <div class="card-body">
                    @if(count($user->outsideVorkJobs) < 1)
                        <div class="text-center m-5">
                        <img src="{{asset('assets/html-template/src/images/nd.svg')}}"
                            style="height: 200px; width: 200px" alt="">
                        <p style="color: #777;">No data found.</p>
                </div>
                @endif
                <ul class="timeline" style="margin-left: -20px;">
                    @foreach($user->outsideVorkJobs->sortByDesc("start_date") as $outsideVorkJob)
                    <li>
                        <div style="margin-left: 30px;padding-bottom: 30px;">
                            <div>{{date("F Y", strtotime($outsideVorkJob->start_date))}}
                                - {{$outsideVorkJob->end_date ? date("F Y", strtotime($outsideVorkJob->end_date)) : "Ongoing"}}</div>
                            <div class="text-dark">{{$outsideVorkJob->role}}</div>
                            <div>{{$outsideVorkJob->employer}}</div>

                            <div class="text-dark mt-1" style="text-decoration: underline">
                                Responsibilities
                            </div>
                            <div class="summernote-description">
                                {!! $outsideVorkJob->responsibilities !!}
                            </div>
                            <div class="text-dark mt-1" style="text-decoration: underline">
                                Achievements
                            </div>
                            <div class="summernote-description">
                                {!! $outsideVorkJob->achievements !!}
                            </div>
                            <div class="mt-1"><span
                                    class="text-dark ">Reference</span>: <a href="javascript:void(0)" onclick="showReferenceDetails('{{json_decode($outsideVorkJob->reference)->name}}')"> {{json_decode($outsideVorkJob->reference)->name}}</a>
                                @if($outsideVorkJob->reference_verification_sent_at !== null && $outsideVorkJob->reference_verified_at === null)
                                <span class="badge badge-info"><em class="ni ni-loader"></em>Pending</span>
                                @elseif($outsideVorkJob->reference_verified_at !== null)
                                <span class="badge badge-success"><em class="ni ni-check"></em>Verified</span>
                                @else
                                <span class="badge badge-warning"><em class="ni ni-alert"></em>Unverified</span>
                                @endif

                                @php
                                $reference = json_decode($outsideVorkJob->reference)
                                @endphp
                                <div class="alert alert-primary mt-3 hidden" style="/* From https://css.glass */
background: rgba(255, 255, 255, 0.2);
border-radius: 16px;
box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
backdrop-filter: blur(5px);
-webkit-backdrop-filter: blur(5px);
border: 1px solid rgba(255, 255, 255, 0.3);" id="referenceBox-{{preg_replace('/\s+/', '', $reference->name)}}">
                                    <h4>{{$reference->name}}</h4>

                                    @if(property_exists($reference, "email"))
                                    <p><em class="ni ni-emails"></em> {{$reference->email}}</p>
                                    @endif
                                    @if(property_exists($reference, "phone_number"))
                                    <p><em class="ni ni-mobile"></em> {{$reference->phone_number}}</p>
                                    @endif
                                    <div class="text-right">
                                        <a href="javascript:void()" class="btn btn-outline-secondary btn-l" type="button" onclick="hideReferenceDetails('{{json_decode($outsideVorkJob->reference)->name}}')"><b>Close</b></a>
                                    </div>
                                </div>
                                <div>


                                </div>
                            </div>
                            @if($user->id == auth()->user()->id)
                            <hr>
                            <div style="float: right;">
                                <a href="{{route("user.outsideVorkJobHistory.remove", ["id" => $outsideVorkJob->id])}}"
                                    onclick="return confirm('Are you sure?')"
                                    class="btn btn-outline-danger"><em class="ni ni-trash"></em>
                                    Remove</a>

                                @if($outsideVorkJob->reference_verified_at === null)
                                <a href="{{route("user.outsideVorkJobHistory.verifyReference", ["id" => $outsideVorkJob->id])}}"
                                    class="btn btn-outline-primary">Verify Reference</a>

                                <a href="{{route("user.outsideVorkJobHistory.edit", ["id" => $outsideVorkJob->id])}}"
                                    class="btn btn-outline-primary">Make Changes</a>
                                @endif

                            </div>
                            @endif
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <div id="certificationsBox">
        <div class="card card-bordered">
            <div class="card-header bg-white border-bottom flex flex-row justify-between items-center">
                <div class="font-weight-bolder mt-1">Certification & Training</div>
                @if($user->id == auth()->user()->id)
                <div><a href="{{route("user.certificationAndTrainingHistory.create", ["user" => auth()->id()])}}"
                        style="float: right"><em style="font-size: 28px;"
                            class="ni ni-plus-circle"></em></a></div>
                @endif
            </div>
            <div class="card-body">
                @if(count($user->certificationsAndTrainings) < 1)
                    <div class="text-center m-5">
                    <img src="{{asset('assets/html-template/src/images/nd.svg')}}"
                        style="height: 200px; width: 200px" alt="">
                    <p style="color: #777;">No data found.</p>
            </div>
            @endif
            <ul class="timeline" style="margin-left: -20px;">
                @foreach($user->certificationsAndTrainings->sortByDesc("start_date") as $certificationAndTraining)
                <li>
                    <div style="margin-left: 30px;padding-bottom: 30px;">
                        <div>{{date("F Y", strtotime($certificationAndTraining->start_date))}}
                            - {{$certificationAndTraining->end_date ? date("F Y", strtotime($certificationAndTraining->end_date)) : "Ongoing"}}</div>
                        <div class="text-dark">{{$certificationAndTraining->programme}}</div>
                        <div>{{$certificationAndTraining->institution}}</div>
                        @if($certificationAndTraining->training_hours)
                        <div>Training hours: {{$certificationAndTraining->training_hours}}</div>
                        @endif
                        <hr>
                        <div style="float: right;">
                            @if($certificationAndTraining->certificate_link)
                            <a target="_blank" href="{{$certificationAndTraining->certificate_link}}" onclick="return confirm('Are you sure?')"
                                class="btn btn-outline-primary">View Certificate</a>
                            @endif
                            @if($user->id == auth()->user()->id)
                            <a href="{{route("user.certificationAndTrainingHistory.remove", ["id" => $certificationAndTraining->id])}}"
                                onclick="return confirm('Are you sure?')"
                                class="btn btn-outline-danger"><em class="ni ni-trash"></em>
                                Remove</a>

                            <a href="{{route("user.certificationAndTrainingHistory.edit", ["id" => $certificationAndTraining->id])}}"
                                class="btn btn-outline-primary">Make Changes</a>
                            @endif
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
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
                                <th>Period</th>
                                <th>Job</th>
                                <th>Employer Feedback</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($number_of_jobs <= 0)
                                <tr>
                                <td colspan="3">
                                    <p class="text-center">You have no completed jobs at the
                                        moment</p>
                                </td>
                                </tr>
                                @else
                                @foreach($job_history as $work)
                                @if($work->job_post && $work->job_post->type != "VOLUNTEER")
                                <tr>
                                    <td>{{($work->period) ? $work->period : 'N/A'}}</td>
                                    <td>{{$work->job_post->category}}</td>
                                    <td>{{($work->rating_and_reviews) ? $work->rating_and_reviews->feedback_message : 'N/A'}}</td>
                                </tr>
                                @endif
                                @endforeach
                                @endif
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
    $("#emptyState").hide()
    $("#vorkHistoryBox").show()
    $("#jobExperienceBox").hide()
    $("#educationBox").hide()
    $("#certificationsBox").hide()
    $(".referenceBox").hide()

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
    //     }, 300)
    // })

    $("#educationLink").on("click", function() {
        clearContentBox()
        $("#loadingState").show()
        $("#emptyState").hide()
        $("#vorkHistoryBox").hide()
        $("#jobExperienceBox").hide()
        // $("#languagesInterestsBox").hide()
        $("#certificationsBox").hide()
        setTimeout(function() {
            $("#loadingState").hide()
            $("#educationBox").show()
        }, 300)
    })

    $("#jobExperienceLink").on("click", function() {
        clearContentBox()
        $("#loadingState").show()
        $("#emptyState").hide()
        $("#vorkHistoryBox").hide()
        $("#jobExperienceBox").hide()
        // $("#languagesInterestsBox").hide()
        $("#certificationsBox").hide()
        setTimeout(function() {
            $("#loadingState").hide()
            $("#jobExperienceBox").show()
        }, 300)
    })

    $("#vorkHistoryLink").on("click", function() {
        clearContentBox()
        $("#loadingState").show()
        $("#emptyState").hide()
        $("#vorkHistoryBox").hide()
        $("#jobExperienceBox").hide()
        // $("#languagesInterestsBox").hide()
        $("#certificationsBox").hide()
        setTimeout(function() {
            $("#loadingState").hide()
            $("#vorkHistoryBox").show()
        }, 300)
    })

    $("#certificationsLink").on("click", function() {
        clearContentBox()
        $("#loadingState").show()
        $("#emptyState").hide()
        $("#vorkHistoryBox").hide()
        $("#jobExperienceBox").hide()
        // $("#languagesInterestsBox").hide()
        $("#certificationsBox").hide()
        setTimeout(function() {
            $("#loadingState").hide()
            $("#certificationsBox").show()
        }, 300)
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

    function displayImageAttachment(event) {
        $("#imageInputTrigger").hide()
        $("#imageAttachment").show()
        var output = document.getElementById('imageAttachment');
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = function() {
            URL.revokeObjectURL(output.src) // free memory
        }
    }

    const showReferenceDetails = (referenceName) => {
        const referenceBox = $(`#referenceBox-${referenceName.replace(/\s/g, '')}`)
        referenceBox.removeClass("hidden")
    }

    const hideReferenceDetails = (referenceName) => {
        const referenceBox = $(`#referenceBox-${referenceName.replace(/\s/g, '')}`)
        referenceBox.addClass("hidden")
    }
</script>
@endsection