@extends("layouts.master")

@section('title')
    Volunteer
@endsection

@section("content")
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title"><em class="icon ni ni-users"></em> Volunteer </h3>
                <div class="nk-block-des text-soft">
                    <p class="hide-mb-sm hide-mb-xs md">
                    <nav>
                        <ul class="breadcrumb breadcrumb-arrow">
                            <li class="breadcrumb-item"><a href="#">Choose type of opportunity</a></li>
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

    <div class="row">
        <div class="col-md-6 mb-3">
            <a href="#">
                <div class="card card-bordered">
                    <div class="card-body text-center p-4">
                        <img src="{{asset("assets/html-template/src/images/join.svg")}}"
                             style="height: 120px; width: 120px;" alt="">
                        <h2>Join</h2>
                        <p>Join an activity in your community</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6">
            <a href="#">
                <div class="card card-bordered">
                    <div class="card-body text-center p-4">
                        <img src="{{asset("assets/html-template/src/images/create.svg")}}"
                             style="height: 120px; width: 120px;" alt="">
                        <h2>Organise</h2>
                        <p>Create an activity in your community</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
@endsection
