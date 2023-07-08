@extends("layouts.master")

@section('title')
    Work
@endsection

@section("content")
    <div>
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Work </h3>
                    <div class="nk-block-des text-soft">
                        <p class="hide-mb-sm hide-mb-xs md">
                        <nav>
                            <ul class="breadcrumb breadcrumb-arrow">
                                <li class="breadcrumb-item"><a href="#">opportunities</a></li>
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

{{--        <div class="row mb-3">--}}
{{--            <div class="col-md-12">--}}
{{--                <h2 style="font-weight: 800;">Are you looking <br> for opportunities <br> or  have one to share?</h2>--}}
{{--            </div>--}}
{{--        </div>--}}

        <div class="row">
            <div class="col-md-6 mb-3">
                <a href="{{route("user.work.type", ["type_of_user" => "seeker"])}}">
                    <div class="card card-bordered " style="border-radius: 16px;">
                        <div class="card-body text-center p-4">
                            <img src="{{asset("assets/html-template/src/images/sj.svg")}}"
                                 style="height: 120px; width: 120px;" alt="">
                            <h4>Seeker</h4>
                            <p>I am looking for job</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6">
                <a href="{{route('user.work.type', ["type_of_user" => "employer"])}}">
                    <div class="card card-bordered"  style="border-radius: 16px;">
                        <div class="card-body text-center p-4">
                            <img src="{{asset("assets/html-template/src/images/hj.svg")}}"
                                 style="height: 120px; width: 120px;" alt="">
                            <h4>Employer</h4>
                            <p>I have a job opportunity</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection
