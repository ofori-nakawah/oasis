@extends("layouts.master")

@section('title')
    Work
@endsection

@section("content")
    <div class="nk-block-head nk-block-head-sm" >
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title"><em class="icon ni ni-briefcase"></em> Work </h3>
                <div class="nk-block-des text-soft">
                    <p class="hide-mb-sm hide-mb-xs md">
                    <nav>
                        <ul class="breadcrumb breadcrumb-arrow">
                            <li class="breadcrumb-item"><a href="#">Opportunities</a></li>
                        </ul>
                    </nav>
                    </p>
                </div>
            </div><!-- .nk-block-head-content -->
            <div class="nk-block-head-content">
                <div class="toggle-wrap nk-block-tools-toggle">
                    <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="pageMenu"><em
                            class="icon ni ni-menu-alt-r"></em></a>
                    <div class="toggle-expand-content" data-content="pageMenu">
                        <ul class="nk-block-tools g-3">
                            <li><a href="{{URL::previous()}}"
                                   class="btn btn-outline-primary"><span>Back</span></a></li>
                        </ul>
                    </div>
                </div><!-- .toggle-wrap -->
            </div><!-- .nk-block-head-content -->
        </div><!-- .nk-block-between -->
    </div><!-- .nk-block-head -->

{{--    <div class="row mb-3">--}}
{{--        <div class="col-md-12">--}}
{{--            <h2 style="font-weight: 800;">What type of <br> opportunity are you <br> looking for?</h2>--}}
{{--        </div>--}}
{{--    </div>--}}

    <div class="row">
        <div class="col-md-6 mb-3">
            <a href="{{route("user.work.jobs", ["type_of_user" => $type_of_user, "type_of_work" => "quick-job"])}}">
                <div class="card card-bordered" style="border-radius: 16px;">
                    <div class="card-body text-center p-4">
                        <img src="{{asset("assets/html-template/src/images/quick.svg")}}"
                             style="height: 120px; width: 120px;" alt="">
                        <h4>Quick Job</h4>
                        <p>{{($type_of_user !== "employer") ? 'Apply for casual jobs or side gigs' : 'Post a casual job'}}</p>
                    </div>
                </div>
            </a>
        </div>
{{--        <div class="col-md-6 mb-3">--}}
{{--            <a href="{{route("user.work.jobs", ["type_of_user" => $type_of_user, "type_of_work" => "fixed-term"])}}" >--}}
{{--                <div class="card card-bordered shadow-lg" style="/* From https://css.glass */--}}
{{--background: rgba(255, 255, 255, 0.2);--}}
{{--border-radius: 16px;--}}
{{--box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);--}}
{{--backdrop-filter: blur(5px);--}}
{{---webkit-backdrop-filter: blur(5px);--}}
{{--border: 1px solid rgba(255, 255, 255, 0.3);">--}}
{{--                    <div class="card-body text-center p-4">--}}
{{--                        <img src="{{asset("assets/html-template/src/images/partTime.svg")}}"--}}
{{--                             style="height: 120px; width: 120px;" alt="">--}}
{{--                        <h4>Fixed Term</h4>--}}
{{--                        <p>{{($type_of_user !== "employer") ? 'Apply for fixed term or part time jobs' : 'Post a fixed term or part time job'}}</p>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </a>--}}
{{--        </div>--}}
    </div>
@endsection
