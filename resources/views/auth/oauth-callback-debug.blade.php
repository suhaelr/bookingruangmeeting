<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OAuth Callback Debug - Sistem Pemesanan Ruang Meeting</title>
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
    <div class="glass-effect rounded-2xl p-8 shadow-2xl text-center max-w-4xl w-full mx-4">
        <div class="mb-6">
            <div class="w-16 h-16 bg-yellow-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-bug text-3xl text-yellow-400"></i>
            </div>
            <h2 class="text-2xl font-bold text-white mb-2">OAuth Callback Debug</h2>
            <p class="text-white/80">Debugging OAuth callback endpoint</p>
        </div>
        
        <div class="bg-white/10 rounded-lg p-6 mb-6 text-left">
            <h3 class="text-lg font-semibold text-white mb-4">Request Information</h3>
            <div class="space-y-2 text-sm">
                <div><strong class="text-yellow-300">URL:</strong> <span class="text-white">{{ request()->url() }}</span></div>
                <div><strong class="text-yellow-300">Method:</strong> <span class="text-white">{{ request()->method() }}</span></div>
                <div><strong class="text-yellow-300">IP:</strong> <span class="text-white">{{ request()->ip() }}</span></div>
                <div><strong class="text-yellow-300">User Agent:</strong> <span class="text-white text-xs">{{ request()->userAgent() }}</span></div>
                <div><strong class="text-yellow-300">Session ID:</strong> <span class="text-white">{{ session()->getId() }}</span></div>
            </div>
        </div>

        <div class="bg-white/10 rounded-lg p-6 mb-6 text-left">
            <h3 class="text-lg font-semibold text-white mb-4">Request Parameters</h3>
            @if(count(request()->all()) > 0)
                <div class="space-y-2 text-sm">
                    @foreach(request()->all() as $key => $value)
                        <div>
                            <strong class="text-yellow-300">{{ $key }}:</strong> 
                            <span class="text-white">{{ is_array($value) ? json_encode($value) : $value }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-white/60">No parameters received</p>
            @endif
        </div>

        <div class="bg-white/10 rounded-lg p-6 mb-6 text-left">
            <h3 class="text-lg font-semibold text-white mb-4">Session Data</h3>
            <div class="space-y-2 text-sm">
                <div><strong class="text-yellow-300">User Logged In:</strong> <span class="text-white">{{ session('user_logged_in') ? 'Yes' : 'No' }}</span></div>
                <div><strong class="text-yellow-300">User Data:</strong> <span class="text-white">{{ session('user_data') ? json_encode(session('user_data')) : 'None' }}</span></div>
                <div><strong class="text-yellow-300">Google OAuth State:</strong> <span class="text-white">{{ session('google_oauth_state') ?: 'None' }}</span></div>
            </div>
        </div>

        <div class="bg-white/10 rounded-lg p-6 mb-6 text-left">
            <h3 class="text-lg font-semibold text-white mb-4">Headers</h3>
            <div class="space-y-2 text-sm max-h-40 overflow-y-auto">
                @foreach(request()->headers->all() as $key => $value)
                    <div>
                        <strong class="text-yellow-300">{{ $key }}:</strong> 
                        <span class="text-white text-xs">{{ is_array($value) ? implode(', ', $value) : $value }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('login') }}" class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Login
            </a>
            <a href="{{ route('oauth.debug') }}" class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                <i class="fas fa-bug mr-2"></i>Debug JSON
            </a>
            <button onclick="location.reload()" class="px-6 py-3 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                <i class="fas fa-refresh mr-2"></i>Refresh
            </button>
        </div>
    </div>

    <script>
        console.log('OAuth Callback Debug Page Loaded');
        console.log('URL:', window.location.href);
        console.log('Parameters:', new URLSearchParams(window.location.search));
        console.log('Session ID:', '{{ session()->getId() }}');
    </script>
</body>
</html>
