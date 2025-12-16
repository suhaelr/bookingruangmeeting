@extends('layouts.auth')

@php
    $pageTitle = 'Verifikasi Email';
    $showLogo = true;
    $showFooter = false;
@endphp

@push('seo-meta')
    @include('components.seo-meta', [
        'page' => 'verify-email',
        'title' => 'Verifikasi Email - Meeting Room Booking',
        'description' => 'Verifikasi email Anda untuk mengaktifkan akun.',
        'keywords' => 'verifikasi email, aktivasi akun, konfirmasi email',
        'canonical' => '/email/verify',
        'robots' => 'noindex, nofollow'
    ])
@endpush

@section('auth-content')
    <div class="rounded-2xl p-8 w-[450px] max-w-full bg-white border border-gray-200">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-envelope-open text-2xl text-indigo-600"></i>
            </div>
            <h1 class="text-2xl font-bold text-black mb-2">Verifikasi Email</h1>
            <p class="text-black">Silakan verifikasi email Anda untuk mengaktifkan akun</p>
        </div>

        @if (session('success'))
            <div class="mt-3 mb-6 bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mt-3 mb-6 bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <div class="text-center mb-6">
            <p class="text-black text-sm mb-4">
                Kami telah mengirimkan email verifikasi ke alamat email yang Anda daftarkan. 
                Silakan cek inbox atau folder spam Anda.
            </p>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <p class="text-blue-800 text-sm">
                    <i class="fas fa-info-circle mr-1"></i>
                    <strong>Penting:</strong> Link verifikasi hanya berlaku selama 24 jam.
                </p>
            </div>
        </div>

        <form method="POST" class="text-left" action="{{ route('verification.resend') }}" id="verifyEmailForm">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-black mb-2">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" 
                           class="w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="Masukkan email yang didaftarkan" required>
                </div>
            </div>

            <button type="submit" id="verifyEmailButton" class="w-full bg-blue-500 hover:bg-blue-800 !text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-300 mt-6 flex items-center justify-center border border-blue-300 disabled:opacity-50 disabled:cursor-not-allowed">
                <span id="verifyEmailButtonText">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Kirim Ulang Email Verifikasi
                </span>
                <span id="verifyEmailButtonLoading" class="hidden">
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    Memproses...
                </span>
            </button>
        </form>

        <div class="text-center mt-6">
            <p class="text-black text-sm">
                Sudah verifikasi? 
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
        const verifyEmailForm = document.getElementById('verifyEmailForm');
        const verifyEmailButton = document.getElementById('verifyEmailButton');
        const verifyEmailButtonText = document.getElementById('verifyEmailButtonText');
        const verifyEmailButtonLoading = document.getElementById('verifyEmailButtonLoading');

        if (verifyEmailForm && verifyEmailButton) {
            verifyEmailForm.addEventListener('submit', function() {
                // Show loading state
                verifyEmailButton.disabled = true;
                verifyEmailButtonText.classList.add('hidden');
                verifyEmailButtonLoading.classList.remove('hidden');
            });
        }
    });
</script>
@endpush
