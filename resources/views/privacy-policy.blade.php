<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kebijakan Privasi - Sistem Pemesanan Ruang Meeting</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
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
    </style>
</head>
<body class="gradient-bg min-h-screen">
    <!-- Mobile Sidebar -->
    @include('components.mobile-sidebar', [
        'userRole' => 'guest',
        'pageTitle' => 'Kebijakan Privasi'
    ])

    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-full shadow-lg mb-4">
                <img src="{{ asset('logo-bgn.png') }}" alt="BGN Logo" class="w-12 h-12 object-contain">
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">Kebijakan Privasi</h1>
            <p class="text-white/80">Sistem Pemesanan Ruang Meeting</p>
            <p class="text-white/60 text-sm mt-2">Terakhir diperbarui: {{ date('d F Y', strtotime('first day of this month')) }}</p>
        </div>

        <!-- Content -->
        <div class="max-w-4xl mx-auto">
            <div class="glass-effect rounded-2xl p-8 shadow-2xl">
                <div class="prose prose-invert max-w-none">
                    
                    <h2 class="text-2xl font-bold text-white mb-4">1. Pengenalan</h2>
                    <p class="text-white/90 mb-6">
                        Sistem Pemesanan Ruang Meeting adalah aplikasi web yang dikembangkan oleh eL PUSDATIN 
                        untuk memfasilitasi pemesanan ruang meeting secara online. Kebijakan privasi ini menjelaskan 
                        bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi pribadi Anda.
                    </p>

                    <h2 class="text-2xl font-bold text-white mb-4">2. Informasi yang Kami Kumpulkan</h2>
                    <div class="text-white/90 mb-6">
                        <h3 class="text-xl font-semibold text-white mb-3">2.1 Informasi Pribadi</h3>
                        <ul class="list-disc list-inside space-y-2 mb-4">
                            <li>Nama lengkap</li>
                            <li>Alamat email</li>
                            <li>Username</li>
                            <li>Nomor telepon (opsional)</li>
                            <li>Departemen/Unit kerja</li>
                            <li>Password (dienkripsi)</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-white mb-3">2.2 Informasi OAuth</h3>
                        <ul class="list-disc list-inside space-y-2 mb-4">
                            <li>Google ID (jika menggunakan Google OAuth)</li>
                            <li>Informasi profil Google (nama, email, foto profil)</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-white mb-3">2.3 Informasi Pemesanan</h3>
                        <ul class="list-disc list-inside space-y-2 mb-4">
                            <li>Detail pemesanan ruang meeting</li>
                            <li>Tanggal dan waktu pemesanan</li>
                            <li>Dokumen perizinan (jika diperlukan)</li>
                            <li>Status pemesanan</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-white mb-3">2.4 Informasi Teknis</h3>
                        <ul class="list-disc list-inside space-y-2 mb-4">
                            <li>Alamat IP</li>
                            <li>Browser dan perangkat yang digunakan</li>
                            <li>Waktu login terakhir</li>
                            <li>Data Cloudflare Turnstile (untuk keamanan)</li>
                        </ul>
                    </div>

                    <h2 class="text-2xl font-bold text-white mb-4">3. Cara Kami Menggunakan Informasi</h2>
                    <div class="text-white/90 mb-6">
                        <p class="mb-4">Kami menggunakan informasi yang dikumpulkan untuk:</p>
                        <ul class="list-disc list-inside space-y-2">
                            <li>Menyediakan layanan pemesanan ruang meeting</li>
                            <li>Memverifikasi identitas pengguna</li>
                            <li>Mengelola dan memproses pemesanan</li>
                            <li>Mengirim notifikasi terkait pemesanan</li>
                            <li>Meningkatkan keamanan sistem</li>
                            <li>Menyediakan dukungan teknis</li>
                            <li>Mematuhi kewajiban hukum</li>
                        </ul>
                    </div>

                    <h2 class="text-2xl font-bold text-white mb-4">4. Keamanan Data</h2>
                    <div class="text-white/90 mb-6">
                        <p class="mb-4">Kami menerapkan berbagai langkah keamanan untuk melindungi informasi Anda:</p>
                        <ul class="list-disc list-inside space-y-2">
                            <li>Enkripsi password menggunakan hash yang aman</li>
                            <li>Koneksi HTTPS untuk semua komunikasi</li>
                            <li>Verifikasi email untuk akun baru</li>
                            <li>Cloudflare Turnstile untuk mencegah bot</li>
                            <li>OAuth 2.0 untuk autentikasi Google</li>
                            <li>Pembatasan akses berdasarkan peran pengguna</li>
                            <li>Pencadangan data secara berkala</li>
                        </ul>
                    </div>

                    <h2 class="text-2xl font-bold text-white mb-4">5. Berbagi Informasi</h2>
                    <div class="text-white/90 mb-6">
                        <p class="mb-4">Kami tidak menjual, menyewakan, atau membagikan informasi pribadi Anda kepada pihak ketiga, kecuali:</p>
                        <ul class="list-disc list-inside space-y-2">
                            <li>Dengan persetujuan eksplisit dari Anda</li>
                            <li>Untuk mematuhi kewajiban hukum</li>
                            <li>Untuk melindungi hak dan keamanan kami atau pengguna lain</li>
                            <li>Dengan penyedia layanan tepercaya yang membantu operasional sistem</li>
                        </ul>
                    </div>

                    <h2 class="text-2xl font-bold text-white mb-4">6. Penyimpanan Data</h2>
                    <div class="text-white/90 mb-6">
                        <p class="mb-4">Data Anda disimpan selama:</p>
                        <ul class="list-disc list-inside space-y-2">
                            <li>Akun pengguna aktif (hingga akun dihapus)</li>
                            <li>Data pemesanan disimpan sesuai dengan kebijakan arsip organisasi</li>
                            <li>Log sistem disimpan maksimal 1 tahun</li>
                            <li>Data yang dihapus akan dihapus secara permanen dari server</li>
                        </ul>
                    </div>

                    <h2 class="text-2xl font-bold text-white mb-4">7. Hak Pengguna</h2>
                    <div class="text-white/90 mb-6">
                        <p class="mb-4">Anda memiliki hak untuk:</p>
                        <ul class="list-disc list-inside space-y-2">
                            <li>Mengakses data pribadi Anda</li>
                            <li>Memperbarui atau memperbaiki informasi yang tidak akurat</li>
                            <li>Menghapus akun dan data terkait</li>
                            <li>Menolak pemrosesan data tertentu</li>
                            <li>Meminta portabilitas data</li>
                            <li>Mengajukan keluhan terkait privasi</li>
                        </ul>
                    </div>

                    <h2 class="text-2xl font-bold text-white mb-4">8. Cookie dan Teknologi Pelacakan</h2>
                    <div class="text-white/90 mb-6">
                        <p class="mb-4">Kami menggunakan:</p>
                        <ul class="list-disc list-inside space-y-2">
                            <li>Session cookies untuk autentikasi</li>
                            <li>Cloudflare Turnstile untuk keamanan</li>
                            <li>Google OAuth untuk login sosial</li>
                            <li>Analytics cookies (jika ada) untuk meningkatkan layanan</li>
                        </ul>
                        <p class="mt-4">Anda dapat mengatur browser untuk menolak cookies, namun hal ini dapat mempengaruhi fungsionalitas sistem.</p>
                    </div>

                    <h2 class="text-2xl font-bold text-white mb-4">9. Perubahan Kebijakan</h2>
                    <div class="text-white/90 mb-6">
                        <p class="mb-4">
                            Kami dapat memperbarui kebijakan privasi ini dari waktu ke waktu. Perubahan akan diberitahukan melalui:
                        </p>
                        <ul class="list-disc list-inside space-y-2">
                            <li>Pemberitahuan di halaman login</li>
                            <li>Email kepada pengguna terdaftar</li>
                            <li>Update di halaman kebijakan privasi ini</li>
                        </ul>
                        <p class="mt-4">Tanggal "Terakhir diperbarui" di bagian atas halaman ini menunjukkan kapan kebijakan terakhir diubah.</p>
                    </div>

                    <h2 class="text-2xl font-bold text-white mb-4">10. Kontak</h2>
                    <div class="text-white/90 mb-6">
                        <p class="mb-4">Jika Anda memiliki pertanyaan tentang kebijakan privasi ini, silakan hubungi:</p>
                        <div class="bg-white/10 rounded-lg p-4">
                            <p><strong>eL PUSDATIN</strong></p>
                            <p>Email: <a href="mailto:SuhaelR@gmail.com" class="text-blue-300 hover:text-blue-200">SuhaelR@gmail.com</a></p>
                            <p>Website: <a href="https://www.pusdatinbgn.web.id" class="text-blue-300 hover:text-blue-200">www.pusdatinbgn.web.id</a></p>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-white/20">
                        <p class="text-white/60 text-sm text-center">
                            Dokumen ini dibuat dengan ❤️ oleh eL PUSDATIN<br>
                            © {{ date('Y') }} Sistem Pemesanan Ruang Meeting. Semua hak dilindungi.
                        </p>
                    </div>

                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="text-center mt-8">
            <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-all duration-300">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Halaman Login
            </a>
        </div>
    </div>

    <!-- WhatsApp Floating Button -->
    @include('components.whatsapp-float')
</body>
</html>
