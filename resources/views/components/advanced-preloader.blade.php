<!-- resources/views/components/preloader.blade.php -->
@props([
'loadingText' => 'Loading...',
'spinnerColor' => '#6366f1',
'backgroundColor' => 'rgba(255, 255, 255, 0.8)'
])

<style>
        /* Preloader Global Styles */
        #preloader-container {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;

                background-color: '{{$backgroundColor}}';
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;
                opacity: 1;
                visibility: visible;
                transition: opacity 0.3s ease-in-out,
                        visibility 0.3s ease-in-out;
        }

        .preloader-content {
                text-align: center;
                display: flex;
                flex-direction: column;
                align-items: center;
        }

        .preloader-spinner {
                width: 60px;
                height: 60px;

                border: 5px solid '{{ $spinnerColor }}';

                border-top: 5px solid transparent;
                border-radius: 50%;
                animation: preloader-spin 1s linear infinite;
                margin-bottom: 15px;
        }

        .preloader-text {
                color: #333;
                font-size: 16px;
                font-weight: 500;
        }

        /* Fallback Animations */
        @keyframes preloader-spin {
                0% {
                        transform: rotate(0deg);
                }

                100% {
                        transform: rotate(360deg);
                }
        }

        @keyframes pulse {
                0% {
                        transform: scale(1);
                }

                50% {
                        transform: scale(1.05);
                }

                100% {
                        transform: scale(1);
                }
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
                .preloader-spinner {
                        width: 50px;
                        height: 50px;
                }

                .preloader-text {
                        font-size: 14px;
                }
        }

        /* Progressive Loading States */
        #preloader-container.loading-complete {
                opacity: 0;
                visibility: hidden;
        }
</style>

<div id="preloader-container">
        <div class="preloader-content">
                <div class="preloader-spinner"></div>
                <div class="preloader-text">{{ $loadingText }}</div>
        </div>
</div>

<script>
        document.addEventListener('DOMContentLoaded', function() {
                const preloaderContainer = document.getElementById('preloader-container');

                // Comprehensive Loading State Management
                function initializePreloader() {
                        // Show preloader immediately
                        preloaderContainer.style.opacity = '1';
                        preloaderContainer.style.visibility = 'visible';

                        // Multiple loading state handlers
                        const loadingCompleteEvents = [
                                'load', // Standard page load
                                'turbolinks:load', // Turbolinks support
                                'livewire:load', // Livewire support
                                'alpine:init', // Alpine.js support
                                'htmx:load' // HTMX support
                        ];

                        function markLoadingComplete() {
                                preloaderContainer.classList.add('loading-complete');

                                // Optional: Completely remove preloader after transition
                                setTimeout(() => {
                                        preloaderContainer.style.display = 'none';
                                }, 300);
                        }

                        // Attach multiple event listeners
                        loadingCompleteEvents.forEach(eventName => {
                                window.addEventListener(eventName, markLoadingComplete);
                        });

                        // Fallback timeout
                        setTimeout(markLoadingComplete, 5000);

                        // Handle page visibility changes
                        document.addEventListener('visibilitychange', function() {
                                if (document.visibilityState === 'visible') {
                                        markLoadingComplete();
                                }
                        });
                }

                // AJAX and Fetch Request Tracking
                function setupRequestTracking() {
                        // XMLHttpRequest tracking
                        const originalXHR = window.XMLHttpRequest.prototype.open;
                        window.XMLHttpRequest.prototype.open = function() {
                                this.addEventListener('loadstart', () => {
                                        preloaderContainer.style.opacity = '1';
                                        preloaderContainer.style.visibility = 'visible';
                                });
                                this.addEventListener('loadend', () => {
                                        preloaderContainer.classList.add('loading-complete');
                                });
                                originalXHR.apply(this, arguments);
                        };

                        // Fetch API tracking
                        const originalFetch = window.fetch;
                        window.fetch = function() {
                                preloaderContainer.style.opacity = '1';
                                preloaderContainer.style.visibility = 'visible';

                                return originalFetch.apply(this, arguments)
                                        .finally(() => {
                                                preloaderContainer.classList.add('loading-complete');
                                        });
                        };
                }

                // Initialize preloader and request tracking
                initializePreloader();
                setupRequestTracking();
        });
</script>