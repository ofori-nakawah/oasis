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
                        <span style="font-size: 10px;"><b>Post is {{$post->status}}</b></span>
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
                                        <div><a href="{{route('user.profile', ['user_id' => $applicant->user->id])}}"
                                                class="font-italic">See profile</a></div>
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

                                <form action="{{route('user.posts.close')}}" method="POST">
                                    {{csrf_field()}}
                                    <input type="hidden" name="job_post_id" value="{{$post->id}}">
                                    <input type="hidden" name="job_type" value="{{$post->type}}">
                                    <input type="hidden" name="user_id" value="{{$post->confirmed_applicant_id}}">
                                    <div class="modal modal-lg fade" tabindex="-1" id="closeJobModal">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                                                    <em class="icon ni ni-cross"></em>
                                                </a>
                                                <div class="modal-header">
                                                    <h5 class="modal-title"><b>Close Job</b></h5>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <p class="text-muted">Complete below information to close
                                                                job.</p>
                                                        </div>
                                                    </div>

                                                    <div class="row" style="margin-top: 15px;">
                                                        <div class="col-md-9 col-xs-9 col-sm-9">
                                                            <label style="font-size: 16px; margin-top: 10px;"><b>Final
                                                                    Payment (GHS)</b></label>
                                                        </div>
                                                        <div class="col-md-3 col-xs-3 col-sm-3">
                                                            <input type="number" class="form-control form-control-lg"
                                                                   required value="{{$post->min_budget}}">
                                                        </div>
                                                    </div>

                                                    <hr>

                                                    <p style="font-size: 16px;"><b>Rating</b></p>
                                                    <div class="row">
                                                        <div class="col-md-4 col-sm-4">
                                                            <div class="text-color-gray" style="font-size: 10px;">
                                                                Expertise
                                                            </div>
                                                        </div>
                                                        <div class="col-md-8 col-sm-8">
                                                            <div class="stars">
                                                                <select class="star-rating" name="expertise_rating"
                                                                        required id="quickJobExpertise">
                                                                    <option value=""></option>
                                                                    <option value="5">Excellent</option>
                                                                    <option value="4">Very Good</option>
                                                                    <option value="3">Average</option>
                                                                    <option value="2">Fair</option>
                                                                    <option value="1">Poor</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <br>

                                                    <div class="row">
                                                        <div class="col-md-4 col-sm-4">
                                                            <div class="text-color-gray" style="font-size: 10px;">Work
                                                                Ethic
                                                            </div>
                                                        </div>
                                                        <div class="col-md-8 col-sm-8">
                                                            <div class="stars">
                                                                <select class="star-rating" name="work_ethic_rating"
                                                                        required id="quickJobWorkEthic">
                                                                    <option value=""></option>
                                                                    <option value="5">Excellent</option>
                                                                    <option value="4">Very Good</option>
                                                                    <option value="3">Average</option>
                                                                    <option value="2">Fair</option>
                                                                    <option value="1">Poor</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <div class="row">
                                                        <div class="col-md-4 col-sm-4">
                                                            <div class="text-color-gray" style="font-size: 10px;">
                                                                Professionalism
                                                            </div>
                                                        </div>
                                                        <div class="col-md-8 col-sm-8">
                                                            <div class="stars">
                                                                <select class="star-rating"
                                                                        name="professionalism_rating" required
                                                                        id="quickJobProfessionalism">
                                                                    <option value=""></option>
                                                                    <option value="5">Excellent</option>
                                                                    <option value="4">Very Good</option>
                                                                    <option value="3">Average</option>
                                                                    <option value="2">Fair</option>
                                                                    <option value="1">Poor</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <br>

                                                    <div class="row">
                                                        <div class="col-md-4 col-sm-4">
                                                            <div class="text-color-gray" style="font-size: 10px;">
                                                                Customer Service
                                                            </div>
                                                        </div>
                                                        <div class="col-md-8 col-sm-8">
                                                            <div class="stars">
                                                                <select class="star-rating"
                                                                        name="customer_service_rating" required
                                                                        id="quickJobCustomerService">
                                                                    <option value=""></option>
                                                                    <option value="5">Excellent</option>
                                                                    <option value="4">Very Good</option>
                                                                    <option value="3">Average</option>
                                                                    <option value="2">Fair</option>
                                                                    <option value="1">Poor</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <br>

                                                    <p style="font-size: 16px;"><b>Review or feedback message</b></p>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="input-group1 mb-3">
                                                                <textarea type="tel"
                                                                          class="form-control form-control-lg @error('feedback_message') is-invalid @enderror"
                                                                          placeholder="Kindly share your review or feedback of the job done."
                                                                          name="feedback_message"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="modal-footer bg-white text-right"
                                                     style="float: right !important;">
                                                    <button style="float: right;" class="btn btn-outline-primary"><b>Continue</b>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            @endforeach
                        </div>
                    </div>
                @endif

                <br>
                @if($post->status != "closed")
                    <div class="pull-right">
                        <a href="#" class="btn btn-outline-danger btn-lg" data-toggle="modal"
                           data-target="#closeJobModal" style="float: right"><b>Close Job</b></a>
                    </div>
                @endif
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
                                        <div><a href="{{route('user.profile', ['user_id' => $applicant->user->id])}}"
                                                class="font-italic">See profile</a></div>
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
                                            @if($post->status == "closed")
                                                <div class="text-muted">VH: <b>{{$applicant->volunteer_hours}}</b></div>
                                            @endif
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

                <form action="{{route('user.posts.close')}}" method="POST">
                    {{csrf_field()}}
                    <input type="hidden" name="job_post_id" value="{{$post->id}}">
                    <input type="hidden" name="job_type" value="{{$post->type}}">
                    <div class="modal modal-lg fade" tabindex="-1" id="closeVolunteerModal">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                                    <em class="icon ni ni-cross"></em>
                                </a>
                                <div class="modal-header">
                                    <h5 class="modal-title"><b>Close Activity</b></h5>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p class="text-muted">Confirm volunteer hours for all participants.</p>
                                        </div>
                                    </div>

                                    <br>

                                    @foreach($post->applications->where("status", "confirmed") as $applicant)
                                        <div class="row">
                                            <div class="col-md-2 text-center">
                                                @if($applicant->user->image_link)
                                                    <img src="{{$applicant->user->image_link}}" alt="">
                                                @else
                                                    <em class="icon ni ni-user" style="font-size: 40px;"></em>
                                                @endif
                                            </div>
                                            <div class="col-md-7">
                                                <div><b>{{$applicant->user->name}}</b></div>
                                                <div class="text-muted"><em
                                                        class="icon ni ni-map-pin text-muted"></em> {{$applicant->user->location_name}}
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" name="volunteer_details[]" class="form-control form-control-lg" required
                                                       value="{{$post->volunteer_hours}}">
                                                <input type="hidden" name="user_id[]" value="{{$applicant->user->id}}">
                                            </div>
                                        </div>
                                    @endforeach

                                </div>
                                <div class="modal-footer bg-white text-right" style="float: right !important;">
                                    <button style="float: right;" class="btn btn-outline-primary"><b>Continue</b>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                @if($post->status != "closed")
                    <div class="pull-right">
                        <a href="#" class="btn btn-outline-danger" style="float: right" data-toggle="modal"
                           data-target="#closeVolunteerModal">Close Activity</a>
                    </div>
                @endif
            @endif

        </div>
    </div>
@endsection
