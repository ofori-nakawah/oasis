<!DOCTYPE html>
<html lang="en" class="js">
<head>
    <meta charset="utf-8">
    <meta name="author" content="AppTechHub Global">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" >
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="{{asset("assets/assets/images/favico.png")}}">
    <!-- Page Title  -->
    <title>VORK | @section("title") @show</title>
    <!-- StyleSheets  -->
    <link rel="stylesheet" href="{{asset("assets/html-template/src/assets/css/dashlite.css?ver=1.4.0")}}">
    <link id="skin-default" rel="stylesheet" href="{{asset("assets/html-template/src/assets/css/skins/theme-egyptian.css?ver=1.4.0")}}">
    {{--    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Signika:wght@300;400;600;700&amp;display=swap">--}}
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
            color: #000;
        }

        hr {
            border-top: none;
            border-bottom: 1px solid #dbdfea;
        }

        table{
            /*border-bottom: 1px solid #8094ae;*/
            border-bottom: 1px solid #e5e9f2;
        }

        *, .link, .btn, .nk-block-title, h1, h2, h3, h4, h5, h6, table, div, span, a, p, .nk-iv-wg2-title .title {
            /* font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif; */
            /* font-family: 'Lilita One', cursive; */
            /* font-family: 'Poppins', sans-serif; */
            /* font-family: 'Source Sans Pro', sans-serif; */
            font-family: 'lato', sans-serif;
        }

        .alignDataTablePaginationCenter{
            width: 100%;
            text-align: center !important;
        }

        /*.form-control-lg{*/
        /*    height: 60px;*/
        /*}*/

        /*.select2-container--default .select2-selection--multiple .select2-selection__rendered{*/
        /*    height: 60px;*/
        /*}*/

        /*#showLocation{*/
        /*    height: 60px;*/
        /*    background: #f5f6fa !important;*/
        /*}*/

        /*.select2-container--default.select2-lg .select2-selection--multiple .select2-selection__choice {*/
        /*    border-radius: 4px;*/
        /*    padding: 0.25rem .75rem;*/
        /*    align-items: center;*/
        /*    vertical-align: middle;*/
        /*    margin-top: 10px;*/
        /*    margin-bottom: 10px;*/
        /*}*/

        div.dataTables_info {position:absolute}
        div.dataTables_wrapper div.dataTables_paginate {float:none; text-align:center}
        .dataTables_paginate {
            width: 100%;
            text-align: center;
        }
        /*.btn-outline-primary:hover, .btn-outline-primary:active{*/
        /*    color: #2263b3 !important*/
        /*}*/

        .pac-container {
            z-index: 1061;
        }
        .modal{z-index: 1060;}
        .modal-backdrop {
            z-index: -1;
        }


    </style>
</head>

<body class="nk-body bg-white npc-general has-sidebar ">
<div class="nk-app-root">
    <!-- main @s -->
    <div class="nk-main ">
        <!-- sidebar @s -->
        <div class="nk-sidebar nk-sidebar-fixed is-light " data-content="sidebarMenu">
            <div class="nk-sidebar-element nk-sidebar-head">
                <div class="nk-sidebar-brand">
                    <a href="" class="logo-link nk-sidebar-logo">
                        <img class="logo-light logo-img" src="{{asset("assets/html-template/src/images/logo_white_bg.png")}}" srcset="{{asset("assets/html-template/src/images/logo_white_bg.png")}} 2x" alt="logo">
                        <img class="logo-dark logo-img" src="{{asset("assets/html-template/src/images/logo_white_bg.png")}}" srcset="{{asset("assets/html-template/src/images/logo_white_bg.png")}} 2x" alt="logo-dark" style="min-width: 120px !important;min-height: 60px;">
                        {{--                        <span class="nio-version">Vendors</span>--}}
                    </a>
                </div>
                <div class="nk-menu-trigger mr-n2">
                    <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu"><em class="icon ni ni-arrow-left"></em></a>
                </div>
            </div><!-- .nk-sidebar-element -->
            <div class="nk-sidebar-element">
                <div class="nk-sidebar-content">
                    <div class="nk-sidebar-menu" data-simplebar>
                        <ul class="nk-menu">
                            <li class="nk-menu-item">
                                <a href="{{route("home")}}" class="nk-menu-link">
                                    <span class="nk-menu-icon"><em class="icon ni ni-growth"></em></span>
                                    <span class="nk-menu-text"> Dashboard</span>
                                </a>
                            </li><!-- .nk-menu-item -->
                            <li class="nk-menu-item">
                                <a href="{{route('user.posts.list')}}" class="nk-menu-link">
                                    <span class="nk-menu-icon"><em class="icon ni ni-edit-alt"></em></span>
                                    <span class="nk-menu-text"> Postings</span>
                                </a>
                            </li><!-- .nk-menu-item -->
                            <li class="nk-menu-item">
                                <a href="{{route("user.notifications")}}" class="nk-menu-link">
                                    <span class="nk-menu-icon"><em class="icon ni ni-bell"></em></span>
                                    <span class="nk-menu-text"> Notifications @if(auth()->user()->unreadNotifications->count() > 0) <span class="badge badge-danger circle">{{auth()->user()->unreadNotifications->count() ?? ''}}</span> @endif</span>
                                </a>
                            </li><!-- .nk-menu-item -->
                            <li class="nk-menu-item">
                                <a href="{{route('user.wallet')}}" class="nk-menu-link">
                                    <span class="nk-menu-icon"><em class="icon ni ni-wallet-alt"></em></span>
                                    <span class="nk-menu-text"> Wallet</span>
                                </a>
                            </li><!-- .nk-menu-item -->
                            <li class="nk-menu-item">
                                <a href="" class="nk-menu-link">
                                    <span class="nk-menu-icon"><em class="icon ni ni-setting-alt"></em></span>
                                    <span class="nk-menu-text"> Settings</span>
                                </a>
                            </li><!-- .nk-menu-item -->
                        </ul><!-- .nk-menu -->
                    </div><!-- .nk-sidebar-menu -->
                </div><!-- .nk-sidebar-content -->
            </div><!-- .nk-sidebar-element -->
        </div>
        <!-- sidebar @e -->
        <!-- wrap @s -->
        <div class="nk-wrap bg-white">
            <!-- main header @s -->
            <div class="nk-header nk-header-fixed is-light">
                <div class="container-fluid">
                    <div class="nk-header-wrap">
                        <div class="nk-menu-trigger d-xl-none ml-n1">
                            <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a>
                        </div>
                        <div class="nk-header-brand d-xl-none">
                            <a href="{{route("home")}}" class="logo-link">
                                <img class="logo-light logo-img" src="{{asset("assets/html-template/src/images/logo_white_bg.png")}}" srcset="{{asset("assets/html-template/src/images/logo_white_bg.png")}} 2x" alt="logo">
                                <img class="logo-dark logo-img" src="{{asset("assets/html-template/src/images/logo_white_bg.png")}}" srcset="{{asset("assets/html-template/src/images/logo_white_bg.png")}} 2x" alt="logo-dark">
                            </a>
                        </div><!-- .nk-header-brand -->
                        <div class="nk-header-news d-none d-xl-block">
                            <div class="nk-news-list">
                            </div>
                        </div><!-- .nk-header-news -->
                        <div class="nk-header-tools">
                            <ul class="nk-quick-nav">
                                <li>
                                    <a href="{{route("user.volunteerism")}}" class="btn btn-outline-primary"><b>Volunteer</b></a>
                                </li>
                                <li>
                                    <a href="{{route("user.work")}}" class="btn btn-outline-success"><b>Work</b></a>
                                </li>
                                <li class="dropdown user-dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <div class="user-toggle">
                                            <div class="user-avatar sm">
                                                <em class="icon ni ni-user-alt"></em>
                                            </div>
                                            <div class="user-info d-none d-md-block">
                                                <div class="user-status">Welcome,</div>
                                                <div class="user-name dropdown-indicator">{{Auth::user()->name}}</div>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-md dropdown-menu-right dropdown-menu-s1">
                                        <div class="dropdown-inner user-card-wrap bg-lighter d-none d-md-block">
                                            <div class="user-card">
                                                <div class="user-avatar">
                                                    <span>AB</span>
                                                </div>
                                                <div class="user-info">
                                                    <span class="lead-text">{{Auth::user()->name}}</span>
                                                    <span class="sub-text">{{Auth::user()->email}}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="dropdown-inner">
                                            <ul class="link-list">
                                                <li><a href="{{route('user.profile', ['user_id' => auth()->id()])}}"><em class="icon ni ni-user-alt"></em><span>View Profile</span></a></li>
                                                {{--                                                <li><a href="javascript:void(0);"><em class="icon ni ni-setting-alt"></em><span>Account Setting</span></a></li>--}}
                                                {{--                                                <li><a href="javascript:void(0);"><em class="icon ni ni-activity-alt"></em><span>Login Activity</span></a></li>--}}
                                            </ul>
                                        </div>
                                        <div class="dropdown-inner">
                                            <ul class="link-list">
                                                <li><a href="javascript:void(0)" onclick="event.preventDefault();
                                                     var conf = confirm('Are you sure you want to logout?');
                                                     if(conf){
                                                        document.getElementById('logout-form').submit();
                                                     }"><em class="icon ni ni-signout"></em><span>Sign out</span></a>
                                                    <form id="logout-form" action="{{route('logout')}}" method="POST" style="display: none;">
                                                        {{ csrf_field() }}
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </li><!-- .dropdown -->
                            </ul><!-- .nk-quick-nav -->
                        </div><!-- .nk-header-tools -->
                    </div><!-- .nk-header-wrap -->
                </div><!-- .container-fliud -->
            </div>
            <!-- main header @e -->
            <!-- content @s -->
            <div class="nk-content bg-white">
                <div class="container-fluid">
                    <div class="nk-content-inner">
                        <div class="nk-content-body">
                            @section("content") @show
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- wrap @e -->
    </div>
    <!-- main @e -->
</div>
<!-- app-root @e -->
<script src="{{asset("assets/html-template/src/assets/js/bundle.js?ver=1.4.0")}}"></script>
<script src="{{asset("assets/html-template/src/assets/js/scripts.js?ver=1.4.0")}}"></script>
<script src="{{asset("assets/html-template/src/assets/js/charts/gd-general.js?ver=1.4.0")}}"></script>

<script>
    oTable = $('.appDataTable').DataTable({
        sDom: 'lrtip',
        "bPaginate": true,
        "bLengthChange": false,
        "pageLength": 10,
        "bFilter": true,
        "dom": 't<"row mt-4 mb-4 text-center align-content-center"<"col-sm-4"><"col-sm-4 alignDataTablePaginationCenter align-center align-content-center text-center"p><"col-sm-4">>',
        "bInfo": false,
        "order": []
    });
    $('.appSearchBox').keyup(function(){
        oTable.search($(this).val()).draw() ;
    });

    @if(Session::has('info'))
    NioApp.Toast('{{ Session::get('info') }}', 'info', {position: 'top-right'});
    @endif

    @if(Session::has('danger'))
    NioApp.Toast('{{ Session::get('danger') }}', 'error', {position: 'top-right'});
    // NioApp.Toast('This is a note for bottom right toast message.', 'info', {position: 'top-right'});
    @endif


    @if(Session::has('success'))
    NioApp.Toast('{{ Session::get('success') }}', 'success', {position: 'top-right'});
    @endif

    @if(Session::has('warning'))
    NioApp.Toast('{{ Session::get('warning') }}', 'warning', {position: 'top-right'});
    @endif


    // NioApp.Toast('This is a note for bottom right toast message.', 'info', {position: 'bottom-full'});
</script>
@section("scripts")

@show
</body>

</html>
