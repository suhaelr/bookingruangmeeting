<!DOCTYPE html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield("title", config("app.name", "Laravel"))</title>

    <!-- Loading Screen Styles - Loaded First -->
    <style>
        #loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #ffffff;
            transition: opacity 0.5s ease;
        }

        #loading-screen .loading-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
        }

        #loading-screen .logo-container img {
            height: 96px;
            width: 96px;
            object-fit: contain;
        }

        @media (min-width: 768px) {
            #loading-screen .logo-container img {
                height: 128px;
                width: 128px;
            }
        }

        #loading-screen .text-container {
            text-align: center;
        }

        #loading-screen .app-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }

        @media (min-width: 768px) {
            #loading-screen .app-name {
                font-size: 1.875rem;
            }
        }

        #loading-screen .loading-text {
            margin-top: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            color: #4b5563;
        }

        @media (min-width: 768px) {
            #loading-screen .loading-text {
                font-size: 1rem;
            }
        }

        #loading-screen .spinner-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        #loading-screen .spinner-dot {
            height: 12px;
            width: 12px;
            border-radius: 50%;
            background-color: #000;
            animation: bounce 1.4s ease-in-out infinite both;
        }

        #loading-screen .spinner-dot:nth-child(1) {
            animation-delay: -0.32s;
        }

        #loading-screen .spinner-dot:nth-child(2) {
            animation-delay: -0.16s;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        @keyframes bounce {

            0%,
            80%,
            100% {
                transform: scale(0);
            }

            40% {
                transform: scale(1);
            }
        }
    </style>

    <!-- SEO Meta Tags -->
    @stack("seo-meta")

    <!-- Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="{{ asset("vendors") }}/jquery/jquery.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />

    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    @stack("styles")

    <!-- Additional Head Content -->
    @stack("head")
</head>

<body class="@yield("body-class", "min-h-screen bg-white")">
    <!-- Loading Screen -->
    <div id="loading-screen">
        <div class="loading-container">
            <!-- Logo -->
            <div class="logo-container">
                <img src="{{ asset("logo-bgn.png") }}" alt="Logo">
            </div>
            <!-- App Name -->
            <div class="text-container">
                <h1 class="app-name">Sistem Informasi Ruang Rapat (SIRUPAT)</h1>
                <p class="loading-text">Memuat aplikasi...</p>
            </div>
            <!-- Loading Spinner -->
            <div class="spinner-container">
                <div class="spinner-dot"></div>
                <div class="spinner-dot"></div>
                <div class="spinner-dot"></div>
            </div>
        </div>
    </div>

    @yield("content")

    <!-- Feather Icons -->
    <script src="{{ asset("vendors") }}/feather-icons/feather.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <!-- Scripts -->
    <script>
        $(document).ready(function() {
            $('#loading-screen').fadeOut(500, function() {
                $(this).remove();
            });
        });

        // Initialize Feather icons
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });

        // General form submission loading state handler
        document.addEventListener('DOMContentLoaded', function() {
            // Get all forms on the page
            const forms = document.querySelectorAll('form');

            forms.forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    // Find all submit buttons within this form
                    const submitButtons = form.querySelectorAll(
                        'button[type="submit"], input[type="submit"]');

                    submitButtons.forEach(function(button) {
                        // Disable the button
                        button.disabled = true;

                        // Try to find separate text and loading elements (common pattern)
                        const buttonText = button.querySelector(
                            '[id$="ButtonText"], [id$="buttonText"]');
                        const buttonLoading = button.querySelector(
                            '[id$="ButtonLoading"], [id$="buttonLoading"]');

                        if (buttonText && buttonLoading) {
                            // Hide text, show loading
                            buttonText.classList.add('hidden');
                            buttonLoading.classList.remove('hidden');
                        } else {
                            // If no separate elements, store original content and show loading
                            if (!button.dataset.originalText) {
                                button.dataset.originalText = button.innerHTML;
                            }

                            // Show spinner icon if available, otherwise just disable
                            const hasSpinner = button.innerHTML.includes('fa-spinner') ||
                                button.innerHTML.includes('spinner');
                            if (!hasSpinner) {
                                button.innerHTML =
                                    '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
                            }
                        }
                    });
                });
            });
        });
    </script>
    @stack("scripts")
</body>

</html>
