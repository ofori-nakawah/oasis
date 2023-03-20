@extends("layouts.master")

@section('title')
    Profile
@endsection

@section("content")
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title"><em class="icon ni ni-user"></em> Profile </h3>
                <div class="nk-block-des text-soft">
                    <p class="hide-mb-sm hide-mb-xs md">
                    <nav>
                        <ul class="breadcrumb breadcrumb-arrow">
                            <li class="breadcrumb-item"><a href="#">Update your languages</a></li>
                        </ul>
                    </nav>
                    </p>
                </div>
            </div><!-- .nk-block-head-content -->
        </div>
    </div>

    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">

            <div class="row">
                <div class="col-md-12">
                    <p>Choose at most 3 languages you are familiar with and can conduct business in.</p>

                    <div class="row mb-4 mt-2">
                        <div class="col-sm-12">
                            @include("utilities.alerts.alerts")
                        </div>
                    </div>

                    <form action="{{route('onboarding.languages.update')}}" method="POST">
                        {{csrf_field()}}
                        <div style="margin-top: 15px;">
                            <div class="card card-bordered" style="padding: 15px;">
                                <div>
                                    @foreach($languages as $language)
                                        <div class="custom-control custom-checkbox" style="margin-right: 15px;margin-bottom: 15px;margin-top: 15px;">
                                            <input type="checkbox" class="custom-control-input" value="{{$language->id}}" name="languages[]" id="{{$language->name}}">
                                            <label class="custom-control-label" for="{{$language->name}}"><b>{{$language->name}}</b></label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div style="margin-top: 15px;">
                            <button class="btn btn-lg btn-success" style="float: right;"><b>Continue</b></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
