<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Meeting Room Booking</title>
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
        input[type="text"], input[type="email"], input[type="password"] {
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
                <i class="fas fa-user-plus text-2xl text-white"></i>
            </div>
            <h1 class="text-2xl font-bold text-white mb-2">Daftar Akun Baru</h1>
            <p class="text-white/80">Buat akun untuk mengakses sistem booking ruang meeting</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 bg-red-500/20 border border-red-500/30 text-red-300 px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-500/20 border border-red-500/30 text-red-300 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="space-y-4">
                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-white mb-2">Username *</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" 
                           class="w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="Masukkan username" required>
                </div>

                <!-- Full Name -->
                <div>
                    <label for="full_name" class="block text-sm font-medium text-white mb-2">Nama Lengkap *</label>
                    <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" 
                           class="w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="Masukkan nama lengkap" required>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-white mb-2">Email *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" 
                           class="w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="Masukkan email" required>
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-white mb-2">No. Telepon</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" 
                           class="w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="Masukkan nomor telepon">
                </div>

                <!-- Department -->
                <div>
                    <label for="department" class="block text-sm font-medium text-white mb-2">Departemen</label>
                    <input type="text" id="department" name="department" value="{{ old('department') }}" 
                           class="w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="Masukkan departemen">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-white mb-2">Password *</label>
                    <input type="password" id="password" name="password" 
                           class="w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="Minimal 8 karakter" required>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-white mb-2">Konfirmasi Password *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" 
                           class="w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="Ulangi password" required>
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-300 mt-6 flex items-center justify-center">
                <i class="fas fa-user-plus mr-2"></i>
                Daftar Sekarang
            </button>
        </form>

        <div class="text-center mt-6">
            <p class="text-white/80 text-sm">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="text-blue-300 hover:text-blue-200 font-semibold">
                    Login di sini
                </a>
            </p>
        </div>
    </div>
</body>
</html>
