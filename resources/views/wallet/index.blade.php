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
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <div class="text-center">
                <div>Balance</div>
                <h2>GHS4,000</h2>
            </div>
            <div class="text-center">
                <a href="" class="btn btn-lg btn-outline-secondary btn-block">Add Cash</a>
            </div>
        </div>
        <div class="col-md-4"></div>
    </div>

    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <br>
            <br>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <a href="">
                        <div class="card card-bordered">
                            <div class="card-body text-center p-4">
                                <img src="{{asset("assets/html-template/src/images/pay.svg")}}"
                                     style="height: 120px; width: 120px;" alt="">
                                <h4>Pay</h4>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="">
                        <div class="card card-bordered">
                            <div class="card-body text-center p-4">
                                <img src="{{asset("assets/html-template/src/images/transfer.svg")}}"
                                     style="height: 120px; width: 120px;" alt="">
                                <h4>Transfer</h4>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <a href="">
                        <div class="card card-bordered">
                            <div class="card-body text-center p-4">
                                <img src="{{asset("assets/html-template/src/images/transactions.svg")}}"
                                     style="height: 120px; width: 120px;" alt="">
                                <h4>Transactions</h4>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-3"></div>
    </div>

@endsection
