@extends("layouts.master")


@section("content")
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title"><em class="icon ni ni-file-pdf"></em> CV Generator</h3>
            <div class="nk-block-des text-soft">
                <p class="hide-mb-sm hide-mb-xs md">
                    <nav>
                        <ul class="breadcrumb breadcrumb-arrow">
                            <li class="breadcrumb-item"><a href="#">VORK Resume Builder</a></li>
                        </ul>
                    </nav>
                    </p>
                </div>
            </div>
            <div class="nk-block-head-content">
                <a href="{{URL::previous()}}"
                   class="btn btn-outline-primary"><span>Back</span></a>
            </div>
        </div>
</div>


<div class="row" style="">
   <div class="col-md-2"></div>
   <div class="col-md-8">
    <div class="text-center mt-5">
                <img src="{{asset('assets/html-template/src/images/cv.png')}}" alt=""
                    style="height: 200px; width: 200px;">
                <p class="text-muted1">VORK CV generator gives you the opportunity to create a professional CV that </br> will help you get the job you want.</p>

                @php
                $location = $data['location'];
                $educationHistories = $data['educationHistories'];

                $isProfileComplete = $location != "" && $educationHistories->count() > 0;
                @endphp

                <div><a class="btn btn-primary btn-lg" @if(!$isProfileComplete) disabled="disabled" @endif href="{{route('user.profile.resume', ["id" => auth()->id()])}}">Generate My Professional CV</a></div>

                <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">
                            @if($location == "")
                                <div class="alert alert-danger mt-5  text-left"> <em class=" ni ni-alert" style="height: 80px !important; width: 80px!important;"> </em> No location record found. Kindly update your location in your profile</div>
                            @endif

                             @if($educationHistories->count() <= 0)
                                <div class="alert alert-danger mt-5  text-left"> <em class=" ni ni-alert" style="height: 80px !important; width: 80px!important;"> </em> No education history record found. Kindly update your education history in your profile</div>
                            @endif
                        </div>
                </div>
                
            </div>
   </div>

</div>


@endsection
