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
                    <div class="card card-bordered" style="height: 270px;">
                        <div class="card-body">
                            <div class="mt-4">
                                <div><b>Total Earned Income</b></div>
                                <h4><b class="text-success">GHS</b> <br>
                                <b class="text-success">{{$dashboard_analytics["total_earnings"]}}</b></h4>

                                <br>

                                <div><b>Estimated Income Tax</b></div>
                                <div><b class="text-danger">GHS {{$dashboard_analytics["estIncomeTax"]}}</b></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="card card-bordered" style="height: 270px;">
                        <div class="card-body" >
                            <div style="margin-top: -15px">
                                <div class="text-center"><b>Income Trend</b></div>
                                <canvas id="chart" style="height: 250px;"></canvas>
                            </div>
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
                    <div class="tab-content" style="padding: 0px;min-height: 280px;">
                        <div class="card-inner tab-pane active" id="volunteer" style="padding: 0px;height: 250px;overflow-y: scroll">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Distance(km)</th>
                                    <th>Description</th>
                                    <th>Volunteer Hours</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($opportunities["volunteer_activities"] as $activity)
                                    <tr>
                                        <td>{{$activity->distance}}</td>
                                        <td><a href="{{route("user.volunteerism.show", ["uuid" => $activity->id])}}">{{$activity->name}}</a></td>
                                        <td>{{$activity->volunteer_hours}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane card-inner" id="quick_job" style="padding: 0px;height: 280px;overflow-y: scroll">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Distance(km)</th>
                                    <th>Description</th>
                                    <th>Budget</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($opportunities["quick_jobs"] as $job)
                                    <tr>
                                        <td>{{$job->distance}}</td>
                                        <td><a href="{{route('user.quick_job.show', ['uuid' => $job->id])}}">{{$job->description}}</a></td>
                                        <td>{{$job->min_budget}} - {{$job->max_budget}}</td>
                                    </tr>
                                @endforeach
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
                        @if($dashboard_analytics["number_of_jobs"] <= 0)
                            <tr>
                                <td colspan="5"><p class="text-center">You have no completed jobs at the moment</p></td>
                            </tr>
                        @else
                            @foreach($job_history as $work)
                                @if($work->job_post && $work->job_post->type != "VOLUNTEER")
                                    <tr>
                                        <td>{{$work->ref_id}}</td>
                                        <td>{{$work->job_post->user->name}}</td>
                                        <td>{{$work->job_post->category}}</td>
                                        <td>GHS {{number_format($work->job_post->final_payment_amount, 2)}}</td>
                                        <td>{{number_format(($work->rating_and_reviews) ? $work->rating_and_reviews->rating : 0, 2)}}</td>
                                    </tr>
                                @endif
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

   <br>

   <div class="row">
       <div class="col-md-12">
           <div class="card card-bordered">
               <div class="card-header bg-white border-bottom">
                   <b>Volunteer History</b>
               </div>
               <div class="card-body" style="padding: 0px;">
                   <table class="table table-striped">
                       <thead>
                       <tr>
                           <th>Activity ID</th>
                           <th>Date</th>
                           <th>Organiser</th>
                           <th>Description</th>
                           <th>Volunteer Hours</th>
                       </tr>
                       </thead>
                       <tbody>
                       @if($dashboard_analytics["number_of_activities"] <= 0)
                           <tr>
                               <td colspan="5"><p class="text-center">You have no completed volunteer activities at the moment</p></td>
                           </tr>
                       @else
                           @foreach($job_history as $work)
                               @if($work->job_post && $work->job_post->type == "VOLUNTEER")
                                   <tr>
                                       <td>{{$work->ref_id}}</td>
                                       <td>{{$work->job_post->date}}</td>
                                       <td>{{$work->job_post->user->name}}</td>
                                       <td>{{$work->job_post->description}}</td>
                                       <td>{{$work->volunteer_hours}}</td>
                                   </tr>
                               @endif
                           @endforeach
                       @endif
                       </tbody>
                   </table>
               </div>
           </div>
       </div>
   </div>

@endsection

@section("scripts")
    <script src="{{asset('assets/html-template/src/assets/js/example-chart.js?ver=1.4.0"')}}"></script>

    <script>
        const ctx = document.getElementById('chart');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'In-flow',
                    data: [{{$incomeData["janIncome"]}}, {{$incomeData["febIncome"]}}, {{$incomeData["marIncome"]}}, {{$incomeData["aprIncome"]}}, {{$incomeData["mayIncome"]}}, {{$incomeData["junIncome"]}}, {{$incomeData["julIncome"]}}, {{$incomeData["augIncome"]}}, {{$incomeData["sepIncome"]}}, {{$incomeData["octIncome"]}}, {{$incomeData["novIncome"]}}, {{$incomeData["decIncome"]}}],
                    borderWidth: 1,
                    fill: false,
                    borderColor: 'green'
                },
                {
                    label: 'Out-flow',
                    data: [{{$taxData["janTax"]}}, {{$taxData["febTax"]}}, {{$taxData["marTax"]}}, {{$taxData["aprTax"]}}, {{$taxData["mayTax"]}}, {{$taxData["junTax"]}}, {{$taxData["julTax"]}}, {{$taxData["augTax"]}}, {{$taxData["sepTax"]}}, {{$taxData["octTax"]}}, {{$taxData["novTax"]}}, {{$taxData["decTax"]}}],
                    borderWidth: 1,
                    fill: false,
                    borderColor: 'red'
                }]
            },
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: 'Income Trend',
                    }
                }
            },
        });
    </script>
@endsection
