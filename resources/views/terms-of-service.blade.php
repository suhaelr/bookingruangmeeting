<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Meta Tags -->
    @include('components.seo-meta', [
        'page' => 'terms_of_service',
        'title' => 'Syarat dan Ketentuan - Sistem Pemesanan Ruang Meeting',
        'description' => 'Syarat dan ketentuan penggunaan sistem pemesanan ruang meeting. Aturan dan ketentuan yang berlaku.',
        'keywords' => 'syarat ketentuan, aturan penggunaan, terms of service, sistem pemesanan',
        'canonical' => '/terms-of-service',
        'robots' => 'index, follow'
    ])
    
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
        'pageTitle' => 'Syarat dan Ketentuan'
    ])

    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-full shadow-lg mb-4">
                <img src="{{ asset('logo-bgn.png') }}" alt="BGN Logo" class="w-12 h-12 object-contain">
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">Syarat dan Ketentuan</h1>
            <p class="text-white/80">Sistem Pemesanan Ruang Meeting</p>
            <p class="text-white/60 text-sm mt-2">Terakhir diperbarui: {{ date('d F Y', strtotime('first day of this month')) }}</p>
        </div>

        <!-- Content -->
        <div class="max-w-4xl mx-auto">
            <div class="glass-effect rounded-2xl p-8 shadow-2xl">
                <div class="prose prose-invert max-w-none">
                    
                    <h2 class="text-2xl font-bold text-white mb-4">1. Penerimaan Syarat</h2>
                    <p class="text-white/90 mb-6">
                        Dengan menggunakan Sistem Pemesanan Ruang Meeting, 
                        Anda menyetujui untuk terikat oleh syarat dan ketentuan ini. Jika Anda tidak menyetujui 
                        syarat dan ketentuan ini, harap tidak menggunakan layanan ini.
                    </p>

                    <h2 class="text-2xl font-bold text-white mb-4">2. Definisi</h2>
                    <div class="text-white/90 mb-6">
                        <ul class="list-disc list-inside space-y-2">
                            <li><strong>"Sistem"</strong> - Sistem Pemesanan Ruang Meeting</li>
                            <li><strong>"Pengguna"</strong> - Individu yang menggunakan sistem</li>
                            <li><strong>"Pemesanan"</strong> - Reservasi ruang meeting melalui sistem</li>
                            <li><strong>"Ruang Meeting"</strong> - Fasilitas yang dapat dipesan</li>
                        </ul>
                    </div>

                    <h2 class="text-2xl font-bold text-white mb-4">3. Penggunaan Sistem</h2>
                    <div class="text-white/90 mb-6">
                        <h3 class="text-xl font-semibold text-white mb-3">3.1 Hak Penggunaan</h3>
                        <p class="mb-4">Anda berhak untuk:</p>
                        <ul class="list-disc list-inside space-y-2 mb-4">
                            <li>Membuat akun pengguna</li>
                            <li>Memesan ruang meeting yang tersedia</li>
                            <li>Mengelola pemesanan Anda</li>
                            <li>Mengakses informasi ruang meeting</li>
                            <li>Menerima notifikasi terkait pemesanan</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-white mb-3">3.2 Kewajiban Pengguna</h3>
                        <p class="mb-4">Anda wajib untuk:</p>
                        <ul class="list-disc list-inside space-y-2 mb-4">
                            <li>Menyediakan informasi yang akurat dan lengkap (username, nama lengkap, email, unit kerja)</li>
                            <li>Memverifikasi email Anda sebelum dapat menggunakan sistem</li>
                            <li>Menggunakan sistem sesuai dengan tujuan yang dimaksud (pemesanan ruang meeting)</li>
                            <li>Menghormati hak pengguna lain dan tidak membuat booking yang bertentangan dengan jadwal yang sudah dikonfirmasi</li>
                            <li>Mematuhi jadwal pemesanan yang telah dibuat dan menepati waktu yang telah ditentukan</li>
                            <li>Merespons permintaan didahulukan dari pengguna lain dalam batas waktu yang ditentukan</li>
                            <li>Memberikan notifikasi jika ada perubahan atau pembatalan melalui sistem</li>
                            <li>Menjaga kerahasiaan akun dan tidak membagikan kredensial login</li>
                        </ul>
                    </div>

                    <h2 class="text-2xl font-bold text-white mb-4">4. Akun Pengguna</h2>
                    <div class="text-white/90 mb-6">
                        <h3 class="text-xl font-semibold text-white mb-3">4.1 Registrasi</h3>
                        <ul class="list-disc list-inside space-y-2 mb-4">
                            <li>Setiap pengguna harus memiliki akun yang valid</li>
                            <li>Informasi yang diberikan harus akurat dan terkini</li>
                            <li>Email harus diverifikasi sebelum dapat menggunakan sistem</li>
                            <li>Satu akun per orang, tidak diperkenankan berbagi akun</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-white mb-3">4.2 Keamanan Akun</h3>
                        <ul class="list-disc list-inside space-y-2 mb-4">
                            <li>Anda bertanggung jawab atas keamanan akun Anda</li>
                            <li>Gunakan password yang kuat dan unik</li>
                            <li>Segera laporkan jika ada aktivitas mencurigakan</li>
                            <li>Jangan berbagi kredensial login dengan orang lain</li>
                        </ul>
                    </div>

                    <h2 class="text-2xl font-bold text-white mb-4">5. Pemesanan Ruang Meeting</h2>
                    <div class="text-white/90 mb-6">
                        <h3 class="text-xl font-semibold text-white mb-3">5.1 Proses Pemesanan</h3>
                        <ul class="list-disc list-inside space-y-2 mb-4">
                            <li>Pemesanan dilakukan secara online melalui sistem</li>
                            <li>Ketersediaan ruang dapat berubah sewaktu-waktu</li>
                            <li>Pemesanan harus dilakukan sesuai dengan jadwal yang tersedia</li>
                            <li>Pengguna tidak dapat membuat booking yang overlap dengan booking miliknya sendiri</li>
                            <li>Dokumen perizinan dalam format PDF (maksimal 2MB) dapat disertakan jika diperlukan</li>
                            <li>Validasi captcha wajib dilakukan saat membuat booking baru</li>
                            <li>Booking baru berstatus "pending" dan menunggu konfirmasi admin</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-white mb-3">5.2 PIC Invitations (Undangan PIC)</h3>
                        <ul class="list-disc list-inside space-y-2 mb-4">
                            <li>Pengguna dapat mengundang PIC (Person In Charge) lain untuk melihat booking mereka</li>
                            <li>PIC yang diundang akan menerima notifikasi undangan</li>
                            <li>PIC yang diundang dapat melihat booking di kalender dashboard mereka</li>
                            <li>Visibilitas deskripsi meeting dapat diatur menjadi "public" (semua PIC) atau "invited_pics_only" (hanya PIC yang diundang)</li>
                            <li>Hanya admin, owner booking, atau PIC yang diundang (jika pengaturan "invited_pics_only") yang dapat melihat deskripsi meeting lengkap</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-white mb-3">5.3 Preempt Booking (Permintaan Didahulukan)</h3>
                        <ul class="list-disc list-inside space-y-2 mb-4">
                            <li>Pengguna dapat meminta booking milik pengguna lain untuk didahulukan jika ada konflik jadwal</li>
                            <li>Pengguna tidak dapat meminta didahulukan booking miliknya sendiri</li>
                            <li>Pemilik booking yang diminta akan menerima notifikasi permintaan didahulukan</li>
                            <li>Pemilik booking dapat merespons dengan "Terima & Batalkan"</li>
                            <li>Jika diterima, sistem akan otomatis membatalkan booking pemilik dan mengonfirmasi booking baru untuk peminta</li>
                            <li>Deadline respons: 60 menit (jika booking > 2 jam sebelum start time) atau 15 menit (jika booking < 2 jam sebelum start time)</li>
                            <li>Jika tidak direspons dalam deadline, permintaan didahulukan akan otomatis kadaluarsa</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-white mb-3">5.4 Pembatalan dan Perubahan</h3>
                        <ul class="list-disc list-inside space-y-2 mb-4">
                            <li>Pembatalan dapat dilakukan sesuai dengan kebijakan yang berlaku</li>
                            <li>Booking dengan status "pending" dapat dibatalkan kapan saja oleh pemilik</li>
                            <li>Booking dengan status "confirmed" hanya dapat dibatalkan jika lebih dari 2 jam sebelum waktu mulai meeting</li>
                            <li>Perubahan booking dapat dilakukan melalui menu "Edit Booking"</li>
                            <li>Pengguna dapat mengedit PIC yang diundang dan visibilitas deskripsi tanpa mengubah detail booking lainnya</li>
                            <li>Notifikasi pembatalan atau perubahan akan dikirim melalui sistem notifikasi</li>
                        </ul>
                    </div>

                    <h2 class="text-2xl font-bold text-white mb-4">6. Kode Etik Penggunaan</h2>
                    <div class="text-white/90 mb-6">
                        <h3 class="text-xl font-semibold text-white mb-3">6.1 Perilaku yang Dilarang</h3>
                        <ul class="list-disc list-inside space-y-2 mb-4">
                            <li>Menggunakan sistem untuk tujuan ilegal atau tidak pantas</li>
                            <li>Mencoba merusak atau mengganggu sistem</li>
                            <li>Menggunakan bot atau script otomatis</li>
                            <li>Melakukan spam atau aktivitas yang mengganggu</li>
                            <li>Menyalahgunakan informasi pengguna lain</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-white mb-3">6.2 Sanksi</h3>
                        <ul class="list-disc list-inside space-y-2 mb-4">
                            <li>Peringatan tertulis untuk pelanggaran ringan</li>
                            <li>Penangguhan akses sementara untuk pelanggaran sedang</li>
                            <li>Penutupan akun permanen untuk pelanggaran berat</li>
                            <li>Tindakan hukum jika diperlukan</li>
                        </ul>
                    </div>

                    <h2 class="text-2xl font-bold text-white mb-4">7. Ketersediaan Layanan</h2>
                    <div class="text-white/90 mb-6">
                        <p class="mb-4">Kami berusaha untuk:</p>
                        <ul class="list-disc list-inside space-y-2 mb-4">
                            <li>Menyediakan layanan 24/7 sebisa mungkin</li>
                            <li>Memberikan notifikasi jika ada maintenance</li>
                            <li>Memperbaiki gangguan teknis secepatnya</li>
                            <li>Menyediakan dukungan teknis yang memadai</li>
                        </ul>
                        <p class="mt-4">
                            Namun, kami tidak dapat menjamin bahwa sistem akan selalu tersedia tanpa gangguan. 
                            Pengguna memahami dan menerima risiko ini.
                        </p>
                    </div>

                    <h2 class="text-2xl font-bold text-white mb-4">8. Privasi dan Keamanan Data</h2>
                    <div class="text-white/90 mb-6">
                        <p class="mb-4">
                            Penggunaan data pribadi Anda diatur dalam Kebijakan Privasi yang terpisah. 
                            Dengan menggunakan sistem ini, Anda juga menyetujui Kebijakan Privasi tersebut.
                        </p>
                        <ul class="list-disc list-inside space-y-2">
                            <li>Data Anda akan dilindungi sesuai dengan standar keamanan</li>
                            <li>Informasi sensitif akan dienkripsi</li>
                            <li>Akses data dibatasi hanya untuk yang berwenang</li>
                            <li>Backup data dilakukan secara berkala</li>
                        </ul>
                    </div>

                    <h2 class="text-2xl font-bold text-white mb-4">9. Hak Kekayaan Intelektual</h2>
                    <div class="text-white/90 mb-6">
                        <p class="mb-4">
                            Semua hak kekayaan intelektual terkait sistem ini, termasuk namun tidak terbatas pada:
                        </p>
                        <ul class="list-disc list-inside space-y-2 mb-4">
                            <li>Kode sumber dan arsitektur sistem</li>
                            <li>Desain antarmuka pengguna</li>
                            <li>Dokumentasi dan manual</li>
                            <li>Logo dan merek dagang</li>
                        </ul>
                        <p class="mt-4">
                            Adalah milik pengembang sistem dan dilindungi oleh undang-undang hak cipta. 
                            Pengguna tidak diperkenankan untuk menyalin, memodifikasi, atau mendistribusikan 
                            tanpa izin tertulis.
                        </p>
                    </div>

                    <h2 class="text-2xl font-bold text-white mb-4">10. Pembatasan Tanggung Jawab</h2>
                    <div class="text-white/90 mb-6">
                        <p class="mb-4">
                            Kami tidak bertanggung jawab atas:
                        </p>
                        <ul class="list-disc list-inside space-y-2 mb-4">
                            <li>Kerugian yang timbul dari penggunaan sistem</li>
                            <li>Gangguan teknis yang tidak dapat dihindari</li>
                            <li>Kehilangan data akibat force majeure</li>
                            <li>Konflik antar pengguna</li>
                            <li>Kerugian tidak langsung atau konsekuensial</li>
                        </ul>
                    </div>

                    <h2 class="text-2xl font-bold text-white mb-4">11. Perubahan Syarat dan Ketentuan</h2>
                    <div class="text-white/90 mb-6">
                        <p class="mb-4">
                            Kami berhak untuk mengubah syarat dan ketentuan ini sewaktu-waktu. 
                            Perubahan akan diberitahukan melalui:
                        </p>
                        <ul class="list-disc list-inside space-y-2 mb-4">
                            <li>Pemberitahuan di halaman login</li>
                            <li>Email kepada pengguna terdaftar</li>
                            <li>Update di halaman syarat dan ketentuan</li>
                        </ul>
                        <p class="mt-4">
                            Penggunaan sistem setelah perubahan dianggap sebagai persetujuan terhadap 
                            syarat dan ketentuan yang baru.
                        </p>
                    </div>

                    <h2 class="text-2xl font-bold text-white mb-4">12. Hukum yang Berlaku</h2>
                    <div class="text-white/90 mb-6">
                        <p class="mb-4">
                            Syarat dan ketentuan ini diatur oleh dan ditafsirkan sesuai dengan 
                            hukum Republik Indonesia. Setiap sengketa yang timbul akan diselesaikan 
                            melalui pengadilan yang berwenang di Indonesia.
                        </p>
                    </div>

                    <h2 class="text-2xl font-bold text-white mb-4">13. Kontak dan Bantuan</h2>
                    <div class="text-white/90 mb-6">
                        <p class="mb-4">Jika Anda memiliki pertanyaan tentang syarat dan ketentuan ini:</p>
                        <div class="bg-white/10 rounded-lg p-4">
                            <p><strong>PUSDATIN</strong></p>
                            <p>Email: <a href="mailto:SuhaelR@gmail.com" class="text-blue-300 hover:text-blue-200">SuhaelR@gmail.com</a></p>
                            <p>Website: <a href="https://www.pusdatinbgn.web.id" class="text-blue-300 hover:text-blue-200">www.pusdatinbgn.web.id</a></p>
                            <p class="mt-2 text-sm">Jam operasional: Senin - Jumat, 08:00 - 17:00 WIB</p>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-white/20">
                        <p class="text-white/60 text-sm text-center">
                            Dokumen ini dibuat dengan ❤️<br>
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
