@extends("layouts.master")

@section('title')
    Work
@endsection

@section("content")
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title"><em class="icon ni ni-briefcase"></em> Work </h3>
                <div class="nk-block-des text-soft">
                    <p class="hide-mb-sm hide-mb-xs md">
                    <nav>
                        <ul class="breadcrumb breadcrumb-arrow">
                            <li class="breadcrumb-item"><a href="#">Apply</a></li>
                            <li class="breadcrumb-item"><a href="#">Available Quick Job Opportunities</a></li>
                            <li class="breadcrumb-item"><a href="#">{{$original_post->category}}</a></li>
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
            @foreach($posts as $post)
                <div class="card card-bordered">
                    <div class="card-header bg-white" style="border-bottom: 1px solid #dbdfea;"><b>{{$post->category}}</b></div>
                    <div class="card-body">
                        <div class="row">
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
                                <div class="title" style="font-size: 10px;color: #777;">Budget (GHS)</div>
                                <b>
                                    <div class="date text-success">{{$post->min_budget}} - {{$post->max_budget}} </div>
                                </b>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-right bg-white" style="border-top: 1px solid #dbdfea;">
                        <span class="pull-left" style="float: left !important;">
                            @if($post->has_already_applied === "yes") <span>Already applied</span> @endif
                        </span>
                        <a href="{{route('user.quick_job.show', ['uuid' => $post->id])}}" class="btn btn-outline-secondary">View Details</a>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="col-md-7">
            @include("utilities.alerts.alerts")
            <div class="card card-bordered">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="title" style="font-size: 10px;color: #777;">Activity Name</div>
                            <div class="issuer"><b>{{$original_post->category}}</b></div>
                        </div>
                        <div class="col-md-4">
                            <div class="title" style="font-size: 10px;color: #777;">Issuer</div>
                            <b>
                                <div class="date text-success">{{$original_post->user->name}}</div>
                            </b>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="title" style="font-size: 10px;color: #777;">Description</div>
                            <div class="issuer"><b>{{$original_post->description}}</b></div>
                            @if($original_post->post_image_link)
                                <img src="{{$original_post->post_image_link}}" style="height: 300px;width: 100%;border-radius: 4px;" alt="">
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="title" style="font-size: 10px;color: #777;">Location</div>
                            <b><div class="location">{{$original_post->location}}</div></b>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="title" style="font-size: 10px;color: #777;">Budget (GHS)</div>
                            <b>
                                <div class="date text-success">{{$original_post->min_budget}} - {{$original_post->max_budget}} <br> <small>@if($original_post->is_negotiable == "on") <span style="font-style: italic; color: #777;">Negotiable</span> @endif @if($original_post->is_includes_tax == "on") <span style="font-style: italic; color: #777;"> & Includes WHT @ 5%.</span> @endif</small></div>
                            </b>
                        </div>
                        <div class="col-md-4">
                            <div class="title" style="font-size: 10px;color: #777;">Date & Time</div>
                            <b>
                                <div class="date text-success">{{$original_post->date}} {{$original_post->time}}</div>
                            </b>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="title" style="font-size: 10px;color: #777;">Other Relevant Information</div>
                            <div class="issuer"><b>{{$original_post->other_relevant_information}}</b></div>
                        </div>
                    </div>
                </div>
                @if($original_post->user->id !== auth()->id())
                    <div class="card-footer text-right bg-white" style="border-top: 1px solid #dbdfea;">
                        @if($original_post->has_already_applied === "yes") <span>You have already applied</span> @elseif($original_post->status === "closed") <span>Post closed</span> @else <a href="{{route("user.apply_for_job", ['uuid' => $original_post->id])}}" class="btn btn-outline-success">Apply</a> @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

