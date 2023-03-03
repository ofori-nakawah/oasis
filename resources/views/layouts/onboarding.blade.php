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
