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
                        <li class="breadcrumb-item"><a href="#">Work</a></li>
                        <li class="breadcrumb-item"><a href="#">Quick Jobs</a></li>
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
@include('work._tabNavigator')

@if(count($posts) <= 0)
    <div class="row" style="margin-top: -45px;">
    <div class="col-md-12 text-center">
        <img src="{{asset('assets/html-template/src/images/nd.svg')}}" style="height: 250px; width: 250px;" alt="">
        <p style="color: #777;">There are no jobs available at the moment. Come back later.</p>
    </div>
    </div>
    @else
    <div class="row">
        <div class="col-md-5">
            @foreach($posts as $post)
            <div class="card card-bordered" style="/* From https://css.glass */
background: rgba(255, 255, 255, 0.2);
border-radius: 16px;
/*box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);*/
backdrop-filter: blur(5px);
margin-bottom: 15px;
-webkit-backdrop-filter: blur(5px);
border: 1px solid #dbdfea;">
                <div class="card-header bg-gray-100" style="border: 1px solid #dbdfea;border-radius: 16px; margin:5px;">
                    <b>{{$post->category}}</b>
                </div>
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
                            <div class="issuer"><b>{{$post->location}} ({{$post->distance}}km away)</b></div>
                        </div>
                        <div class="col-md-6">
                            <div class="title" style="font-size: 10px;color: #777;">Budget (GHS)</div>
                            <b>
                                <div class="date text-success">{{$post->min_budget}}
                                    - {{$post->max_budget}}</div>
                            </b>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right bg-white" style="border-top: 1px solid #dbdfea;">
                    <span class="pull-left" style="float: left !important;">
                        @if($post->has_already_applied === "yes") <span>Already applied</span> @endif
                    </span>
                    <a href="{{route('user.quick_job.show', ['uuid' => $post->id])}}"
                        class="btn btn-outline-secondary">View Details</a>
                </div>
            </div>
            @endforeach
        </div>
        <div class="col-md-7 d-none d-md-block">
            <div class="text-center" style="margin-top: 120px;">
                <img src="{{asset('assets/html-template/src/images/details.svg')}}" alt=""
                    style="height: 250px; width: 250px;">
                <p style="color: #777;">Select any job to view more details.</p>
            </div>
        </div>
    </div>
    @endif
    @endsection