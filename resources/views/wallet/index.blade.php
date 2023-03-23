@extends("layouts.master")

@section('title')
    Wallet
@endsection

@section("content")
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title"><em class="icon ni ni-wallet-alt"></em> Wallet </h3>
                <div class="nk-block-des text-soft">
                    <p class="hide-mb-sm hide-mb-xs md">
                    <nav>
                        <ul class="breadcrumb breadcrumb-arrow">
                            <li class="breadcrumb-item"><a href="#">My VORK Wallet</a></li>
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
        <div class="col-md-4">
            <div class="card card-bordered">
                <div class="card-body" style="padding-top: 40px;padding-bottom: 40px;">
                    <div class="text-center">
                        <div style="color: #777;">Balance</div>
                        <h2>GHS{{number_format(auth()->user()->available_balance, 2)}}</h2>
                    </div>
                    <div class="text-center">
                        <a href="" class="btn btn-lg btn-primary btn-block"><b>Add Cash</b></a>
                    </div>
                </div>
                <div class="card-footer bg-white border-top">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-6">
                            <a href="" class="btn btn-lg btn-outline-secondary btn-block"><b>Pay</b></a>
                        </div>
                        <div class="col-md-6 col-sm-6 col-6">
                            <a href="" class="btn btn-lg btn-outline-secondary btn-block"><b>Transfer</b></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4"></div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-4">
            <div class="card card-bordered undelineLinks" style="text-align: left !important;">
                <div class="card-header bg-white"><b>My VORK Wallet</b></div>
                <div class="card-body border-top" style="padding-top: 15px;padding-bottom: 10px;">
                    <a id="recentTransactionsLink" href="javascript:void(0)" class="text-muted">Transaction History <span
                            style="float: right;"><em class="icon ni ni-chevron-right"
                                                      style="font-size: 22px;"></em></span></a>
                </div>
                <div class="card-body border-top" style="padding-top: 15px;padding-bottom: 10px;">
                    <a id="calculateTaxLink" href="javascript:void(0)" class="text-muted">Calculate Tax <span
                            style="float: right;"><em class="icon ni ni-chevron-right"
                                                      style="font-size: 22px;"></em></span></a>
                </div>
            </div>
            <br>
        </div>
        <div class="col-md-8">
            <div id="infoContentBox">
                <div id="emptyState">
                    <div class="text-center" style="margin-top: 10px;">
                        <img src="{{asset('assets/html-template/src/images/details.svg')}}" alt=""
                             style="height: 200px; width: 200px;">
                        <p style="color: #777;">Click on link you get information.</p>
                    </div>
                </div>
                <div id="loadingState">
                    <p class="text-center" style="color: #777;margin-top: 25px;">Loading...</p>
                </div>
                <div id="recentTransactionsBox">
                    <div class="card card-bordered">
                        <div class="card-header bg-white border-bottom">
                            <b>Recent Transactions</b>
                        </div>
                        <div class="card-body" style="padding: 0px;">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Vork 1%</th>
                                    <th>PAYE 5%</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($jobCount == 0)
                                    <tr>
                                        <td colspan="5"><p class="text-center">You have no transactions</p></td>
                                    </tr>
                                @else
                                    @foreach($job_history as $work)
                                        @if($work->job_post && $work->job_post->type != "VOLUNTEER")
                                            <tr>
                                                <td>{{$work->ref_id}}</td>
                                                <td>WORK PAYMENT</td>
                                                <td>GHS {{number_format($work->job_post->final_payment_amount, 2)}}</td>
                                                <td>{{(1/ 100) * $notify->data["post"]["final_payment_amount"]}}</td>
                                                <td>{{(5/ 100) * $notify->data["post"]["final_payment_amount"]}}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div id="calculateTaxBox">
                    <div class="text-center" style="margin-top: 0px;">
                        <img src="{{asset('assets/html-template/src/images/wip.svg')}}"
                             style="height: 200px; width: 200px" alt="">
                        <p style="color: #777;">This feature is in maintenance mode. Come back later</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section("scripts")
    <script>
        $("#emptyState").hide()
        $("#loadingState").hide()
        $("#recentTransactionsBox").show()
        $("#calculateTaxBox").hide()

        $("#recentTransactionsLink").on("click", function () {
            clearContentBox()
            $("#loadingState").show()
            $("#emptyState").hide()
            $("#recentTransactionsBox").hide()
            $("#calculateTaxBox").hide()
            $("#certificationsBox").hide()
            setTimeout(function () {
                $("#loadingState").hide()
                $("#recentTransactionsBox").show()
            }, 2000)
        })

        $("#calculateTaxLink").on("click", function () {
            clearContentBox()
            $("#loadingState").show()
            $("#emptyState").hide()
            $("#recentTransactionsBox").hide()
            $("#calculateTaxBox").hide()
            $("#certificationsBox").hide()
            setTimeout(function () {
                $("#loadingState").hide()
                $("#calculateTaxBox").show()
            }, 2000)
        })

        function clearContentBox() {
            $("#emptyState").hide()
            $("#loadingState").hide()
            $("#vorkHistoryBox").hide()
            $("#jobExperienceBox").hide()
        }

    </script>
@endsection
