<!-- resources/views/components/no-internet-connection.blade.php -->
@props([
'title' => 'No Internet Connection',
'message' => 'Please check your network connection and try again.',
'retryButtonText' => 'Retry',
])

<style>
        /* No Internet Connection Styles */
        #no-internet-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: #f3f6f9;
                display: none;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                z-index: 9999;
                text-align: center;
                padding: 20px;
        }

        .no-internet-content {
                max-width: 500px;
                background-color: white;
                padding: 40px;
                border-radius: 12px;
                /* box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); */
                text-align: center;
        }

        .no-internet-icon {
                width: 150px;
                height: 150px;
                margin-bottom: 20px;
                opacity: 0.7;
        }

        .no-internet-title {
                font-size: 24px;
                color: #333;
                margin-bottom: 15px;
                font-weight: 600;
        }

        .no-internet-message {
                font-size: 16px;
                color: #666;
                margin-bottom: 25px;
                line-height: 1.6;
        }

        .retry-button {
                background-color: #4f46e5;
                color: white;
                border: none;
                padding: 12px 25px;
                border-radius: 8px;
                font-size: 16px;
                cursor: pointer;
                transition: background-color 0.3s ease;
        }

        .retry-button:hover {
                background-color: #4338ca;
        }

        /* Responsive Adjustments */
        @media (max-width: 600px) {
                .no-internet-content {
                        padding: 20px;
                        width: 90%;
                }

                .no-internet-icon {
                        width: 100px;
                        height: 100px;
                }

                .no-internet-title {
                        font-size: 20px;
                }

                .no-internet-message {
                        font-size: 14px;
                }
        }
</style>

<div id="no-internet-overlay">
        <div class="no-internet-content border-1">
                <svg class="no-internet-icon" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                </svg>
                <h2 class="no-internet-title">{{ $title }}</h2>
                <p class="no-internet-message">{{ $message }}</p>
                <button id="retry-connection-btn" class="btn btn-primary btn-full">{{ $retryButtonText }}</button>
        </div>
</div>

<script>
        document.addEventListener('DOMContentLoaded', function() {
                const noInternetOverlay = document.getElementById('no-internet-overlay');
                const retryButton = document.getElementById('retry-connection-btn');

                // Network status checking function
                function checkInternetConnection() {
                        return navigator.onLine;
                }

                // Show no internet overlay
                function showNoInternetOverlay() {
                        noInternetOverlay.style.display = 'flex';
                }

                // Hide no internet overlay
                function hideNoInternetOverlay() {
                        noInternetOverlay.style.display = 'none';
                }

                // Network event listeners
                window.addEventListener('online', function() {
                        hideNoInternetOverlay();
                        // Optional: Reload the page or refresh content
                        location.reload();
                });

                window.addEventListener('offline', function() {
                        showNoInternetOverlay();
                });

                // Retry button functionality
                retryButton.addEventListener('click', function() {
                        if (checkInternetConnection()) {
                                // If connection is restored, reload the page
                                location.reload();
                        } else {
                                // Show error or keep overlay
                                alert('No internet connection. Please check your network.');
                        }
                });

                // Initial connection check
                if (!checkInternetConnection()) {
                        showNoInternetOverlay();
                }

                // Periodic connection check
                setInterval(function() {
                        if (!checkInternetConnection()) {
                                showNoInternetOverlay();
                        }
                }, 5000);
        });
</script>