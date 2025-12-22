<!-- P2P Payment Modal -->
<div class="modal fade" id="p2pPaymentModal" tabindex="-1" role="dialog" aria-labelledby="p2pPaymentModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="p2pPaymentModalLabel">Complete Payment</h5>
                <button type="button" class="close" id="closeModalBtn" aria-label="Close">
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
                <button type="button" id="checkPaymentStatusBtn" class="btn btn-primary" style="display: none;" onclick="checkPaymentStatusManually()">Done - Check Payment</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let paymentCheckInterval = null;
    let paymentReference = null;
    let paymentType = null;

    // Initialize payment modal function - make it globally available
    window.initP2PPayment = function(postId, applicationId, type) {
        console.log('initP2PPayment called', {postId, applicationId, type});
        paymentType = type; // 'initial' or 'final'
        
        // Reset payment state
        paymentCompleted = false;
        paymentCancelled = false;
        
        // Check if jQuery and modal are available
        if (typeof $ === 'undefined') {
            console.error('jQuery is not loaded');
            alert('Error: jQuery is not loaded. Please refresh the page.');
            return;
        }
        
        if (!$('#p2pPaymentModal').length) {
            console.error('Payment modal element not found');
            alert('Error: Payment modal not found. Please refresh the page.');
            return;
        }
        
        // Show modal (with backdrop static and keyboard disabled)
        $('#p2pPaymentModal').modal({
            backdrop: 'static',
            keyboard: false
        });
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
                console.log('Payment initiation response:', response);
                if (response.status && response.data && response.data.authorization_url) {
                    // Load Paystack checkout in iframe
                    paymentReference = response.data.reference;
                    $('#paymentIframe').attr('src', response.data.authorization_url);
                    $('#paymentLoading').hide();
                    $('#paymentIframeContainer').show();
                    $('#checkPaymentStatusBtn').show(); // Show Done button
                    
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
                console.error('Payment initiation error:', xhr);
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
                        paymentCompleted = true;
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

    // Manual payment status check function
    function checkPaymentStatusManually() {
        if (!paymentReference) {
            showPaymentError('Payment reference not found');
            return;
        }
        
        // Show loading
        $('#checkPaymentStatusBtn').prop('disabled', true).text('Checking...');
        
        $.ajax({
            url: '{{ route("p2p.payment.status") }}',
            method: 'GET',
            data: {
                reference: paymentReference
            },
            success: function(response) {
                console.log('Manual payment status check:', response);
                if (response.status && response.is_successful) {
                    // Payment successful - redirect to callback
                    paymentCompleted = true;
                    if (paymentCheckInterval) {
                        clearInterval(paymentCheckInterval);
                    }
                    $('#p2pPaymentModal').modal('hide');
                    
                    const callbackUrl = '{{ route("p2p.payment.callback") }}?reference=' + paymentReference + '&payment_type=' + paymentType;
                    window.location.href = callbackUrl;
                } else if (response.status && response.transaction_status === 'pending') {
                    // Still pending
                    $('#checkPaymentStatusBtn').prop('disabled', false).text('Done - Check Payment');
                    alert('Payment is still pending. Please wait a moment and try again, or check your email for payment confirmation.');
                } else if (response.status && response.transaction_status === 'failed') {
                    // Payment failed
                    showPaymentError('Payment failed. Please try again.');
                    $('#checkPaymentStatusBtn').prop('disabled', false).text('Done - Check Payment');
                } else {
                    // Unknown status
                    $('#checkPaymentStatusBtn').prop('disabled', false).text('Done - Check Payment');
                    alert('Unable to verify payment status. Please check your email or try again later.');
                }
            },
            error: function(xhr) {
                console.error('Payment status check error:', xhr);
                $('#checkPaymentStatusBtn').prop('disabled', false).text('Done - Check Payment');
                alert('Error checking payment status. Please try again or contact support.');
            }
        });
    }

    // Prevent modal from closing without confirmation
    let paymentCompleted = false;
    let paymentCancelled = false;
    
    // Handle close button click
    $('#closeModalBtn, #cancelPaymentBtn').on('click', function(e) {
        e.preventDefault();
        
        if (paymentCompleted || paymentCancelled) {
            closeModal();
            return;
        }
        
        // Show confirmation prompt
        if (confirm('Are you sure you want to cancel this payment? The payment process will be interrupted.')) {
            paymentCancelled = true;
            closeModal();
        }
    });
    
    // Prevent closing by clicking outside or pressing ESC
    $('#p2pPaymentModal').on('hide.bs.modal', function(e) {
        if (!paymentCompleted && !paymentCancelled) {
            e.preventDefault();
            e.stopPropagation();
            
            // Show confirmation prompt
            if (confirm('Are you sure you want to cancel this payment? The payment process will be interrupted.')) {
                paymentCancelled = true;
                closeModal();
            }
            return false;
        }
    });
    
    // Function to close modal safely
    function closeModal() {
        if (paymentCheckInterval) {
            clearInterval(paymentCheckInterval);
            paymentCheckInterval = null;
        }
        $('#paymentIframe').attr('src', '');
        $('#checkPaymentStatusBtn').hide().prop('disabled', false).text('Done - Check Payment');
        $('#p2pPaymentModal').modal('hide');
        paymentReference = null;
        paymentType = null;
        paymentCompleted = false;
        paymentCancelled = false;
    }

    // Clean up on modal close
    $('#p2pPaymentModal').on('hidden.bs.modal', function() {
        if (paymentCheckInterval) {
            clearInterval(paymentCheckInterval);
            paymentCheckInterval = null;
        }
        $('#paymentIframe').attr('src', '');
        $('#checkPaymentStatusBtn').hide().prop('disabled', false).text('Done - Check Payment');
        paymentReference = null;
        paymentType = null;
        paymentCompleted = false;
        paymentCancelled = false;
    });

    // Listen for postMessage from Paystack iframe (if they support it)
    window.addEventListener('message', function(event) {
        // Verify origin is Paystack
        if (event.origin.includes('paystack.com')) {
            if (event.data && event.data.status === 'success') {
                // Payment successful
                paymentCompleted = true;
                if (paymentCheckInterval) {
                    clearInterval(paymentCheckInterval);
                }
                $('#p2pPaymentModal').modal('hide');
                
                const callbackUrl = '{{ route("p2p.payment.callback") }}?reference=' + paymentReference + '&payment_type=' + paymentType;
                window.location.href = callbackUrl;
            }
        }
    });
});
</script>
