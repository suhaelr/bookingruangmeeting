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
            background: #ffffff;
        }
        .glass-effect {
            background: #ffffff;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 0, 0, 0.2);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        /* Ensure all text is black */
        .text-black {
            color: #000000 !important;
        }
        .text-black\/80 {
            color: #000000 !important;
        }
        .text-black\/90 {
            color: #000000 !important;
        }
        .text-black\/60 {
            color: #000000 !important;
        }
        h1, h2, h3, h4, h5, h6 {
            color: #000000 !important;
        }
        p, li, span, div {
            color: #000000 !important;
        }
        a {
            color: #000000 !important;
        }
        a:hover {
            color: #1f2937 !important;
        }
        .prose-invert {
            color: #000000 !important;
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
            <h1 class="text-4xl font-bold text-black mb-2">Syarat dan Ketentuan</h1>
            <p class="text-black">Sistem Pemesanan Ruang Meeting</p>
            <p class="text-black text-sm mt-2">Terakhir diperbarui: {{ date('d F Y', strtotime('first day of this month')) }}</p>
        </div>

        <!-- Content -->
        <div class="max-w-4xl mx-auto">
            <div class="glass-effect rounded-2xl p-8 shadow-2xl">
                <div class="prose prose-invert max-w-none">
                    
                    <h2 class="text-2xl font-bold text-black mb-4">1. Penerimaan Syarat</h2>
                    <p class="text-black/90 mb-6">
                        Dengan menggunakan Sistem Pemesanan Ruang Meeting, 
                        Anda menyetujui untuk terikat oleh syarat dan ketentuan ini. Jika Anda tidak menyetujui 
                        syarat dan ketentuan ini, harap tidak menggunakan layanan ini.
                    </p>

                    <h2 class="text-2xl font-bold text-black mb-4">2. Definisi</h2>
                    <div class="text-black/90 mb-6">
                        <ul class="list-disc list-inside space-y-2">
                            <li><strong>"Sistem"</strong> - Sistem Pemesanan Ruang Meeting</li>
                            <li><strong>"Pengguna"</strong> - Individu yang menggunakan sistem</li>
                            <li><strong>"Pemesanan"</strong> - Reservasi ruang meeting melalui sistem</li>
                            <li><strong>"Ruang Meeting"</strong> - Fasilitas yang dapat dipesan</li>
                        </ul>
                    </div>

                    <h2 class="text-2xl font-bold text-black mb-4">3. Penggunaan Sistem</h2>
                    <div class="text-black/90 mb-6">
                        <h3 class="text-xl font-semibold text-black mb-3">3.1 Hak Penggunaan</h3>
                        <p class="mb-4">Anda berhak untuk:</p>
                        <ul class="list-disc list-inside space-y-2 mb-4">
                            <li>Membuat akun pengguna</li>
                            <li>Memesan ruang meeting yang tersedia</li>
                            <li>Mengelola pemesanan Anda</li>
                            <li>Mengakses informasi ruang meeting</li>
                            <li>Menerima notifikasi terkait pemesanan</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-black mb-3">3.2 Kewajiban Pengguna</h3>
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

                    <h2 class="text-2xl font-bold text-black mb-4">4. Akun Pengguna</h2>
                    <div class="text-black/90 mb-6">
                        <h3 class="text-xl font-semibold text-black mb-3">4.1 Registrasi</h3>
                        <ul class="list-disc list-inside space-y-2 mb-4">
                            <li>Setiap pengguna harus memiliki akun yang valid</li>
                            <li>Informasi yang diberikan harus akurat dan terkini</li>
                            <li>Email harus diverifikasi sebelum dapat menggunakan sistem</li>
                            <li>Satu akun per orang, tidak diperkenankan berbagi akun</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-black mb-3">4.2 Keamanan Akun</h3>
                        <ul class="list-disc list-inside space-y-2 mb-4">
                            <li>Anda bertanggung jawab atas keamanan akun Anda</li>
                            <li>Gunakan password yang kuat dan unik</li>
                            <li>Segera laporkan jika ada aktivitas mencurigakan</li>
                            <li>Jangan berbagi kredensial login dengan orang lain</li>
                        </ul>
                    </div>

                    <h2 class="text-2xl font-bold text-black mb-4">5. Pemesanan Ruang Meeting</h2>
                    <div class="text-black/90 mb-6">
                        <h3 class="text-xl font-semibold text-black mb-3">5.1 Proses Pemesanan</h3>
                        <ul class="list-disc list-inside space-y-2 mb-4">
                            <li>Pemesanan dilakukan secara online melalui sistem</li>
                            <li>Ketersediaan ruang dapat berubah sewaktu-waktu</li>
                            <li>Pemesanan harus dilakukan sesuai dengan jadwal yang tersedia</li>
                            <li>Pengguna tidak dapat membuat booking yang overlap dengan booking miliknya sendiri</li>
                            <li>Dokumen perizinan dalam format PDF (maksimal 2MB) dapat disertakan jika diperlukan</li>
                            <li>Validasi captcha wajib dilakukan saat membuat booking baru</li>
                            <li>Booking baru berstatus "pending" dan menunggu konfirmasi admin</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-black mb-3">5.2 PIC Invitations (Undangan PIC)</h3>
                        <ul class="list-disc list-inside space-y-2 mb-4">
                            <li>Pengguna dapat mengundang PIC (Person In Charge) lain untuk melihat booking mereka</li>
                            <li>PIC yang diundang akan menerima notifikasi undangan</li>
                            <li>PIC yang diundang dapat melihat booking di kalender dashboard mereka</li>
                            <li>Visibilitas deskripsi meeting dapat diatur menjadi "public" (semua PIC) atau "invited_pics_only" (hanya PIC yang diundang)</li>
                            <li>Hanya admin, owner booking, atau PIC yang diundang (jika pengaturan "invited_pics_only") yang dapat melihat deskripsi meeting lengkap</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-black mb-3">5.3 Preempt Booking (Permintaan Didahulukan)</h3>
                        <ul class="list-disc list-inside space-y-2 mb-4">
                            <li>Pengguna dapat meminta booking milik pengguna lain untuk didahulukan jika ada konflik jadwal</li>
                            <li>Pengguna tidak dapat meminta didahulukan booking miliknya sendiri</li>
                            <li>Pemilik booking yang diminta akan menerima notifikasi permintaan didahulukan</li>
                            <li>Pemilik booking dapat merespons dengan "Terima & Batalkan"</li>
                            <li>Jika diterima, sistem akan otomatis membatalkan booking pemilik dan mengonfirmasi booking baru untuk peminta</li>
                            <li>Deadline respons: 60 menit (jika booking > 2 jam sebelum start time) atau 15 menit (jika booking < 2 jam sebelum start time)</li>
                            <li>Jika tidak direspons dalam deadline, permintaan didahulukan akan otomatis kadaluarsa</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-black mb-3">5.4 Pembatalan dan Perubahan</h3>
                        <ul class="list-disc list-inside space-y-2 mb-4">
                            <li>Pembatalan dapat dilakukan sesuai dengan kebijakan yang berlaku</li>
                            <li>Booking dengan status "pending" dapat dibatalkan kapan saja oleh pemilik</li>
                            <li>Booking dengan status "confirmed" hanya dapat dibatalkan jika lebih dari 2 jam sebelum waktu mulai meeting</li>
                            <li>Perubahan booking dapat dilakukan melalui menu "Edit Booking"</li>
                            <li>Pengguna dapat mengedit PIC yang diundang dan visibilitas deskripsi tanpa mengubah detail booking lainnya</li>
                            <li>Notifikasi pembatalan atau perubahan akan dikirim melalui sistem notifikasi</li>
                        </ul>
                    </div>

                    <h2 class="text-2xl font-bold text-black mb-4">6. Kode Etik Penggunaan</h2>
                    <div class="text-black/90 mb-6">
                        <h3 class="text-xl font-semibold text-black mb-3">6.1 Perilaku yang Dilarang</h3>
                        <ul class="list-disc list-inside space-y-2 mb-4">
                            <li>Menggunakan sistem untuk tujuan ilegal atau tidak pantas</li>
                            <li>Mencoba merusak atau mengganggu sistem</li>
                            <li>Menggunakan bot atau script otomatis</li>
                            <li>Melakukan spam atau aktivitas yang mengganggu</li>
                            <li>Menyalahgunakan informasi pengguna lain</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-black mb-3">6.2 Sanksi</h3>
                        <ul class="list-disc list-inside space-y-2 mb-4">
                            <li>Peringatan tertulis untuk pelanggaran ringan</li>
                            <li>Penangguhan akses sementara untuk pelanggaran sedang</li>
                            <li>Penutupan akun permanen untuk pelanggaran berat</li>
                            <li>Tindakan hukum jika diperlukan</li>
                        </ul>
                    </div>

                    <h2 class="text-2xl font-bold text-black mb-4">7. Ketersediaan Layanan</h2>
                    <div class="text-black/90 mb-6">
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

                    <h2 class="text-2xl font-bold text-black mb-4">8. Privasi dan Keamanan Data</h2>
                    <div class="text-black/90 mb-6">
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

                    <h2 class="text-2xl font-bold text-black mb-4">9. Hak Kekayaan Intelektual</h2>
                    <div class="text-black/90 mb-6">
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

                    <h2 class="text-2xl font-bold text-black mb-4">10. Pembatasan Tanggung Jawab</h2>
                    <div class="text-black/90 mb-6">
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

                    <h2 class="text-2xl font-bold text-black mb-4">11. Perubahan Syarat dan Ketentuan</h2>
                    <div class="text-black/90 mb-6">
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

                    <h2 class="text-2xl font-bold text-black mb-4">12. Hukum yang Berlaku</h2>
                    <div class="text-black/90 mb-6">
                        <p class="mb-4">
                            Syarat dan ketentuan ini diatur oleh dan ditafsirkan sesuai dengan 
                            hukum Republik Indonesia. Setiap sengketa yang timbul akan diselesaikan 
                            melalui pengadilan yang berwenang di Indonesia.
                        </p>
                    </div>

                    <h2 class="text-2xl font-bold text-black mb-4">13. Kontak dan Bantuan</h2>
                    <div class="text-black/90 mb-6">
                        <p class="mb-4">Jika Anda memiliki pertanyaan tentang syarat dan ketentuan ini:</p>
                        <div class="bg-white/10 rounded-lg p-4">
                            <p><strong>PUSDATIN</strong></p>
                            <p>Website: <a href="https://www.pusdatinbgn.web.id" class="text-blue-300 hover:text-blue-200">www.pusdatinbgn.web.id</a></p>
                            <p class="mt-2 text-sm">Jam operasional: Senin - Jumat, 08:00 - 17:00 WIB</p>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-white/20">
                        <p class="text-black/60 text-sm text-center">
                            <span onclick="showChangelogModal()" class="text-black/80 font-medium cursor-pointer hover:text-black underline transition-colors duration-300">Versi Aplikasi v2.1.5</span><br>
                            Dokumen ini dibuat dengan ❤️<br>
                            © {{ date('Y') }} Sistem Pemesanan Ruang Meeting. Semua hak dilindungi.
                        </p>
                    </div>

                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="text-center mt-8">
            <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 bg-white/20 text-black rounded-lg hover:bg-white/30 transition-all duration-300">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Halaman Login
            </a>
        </div>
    </div>

    <!-- WhatsApp Floating Button -->
    @include('components.whatsapp-float')

    <script>
        // Changelog Modal Functions
        window.showChangelogModal = function() {
            const modalHtml = `
                <div id="changelogModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-2 sm:p-4" onclick="closeChangelogModal()">
                    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
                        <div class="sticky top-0 bg-white border-b border-gray-200 z-10 p-4 sm:p-6 pb-4 flex justify-between items-center">
                            <h3 class="text-lg sm:text-xl font-bold text-black">Changelog Aplikasi</h3>
                            <button type="button" onclick="closeChangelogModal()" class="text-gray-500 hover:text-gray-700 p-2 -mr-2">
                                <i class="fas fa-times text-xl sm:text-2xl"></i>
                            </button>
                        </div>
                        
                        <div class="p-4 sm:p-6">
                            <!-- v2.1.5 -->
                            <div class="mb-6">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-lg font-bold text-black">v2.1.5 (2025) - Penghapusan Fitur Preempt</h4>
                                    <span class="text-sm text-gray-500">November 2025</span>
                                </div>
                                <ul class="space-y-2 text-sm text-gray-700">
                                    <li class="flex items-start">
                                        <i class="fas fa-minus-circle text-orange-500 mr-2 mt-1"></i>
                                        <span><strong>Penghapusan Fitur Didahulukan Meeting</strong> - Fitur "Minta Didahulukan" (preempt request) telah dihapus dari sistem untuk menyederhanakan proses booking</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Penyederhanaan Alur Booking</strong> - Sistem booking sekarang lebih sederhana tanpa fitur preempt, fokus pada booking langsung</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Perbaikan Validasi Konflik</strong> - Validasi konflik jadwal tetap berfungsi untuk mencegah double booking</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- v2.1.4 -->
                            <div class="mb-6 border-t border-gray-200 pt-6">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-lg font-bold text-black">v2.1.4 (2025) - Field Kapasitas Opsional</h4>
                                    <span class="text-sm text-gray-500">November 2025</span>
                                </div>
                                <ul class="space-y-2 text-sm text-gray-700">
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Field Kapasitas Menjadi Opsional</strong> - Field kapasitas di form create room sekarang opsional, admin bisa membuat room tanpa mengisi kapasitas</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Fleksibilitas Input Data</strong> - Admin memiliki lebih banyak fleksibilitas dalam membuat room meeting tanpa harus mengisi kapasitas</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- v2.1.3 -->
                            <div class="mb-6 border-t border-gray-200 pt-6">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-lg font-bold text-black">v2.1.3 (2025) - Mobile Calendar Enhancement</h4>
                                    <span class="text-sm text-gray-500">November 2025</span>
                                </div>
                                <ul class="space-y-2 text-sm text-gray-700">
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Modal Daftar Meeting untuk Mobile</strong> - Klik box tanggal di kalender mobile menampilkan modal dengan semua meeting untuk hari tersebut</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Detail Meeting dari Modal</strong> - Klik item meeting di modal menampilkan detail lengkap seperti popup detail meeting</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Pengalaman Mobile yang Lebih Baik</strong> - Tidak perlu scroll di dalam kalender, semua meeting ditampilkan dalam modal yang mudah diakses</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Scroll Tetap Berfungsi</strong> - Scroll di dalam kalender tetap berfungsi normal, modal hanya muncul saat klik box tanggal</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- v2.1.2 -->
                            <div class="mb-6 border-t border-gray-200 pt-6">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-lg font-bold text-black">v2.1.2 (2025) - Real-time Notifications</h4>
                                    <span class="text-sm text-gray-500">November 2025</span>
                                </div>
                                <ul class="space-y-2 text-sm text-gray-700">
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Notifikasi Real-time untuk Admin dan User</strong> - Notifikasi auto-refresh setiap 10 detik tanpa perlu refresh browser</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Peningkatan Responsivitas Notifikasi</strong> - Notifikasi masuk secara real-time dalam maksimal 10 detik</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Pengalaman Pengguna yang Lebih Baik</strong> - Admin dan user tidak perlu refresh browser untuk melihat notifikasi baru</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- v2.1.1 -->
                            <div class="mb-6 border-t border-gray-200 pt-6">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-lg font-bold text-black">v2.1.1 (2025) - Bug Fixes</h4>
                                    <span class="text-sm text-gray-500">November 2025</span>
                                </div>
                                <ul class="space-y-2 text-sm text-gray-700">
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Perbaikan Bug Notifikasi Admin</strong> - Teks "Admin Notifikasis" diperbaiki menjadi "Admin Notifikasi"</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Perbaikan Mark as Read</strong> - Badge count berkurang dengan benar setelah klik notifikasi</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Perbaikan Mark All as Read</strong> - Fungsi mark all as read sekarang bekerja dengan benar</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Perbaikan Badge Count</strong> - Badge count ter-update secara real-time setelah mark as read</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Perbaikan Error Handling</strong> - Menambahkan error handling dan logging yang lebih baik untuk notifikasi</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- v1.0.0 -->
                            <div class="mb-4 border-t border-gray-200 pt-6">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-lg font-bold text-black">v1.0.0 (2025) - Initial Release</h4>
                                    <span class="text-sm text-gray-500">Oktober 2025</span>
                                </div>
                                <ul class="space-y-2 text-sm text-gray-700">
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span>Sistem authentication lengkap</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span>Dashboard user dan admin</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span>Sistem booking dengan validasi cerdas</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span>Manajemen user dan ruang meeting</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span>Sistem notifikasi real-time</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span>Export data dalam format CSV</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span>Responsive design</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span>Bahasa Indonesia dasar</span>
                                    </li>
                                </ul>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <button type="button" onclick="closeChangelogModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 w-full sm:w-auto">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            
            // Add event listener for ESC key
            document.addEventListener('keydown', function escHandler(e) {
                if (e.key === 'Escape') {
                    const modal = document.getElementById('changelogModal');
                    if (modal && !modal.classList.contains('hidden')) {
                        closeChangelogModal();
                        document.removeEventListener('keydown', escHandler);
                    }
                }
            });
        };

        window.closeChangelogModal = function() {
            const modal = document.getElementById('changelogModal');
            if (modal) {
                modal.remove();
            }
        };
    </script>
</body>
</html>
