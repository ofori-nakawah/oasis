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
                            <li class="breadcrumb-item"><a href="#">{{$original_post->title}}</a></li>

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



    <div class="row mb-4">
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-10">
                    <div class="form-control-wrap" style="margin-bottom: 15px;">
                        <div class="form-icon form-icon-left">
                            <em class="icon ni ni-search"></em>
                        </div>
                        <input type="text" class="form-control form-control-lg" name="search" id="default-01" placeholder="Search posts">
                    </div>
                </div>
                <div class="col-md-2">
                    <a href="#" class="btn btn-outline-secondary" style="float: right;"><em class="icon ni ni-filter"></em></a>
                </div>
            </div>
            @foreach($posts as $post)
                <a href="{{route('user.show_fixed_term_job_details.show', ["uuid" => $post->id])}}" style="text-decoration: none !important;">
                    <div class="card card-bordered mb-3" style="/* From https://css.glass */
background: rgba(255, 255, 255, 0.2);
border-radius: 16px;
box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
backdrop-filter: blur(5px);
-webkit-backdrop-filter: blur(5px);
border: 1px solid rgba(255, 255, 255, 0.3);">
                        <div class="card-header bg-white text-primary" style="border-bottom: 1px solid #dbdfea;">
                            <b>{{$post->title}}</b> @if($post->is_internship === "yes") <span class="badge badge-secondary">Internship</span> @endif</div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <div class="title" style="font-size: 10px;color: #777;">Company</div>
                                    <div class="issuer text-danger"><b>{{$post->employer}}</b></div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <div class="title" style="font-size: 10px;color: #777;">Budget (GHS/month)</div>
                                    <div class="issuer text-success"><b>{{$post->min_budget}} - {{$post->max_budget}}</b></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="title" style="font-size: 10px;color: #777;">Duration</div>
                                    <div class="issuer "><b>{{$post->duration}} month(s)</b></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="title" style="font-size: 10px;color: #777;">Location</div>
                                    <div class="issuer text-dark"><b>{{$post->location}} ({{$post->distance}}km)</b></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>

            @endforeach
        </div>
        <div class="col-md-8">
            <div class="card card-bordered" style="/* From https://css.glass */
background: rgba(255, 255, 255, 0.2);
border-radius: 16px;
box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
backdrop-filter: blur(5px);
-webkit-backdrop-filter: blur(5px);
border: 1px solid rgba(255, 255, 255, 0.3);">
                <div class="card-header bg-white text-primary" style="border-bottom: 1px solid #dbdfea;">
                    <b>{{$original_post->title}}</b> @if($original_post->is_internship === "yes") <span class="badge badge-secondary">Internship</span> @endif <span style="float:right"><em class="ni ni-clock"></em> {{$original_post->postedOn}}</span></div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <div class="title" style="font-size: 10px;color: #777;">Company</div>
                            <div class="issuer"><b>{{$original_post->employer}}</b></div>
                        </div>
                        <div class="col-md-6">
                            <div class="title" style="font-size: 10px;color: #777;">Location</div>
                            <b><div class="location">{{$original_post->location}}</div></b>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <div class="title" style="font-size: 10px;color: #777;">Description</div>
                            <pre><div class="issuer"><b>{{$original_post->description}}</b></div></pre>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <div class="title" style="font-size: 10px;color: #777;">Qualifications</div>
                            <pre><div class="issuer"><b>{{$original_post->qualifications}}</b></div></pre>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-8">
                            <div class="title" style="font-size: 10px;color: #777;">Budget (GHS/month)</div>
                            <b>
                                <div class="date text-success">{{$original_post->min_budget}} - {{$original_post->max_budget}} <br> @if($original_post->is_negotiable == "yes") <span class="badge badge-outline-secondary">Negotiable</span> @endif</div>
                            </b>
                        </div>
                        <div class="col-md-4">
                            <div class="title" style="font-size: 10px;color: #777;">Term</div>
                            <b>
                                <div class="date text-danger">{{$original_post->duration}} months <br> <span>@if($original_post->is_renewable == "yes") <span class="badge badge-outline-secondary">Renewable</span> @endif</span></div>
                            </b>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="title" style="font-size: 10px;color: #777;">Application Deadline</div>
                            <div class="issuer"><b>{{$original_post->date}}</b></div>
                        </div>
                    </div>
                </div>
                @if($original_post->user->id !== auth()->id())
                    <div class="card-footer text-right bg-white" style="border-top: 1px solid #dbdfea;">
                        @if($original_post->has_already_applied === "yes") <span>You have already applied</span> @elseif($original_post->status === "closed") <span>Post closed</span> @else <a href="{{route("user.apply_for_job", ['uuid' => $original_post->id])}}" class="btn btn-outline-success"><b>Apply</b></a> @endif
                    </div>
                @endif
            </div>
        </div>

    </div>

@endsection

