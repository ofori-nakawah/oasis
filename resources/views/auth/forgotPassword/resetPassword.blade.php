
@extends("layouts.onboarding")

@section("title")
    Password Reset
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
                    <h1 class="nk-block-title"><b>Reset Password</b></h1>
                    <div class="nk-block-des">
                        <p>Retrieve your account to access a world of endless opportunities.</p>
                    </div>
                </div>
            </div><!-- .nk-block-head -->
            <form action="{{route("auth.password_reset.executePasswordReset")}}" method="POST">
                {{ csrf_field() }}
                <input type="hidden" name="user_id" value="{{$user->id}}">
                <div class="form-group">
                    <div class="form-label-group{{ $errors->has('password') ? ' text-danger' : '' }}">
                        <label class="form-label" for="password"><b>Password</b></label>
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
                    <div class="form-label-group{{ $errors->has('password_confirmation') ? ' text-danger' : '' }}">
                        <label class="form-label" for="password_confirmation"><b>Confirm Password</b></label>
                    </div>
                    <div class="form-control-wrap">
                        <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch" data-target="password_confirmation">
                            <em class="passcode-icon icon-show icon ni ni-eye"></em>
                            <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                        </a>
                        <div class="form-icon form-icon-left">
                            <em class="icon ni ni-lock"></em>
                        </div>
                        <input type="password" class="form-control form-control-lg{{ $errors->has('password_confirmation') ? ' error' : '' }}" id="password_confirmation" name="password_confirmation" placeholder="Confirm your new password">
                    </div>
                    @if($errors->has('password_confirmation'))
                        <span class="help-block text-danger">
                                               <small>{{$errors->first('password_confirmation')}}</small>
                                            </span>
                    @endif
                </div><!-- .foem-group -->
                <div class="form-group">
                    <button class="btn btn-lg btn-primary btn-block" type="submit"><b>Reset Password</b></button>
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
