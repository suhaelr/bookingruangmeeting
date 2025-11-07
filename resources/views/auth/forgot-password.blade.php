<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Meeting Room Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .gradient-bg {
            background: #ffffff;
        }
        
        .glass-effect {
            background: #ffffff;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 0, 0, 0.2);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        /* Input styling */
        input[type="email"] {
            background-color: #ffffff !important;
            color: #000000 !important;
            border: 1px solid #d1d5db !important;
        }
        
        input::placeholder {
            color: #9ca3af !important;
        }
        
        input:focus {
            background-color: #ffffff !important;
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2) !important;
        }
        
        /* Ensure all text is black */
        .text-white {
            color: #000000 !important;
        }
        
        .text-white\/80 {
            color: #000000 !important;
        }
        
        label {
            color: #000000 !important;
        }
        
        p {
            color: #000000 !important;
        }
        
        h1 {
            color: #000000 !important;
        }
        
        a {
            color: #000000 !important;
        }
        
        a:hover {
            color: #1f2937 !important;
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    <!-- Mobile Sidebar -->
    @include('components.mobile-sidebar', [
        'userRole' => 'guest',
        'pageTitle' => 'Lupa Password'
    ])
    <div class="glass-effect rounded-2xl p-8 w-full max-w-md shadow-2xl">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-key text-2xl text-indigo-600"></i>
            </div>
            <h1 class="text-2xl font-bold text-black mb-2">Lupa Password?</h1>
            <p class="text-black">Masukkan email Anda untuk menerima link reset password</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            
            <div class="space-y-4">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-black mb-2">Email *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" 
                           class="w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="Masukkan email yang terdaftar" required>
                </div>
            </div>

            <button type="submit" class="w-full bg-gray-200 hover:bg-gray-300 text-black font-semibold py-3 px-4 rounded-lg transition-colors duration-300 mt-6 flex items-center justify-center border border-gray-300">
                <i class="fas fa-paper-plane mr-2"></i>
                Kirim Link Reset
            </button>
        </form>

        <div class="text-center mt-6">
            <p class="text-black text-sm">
                Ingat password? 
                <a href="{{ route('login') }}" class="text-black hover:text-gray-800 font-semibold underline">
                    Login di sini
                </a>
            </p>
        </div>
    </div>

    <!-- WhatsApp Floating Button -->
    @include('components.whatsapp-float')
</body>
</html>
