<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - Meeting Room Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        /* Input styling */
        input[type="email"] {
            background-color: rgba(255, 255, 255, 0.2) !important;
            color: white !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
        }
        
        input::placeholder {
            color: rgba(255, 255, 255, 0.6) !important;
        }
        
        input:focus {
            background-color: rgba(255, 255, 255, 0.3) !important;
            border-color: #3182ce !important;
            box-shadow: 0 0 0 2px rgba(49, 130, 206, 0.2) !important;
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    <div class="glass-effect rounded-2xl p-8 w-full max-w-md shadow-2xl">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-envelope-open text-2xl text-white"></i>
            </div>
            <h1 class="text-2xl font-bold text-white mb-2">Verifikasi Email</h1>
            <p class="text-white/80">Silakan verifikasi email Anda untuk mengaktifkan akun</p>
        </div>

        @if (session('success'))
            <div class="mb-6 bg-green-500/20 border border-green-500/30 text-green-300 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-500/20 border border-red-500/30 text-red-300 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <div class="text-center mb-6">
            <p class="text-white/80 text-sm mb-4">
                Kami telah mengirimkan email verifikasi ke alamat email yang Anda daftarkan. 
                Silakan cek inbox atau folder spam Anda.
            </p>
            
            <div class="bg-blue-500/20 border border-blue-500/30 rounded-lg p-4 mb-4">
                <p class="text-blue-300 text-sm">
                    <i class="fas fa-info-circle mr-1"></i>
                    <strong>Penting:</strong> Link verifikasi hanya berlaku selama 24 jam.
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('verification.resend') }}">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-white mb-2">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" 
                           class="w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="Masukkan email yang didaftarkan" required>
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-300 mt-6 flex items-center justify-center">
                <i class="fas fa-paper-plane mr-2"></i>
                Kirim Ulang Email Verifikasi
            </button>
        </form>

        <div class="text-center mt-6">
            <p class="text-white/80 text-sm">
                Sudah verifikasi? 
                <a href="{{ route('login') }}" class="text-blue-300 hover:text-blue-200 font-semibold">
                    Login di sini
                </a>
            </p>
        </div>
    </div>

    <!-- WhatsApp Floating Button -->
    @include('components.whatsapp-float')
</body>
</html>
