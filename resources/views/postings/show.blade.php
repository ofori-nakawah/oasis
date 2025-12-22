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
                            style="font-size: 10px;color: #777;">@if($_post->type === "VOLUNTEER")
                            Activity Name
                            @endif

                            @if($_post->type === "QUICK_JOB" || $_post->type === "P2P")
                            Category
                            @endif

                            @if($_post->type === "FIXED_TERM_JOB" || $_post->type === "PERMANENT_JOB")
                            Title
                            @endif</div>
                        <div class="issuer">
                            <b>@if($_post->type === "VOLUNTEER")
                                {{$_post->name}}
                                @endif

                                @if($_post->type === "QUICK_JOB" || $_post->type === "P2P")
                                {{$_post->category}}
                                @endif

                                @if($_post->type === "FIXED_TERM_JOB" || $_post->type === "PERMANENT_JOB")
                                {{$_post->title}}
                                @endif</b>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-right bg-white" style="border-top: 1px solid #dbdfea;">
                @if($_post->status !== "closed")
                <a href="#" onclick="shareLink()" style="float: left !important;margin-top: 5px;" data-toggle="modal"
                    data-target="#sharePostbModal-{{$_post->id}}"><em class="icon ni ni-link" style="font-size: 24px;"></em> </a>
                @endif
                <div class="modal modal-lg fade" tabindex="-1" id="sharePostbModal-{{$_post->id}}">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                                <em class="icon ni ni-cross"></em>
                            </a>
                            <div class="modal-header">
                                <h5 class="modal-title"><b>Share Post</b></h5>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <p style="float: left !important;">Copy post and share it with your family and friends on all platforms.</p>
                                        <input value="{{($_post->type === "VOLUNTEER") ? route("user.volunteerism.show", ["uuid" => $_post->id]) : route("user.quick_job.show", ["uuid" => $_post->id])}}" id="shareLink-{{$_post->id}}" type="text" readonly class="form-control">
                                        <br>
                                        <button class="btn btn-outline-primary copyLinkButton" onclick="copyLinkToClipboard(`{{$_post->id}}`)"><b>Copy</b></button>
                                        <span class="copyStatus"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if($_post->status === "closed")
                <span>Post Closed</span>
                @else
                <a href="{{route('user.posts.edit', ['uuid' => $_post->id])}}"
                    class="btn btn-outline-warning">Edit</a>
                @endif
                <a href="{{route('user.posts.show', ['uuid' => $_post->id])}}"
                    class="btn btn-outline-secondary">Status</a>
            </div>
        </div>
        @endforeach
    </div>
    <div class="col-md-7">
        @include("utilities.alerts.alerts")

        @if($post->type === "FIXED_TERM_JOB" || $post->type === "PERMANENT_JOB")
        <div class="card card-bordered">
            <div class="card-header bg-white" style="border-bottom: 1px solid #dbdfea;"><b>{{$post->type}} <span
                        style="float: right">{{$post->date}} {{$post->time}}</span></b></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="title"
                            style="font-size: 10px;color: #777;">Title</div>
                        <div class="issuer">
                            <b>{{$post->title}}</b>
                        </div>
                        <div>{{$post->number_of_participants_applied}} applicant(s)</div>
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

        @if($post->status !== "closed")
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
            <div class="text-center">
                <img src="{{asset('assets/html-template/src/images/n_a.svg')}}" alt=""
                    style="height: 120px; width: 120px;">
                <p class="text-muted">You have {{count($post->applications)}} applicant(s) for this job</p>
                <div><button class="btn btn-outline-primary" onclick="showShortlistedApplicants()">Generate Shortlist</button></div>
                <br>
                <div><a href="javascript:void(0)" onclick="showAllApplicants()">View all applicants</a></div>
            </div>

            <div id="shortlistedApplicants" class="mt-4">
                <p class="text-muted">Showing shortlisted applicants</p>

                @foreach($post->applications->take(4) as $applicant)
                <div class="row">
                    <div class="col-md-3 text-center">
                        @if($applicant->user->profile_picture)
                        <img src="{{$applicant->user->profile_picture}}" style="height: 100px;width: 100px;border: 1px solid #ccc; border-radius: 50%;" alt="">
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
                <br>
                @endforeach
            </div>

            <div id="allApplicants" class="mt-4">
                <p class="text-muted">Showing all applicants</p>
                @foreach($post->applications as $applicant)
                <div class="row">
                    <div class="col-md-3 text-center">
                        @if($applicant->user->profile_picture)
                        <img src="{{$applicant->user->profile_picture}}" style="height: 100px;width: 100px;border: 1px solid #ccc; border-radius: 50%;" alt="">
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
                <br>
                @endforeach
            </div>
            @endif
        </div>
    </div>
    @endif

    @if($post->is_job_applicant_confirmed == 1)
    <div class="card card-bordered">
        <div class="card-header bg-white border-bottom"><b>Selected Applicant</b></div>
        <div class="card-body">
            @foreach($post->applications as $applicant)
            @if($applicant->user->id == $post->confirmed_applicant_id)
            <div class="row">
                <div class="col-md-3 text-center">
                    @if($applicant->user->profile_picture)
                    <img src="{{$applicant->user->profile_picture}}" style="height: 100px;width: 100px;border: 1px solid #ccc; border-radius: 50%;" alt="">
                    @else
                    <em class="icon ni ni-user" style="font-size: 40px;"></em>
                    @endif
                </div>
                <div class="col-md-7">
                    <div><b>{{$applicant->user->name}}</b></div>
                    @if($post->status !== "closed")
                    <div class="text-muted"><em
                            class="icon ni ni-map-pin text-muted"></em> {{$applicant->user->location_name}}
                    </div>

                    @endif
                    @if($post->status === "closed")
                    <div class="text-muted">Income: GHS {{$post->final_payment_amount}}/Month
                    </div>
                    <div class="text-muted"> Term: {{$post->duration}} months
                    </div>

                    <br>

                    <div class="card bg-lighter">
                        <div class="card-body">
                            <div class="title" style="font-size: 10px;color: #777;">Start Date </div>
                            <div class="issuer"><b>{{($post->final_start_date) ? date ("l jS F Y", strtotime($post->final_start_date)) : 'N/A'}}</b></div>

                            <br>

                            <div class="title" style="font-size: 10px;color: #777;">End Date </div>
                            <div class="issuer"><b>{{($post->final_end_date) ? date ("l jS F Y", strtotime($post->final_end_date)) : 'N/A'}}</b></div>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="col-md-2">
                    <a href="#" onclick="shareLink()" data-toggle="modal"
                        data-target="#viewPhoneNumberModal-{{$applicant->id}}"><em
                            class="icon ni ni-mobile"
                            style="font-size: 30px;float: right;"></em></a>
                </div>
            </div>
            <div class="modal modal-lg fade" tabindex="-1" id="viewPhoneNumberModal-{{$applicant->id}}">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                            <em class="icon ni ni-cross"></em>
                        </a>
                        <div class="modal-header">
                            <h5 class="modal-title"><b>Call Applicant</b></h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <p style="float: left !important;">View phone number of applicant and make the call.</p>
                                    <input value="{{$applicant->user->phone_number}}" id="showPhoneNumber-{{$applicant->id}}" type="text" readonly class="form-control">
                                    <br>
                                    <button class="btn btn-outline-primary copyLinkButton" onclick="copyApplicantPhoneNumberToClipboard(`{{$applicant->id}}`)"><b>Copy</b></button>
                                    <span class="copyStatus"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            @endif

            <form action="{{route('user.posts.close')}}" method="POST">
                {{csrf_field()}}
                <input type="hidden" name="job_post_id" value="{{$post->id}}">
                <input type="hidden" name="job_type" value="{{$post->type}}">
                <input type="hidden" id="_start_date" name="start_date" value="{{$post->start_date}}">
                <input type="hidden" id="_end_date" name="end_date" value="{{$post->end_date}}">
                <input type="hidden" id="_monthly_payment" name="monthly_payment" value="{{$post->max_budget}}">
                <input type="hidden" name="user_id" value="{{$post->confirmed_applicant_id}}">
                <div class="modal fade" tabindex="-1" id="closeFixedTermJobModal">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            @php
                            $selectedApplicant = $post->applications->where("status", "confirmed")->first()
                            @endphp
                            <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                                <em class="icon ni ni-cross"></em>
                            </a>
                            <div class="modal-header">
                                <h5 class="modal-title"><b>Close Job</b></h5>
                            </div>
                            <div class="modal-body">
                                @if(!$selectedApplicant)
                                <div>
                                    <p>No applicant selected</p>
                                </div>
                                @else
                                <div id="closeFixedTermJobContent">
                                    <div>
                                        <div class="title" style="font-size: 10px;color: #777;">Selected Applicant</div>
                                        <div class="issuer"><b>{{$selectedApplicant->user->name}}</b></div>
                                    </div>

                                    <div>
                                        <div class="row">
                                            <div class="col-md-6 mt-3">
                                                <div class="title" style="font-size: 10px;color: #777;">Start Date <a href="javascript:void(0)" onclick="changeStartDateClicked()"><em class="icon ni ni-pen-fill" style="font-size: 16px;"></em></a></div>
                                                <div class="issuer"><b><span id="startDateValue">{{date ("l jS F Y", strtotime($post->date))}}</span></b></div>
                                            </div>
                                            @if($post->type !== "PERMANENT_JOB")
                                            <div class="col-md-6 mt-3">
                                                <div class="title" style="font-size: 10px;color: #777;">End Date <a href="javascript:void(0)"><em class="icon ni ni-pen-fill" style="font-size: 16px;" onclick="changeEndDateClicked()"></em></a></div>
                                                <div class="issuer"><b><span id="endDateValue">{{date ("l jS F Y", strtotime($post->end_date))}}</span></b>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <div class="title" style="font-size: 10px;color: #777;">Estimated Monthly Payment <a href="javascript:void(0)" onclick="changeMonthlyPaymentClicked()"><em class="icon ni ni-pen-fill" style="font-size: 16px;"></em></a></div>
                                        <div class="issuer"><b>GHS <span id="monthlyPaymentValue">{{$post->max_budget}}</span></b></div>
                                    </div>
                                </div>

                                <div id="changeStartDate">
                                    <label for="startDate">Change Start Date</label>
                                    <input type="date" id="startDateInput" class="form-control">
                                    <div class="text-right">
                                        <button type="button" class="btn btn-primary mt-3 ml-2" style="float: right;" onclick="submitStartDateChangeClicked()">Save Changes</button>
                                        <button type="button" class="btn btn-lighter mt-3" onclick="cancelStartDateChangeClicked()" style="float: right;">Cancel</button>
                                    </div>
                                </div>

                                @if($post->type !== "PERMANENT_JOB")
                                <div id="changeEndDate">
                                    <label for="endDate">Change End Date</label>
                                    <input type="date" id="endDateInput" class="form-control">
                                    <div class="text-right">
                                        <button type="button" class="btn btn-primary mt-3 ml-2" style="float: right;" onclick="submitEndDateChangeClicked()">Save Changes</button>
                                        <button type="button" class="btn btn-lighter mt-3" onclick="cancelEndDateChangeClicked()" style="float: right;">Cancel</button>
                                    </div>
                                </div>
                                @endif

                                <div id="changeMonthlyPayment">
                                    <label for="monthlyPayment">Change Estimated Monthly Payment</label>
                                    <input type="number" id="monthlyPaymentInput" class="form-control">
                                    <div class="text-right">
                                        <button type="button" class="btn btn-primary mt-3 ml-2" style="float: right;" onclick="submitMonthlyPaymentChangeClicked()">Save Changes</button>
                                        <button type="button" class="btn btn-lighter mt-3" onclick="cancelMonthlyPaymentChangeClicked()" style="float: right;">Cancel</button>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="modal-footer bg-white text-right" id="submitButton">
                                <button style="float: right" type="button" class="btn btn-outline-primary" onclick="closeFixedTermJobClicked()"><b>Continue</b>
                                </button>
                            </div>
                            <div class="modal-footer bg-white " id="closeFixedTermJobConfirmation">
                                <div>Are you sure you want to close job?</div>
                                <div class="text-right" style="padding-bottom: 15px;">
                                    <button type="submit" class="btn btn-primary mt-3 ml-2" style="float: right;">Close Job</button>
                                    <button type="button" class="btn btn-lighter mt-3" onclick="cancelFixedTermJobClose()" style="float: right;">Cancel</button>
                                </div>
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
            data-target="#closeFixedTermJobModal" style="float: right"><b>Close Job</b></a>
    </div>
    @endif
    @endif

    @if($post->type === "QUICK_JOB" || $post->type === "P2P")
    <div class="card card-bordered">
        <div class="card-header bg-white" style="border-bottom: 1px solid #dbdfea;"><b>{{$post->type}} <span
                    style="float: right">{{$post->date}} {{$post->time}}</span></b></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="title"
                        style="font-size: 10px;color: #777;">{{($post->type !== 'VOLUNTEER') ? 'Category' : 'Activity Name'}}</div>
                    <div class="issuer">
                        <b>{{($post->type !== 'VOLUNTEER') ? $post->category : $post->name}}</b>
                    </div>
                    <div>{{$post->number_of_participants_applied}} {{ $post->type === "P2P" ? " issued" : " applicants shortlisted"}}</div>
                    @php
                        $quotesSubmitted = $post->applications->filter(function($application) {
                            return !is_null($application->quote) && $application->quote != '';
                        })->count();
                    @endphp
                    <div>{{ $quotesSubmitted }} quote response{{ $quotesSubmitted === 1 ? '' : 's' }}</div>
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
                @if($applicant->user->profile_picture)
                <img src="{{$applicant->user->profile_picture}}" style="height: 100px;width: 100px;border: 1px solid #ccc; border-radius: 50%;" alt="">
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
                        @if ($post->type === "P2P")
                            <div class="border p-3 bg-gray-100 mt-2" style="border-radius: 18px;">
                                <div class="text-muted"> Quote: {{$applicant->quote}}</div>
                            <div class="text-muted"> Comments: {{$applicant->comments}}</div>
                            </div>
                        @endif
            </div>
            <div class="col-md-2">
                @if($post->is_job_applicant_confirmed != 1)
                    @if($post->type === "P2P" && !empty($applicant->quote))
                        {{-- For P2P jobs with quotes, trigger payment flow --}}
                        <a href="#" onclick="initP2PPayment('{{$post->id}}', '{{$applicant->id}}', 'initial'); return false;"
                            title="Approve Quote & Pay"><em
                                class="icon ni ni-plus-circle text-success"
                                style="font-size: 30px;float: right;"></em></a>
                    @else
                        {{-- For non-P2P or P2P without quotes, use standard confirmation --}}
                        <a href="{{route('user.posts.confirm_decline_applicant', ['application_id' => $applicant->id, 'action' => 'confirm'])}}"
                            onclick="return confirm('Are you sure?')"><em
                                class="icon ni ni-plus-circle text-success"
                                style="font-size: 30px;float: right;"></em></a>
                    @endif
                @endif
            </div>
        </div>
        <br>
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
                @if($applicant->user->profile_picture)
                <img src="{{$applicant->user->profile_picture}}" style="height: 100px;width: 100px;border: 1px solid #ccc; border-radius: 50%;" alt="">
                @else
                <em class="icon ni ni-user" style="font-size: 40px;"></em>
                @endif
            </div>
            <div class="col-md-7">
                <div><b>{{$applicant->user->name}}</b></div>
                <div class="text-muted"><em
                        class="icon ni ni-map-pin text-muted"></em> {{$applicant->user->location_name}}
                </div>
                @if($post->status === "closed")
                <div class="text-muted"><em class="icon ni ni-money"></em> GHS {{$post->final_payment_amount}}
                </div>
                <div class="text-muted"><em class="icon ni ni-star-fill"></em> {{$post->job_done_overall_rating}}
                </div>
                @endif
            </div>
            <div class="col-md-2">
                <a href="#" onclick="shareLink()" data-toggle="modal"
                    data-target="#viewPhoneNumberModal-{{$applicant->id}}"><em
                        class="icon ni ni-mobile"
                        style="font-size: 30px;float: right;"></em></a>
            </div>
        </div>
        <div class="modal modal-lg fade" tabindex="-1" id="viewPhoneNumberModal-{{$applicant->id}}">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                    <div class="modal-header">
                        <h5 class="modal-title"><b>Call Applicant</b></h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <p style="float: left !important;">View phone number of applicant and make the call.</p>
                                <input value="{{$applicant->user->phone_number}}" id="showPhoneNumber-{{$applicant->id}}" type="text" readonly class="form-control">
                                <br>
                                <button class="btn btn-outline-primary copyLinkButton" onclick="copyApplicantPhoneNumberToClipboard(`{{$applicant->id}}`)"><b>Copy</b></button>
                                <span class="copyStatus"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        @endif

        @if($post->type === "P2P" && $post->is_job_applicant_confirmed == 1 && $post->status !== 'closed')
            {{-- For P2P jobs, show evaluation form first, then trigger payment --}}
            @php
                $confirmedApplication = $post->applications->firstWhere('user_id', $post->confirmed_applicant_id);
            @endphp
            @if($confirmedApplication)
                <form id="p2pCloseJobForm" data-post-id="{{$post->id}}" data-application-id="{{$confirmedApplication->id}}">
                    {{csrf_field()}}
                    <div class="modal modal-lg fade" tabindex="-1" id="p2pCloseJobModal">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                                    <em class="icon ni ni-cross"></em>
                                </a>
                                <div class="modal-header">
                                    <h5 class="modal-title"><b>Close Job & Pay</b></h5>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p class="text-muted">Complete below information to close job and make final payment.</p>
                                        </div>
                                    </div>

                                    <hr>

                                    <p style="font-size: 16px;"><b>Rating</b></p>
                                    <div class="row">
                                        <div class="col-md-4 col-sm-4">
                                            <div class="text-color-gray" style="font-size: 10px;">Expertise</div>
                                        </div>
                                        <div class="col-md-8 col-sm-8">
                                            <div class="stars">
                                                <select class="star-rating" name="expertise_rating" required id="p2pExpertise">
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
                                            <div class="text-color-gray" style="font-size: 10px;">Work Ethic</div>
                                        </div>
                                        <div class="col-md-8 col-sm-8">
                                            <div class="stars">
                                                <select class="star-rating" name="work_ethic_rating" required id="p2pWorkEthic">
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
                                            <div class="text-color-gray" style="font-size: 10px;">Professionalism</div>
                                        </div>
                                        <div class="col-md-8 col-sm-8">
                                            <div class="stars">
                                                <select class="star-rating" name="professionalism_rating" required id="p2pProfessionalism">
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
                                            <div class="text-color-gray" style="font-size: 10px;">Customer Service</div>
                                        </div>
                                        <div class="col-md-8 col-sm-8">
                                            <div class="stars">
                                                <select class="star-rating" name="customer_service_rating" required id="p2pCustomerService">
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
                                                    class="form-control form-control-lg"
                                                    placeholder="Kindly share your review or feedback of the job done."
                                                    name="feedback_message"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="modal-footer bg-white text-right" style="float: right !important;">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="submit" style="float: right;" class="btn btn-outline-primary"><b>Continue to Payment</b></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <a href="#" class="btn btn-outline-danger btn-lg" data-toggle="modal" data-target="#p2pCloseJobModal" style="float: right"><b>Close Job & Pay</b></a>
            @endif
        @else
            {{-- For non-P2P jobs, use standard close form --}}
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
                                    <input type="number" class="form-control form-control-lg" name="final_payment_amount"
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
        @endif
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
                    <b>{{($post->type !== 'VOLUNTEER') ? $post->category : $post->name}}</b>
                </div>
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
            @if($applicant->user->profile_picture)
            <img src="{{$applicant->user->profile_picture}}" style="height: 100px;width: 100px;border: 1px solid #ccc; border-radius: 50%;" alt="">
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
            @if ($applicant->status != "confirmed" && $applicant->status != "declined")
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
    <br>
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
            @if($applicant->user->profile_picture)
            <img src="{{$applicant->user->profile_picture}}" style="height: 100px;width: 100px;border: 1px solid #ccc; border-radius: 50%;" alt="">
            @else
            <em class="icon ni ni-user" style="font-size: 40px;"></em>
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
            <a href="#" onclick="shareLink()" data-toggle="modal"
                data-target="#viewPhoneNumberModal-{{$applicant->id}}"><em
                    class="icon ni ni-mobile"
                    style="font-size: 30px;float: right;"></em></a>
        </div>
    </div>
    <div class="modal modal-lg fade" tabindex="-1" id="viewPhoneNumberModal-{{$applicant->id}}">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title"><b>Call Applicant</b></h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p style="float: left !important;">View phone number of applicant and make the call.</p>
                            <input value="{{$applicant->user->phone_number}}" id="showPhoneNumber-{{$applicant->id}}" type="text" readonly class="form-control">
                            <br>
                            <button class="btn btn-outline-primary copyLinkButton" onclick="copyApplicantPhoneNumberToClipboard(`{{$applicant->id}}`)"><b>Copy</b></button>
                            <span class="copyStatus"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
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
                        <div class="col-md-2 col-lg-2 col-sm-2 text-center">
                            @if($applicant->user->profile_picture)
                            <img src="{{$applicant->user->profile_picture}}" style="height: 100px;width: 100px;border: 1px solid #ccc; border-radius: 50%;" alt="">
                            @else
                            <em class="icon ni ni-user" style="font-size: 40px;"></em>
                            @endif
                        </div>
                        <div class="col-md-7 col-sm-7 col-lg-7">
                            <div><b>{{$applicant->user->name}}</b></div>
                            <div class="text-muted"><em
                                    class="icon ni ni-map-pin text-muted"></em> {{$applicant->user->location_name}}
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-3 col-lg-3">
                            <input type="number" name="volunteer_details[]" class="form-control form-control-lg" required
                                value="{{$post->volunteer_hours}}">
                            <input type="hidden" name="user_id[]" value="{{$applicant->user->id}}">
                        </div>
                    </div>
                    <br>
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

