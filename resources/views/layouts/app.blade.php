<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', config('app.name', 'Laravel'))</title>
    
    <!-- SEO Meta Tags -->
    @stack('seo-meta')
    
    <!-- Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    
    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('styles')
    
    <!-- Additional Head Content -->
    @stack('head')
</head>
<body class="@yield('body-class', 'min-h-screen bg-white')">
    @yield('content')
    
    <!-- Feather Icons -->
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    
    <!-- Scripts -->
    <script>
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
                    const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
                    
                    submitButtons.forEach(function(button) {
                        // Disable the button
                        button.disabled = true;
                        
                        // Try to find separate text and loading elements (common pattern)
                        const buttonText = button.querySelector('[id$="ButtonText"], [id$="buttonText"]');
                        const buttonLoading = button.querySelector('[id$="ButtonLoading"], [id$="buttonLoading"]');
                        
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
                            const hasSpinner = button.innerHTML.includes('fa-spinner') || button.innerHTML.includes('spinner');
                            if (!hasSpinner) {
                                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
                            }
                        }
                    });
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>

