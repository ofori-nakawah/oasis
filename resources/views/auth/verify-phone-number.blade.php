
@extends("layouts.onboarding")

@section("title")
    Verify Phone
@endsection

@section("content")
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <div class="row mb-4 mt-n2">
                <div class="col-sm-12">
                    @include("utilities.alerts.alerts")
                </div>
            </div>
            <div class="brand-logo pb-5 text-center">
                <a href="" class="logo-link">
                    <img class="logo-light logo-img logo-img-lg" src="{{asset("assets/html-template/src/images/logo_white_bg.png")}}" srcset="{{asset("assets/html-template/src/images/logo_white_bg.png")}}" alt="logo">
                    <img class="logo-dark logo-img logo-img-lg" src="{{asset("assets/html-template/src/images/logo_white_bg.png")}}" srcset="{{asset("assets/html-template/src/images/logo_white_bg.png")}}" alt="logo-dark">
                </a>
            </div>
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <h1 class="nk-block-title"><b>Verify Phone</b></h1>
                    <div class="nk-block-des">
                        <p>Login to access a world of endless opportunities.</p>
                    </div>
                </div>
            </div><!-- .nk-block-head -->
            <form action="{{route("onboarding.verify_phone_number.submit")}}" method="POST">
                {{ csrf_field() }}
                <div class="form-group">
                    <div class="form-label-group{{ $errors->has('code') ? ' text-danger' : '' }}">
                        <label class="form-label" for="default-01"><b>Code</b></label>
                        <a class="link link-primary link-sm" tabindex="-1" href="{{route('auth.resend_otp', ['user' => $user])}}"><b class="text-success">Resend Code</b></a>

                    </div>
                    <div class="form-control-wrap">
                        <input type="hidden" name="phone_number" value="{{$user->phone_number}}">
                        <input type="number" class="form-control form-control-lg{{ $errors->has('code') ? ' error' : '' }}" name="code" id="default-01" placeholder="___   ___   ___   ___   ___   ___" style="text-align: center !important;">
                    </div>
                    @if($errors->has('code'))
                        <span class="help-block text-danger">
                                               <small class="text-danger">{{$errors->first('code')}}</small>
                                            </span>
                    @endif
                </div><!-- .foem-group -->
                <div class="form-group">
                    <button class="btn btn-lg btn-primary btn-block" type="submit"><b>Verify</b></button>
                </div>
            </form><!-- form -->
            <br>
            <div>Already have an account? <a href="{{route("login")}}"><b>Login</b></a></div>
        </div>
    </div>

    <div class="nk-block nk-auth-footer text-center" style="margin-top: 40px;">
        <div class="nk-block-between">
            <ul class="nav nav-sm">
                <li class="nav-item">
                    <a class="nav-link " href="#">Terms & Condition</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " href="#">Privacy Policy</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " href="#">Help</a>
                </li>
            </ul><!-- .nav -->
        </div>
        <div class="mt-3">
            <p class="">&copy; {{date('Y')}} VORK Technologies. All Rights Reserved.</p>
        </div>
    </div>
@endsection
