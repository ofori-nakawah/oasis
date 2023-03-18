@extends("layouts.master")

@section('title')
    Dashboard
@endsection

@section("content")
   <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title"><em class="icon ni ni-growth"></em> Dashboard </h3>
                <div class="nk-block-des text-soft">
                    <p class="hide-mb-sm hide-mb-xs md">
                    <nav>
                        <ul class="breadcrumb breadcrumb-arrow">
                            <li class="breadcrumb-item"><a href="#">VORK Overview</a></li>
                        </ul>
                    </nav>
                    </p>
                </div>
            </div><!-- .nk-block-head-content -->
            <div class="nk-block-head-content">
                <a href=""
                   class="btn btn-outline-primary"><span>Reload</span></a></li>
            </div><!-- .nk-block-head-content -->
        </div><!-- .nk-block-between -->
    </div><!-- .nk-block-head -->

    <div class="row">
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-3">
                    <div class="card card-bordered">
                        <div class="card-body">
                            <p>Overall Rating</p>
                            <h5>{{$dashboard_analytics["average_rating"]}}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-bordered">
                        <div class="card-body">
                            <p>Jobs Executed</p>
                            <h5>{{$dashboard_analytics["number_of_jobs"]}}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-bordered">
                        <div class="card-body">
                            <p>Volunteer Hours</p>
                            <h5>{{$dashboard_analytics["volunteer_hours"]}}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-bordered">
                        <div class="card-body">
                            <p>Activities</p>
                            <h5>{{$dashboard_analytics["number_of_activities"]}}</h5>
                        </div>
                    </div>
                </div>
            </div>

            <br>

            <div class="row">
                <div class="col-md-3">
                    <div class="card card-bordered" style="height: 240px;">
                        <div class="card-body">
                            <div class="mt-2">
                                <div><b>Income</b></div>
                                <h4><b class="text-success">GHS</b> <br>
                                <b class="text-success">{{$dashboard_analytics["total_earnings"]}}</b></h4>

                                <br>

                                <div><b>Est. Income Tax</b></div>
                                <div><b class="text-danger">GHS 3,000</b></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="card card-bordered" style="height: 240px;">
                        <div class="card-body">

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-bordered">
                <div class="card-header bg-white border-bottom" >
                    <b>Opportunities Around You</b>
                </div>
                <div class="card-body" style="padding: 0px;">
                    <ul class="nav nav-tabs nav-tabs-mb-icon nav-tabs-card">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#volunteer"><span>Volunteer</span></a>
                        </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#quick_job"><span>Quick Job</span></a>
                            </li>

                    </ul><!-- .nav-tabs -->
                    <div class="tab-content" style="padding: 0px;min-height: 250px;">
                        <div class="card-inner tab-pane active" id="volunteer" style="padding: 0px;">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Dist(km)</th>
                                    <th>Description</th>
                                    <th>V.Hours</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>0.6</td>
                                    <td><a href="">Desilting of gutter at Accra Academy school</a></td>
                                    <td>5</td>
                                </tr>
                                <tr>
                                    <td>2.6</td>
                                    <td>Beach cleaning at labadi</td>
                                    <td>5</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane card-inner" id="quick_job" style="padding: 0px;">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Dist(km)</th>
                                    <th>Description</th>
                                    <th>Budget</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>0.6</td>
                                    <td><a href="">Desilting of gutter at Accra Academy school</a></td>
                                    <td>500</td>
                                </tr>
                                <tr>
                                    <td>2.6</td>
                                    <td>Beach cleaning at labadi</td>
                                    <td>500</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-bordered">
                <div class="card-header bg-white border-bottom">
                    <b>Work History</b>
                </div>
                <div class="card-body" style="padding: 0px;">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Job ID</th>
                            <th>Issuer</th>
                            <th>Category</th>
                            <th>Income</th>
                            <th>Rating</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>QJ-234333</td>
                            <td>Bernard Ofori</td>
                            <td>Painting</td>
                            <td>400</td>
                            <td>3.5</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
