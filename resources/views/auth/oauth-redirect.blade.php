<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting... - Sistem Pemesanan Ruang Meeting</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center">
    <div class="glass-effect rounded-2xl p-8 shadow-2xl text-center max-w-md w-full mx-4">
        <div class="mb-6">
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check-circle text-3xl text-green-400"></i>
            </div>
            <h2 class="text-2xl font-bold text-white mb-2">Login Berhasil!</h2>
            <p class="text-white/80">Sedang mengarahkan ke dashboard...</p>
        </div>
        
        <div class="mb-6">
            <div class="flex justify-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-white"></div>
            </div>
        </div>
        
        <div class="text-sm text-white/60">
            <p>Jika tidak otomatis redirect, <a href="{{ $redirectUrl }}" class="text-white underline hover:text-white/80">klik di sini</a></p>
        </div>
    </div>

    <script>
        // Debug information
        console.log('OAuth Redirect Page Loaded');
        console.log('Redirect URL:', '{{ $redirectUrl }}');
        console.log('User Role:', '{{ $userRole }}');
        console.log('Session ID:', '{{ session()->getId() }}');
        
        // Check session data
        fetch('/debug/session')
            .then(response => response.json())
            .then(data => {
                console.log('Session Debug Data:', data);
            })
            .catch(error => {
                console.error('Error fetching session data:', error);
            });

        // Multiple redirect attempts
        function attemptRedirect() {
            console.log('Attempting redirect to:', '{{ $redirectUrl }}');
            
            // Method 1: Direct redirect
            window.location.href = '{{ $redirectUrl }}';
            
            // Method 2: Fallback with delay
            setTimeout(function() {
                if (window.location.href === '{{ $redirectUrl }}') {
                    console.log('Redirect successful');
                } else {
                    console.log('Redirect failed, trying alternative method');
                    window.location.replace('{{ $redirectUrl }}');
                }
            }, 1000);
            
            // Method 3: Final fallback
            setTimeout(function() {
                if (window.location.href !== '{{ $redirectUrl }}') {
                    console.log('Using final fallback redirect');
                    document.location.href = '{{ $redirectUrl }}';
                }
            }, 3000);
        }

        // Start redirect attempts
        attemptRedirect();
    </script>
</body>
</html>
