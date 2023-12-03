

@extends("layouts.onboarding")

@section("title")
    Login
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
                    <h1 class="nk-block-title"><b>Sign In</b></h1>
                    <div class="nk-block-des">
{{--                        <p>Login to access a world of endless opportunities.</p>--}}
                    </div>
                </div>
            </div><!-- .nk-block-head -->
            <form action="{{route("user.login")}}" method="POST">
                {{ csrf_field() }}
                <div class="form-group">
                    <div class="form-label-group{{ $errors->has('email_or_phone_number') ? ' text-danger' : '' }}">
                        <label class="form-label" for="default-01"><b>Email Or Phone Number</b></label>
                    </div>
                    <div class="form-control-wrap">
                        <div class="form-icon form-icon-left">
                            <em class="icon ni ni-user"></em>
                        </div>
                        <input type="text" class="form-control form-control-lg{{ $errors->has('email_or_phone_number') ? ' error' : '' }}" name="email_or_phone_number" id="default-01" placeholder="Enter your email address or phone number">
                    </div>
                    @if($errors->has('email_or_phone_number'))
                        <span class="help-block text-danger">
                                               <small>{{$errors->first('email_or_phone_number')}}</small>
                                            </span>
                    @endif
                </div><!-- .foem-group -->
                <div class="form-group">
                    <div class="form-label-group{{ $errors->has('password') ? ' text-danger' : '' }}">
                        <label class="form-label" for="password"><b>Password</b></label>
                        <a class="link link-primary link-sm" tabindex="-1" href="{{route('auth.forgotPassword')}}">Forgot Password?</a>
                    </div>
                    <div class="form-control-wrap">
                        <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch" data-target="password">
                            <em class="passcode-icon icon-show icon ni ni-eye"></em>
                            <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                        </a>
                        <div class="form-icon form-icon-left">
                            <em class="icon ni ni-lock"></em>
                        </div>
                        <input type="password" class="form-control form-control-lg{{ $errors->has('password') ? ' error' : '' }}" id="password" name="password" placeholder="Enter your password">
                    </div>
                    @if($errors->has('password'))
                        <span class="help-block text-danger">
                                               <small>{{$errors->first('password')}}</small>
                                            </span>
                    @endif
                </div><!-- .foem-group -->
                <div class="form-group">
                    <button class="btn btn-lg btn-primary btn-block" type="submit"><b>Sign in</b></button>
                </div>
            </form><!-- form -->
            <br>
            <div>Dont have an account yet? <a href="{{route("onboarding.register")}}"><b>Register</b></a></div>
{{--            <div class="nk-block nk-auth-footer text-center" style="margin-top:20px;">--}}
{{--                <div class="nk-block-between">--}}
{{--                    <ul class="nav nav-sm text-center">--}}
{{--                        <li class="nav-item">--}}
{{--                            <a class="nav-link text-muted" href="#">Terms & Condition</a>--}}
{{--                        </li>--}}
{{--                        <li class="nav-item">--}}
{{--                            <a class="nav-link text-muted" href="#">Privacy Policy</a>--}}
{{--                        </li>--}}
{{--                        <li class="nav-item">--}}
{{--                            <a class="nav-link text-muted" href="#">Help</a>--}}
{{--                        </li>--}}
{{--                    </ul><!-- .nav -->--}}
{{--                </div>--}}
{{--                <div class="mt-3">--}}
{{--                    <p class="text-muted">&copy; {{date('Y')}} VORK Technoligies. All Rights Reserved.</p>--}}
{{--                </div>--}}
{{--            </div><!-- .nk-block -->--}}
        </div><!-- .nk-block -->
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

