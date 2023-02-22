@extends("layouts.master")

@section('title')
    Posts
@endsection

@section("content")
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title"><em class="icon ni ni-edit-alt"></em> Posts </h3>
                <div class="nk-block-des text-soft">
                    <p class="hide-mb-sm hide-mb-xs md">
                    <nav>
                        <ul class="breadcrumb breadcrumb-arrow">
                            <li class="breadcrumb-item"><a href="#">My Posts</a></li>
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
            @foreach($posts as $_post)
                <div class="card card-bordered">
                    <div class="card-header bg-white" style="border-bottom: 1px solid #dbdfea;"><b>{{$_post->type}}
                            <span style="float: right">{{$_post->created_at}}</span></b></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="title"
                                     style="font-size: 10px;color: #777;">{{($_post->type !== 'VOLUNTEER') ? 'Category' : 'Activity Name'}}</div>
                                <div class="issuer">
                                    <b>{{($_post->type !== 'VOLUNTEER') ? $_post->category : $_post->name}}</b></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-right bg-white" style="border-top: 1px solid #dbdfea;">
                        <a href="{{route('user.posts.edit', ['uuid' => $_post->id])}}" class="btn btn-outline-warning">Edit</a>
                        <a href="{{route('user.posts.show', ['uuid' => $_post->id])}}"
                           class="btn btn-outline-secondary">Status</a>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="col-md-7">
            @include("utilities.alerts.alerts")

            @if($post->type === "QUICK_JOB")
                <div class="card card-bordered">
                    <div class="card-header bg-white" style="border-bottom: 1px solid #dbdfea;"><b>{{$post->type}} <span
                                style="float: right">{{$post->date}} {{$post->time}}</span></b></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="title"
                                     style="font-size: 10px;color: #777;">{{($post->type !== 'VOLUNTEER') ? 'Category' : 'Activity Name'}}</div>
                                <div class="issuer">
                                    <b>{{($post->type !== 'VOLUNTEER') ? $post->category : $post->name}}</b></div>
                                <div>{{$post->number_of_participants_applied}} applicants shortlisted</div>
                                @if($post->number_of_participants_confirmed > 0)
                                    <div>{{$post->number_of_participants_confirmed}} selected</div> @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top">
                        <span
                            style="font-size: 10px;float: right;"><b>Published {{$post->created_at->diffForHumans()}}</b></span>
                    </div>
                </div>

                <div class="card card-bordered">
                    <div class="card-header bg-white border-bottom"><b>Shortlisted Applicants</b></div>
                    <div class="card-body">
                        @if(count($post->applications) <= 0)
                                <div class="text-center">
                                    <img src="{{asset('assets/html-template/src/images/n_a.svg')}}" alt=""
                                         style="height: 120px; width: 120px;">
                                    <p class="text-muted">There are no applicants yet</p>
                                </div>
                        @else
                            @foreach($post->applications as $applicant)
                                <div class="row">
                                    <div class="col-md-3 text-center">
                                        @if($applicant->user->image_link)
                                            <img src="{{$applicant->user->image_link}}" alt="">
                                        @else
                                            <em class="icon ni ni-user" style="font-size: 80px;"></em>
                                        @endif
                                    </div>
                                    <div class="col-md-7">
                                        <div><b>{{$applicant->user->name}}</b></div>
                                        <div class="text-muted"><em
                                                class="icon ni ni-map-pin text-muted"></em> {{$applicant->user->location_name}}
                                        </div>
                                        <div><em
                                                class="icon ni ni-star-fill text-warning"></em> {{$applicant->user->rating}}
                                        </div>
                                        <div><a href="{{route('user.profile', ['user_id' => $applicant->user->id])}}" class="font-italic">See profile</a></div>
                                    </div>
                                    <div class="col-md-2">
                                        @if($post->is_job_applicant_confirmed != 1)
                                            <a href="{{route('user.posts.confirm_decline_applicant', ['application_id' => $applicant->id, 'action' => 'confirm'])}}"
                                               onclick="return confirm('Are you sure?')"><em
                                                    class="icon ni ni-plus-circle text-success"
                                                    style="font-size: 30px;float: right;"></em></a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                @if($post->is_job_applicant_confirmed == 1)
                    <div class="card card-bordered">
                        <div class="card-header bg-white border-bottom"><b>Selected Applicant</b></div>
                        <div class="card-body">
                            @foreach($post->applications as $applicant)
                                @if($applicant->user->id == $post->confirmed_applicant_id)
                                    <div class="row">
                                        <div class="col-md-3 text-center">
                                            @if($applicant->user->image_link)
                                                <img src="{{$applicant->user->image_link}}" alt="">
                                            @else
                                                <em class="icon ni ni-user" style="font-size: 80px;"></em>
                                            @endif
                                        </div>
                                        <div class="col-md-7">
                                            <div><b>{{$applicant->user->name}}</b></div>
                                            <div class="text-muted"><em
                                                    class="icon ni ni-map-pin text-muted"></em> {{$applicant->user->location_name}}
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <a href="" onclick="return confirm('Are you sure?')"><em
                                                    class="icon ni ni-mobile"
                                                    style="font-size: 30px;float: right;"></em></a>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <br>

                <div class="pull-right">
                    <a href="#" class="btn btn-outline-danger" style="float: right">Close Job</a>
                </div>
            @endif

            {{------------------------------------ VOLUNTEER -----------------------------------------}}

            @if($post->type === "VOLUNTEER")
                <div class="card card-bordered">
                    <div class="card-header bg-white" style="border-bottom: 1px solid #dbdfea;"><b>{{$post->type}} <span
                                style="float: right">{{$post->date}} {{$post->time}}</span></b></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="title"
                                     style="font-size: 10px;color: #777;">{{($post->type !== 'VOLUNTEER') ? 'Category' : 'Activity Name'}}</div>
                                <div class="issuer">
                                    <b>{{($post->type !== 'VOLUNTEER') ? $post->category : $post->name}}</b></div>
                                <div>{{$post->number_of_participants_applied}} applicants applied</div>
                                <div>{{$post->number_of_participants_confirmed}} confirmed</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top">
                        <span
                            style="font-size: 10px;float: right;"><b>Published {{$post->created_at->diffForHumans()}}</b></span>
                    </div>
                </div>

                <div class="card card-bordered">
                    <div class="card-header bg-white border-bottom"><b>Applied Participants</b></div>
                    <div class="card-body">
                        @if(count($post->applications) <= 0)
                                <div class="text-center">
                                    <img src="{{asset('assets/html-template/src/images/n_a.svg')}}" alt=""
                                         style="height: 120px; width: 120px;">
                                    <p class="text-muted">No participants have applied</p>
                                </div>
                        @else
                            @foreach($post->applications as $applicant)
                                <div class="row">
                                    <div class="col-md-3 text-center">
                                        @if($applicant->user->image_link)
                                            <img src="{{$applicant->user->image_link}}" alt="">
                                        @else
                                            <em class="icon ni ni-user" style="font-size: 80px;"></em>
                                        @endif
                                    </div>
                                    <div class="col-md-7">
                                        <div><b>{{$applicant->user->name}}</b></div>
                                        <div class="text-muted"><em
                                                class="icon ni ni-map-pin text-muted"></em> {{$applicant->user->location_name}}
                                        </div>
                                        <div><em
                                                class="icon ni ni-star-fill text-warning"></em> {{$applicant->user->rating}}
                                        </div>
                                        <div><a href="{{route('user.profile', ['user_id' => $applicant->user->id])}}" class="font-italic">See profile</a></div>
                                    </div>
                                    <div class="col-md-2">
                                        @if ($applicant->status != "confirmed")
                                        <a href="{{route('user.posts.confirm_decline_applicant', ['application_id' => $applicant->id, 'action' => 'decline'])}}"
                                           onclick="return confirm('Are you sure?')"><em
                                                class="icon ni ni-cross-circle text-danger"
                                                style="font-size: 30px;float: right;"></em></a>

                                        <a href="{{route('user.posts.confirm_decline_applicant', ['application_id' => $applicant->id, 'action' => 'confirm'])}}"
                                           onclick="return confirm('Are you sure?')"><em
                                                class="icon ni ni-plus-circle text-success"
                                                style="font-size: 30px;float: right;"></em></a>
                                            @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="card card-bordered">
                    <div class="card-header bg-white border-bottom"><b>Confirmed Participants</b></div>
                    <div class="card-body">
                        @if(count($post->applications->where("status", "confirmed")) <= 0)
                            <div class="text-center">
                                <img src="{{asset('assets/html-template/src/images/n_a.svg')}}" alt=""
                                     style="height: 120px; width: 120px;">
                                <p class="text-muted">No participants have been confirmed</p>
                            </div>
                        @else
                            @foreach($post->applications as $applicant)
                                @if($applicant->status == "confirmed")
                                    <div class="row">
                                        <div class="col-md-3 text-center">
                                            @if($applicant->user->image_link)
                                                <img src="{{$applicant->user->image_link}}" alt="">
                                            @else
                                                <em class="icon ni ni-user" style="font-size: 80px;"></em>
                                            @endif
                                        </div>
                                        <div class="col-md-7">
                                            <div><b>{{$applicant->user->name}}</b></div>
                                            <div class="text-muted"><em
                                                    class="icon ni ni-map-pin text-muted"></em> {{$applicant->user->location_name}}
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <a href="" onclick="return confirm('Are you sure?')"><em
                                                    class="icon ni ni-mobile"
                                                    style="font-size: 30px;float: right;"></em></a>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
                <br>

                <div class="pull-right">
                    <a href="#" class="btn btn-outline-danger" style="float: right">Close Activity</a>
                </div>
            @endif

        </div>
    </div>
@endsection
