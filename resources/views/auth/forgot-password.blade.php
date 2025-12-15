@extends('layouts.auth')

@php
    $pageTitle = 'Lupa Password';
    $showLogo = true;
    $showFooter = false;
@endphp

@push('seo-meta')
    @include('components.seo-meta', [
        'page' => 'forgot-password',
        'title' => 'Lupa Password - Meeting Room Booking',
        'description' => 'Reset password untuk akun Anda. Masukkan email untuk menerima link reset password.',
        'keywords' => 'lupa password, reset password, pemulihan akun',
        'canonical' => '/password/reset',
        'robots' => 'noindex, nofollow'
    ])
@endpush

@section('auth-content')
    <div class="rounded-2xl p-8 border border-gray-200 w-[450px] max-w-full">
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

        <form method="POST" class="text-left" action="{{ route('password.email') }}">
            @csrf
            
            <div class="space-y-4">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-black mb-2">
                        Email
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" 
                           class="w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="Masukkan email yang terdaftar" required>
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-800 !text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-300 mt-6 flex items-center justify-center border border-blue-300">
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
@endsection
