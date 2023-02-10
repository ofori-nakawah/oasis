<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="OrdaGH Dashboard">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="{{asset("assets/assets/images/favico.png")}}">
    <!-- Page Title  -->
    <title>OrdaGH | Login</title>
    <!-- StyleSheets  -->
    <link rel="stylesheet" href="{{asset("assets/html-template/src/assets/css/dashlite.css?ver=1.4.0")}}">
    <link id="skin-default" rel="stylesheet" href="{{asset("assets/html-template/src/assets/css/skins/theme-egyptian.css?ver=1.4.0")}}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">    <style>
        *, .link, .btn, .nk-block-title, h1, h2, h3, h4, h5, h6, table, div, span, a, p, .nk-iv-wg2-title .title{
            /* font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif; */
            /*font-family: 'Lilita One', cursive;*/
            /*font-family: 'Poppins', sans-serif;*/
            /*font-family: "Signika",Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;*/
            font-family: 'lato', sans-serif;
            /*font-weight: 600 !important;*/
            line-height: 1.5;
        }

        .form-control-lg{
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
                        <img class="logo-light logo-img logo-img-lg" src="{{asset("assets/html-template/src/images/logo_white_bg.png")}}" srcset="{{asset("assets/html-template/src/images/logo_white_bg.png")}}" alt="logo">
                        <img class="logo-dark logo-img logo-img-lg" src="{{asset("assets/html-template/src/images/logo_white_bg.png")}}" srcset="{{asset("assets/html-template/src/images/logo_white_bg.png")}}" alt="logo-dark">
                    </a>
                </div>
                <div class="nk-block-head text-center">
                    <div class="nk-block-head-content">
                        <h5 class="nk-block-title">Sign-In</h5>
                        <div class="nk-block-des">
                            <p>Login to access a world of endless opportunities.</p>
                        </div>
                    </div>
                </div><!-- .nk-block-head -->
                <form action="{{route("user.login")}}" method="POST">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <div class="form-label-group{{ $errors->has('email_or_phone_number') ? ' text-danger' : '' }}">
                            <label class="form-label" for="default-01">Email</label>
                        </div>
                        <div class="form-control-wrap">
                            <div class="form-icon form-icon-left">
                                <em class="icon ni ni-user"></em>
                            </div>
                            <input type="email" class="form-control form-control-lg{{ $errors->has('email_or_phone_number') ? ' error' : '' }}" name="email_or_phone_number" id="default-01" placeholder="Enter your email address or phone number">
                        </div>
                        @if($errors->has('email_or_phone_number'))
                            <span class="help-block text-danger">
                                               <small>{{$errors->first('email_or_phone_number')}}</small>
                                            </span>
                        @endif
                    </div><!-- .foem-group -->
                    <div class="form-group">
                        <div class="form-label-group{{ $errors->has('password') ? ' text-danger' : '' }}">
                            <label class="form-label" for="password">Password</label>
                            <a class="link link-primary link-sm" tabindex="-1" href="">Forgot Password?</a>
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
                        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
                    </div>
                </form><!-- form -->


            </div><!-- .nk-block -->
            <div class="nk-block nk-auth-footer">
                <div class="nk-block-between">
                    <ul class="nav nav-sm">
                        <li class="nav-item">
                            <a class="nav-link" href="#">Terms & Condition</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Privacy Policy</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Help</a>
                        </li>
                        <li class="nav-item dropup">
                            <a class="dropdown-toggle dropdown-indicator has-indicator nav-link" data-toggle="dropdown" data-offset="0,10"><small>English</small></a>
                            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                <ul class="language-list">
                                    <li>
                                        <a href="#" class="language-item">
                                            <img src="./images/flags/english.png" alt="" class="language-flag">
                                            <span class="language-name">English</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="language-item">
                                            <img src="./images/flags/spanish.png" alt="" class="language-flag">
                                            <span class="language-name">Español</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="language-item">
                                            <img src="./images/flags/french.png" alt="" class="language-flag">
                                            <span class="language-name">Français</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="language-item">
                                            <img src="./images/flags/turkey.png" alt="" class="language-flag">
                                            <span class="language-name">Türkçe</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul><!-- .nav -->
                </div>
                <div class="mt-3">
                    <p>&copy; 2020 AppTechHub Global Limited. All Rights Reserved.</p>
                </div>
            </div><!-- .nk-block -->
        </div><!-- .nk-split-content -->
        <div class="nk-split-content nk-split-stretch bg-lighter d-flex toggle-break-lg toggle-slide toggle-slide-right" data-content="athPromo" data-toggle-screen="lg" data-toggle-overlay="true">
            <div class="slider-wrap w-100 w-max-550px p-3 p-sm-5 m-auto">
                <div class="slider-init" data-slick='{"dots":true, "arrows":false}'>
                    <div class="slider-item">
                        <div class="nk-feature nk-feature-center">
                            <div class="nk-feature-img">
                                <img class="round" src="{{asset("assets/assets/images/fast.svg")}}" style="height:360px;" srcset="{{asset("assets/assets/images/fast.svg")}}" alt="dashboard ordagh eshopping">
                            </div>
                            <div class="nk-feature-content py-4 p-sm-5">
                                <h4>Secure</h4>
                                <p>Drivers, Delivery Agents, Vehicles and Motorbikes are vetted and the goods are insured. Our platform is also secured with latest security features for our users to access it for their delivery requests.</p>
                            </div>
                        </div>
                    </div><!-- .slider-item -->
                    <div class="slider-item">
                        <div class="nk-feature nk-feature-center">
                            <div class="nk-feature-img">
                                <img class="round" src="{{asset("assets/assets/images/best_prices.svg")}}" style="height:360px;" srcset="{{asset("assets/assets/images/best_prices.svg")}}" alt="dashboard ordagh eshopping">
                            </div>
                            <div class="nk-feature-content py-4 p-sm-5">
                                <h4>Best Prices</h4>
                                <p>Our system gives one of the best prices for motorbikes delivery services as well as allow drivers bid for deliveries on our platform ensuring competitive and best prices.</p>
                            </div>
                        </div>
                    </div><!-- .slider-item -->
                    <div class="slider-item">
                        <div class="nk-feature nk-feature-center">
                            <div class="nk-feature-img">
                                <img class="round" src="{{asset("assets/assets/images/easy_access.svg")}}" style="height:360px;" srcset="{{asset("assets/assets/images/easy_access.svg")}}" alt="dashboard ordagh eshopping">
                            </div>
                            <div class="nk-feature-content py-4 p-sm-5">
                                <h4>Easy Access</h4>
                                <p>Access to our large networks of motorbikes, trucks, vans, pickups, towing cars and so on, as never been easier with our webapp, Mobile App and USSD for both online and offline delivery requests.</p>
                            </div>
                        </div>
                    </div><!-- .slider-item -->
                </div><!-- .slider-init -->
                <div class="slider-dots"></div>
                <div class="slider-arrows"></div>
            </div><!-- .slider-wrap -->
        </div><!-- .nk-split-content -->
    </div><!-- .nk-split -->
</div><!-- app body @e -->
<!-- JavaScript -->
<script src="{{asset("assets/html-template/src/assets/js/bundle.js?ver=1.4.0")}}"></script>
<script src="{{asset("assets/html-template/src/assets/js/scripts.js?ver=1.4.0")}}"></script>
</body>

</html>