@section("scripts")
<script>
    $("#shortlistedApplicants").hide()
    $("#allApplicants").hide()
    $("#changeStartDate").hide()
    $("#changeEndDate").hide()
    $("#changeMonthlyPayment").hide()
    $("#closeFixedTermJobConfirmation").hide()

    const formatDate = (date) => {
        var date = new Date(date);
        var day = date.getDate();
        var monthIndex = date.getMonth();
        var year = date.getFullYear();

        var monthNames = [
            "January", "February", "March", "April", "May", "June", "July",
            "August", "September", "October", "November", "December"
        ];

        var options = {
            weekday: 'long'
        };
        var dayOfWeek = date.toLocaleDateString('en-US', options);

        var suffix = getNumberSuffix(day);

        var formattedDate = dayOfWeek + " " + day + suffix + " " + monthNames[monthIndex] + " " + year;
        return formattedDate;
    }

    function getNumberSuffix(day) {
        if (day >= 11 && day <= 13) {
            return "th";
        }

        switch (day % 10) {
            case 1:
                return "st";
            case 2:
                return "nd";
            case 3:
                return "rd";
            default:
                return "th";
        }
    }

    const changeStartDateClicked = () => {
        $("#closeFixedTermJobContent").hide()
        $("#submitButton").hide()
        $("#changeStartDate").show("slow")
    }

    const changeEndDateClicked = () => {
        $("#closeFixedTermJobContent").hide()
        $("#submitButton").hide()
        $("#changeEndDate").show("slow")
    }

    const changeMonthlyPaymentClicked = () => {
        $("#closeFixedTermJobContent").hide()
        $("#submitButton").hide()
        $("#changeMonthlyPayment").show("slow")
    }

    const cancelStartDateChangeClicked = () => {
        $("#changeStartDate").hide()
        $("#submitButton").show()
        $("#closeFixedTermJobContent").show("slow")
    }

    const cancelEndDateChangeClicked = () => {
        $("#changeEndDate").hide()
        $("#submitButton").show()
        $("#closeFixedTermJobContent").show("slow")
    }

    const cancelMonthlyPaymentChangeClicked = () => {
        $("#changeMonthlyPayment").hide()
        $("#submitButton").show()
        $("#closeFixedTermJobContent").show("slow")
    }

    const closeFixedTermJobClicked = () => {
        $("#submitButton").hide()
        $("#closeFixedTermJobConfirmation").show("slow")
    }

    const cancelFixedTermJobClose = () => {
        $("#submitButton").show()
        $("#closeFixedTermJobConfirmation").hide()
    }

    const submitStartDateChangeClicked = () => {
        const startDate = document.getElementById("startDateInput").value
        if (startDate === "") {
            cancelStartDateChangeClicked()
            return
        }

        document.getElementById("startDateValue").innerHTML = formatDate(startDate);
        document.getElementById("_start_date").value = startDate

        cancelStartDateChangeClicked()
    }

    const submitEndDateChangeClicked = () => {
        const endDate = document.getElementById("endDateInput").value
        if (endDate === "") {
            cancelEndDateChangeClicked()
            return
        }

        document.getElementById("endDateValue").innerHTML = formatDate(endDate);
        document.getElementById("_end_date").value = endDate

        cancelEndDateChangeClicked()
    }

    const submitMonthlyPaymentChangeClicked = () => {
        const monthlyPayment = document.getElementById("monthlyPaymentInput").value
        if (monthlyPayment === "") {
            cancelMonthlyPaymentChangeClicked()
            return
        }

        document.getElementById("monthlyPaymentValue").innerHTML = monthlyPayment;
        document.getElementById("_monthly_payment").value = monthlyPayment

        cancelMonthlyPaymentChangeClicked()
    }




    function shareLink() {
        $(".copyStatus").hide()
        $(".copyStatus").html("")
        $(`.copyLinkButton`).show();
    }

    // function copyLinkToClipboard(uuid) {
    //     var copyText = document.getElementById(`shareLink-${uuid}`);
    //
    //     // Select the text field
    //     copyText.select();
    //     copyText.setSelectionRange(0, 99999); // For mobile devices
    //
    //     // Copy the text inside the text field
    //     navigator.clipboard.writeText(copyText.value);
    //
    //     $(`.copyLinkButton`).hide();
    //     const successMessage = `<div class="text-success"><em class="icon ni ni-copy"></em> Copied to clipboard</div>`
    //     $(".copyStatus").append(successMessage);
    //     $(".copyStatus").show();
    // }

    function copyApplicantPhoneNumberToClipboard(uuid) {
        var copyText = document.getElementById(`showPhoneNumber-${uuid}`);

        // Select the text field
        copyText.select();
        copyText.setSelectionRange(0, 99999); // For mobile devices

        // Copy the text inside the text field
        navigator.clipboard.writeText(copyText.value);

        $(`.copyLinkButton`).hide();
        const successMessage = `<div class="text-success"><em class="icon ni ni-copy"></em> Copied to clipboard</div>`
        $(".copyStatus").append(successMessage);
        $(".copyStatus").show();
    }

    // Handle P2P Close Job Form Submission
    $('#p2pCloseJobForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const postId = form.data('post-id');
        const applicationId = form.data('application-id');
        const submitBtn = form.find('button[type="submit"]');
        
        // Get form data
        const formData = {
            post_id: postId,
            application_id: applicationId,
            expertise_rating: form.find('[name="expertise_rating"]').val(),
            work_ethic_rating: form.find('[name="work_ethic_rating"]').val(),
            professionalism_rating: form.find('[name="professionalism_rating"]').val(),
            customer_service_rating: form.find('[name="customer_service_rating"]').val(),
            feedback_message: form.find('[name="feedback_message"]').val(),
            _token: '{{ csrf_token() }}'
        };
        
        // Validate ratings
        if (!formData.expertise_rating || !formData.work_ethic_rating || 
            !formData.professionalism_rating || !formData.customer_service_rating) {
            alert('Please provide all ratings before proceeding.');
            return;
        }
        
        // Disable submit button
        submitBtn.prop('disabled', true).text('Processing...');
        
        // Save rating and initiate payment
        $.ajax({
            url: '{{ route("p2p.save.rating.and.initiate.payment") }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.status) {
                    // Close the evaluation modal
                    $('#p2pCloseJobModal').modal('hide');
                    
                    // Initiate payment with the saved rating
                    if (response.data && response.data.authorization_url) {
                        // Show payment modal - initP2PPayment will handle the payment flow
                        if (typeof initP2PPayment === 'function') {
                            initP2PPayment(postId, applicationId, 'final');
                        } else {
                            // Fallback: show payment URL directly
                            window.location.href = response.data.authorization_url;
                        }
                    } else if (response.skip_payment) {
                        // No payment required
                        alert('Job closed successfully!');
                        location.reload();
                    } else {
                        alert('Failed to initiate payment. Please try again.');
                        submitBtn.prop('disabled', false).text('Continue to Payment');
                    }
                } else {
                    alert(response.message || 'Failed to save rating. Please try again.');
                    submitBtn.prop('disabled', false).text('Continue to Payment');
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || xhr.responseJSON?.errors || 'An error occurred';
                alert(typeof error === 'object' ? JSON.stringify(error) : error);
                submitBtn.prop('disabled', false).text('Continue to Payment');
            }
        });
    });

    const showShortlistedApplicants = () => {
        $("#shortlistedApplicants").show("slow")
        $("#allApplicants").hide()
    }

    const showAllApplicants = () => {
        $("#shortlistedApplicants").hide()
        $("#allApplicants").show("slow")
    }
</script>

{{-- Include P2P Payment Modal --}}
@include('components.p2p-payment-modal')
@endsection