
@extends("layouts.onboarding")

@section("title")
    Forgot Password
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
                    <h1 class="nk-block-title"><b>Password Reset</b></h1>
                    <div class="nk-block-des">
                        <p>Retrieve your account to access a world of endless opportunities.</p>
                    </div>
                </div>
            </div><!-- .nk-block-head -->
            <form action="{{route("auth.verifyAccount.submit")}}" method="POST">
                {{ csrf_field() }}
                <div class="form-group">
                    <div class="form-control-wrap">
                        <input type="text" placeholder="Enter your email or phone number to retrieve account" class="form-control form-control-lg{{ $errors->has('code') ? ' error' : '' }}" name="email_phone_number" id="default-01">
                    </div>
                    @if($errors->has('email_phone_number'))
                        <span class="help-block text-danger">
                                               <small class="text-danger">{{$errors->first('email_phone_number')}}</small>
                                            </span>
                    @endif
                </div><!-- .foem-group -->
                <div class="form-group">
                    <button class="btn btn-lg btn-primary btn-block" type="submit"><b>Continue</b></button>
                </div>
            </form><!-- form -->
            <br>
            <div>Already have an account? <a href="{{route("login")}}"><b>Login</b></a></div>
        </div>
    </div>

    <div class="n" style="margin-top: 40px;text-align: center">
        <div>
            <a href="https://myvork.com/terms-of-use/" target="_blank"><b>Terms</b></a> | <a href="https://myvork.com/privacy-policy-statement/" target="_blank"><b>Privacy</b></a> | <a href="https://myvork.com/help-centre/" target="_blank"><b>Help</b></a>
        </div>
        <div class="mt-3">
            <p class="">&copy; {{date('Y')}} VORK Technologies. All Rights Reserved.</p>
        </div>
    </div>
@endsection
