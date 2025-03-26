<!-- resources/views/layouts/preloader.blade.php -->
<style>
        /* Preloader Styles */
        #preloader {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: #f3f4f6;
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;
                transition: opacity 0.3s ease-in-out;
        }

        .preloader-spinner {
                width: 60px;
                height: 60px;
                border: 5px solid #6366f1;
                /* Indigo color */
                border-top: 5px solid transparent;
                border-radius: 50%;
                animation: spin 1s linear infinite;
        }

        @keyframes spin {
                0% {
                        transform: rotate(0deg);
                }

                100% {
                        transform: rotate(360deg);
                }
        }

        body.loading {
                overflow: hidden;
        }
</style>

<!-- Preloader HTML -->
<div id="preloader">
        <div class="preloader-spinner"></div>
</div>

<script>
        document.addEventListener('DOMContentLoaded', function() {
                // Function to show preloader
                function showPreloader() {
                        document.body.classList.add('loading');
                        document.getElementById('preloader').style.opacity = '1';
                        document.getElementById('preloader').style.visibility = 'visible';
                }

                // Function to hide preloader
                function hidePreloader() {
                        document.body.classList.remove('loading');
                        document.getElementById('preloader').style.opacity = '0';
                        setTimeout(() => {
                                document.getElementById('preloader').style.visibility = 'hidden';
                        }, 300);
                }

                // Show preloader on initial page load
                showPreloader();

                // Hide preloader when page is fully loaded
                window.addEventListener('load', hidePreloader);

                // Handle navigation events
                document.addEventListener('turbolinks:load', hidePreloader);

                // For Livewire projects
                document.addEventListener('livewire:load', hidePreloader);

                // Intercept form submissions and ajax requests to show preloader
                document.addEventListener('submit', showPreloader);

                // For jQuery AJAX (if you're using it)
                if (window.jQuery) {
                        $(document).ajaxStart(showPreloader);
                        $(document).ajaxStop(hidePreloader);
                }
        });
</script>