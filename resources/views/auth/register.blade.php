<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Vork Dashboard">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="{{asset("assets/assets/images/favico.png")}}">
    <!-- Page Title  -->
    <title>VORK | Register</title>
    <!-- StyleSheets  -->
    <link rel="stylesheet" href="{{asset("assets/html-template/src/assets/css/dashlite.css?ver=1.4.0")}}">
    <link id="skin-default" rel="stylesheet"
          href="{{asset("assets/html-template/src/assets/css/skins/theme-egyptian.css?ver=1.4.0")}}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap"
        rel="stylesheet">
    <style>
        *, .link, .btn, .nk-block-title, h1, h2, h3, h4, h5, h6, table, div, span, a, p, .nk-iv-wg2-title .title {
            /* font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif; */
            /*font-family: 'Lilita One', cursive;*/
            /*font-family: 'Poppins', sans-serif;*/
            /*font-family: "Signika",Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;*/
            font-family: 'lato', sans-serif;
            /*font-weight: 600 !important;*/
            line-height: 1.5;
            color: #000;
        }

        . {
            height: 60px;
        }

    </style>
</head>

<body class="nk-body npc-crypto ui-clean pg-auth">
<!-- app body @s -->
<div class="nk-app-root">
    <div class="nk-split nk-split-page nk-split-md">
        <div class="nk-split-content nk-block-area nk-block-area-column nk-auth-container">
            <div class="absolute-top-right d-lg-none p-3 p-sm-5">
                <a href="#" class="toggle btn-white btn btn-icon btn-light" data-target="athPromo"><em class="icon ni ni-info"></em></a>
            </div>
            <div class="nk-block nk-block-middle nk-auth-body">
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
                            <p>Register to join a world of endless opportunities.</p>
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
                            <input type="text" class="form-control form-control-lg{{ $errors->has('name') ? ' error' : '' }}"
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
                            <input type="tel" class="form-control form-control-lg{{ $errors->has('phone_number') ? ' error' : '' }}"
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
                            <input type="password" class="form-control form-control-lg{{ $errors->has('password') ? ' error' : '' }}"
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
                            <input type="password" class="form-control form-control-lg{{ $errors->has('password_confirmation') ? ' error' : '' }}"
                                   id="password_confirmation" name="password_confirmation" placeholder="Confirm your password">
                        </div>
                        @if($errors->has('password_confirmation'))
                            <span class="help-block text-danger">
                                               <small class="text-danger">{{$errors->first('password_confirmation')}}</small>
                                            </span>
                        @endif
                    </div><!-- .foem-group -->
                    <div class="form-group">
                        <button class="btn btn-lg btn-primary btn-block" type="submit"><b>Register</b></button>
                    </div>
                </form><!-- form -->

                <br>
                <div>Already have an account? <a href="{{route("login")}}"><b>Login</b></a></div>
            </div><!-- .nk-block -->
            <div class="nk-block nk-auth-footer">
                <div class="nk-block-between">
                    <ul class="nav nav-sm">
                        <li class="nav-item">
                            <a class="nav-link text-muted" href="#">Terms & Condition</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-muted" href="#">Privacy Policy</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-muted" href="#">Help</a>
                        </li>
                    </ul><!-- .nav -->
                </div>
                <div class="mt-3">
                    <p class="text-muted">&copy; {{date('Y')}} VORK Technoligies. All Rights Reserved.</p>
                </div>
            </div><!-- .nk-block -->
        </div><!-- .nk-split-content -->
        <div class="nk-split-content nk-split-stretch bg-lighter d-flex toggle-break-lg toggle-slide toggle-slide-right"
             data-content="athPromo" data-toggle-screen="lg" data-toggle-overlay="true">
            <div class="slider-wrap w-100 w-max-550px p-3 p-sm-5 m-auto">
                <div class="slider-init" data-slick='{"dots":false, "arrows":false}'>
                    <div class="slider-item">
                        <div class="nk-feature nk-feature-center">
                            <div class="nk-feature-img">
                                <img class="round" src="{{asset("assets/html-template/src/images/tag.svg")}}"
                                     style="height:360px;" srcset="{{asset("assets/html-template/src/images/tag.svg")}}"
                                     alt="VORK">
                            </div>
                            <div class="nk-feature-content py-4 p-sm-5">
                                <h4>Endless Opportunities</h4>
                                <p>Explore a world of endless opportunities with VORK.</p>
                            </div>
                        </div>
                    </div><!-- .slider-item -->
                </div><!-- .slider-wrap -->
            </div><!-- .nk-split-content -->
        </div><!-- .nk-split -->
    </div><!-- app body @e -->
    <!-- JavaScript -->
    <script src="{{asset("assets/html-template/src/assets/js/bundle.js?ver=1.4.0")}}"></script>
    <script src="{{asset("assets/html-template/src/assets/js/scripts.js?ver=1.4.0")}}"></script>
</body>

</html>
