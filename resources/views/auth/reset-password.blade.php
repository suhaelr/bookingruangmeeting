@extends('layouts.auth')

@php
    $pageTitle = 'Reset Password';
    $showLogo = true;
    $showFooter = false;
@endphp

@push('seo-meta')
    @include('components.seo-meta', [
        'page' => 'reset-password',
        'title' => 'Reset Password - Meeting Room Booking',
        'description' => 'Reset password untuk akun Anda. Masukkan password baru.',
        'keywords' => 'reset password, ubah password, password baru',
        'canonical' => '/password/reset',
        'robots' => 'noindex, nofollow'
    ])
@endpush

@section('auth-content')
    <div class="rounded-2xl p-8 w-[450px] max-w-full bg-white border border-gray-200">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-lock text-2xl text-indigo-600"></i>
            </div>
            <h1 class="text-2xl font-bold text-black mb-2">Reset Password</h1>
            <p class="text-black">Masukkan password baru untuk akun Anda</p>
        </div>

        @if ($errors->any())
            <div class="mt-3 mb-6 bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="mt-3 mb-6 bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" class="text-left" action="{{ route('password.update') }}" id="resetPasswordForm">
            @csrf
            
            <input type="hidden" name="token" value="{{ $token }}">
            
            <div class="space-y-4">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-black mb-2">
                        Email
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" 
                           class="w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="Masukkan email" required>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-black mb-2">
                        Password Baru
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password" name="password" 
                           class="w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="Minimal 8 karakter" required>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-black mb-2">
                        Konfirmasi Password Baru
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation" 
                           class="w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="Ulangi password baru" required>
                </div>
            </div>

            <button type="submit" id="resetPasswordButton" class="w-full bg-blue-500 hover:bg-blue-800 !text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-300 mt-6 flex items-center justify-center border border-blue-300 disabled:opacity-50 disabled:cursor-not-allowed">
                <span id="resetPasswordButtonText">
                    <i class="fas fa-save mr-2"></i>
                    Reset Password
                </span>
                <span id="resetPasswordButtonLoading" class="hidden">
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    Memproses...
                </span>
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

@push('auth-scripts')
<script>
    // Form submission loading state
    document.addEventListener('DOMContentLoaded', function() {
        const resetPasswordForm = document.getElementById('resetPasswordForm');
        const resetPasswordButton = document.getElementById('resetPasswordButton');
        const resetPasswordButtonText = document.getElementById('resetPasswordButtonText');
        const resetPasswordButtonLoading = document.getElementById('resetPasswordButtonLoading');

        if (resetPasswordForm && resetPasswordButton) {
            resetPasswordForm.addEventListener('submit', function() {
                // Show loading state
                resetPasswordButton.disabled = true;
                resetPasswordButtonText.classList.add('hidden');
                resetPasswordButtonLoading.classList.remove('hidden');
            });
        }
    });
</script>
@endpush
