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
        </div><!-- .nk-block-between -->
    </div><!-- .nk-block-head -->

    <div class="row">
        <div class="col-md-6 col-lg-4">
            <div class="card card-bordered">
                <div class="card-body" style="padding: 30px;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div style="color: #777; font-size: 14px;">Balance</div>
                        <a href="{{ route('user.wallet') }}" class="text-muted" style="font-size: 18px; text-decoration: none;" title="Refresh">
                            <em class="icon ni ni-reload"></em>
                        </a>
                    </div>
                    <div class="mb-4">
                        <h2 class="mb-0" style="font-size: 32px; font-weight: 600;">GHS {{ number_format($balance ?? auth()->user()->available_balance, 2) }}</h2>
                    </div>
                    <div class="d-flex" style="margin: 0 -5px;">
                        <button type="button" class="btn btn-primary flex-fill d-flex align-items-center justify-content-center" data-toggle="modal" data-target="#topupModal" style="padding: 12px; margin: 0 5px;">
                            <em class="icon ni ni-wallet-in" style="font-size: 16px; margin-right: 6px;"></em>
                            <span>Top Up</span>
                        </button>
                        <button type="button" class="btn btn-outline-secondary flex-fill d-flex align-items-center justify-content-center" data-toggle="modal" data-target="#withdrawModal" style="padding: 12px; margin: 0 5px;">
                            <em class="icon ni ni-wallet-out" style="font-size: 16px; margin-right: 6px;"></em>
                            <span>Withdraw</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-bordered">
                <div class="card-header bg-white border-bottom">
                    <div class="row align-items-center">
                        <div class="col">
                            <b>Transaction History</b>
    </div>
                        <div class="col-auto">
                            <div class="form-group mb-0">
                                <select id="filterType" class="form-control form-control-sm" style="display: inline-block; width: auto;">
                                    <option value="">All Types</option>
                                    <option value="topup">Topup</option>
                                    <option value="withdrawal">Withdrawal</option>
                                    <option value="payment">Payment</option>
                                    <option value="earning">Earning</option>
                                    <option value="refund">Refund</option>
                                </select>
                </div>
            </div>
                        <div class="col-auto">
                            <div class="form-group mb-0">
                                <select id="filterStatus" class="form-control form-control-sm" style="display: inline-block; width: auto;">
                                    <option value="">All Status</option>
                                    <option value="success">Success</option>
                                    <option value="pending">Pending</option>
                                    <option value="failed">Failed</option>
                                </select>
                            </div>
        </div>
                    </div>
                        </div>
                        <div class="card-body" style="padding: 0px;">
                    <div id="transactionsTableContainer">
                        <table class="table table-striped mb-0">
                                <thead>
                                <tr>
                                <th>Date</th>
                                    <th>Type</th>
                                <th>Description</th>
                                    <th>Amount</th>
                                <th>Status</th>
                                </tr>
                                </thead>
                            <tbody id="transactionsTableBody">
                            @if(isset($transactions) && $transactions->count() > 0)
                                @foreach($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->created_at->format('M d, Y') }}</td>
                                        <td>
                                            @if($transaction->transaction_category === 'credit')
                                                <span class="badge badge-success">Credit</span>
                                            @else
                                                <span class="badge badge-danger">Debit</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($transaction->transaction_type === 'topup')
                                                Wallet Topup
                                            @elseif($transaction->transaction_type === 'withdrawal')
                                                Withdrawal to Bank Account
                                            @elseif($transaction->transaction_type === 'payment')
                                                Payment for Service
                                            @elseif($transaction->transaction_type === 'earning')
                                                Earning from Completed Work
                                            @elseif($transaction->transaction_type === 'refund')
                                                Refund
                                            @else
                                                Transaction
                                            @endif
                                        </td>
                                        <td>
                                            @if($transaction->transaction_category === 'credit')
                                                <span class="text-success">+GHS {{ number_format($transaction->amount, 2) }}</span>
                                            @else
                                                <span class="text-danger">-GHS {{ number_format($transaction->amount, 2) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($transaction->status === 'success')
                                                <span class="badge badge-success">Success</span>
                                            @elseif($transaction->status === 'pending')
                                                <span class="badge badge-warning">Pending</span>
                                            @elseif($transaction->status === 'failed')
                                                <span class="badge badge-danger">Failed</span>
                                            @else
                                                <span class="badge badge-secondary">{{ ucfirst($transaction->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <p class="text-muted mb-0">You have no transactions yet</p>
                                    </td>
                                            </tr>
                                @endif
                                </tbody>
                            </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Up Modal -->
    <div class="modal fade" id="topupModal" tabindex="-1" role="dialog" aria-labelledby="topupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="topupModalLabel">Top Up Wallet</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="topupForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="topupAmount">Amount (GHS)</label>
                            <input type="number" class="form-control" id="topupAmount" name="amount" min="1" step="0.01" required>
                            <small class="form-text text-muted">Minimum amount: GHS 1.00</small>
                        </div>
                        <div id="topupError" class="alert alert-danger" style="display: none;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Proceed to Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Withdraw Modal -->
    <div class="modal fade" id="withdrawModal" tabindex="-1" role="dialog" aria-labelledby="withdrawModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="withdrawModalLabel">Withdraw Funds</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="withdrawForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="withdrawAmount">Amount (GHS)</label>
                            <input type="number" class="form-control" id="withdrawAmount" name="amount" min="10" step="0.01" required>
                            <small class="form-text text-muted">Minimum withdrawal: GHS 10.00. Available balance: GHS {{ number_format($balance ?? auth()->user()->available_balance, 2) }}</small>
                        </div>
                        <div class="form-group">
                            <label for="accountName">Account Name</label>
                            <input type="text" class="form-control" id="accountName" name="account_name" required>
                        </div>
                        <div class="form-group">
                            <label for="accountNumber">Account Number</label>
                            <input type="text" class="form-control" id="accountNumber" name="account_number" required>
                        </div>
                        <div class="form-group">
                            <label for="bankCode">Bank Code</label>
                            <input type="text" class="form-control" id="bankCode" name="bank_code" required>
                            <small class="form-text text-muted">Enter your bank's code (e.g., 058 for GT Bank)</small>
                        </div>
                        <div id="withdrawError" class="alert alert-danger" style="display: none;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Request Withdrawal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Payment Modal (for Top Up) -->
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Complete Payment</h5>
                    <button type="button" class="close" id="closePaymentModalBtn" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="paymentLoading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-3">Initializing payment...</p>
                    </div>
                    <div id="paymentIframeContainer" style="display: none;">
                        <iframe id="paymentIframe" src="" style="width: 100%; height: 600px; border: none;"></iframe>
                    </div>
                    <div id="paymentError" class="alert alert-danger" style="display: none;">
                        <strong>Error:</strong> <span id="paymentErrorText"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelPaymentBtn">Cancel Payment</button>
                    <button type="button" id="checkPaymentStatusBtn" class="btn btn-primary" style="display: none;">Done - Check Payment</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section("scripts")
    <script>
        $(document).ready(function() {
            let paymentCheckInterval = null;
            let paymentReference = null;

            // Top Up Form Submission
            $('#topupForm').on('submit', function(e) {
                e.preventDefault();
                
                const amount = parseFloat($('#topupAmount').val());
                if (amount < 1) {
                    $('#topupError').text('Minimum topup amount is GHS 1.00').show();
                    return;
                }

                $('#topupError').hide();
                $('#topupModal').modal('hide');

                // Show payment modal
                $('#paymentModal').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('#paymentModal').modal('show');

                // Reset payment UI
                $('#paymentLoading').show();
                $('#paymentIframeContainer').hide();
                $('#paymentError').hide();
                $('#checkPaymentStatusBtn').hide();

                // Initiate topup
                $.ajax({
                    url: '{{ route("wallet.topup") }}',
                    method: 'POST',
                    data: {
                        amount: amount,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status && response.data && response.data.authorization_url) {
                            paymentReference = response.data.reference;
                            $('#paymentIframe').attr('src', response.data.authorization_url);
                            $('#paymentLoading').hide();
                            $('#paymentIframeContainer').show();
                            $('#checkPaymentStatusBtn').show();
                            
                            // Start polling for payment status
                            startPaymentStatusPolling(paymentReference);
                        } else {
                            showPaymentError(response.message || 'Failed to initialize payment');
                        }
                    },
                    error: function(xhr) {
                        const error = xhr.responseJSON?.message || 'An error occurred';
                        showPaymentError(error);
                    }
                });
            });

            // Withdraw Form Submission
            $('#withdrawForm').on('submit', function(e) {
                e.preventDefault();
                
                const formData = {
                    amount: parseFloat($('#withdrawAmount').val()),
                    account_name: $('#accountName').val(),
                    account_number: $('#accountNumber').val(),
                    bank_code: $('#bankCode').val(),
                    _token: '{{ csrf_token() }}'
                };

                $('#withdrawError').hide();
                const submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true).text('Processing...');

                $.ajax({
                    url: '{{ route("wallet.withdraw") }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.status) {
                            $('#withdrawModal').modal('hide');
                            alert('Withdrawal request submitted successfully. Your balance will be updated once the transfer is processed.');
                            location.reload();
                        } else {
                            $('#withdrawError').text(response.message || 'Failed to process withdrawal').show();
                            submitBtn.prop('disabled', false).text('Request Withdrawal');
                        }
                    },
                    error: function(xhr) {
                        const error = xhr.responseJSON?.message || 'An error occurred';
                        $('#withdrawError').text(error).show();
                        submitBtn.prop('disabled', false).text('Request Withdrawal');
                    }
                });
            });

            // Payment Status Polling
            function startPaymentStatusPolling(reference) {
                if (paymentCheckInterval) {
                    clearInterval(paymentCheckInterval);
                }

                paymentCheckInterval = setInterval(function() {
                    $.ajax({
                        url: '{{ route("p2p.payment.status") }}?reference=' + reference,
                        method: 'GET',
                        success: function(response) {
                            if (response.status === 'success') {
                                clearInterval(paymentCheckInterval);
                                $('#paymentModal').modal('hide');
                                alert('Payment successful! Your wallet has been topped up.');
                                location.reload();
                            } else if (response.status === 'failed') {
                                clearInterval(paymentCheckInterval);
                                showPaymentError('Payment failed. Please try again.');
                            }
                        },
                        error: function() {
                            // Continue polling on error
                        }
                    });
                }, 3000); // Poll every 3 seconds
            }

            function showPaymentError(message) {
                $('#paymentLoading').hide();
                $('#paymentIframeContainer').hide();
                $('#paymentError').show();
                $('#paymentErrorText').text(message);
            }

            // Close Payment Modal
            $('#closePaymentModalBtn, #cancelPaymentBtn').on('click', function() {
                if (paymentCheckInterval) {
                    clearInterval(paymentCheckInterval);
                    paymentCheckInterval = null;
                }
                $('#paymentIframe').attr('src', '');
                $('#paymentModal').modal('hide');
            });

            // Check Payment Status Button
            $('#checkPaymentStatusBtn').on('click', function() {
                if (paymentReference) {
                    $.ajax({
                        url: '{{ route("p2p.payment.status") }}?reference=' + paymentReference,
                        method: 'GET',
                        success: function(response) {
                            if (response.status === 'success') {
                                $('#paymentModal').modal('hide');
                                alert('Payment successful! Your wallet has been topped up.');
                                location.reload();
                            } else {
                                alert('Payment is still pending. Please wait a moment and try again.');
                            }
                        }
                    });
                }
            });

            // Transaction Filters
            $('#filterType, #filterStatus').on('change', function() {
                loadTransactions();
            });

            function loadTransactions() {
                const filters = {
                    type: $('#filterType').val(),
                    status: $('#filterStatus').val(),
                    _token: '{{ csrf_token() }}'
                };

                $.ajax({
                    url: '{{ route("wallet.transactions") }}',
                    method: 'GET',
                    data: filters,
                    success: function(response) {
                        if (response.status) {
                            const tbody = $('#transactionsTableBody');
                            tbody.empty();

                            if (response.data.length === 0) {
                                tbody.append('<tr><td colspan="5" class="text-center py-4"><p class="text-muted mb-0">No transactions found</p></td></tr>');
                            } else {
                                response.data.forEach(function(transaction) {
                                    const categoryBadge = transaction.category === 'credit' 
                                        ? '<span class="badge badge-success">Credit</span>'
                                        : '<span class="badge badge-danger">Debit</span>';
                                    
                                    const amountClass = transaction.category === 'credit' ? 'text-success' : 'text-danger';
                                    const amountSign = transaction.category === 'credit' ? '+' : '-';
                                    
                                    let statusBadge = '';
                                    if (transaction.status === 'success') {
                                        statusBadge = '<span class="badge badge-success">Success</span>';
                                    } else if (transaction.status === 'pending') {
                                        statusBadge = '<span class="badge badge-warning">Pending</span>';
                                    } else if (transaction.status === 'failed') {
                                        statusBadge = '<span class="badge badge-danger">Failed</span>';
                                    } else {
                                        statusBadge = '<span class="badge badge-secondary">' + transaction.status.charAt(0).toUpperCase() + transaction.status.slice(1) + '</span>';
                                    }

                                    tbody.append(
                                        '<tr>' +
                                        '<td>' + transaction.created_at_formatted + '</td>' +
                                        '<td>' + categoryBadge + '</td>' +
                                        '<td>' + transaction.description + '</td>' +
                                        '<td><span class="' + amountClass + '">' + amountSign + 'GHS ' + parseFloat(transaction.amount).toFixed(2) + '</span></td>' +
                                        '<td>' + statusBadge + '</td>' +
                                        '</tr>'
                                    );
                                });
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection
