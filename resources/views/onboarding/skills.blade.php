@extends("layouts.onboarding")

@section("title")
    Skills & Interests - Onboarding
@endsection

@section("content")
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title"><em class="icon ni ni-globe"></em> Onboarding | Skills & Interests </h3>
                <div class="nk-block-des text-soft">
                    <p class="hide-mb-sm hide-mb-xs md">
                    <nav>
                        <ul class="breadcrumb breadcrumb-arrow">
                            <li class="breadcrumb-item"><a href="#"><b>What are your skills and interests?</b></a></li>
                        </ul>
                    </nav>
                    </p>
                </div>
            </div><!-- .nk-block-head-content -->
            <div class="nk-block-head-content">
                <a class="btn btn-outline-danger" href="javascript:void(0)" onclick="event.preventDefault();
                                                     var conf = confirm('Are you sure you want to logout?');
                                                     if(conf){
                                                        document.getElementById('logout-form').submit();
                                                     }"><span>Sign out</span></a>
                <form id="logout-form" action="{{route('logout')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
            </div><!-- .nk-block-head-content -->
        </div><!-- .nk-block-between -->
    </div><!-- .nk-block-head -->
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-4 col-xs-4 col-sm-4 col-4">
                    <hr style="border: 3px solid #000;border-radius: 4px;">
                </div>
                <div class="col-md-4 col-xs-4 col-sm-4 col-4">
                    <hr style="border: 3px solid #000;border-radius: 4px;">
                </div>
                <div class="col-md-4 col-xs-4 col-sm-4 col-4">
                    <hr style="border: 3px solid #ccc;border-radius: 4px;">
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <p>Choose at most 3 skills you have or area of interest from the list below</p>

                    <div style="margin-top: 15px;">
                        <div class="card card-bordered" style="padding: 15px;">
                            <div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="customCheck2">
                                    <label class="custom-control-label" for="customCheck2">Option Label</label>
                                </div>

                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="customCheck">
                                    <label class="custom-control-label" for="customCheck">Option Label</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 15px;">
                        <button class="btn btn-lg btn-success" style="float: right;"><b>Continue</b></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
