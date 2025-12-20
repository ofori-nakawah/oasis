<!-- P2P Payment Modal -->
<div class="modal fade" id="p2pPaymentModal" tabindex="-1" role="dialog" aria-labelledby="p2pPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="p2pPaymentModalLabel">Complete Payment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    let paymentCheckInterval = null;
    let paymentReference = null;
    let paymentType = null;

    // Initialize payment modal
    window.initP2PPayment = function(postId, applicationId, type) {
        paymentType = type; // 'initial' or 'final'
        
        // Show modal
        $('#p2pPaymentModal').modal('show');
        
        // Reset UI
        $('#paymentLoading').show();
        $('#paymentIframeContainer').hide();
        $('#paymentError').hide();
        
        // Determine endpoint based on payment type
        const endpoint = type === 'initial' 
            ? '{{ route("p2p.initiate.quote.approval.payment") }}'
            : '{{ route("p2p.initiate.job.closure.payment") }}';
        
        // Initiate payment
        $.ajax({
            url: endpoint,
            method: 'POST',
            data: {
                post_id: postId,
                application_id: applicationId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.status && response.data && response.data.authorization_url) {
                    // Load Paystack checkout in iframe
                    paymentReference = response.data.reference;
                    $('#paymentIframe').attr('src', response.data.authorization_url);
                    $('#paymentLoading').hide();
                    $('#paymentIframeContainer').show();
                    
                    // Start polling for payment status
                    startPaymentStatusPolling(paymentReference);
                } else if (response.skip_payment) {
                    // No payment required
                    $('#p2pPaymentModal').modal('hide');
                    location.reload(); // Reload page to show updated status
                } else {
                    showPaymentError(response.message || 'Failed to initialize payment');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Failed to initialize payment';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                showPaymentError(errorMessage);
            }
        });
    };

    // Start polling for payment status
    function startPaymentStatusPolling(reference) {
        // Clear any existing interval
        if (paymentCheckInterval) {
            clearInterval(paymentCheckInterval);
        }
        
        // Poll every 3 seconds
        paymentCheckInterval = setInterval(function() {
            $.ajax({
                url: '{{ route("p2p.payment.status") }}',
                method: 'GET',
                data: {
                    reference: reference
                },
                success: function(response) {
                    if (response.status && response.is_successful) {
                        // Payment successful
                        clearInterval(paymentCheckInterval);
                        $('#p2pPaymentModal').modal('hide');
                        
                        // Redirect to callback or reload page
                        const callbackUrl = '{{ route("p2p.payment.callback") }}?reference=' + reference + '&payment_type=' + paymentType;
                        window.location.href = callbackUrl;
                    } else if (response.status && response.transaction_status === 'failed') {
                        // Payment failed
                        clearInterval(paymentCheckInterval);
                        showPaymentError('Payment failed. Please try again.');
                    }
                },
                error: function() {
                    // Continue polling on error
                }
            });
        }, 3000);
    }

    // Show payment error
    function showPaymentError(message) {
        $('#paymentLoading').hide();
        $('#paymentIframeContainer').hide();
        $('#paymentErrorText').text(message);
        $('#paymentError').show();
        
        // Clear polling interval
        if (paymentCheckInterval) {
            clearInterval(paymentCheckInterval);
        }
    }

    // Clean up on modal close
    $('#p2pPaymentModal').on('hidden.bs.modal', function() {
        if (paymentCheckInterval) {
            clearInterval(paymentCheckInterval);
            paymentCheckInterval = null;
        }
        $('#paymentIframe').attr('src', '');
        paymentReference = null;
        paymentType = null;
    });

    // Listen for postMessage from Paystack iframe (if they support it)
    window.addEventListener('message', function(event) {
        // Verify origin is Paystack
        if (event.origin.includes('paystack.com')) {
            if (event.data && event.data.status === 'success') {
                // Payment successful
                if (paymentCheckInterval) {
                    clearInterval(paymentCheckInterval);
                }
                $('#p2pPaymentModal').modal('hide');
                
                const callbackUrl = '{{ route("p2p.payment.callback") }}?reference=' + paymentReference + '&payment_type=' + paymentType;
                window.location.href = callbackUrl;
            }
        }
    });
})();
</script>
