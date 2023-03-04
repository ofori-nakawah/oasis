
@extends("layouts.onboarding")

@section("title")
    Register
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
                    <img class="logo-light logo-img logo-img-lg"
                         src="{{asset("assets/html-template/src/images/logo_white_bg.png")}}"
                         srcset="{{asset("assets/html-template/src/images/logo_white_bg.png")}}" alt="logo">
                    <img class="logo-dark logo-img logo-img-lg"
                         src="{{asset("assets/html-template/src/images/logo_white_bg.png")}}"
                         srcset="{{asset("assets/html-template/src/images/logo_white_bg.png")}}" alt="logo-dark">
                </a>
            </div>
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <h1 class="nk-block-title"><b>Register</b></h1>
                    <div class="nk-block-des">
{{--                        <p>Register to join a world of endless opportunities.</p>--}}
                    </div>
                </div>
            </div><!-- .nk-block-head -->
            <form action="{{route("onboarding.register.submit")}}" method="POST">
                {{ csrf_field() }}
                <div class="form-group">
                    <div class="form-label-group{{ $errors->has('name') ? ' text-danger' : '' }}">
                        <label class="form-label" for="default-01"><b>Name</b></label>
                    </div>
                    <div class="form-control-wrap">
                        <div class="form-icon form-icon-left">
                            <em class="icon ni ni-user"></em>
                        </div>
                        <input type="text"
                               class="form-control form-control-lg{{ $errors->has('name') ? ' error' : '' }}"
                               name="name" id="default-01" placeholder="Enter your full name">
                    </div>
                    @if($errors->has('name'))
                        <span class="help-block text-danger">
                                               <small class="text-danger">{{$errors->first('name')}}</small>
                                            </span>
                    @endif
                </div><!-- .foem-group -->

                <div class="form-group">
                    <div class="form-label-group{{ $errors->has('country') ? ' text-danger' : '' }}">
                        <label class="form-label" for="default-01"><b>Country</b></label>
                    </div>
                    <div class="form-control-wrap">
                        <select type="email"
                                class="form-control form-control-lg{{ $errors->has('country') ? ' error' : '' }}"
                                name="country" id="default-01">
                            <option value="">Choose an option</option>
                            @foreach($countries as $country)
                                <option value="{{$country->id}}">{{$country->name}}
                                    | {{$country->tel_code}}</option>
                            @endforeach
                        </select>
                    </div>
                    @if($errors->has('country'))
                        <span class="help-block text-danger">
                                               <small class="text-danger">{{$errors->first('country')}}</small>
                                            </span>
                    @endif
                </div>

                <div class="form-group">
                    <div class="form-label-group{{ $errors->has('phone_number') ? ' text-danger' : '' }}">
                        <label class="form-label" for="default-01"><b>Phone Number</b></label>
                    </div>
                    <div class="form-control-wrap">
                        <div class="form-icon form-icon-left">
                            <em class="icon ni ni-mobile"></em>
                        </div>
                        <input type="tel"
                               class="form-control form-control-lg{{ $errors->has('phone_number') ? ' error' : '' }}"
                               name="phone_number" id="default-01" placeholder="Enter your phone number">
                    </div>
                    @if($errors->has('phone_number'))
                        <span class="help-block text-danger">
                                               <small class="text-danger">{{$errors->first('phone_number')}}</small>
                                            </span>
                    @endif
                </div>
                <div class="form-group">
                    <div class="form-label-group{{ $errors->has('email') ? ' text-danger' : '' }}">
                        <label class="form-label" for="default-01"><b>Email</b></label>
                    </div>
                    <div class="form-control-wrap">
                        <div class="form-icon form-icon-left">
                            <em class="icon ni ni-emails"></em>
                        </div>
                        <input type="email"
                               class="form-control form-control-lg{{ $errors->has('email') ? ' error' : '' }}"
                               name="email" id="default-01"
                               placeholder="Enter your email address">
                    </div>
                    @if($errors->has('email'))
                        <span class="help-block text-danger">
                                               <small class="text-danger">{{$errors->first('email')}}</small>
                                            </span>
                    @endif
                </div><!-- .foem-group -->
                <div class="form-group">
                    <div class="form-label-group{{ $errors->has('password') ? ' text-danger' : '' }}">
                        <label class="form-label" for="password"><b>Password</b></label>
                    </div>
                    <div class="form-control-wrap">
                        <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch"
                           data-target="password">
                            <em class="passcode-icon icon-show icon ni ni-eye"></em>
                            <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                        </a>
                        <div class="form-icon form-icon-left">
                            <em class="icon ni ni-lock"></em>
                        </div>
                        <input type="password"
                               class="form-control form-control-lg{{ $errors->has('password') ? ' error' : '' }}"
                               id="password" name="password" placeholder="Enter your password">
                    </div>
                    @if($errors->has('password'))
                        <span class="help-block text-danger">
                                               <small class="text-danger">{{$errors->first('password')}}</small>
                                            </span>
                    @endif
                </div><!-- .foem-group -->
                <div class="form-group">
                    <div class="form-label-group{{ $errors->has('password_confirmation') ? ' text-danger' : '' }}">
                        <label class="form-label" for="password_confirmation"><b>Confirm Password</b></label>
                    </div>
                    <div class="form-control-wrap">
                        <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch"
                           data-target="password_confirmation">
                            <em class="passcode-icon icon-show icon ni ni-eye"></em>
                            <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                        </a>
                        <div class="form-icon form-icon-left">
                            <em class="icon ni ni-lock"></em>
                        </div>
                        <input type="password"
                               class="form-control form-control-lg{{ $errors->has('password_confirmation') ? ' error' : '' }}"
                               id="password_confirmation" name="password_confirmation"
                               placeholder="Confirm your password">
                    </div>
                    @if($errors->has('password_confirmation'))
                        <span class="help-block text-danger">
                                               <small
                                                   class="text-danger">{{$errors->first('password_confirmation')}}</small>
                                            </span>
                    @endif
                </div><!-- .foem-group -->
                <div class="custom-control custom-control-lg custom-checkbox" style="margin-bottom: 15px;">
                    <input type="checkbox" class="custom-control-input" required id="customCheck2">
                    <label class="custom-control-label" for="customCheck2">Accept our <a href="https://myvork.com/terms-of-use/"><b>terms of use</b> </a> and <a href="https://myvork.com/privacy-policy-statement/"><b>privacy policy</b></a></label>
                </div>

                <div class="form-group">
                    <button class="btn btn-lg btn-primary btn-block" type="submit"><b>Register</b></button>
                </div>
            </form><!-- form -->

            <br>
            <div>Already have an account? <a href="{{route("login")}}"><b>Login</b></a></div>
        </div>
    </div>

    <div class="nk-block nk-auth-footer" style="margin-top: 40px;">
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
