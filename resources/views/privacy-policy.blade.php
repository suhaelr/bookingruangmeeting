<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Meta Tags -->
    @include('components.seo-meta', [
        'page' => 'privacy_policy',
        'title' => 'Kebijakan Privasi - Sistem Pemesanan Ruang Meeting',
        'description' => 'Kebijakan privasi sistem pemesanan ruang meeting. Informasi tentang pengumpulan, penggunaan, dan perlindungan data pribadi.',
        'keywords' => 'kebijakan privasi, perlindungan data, privasi pengguna, sistem pemesanan',
        'canonical' => '/privacy-policy',
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
                        Sistem Pemesanan Ruang Meeting adalah aplikasi web untuk memfasilitasi pemesanan ruang meeting secara online. Kebijakan privasi ini menjelaskan 
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
                            <li>Unit kerja</li>
                            <li>Password (dienkripsi)</li>
                            <li>Role/Peran pengguna (admin atau user)</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-white mb-3">2.2 Informasi Pemesanan</h3>
                        <ul class="list-disc list-inside space-y-2 mb-4">
                            <li>Detail pemesanan ruang meeting (judul, deskripsi, waktu)</li>
                            <li>Tanggal dan waktu pemesanan (start_time, end_time)</li>
                            <li>Dokumen perizinan dalam format PDF (jika diperlukan)</li>
                            <li>Status pemesanan (pending, confirmed, cancelled, completed)</li>
                            <li>Unit kerja pemesan</li>
                            <li>Jumlah peserta dan daftar email peserta (opsional)</li>
                            <li>Kebutuhan khusus meeting</li>
                            <li>Pengaturan visibilitas deskripsi (public atau invited_pics_only)</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-white mb-3">2.3 Informasi PIC Invitations</h3>
                        <ul class="list-disc list-inside space-y-2 mb-4">
                            <li>Daftar PIC (Person In Charge) yang diundang untuk meeting</li>
                            <li>Status undangan PIC</li>
                            <li>Riwayat undangan yang dikirim dan diterima</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-white mb-3">2.4 Informasi Preempt Booking</h3>
                        <ul class="list-disc list-inside space-y-2 mb-4">
                            <li>Permintaan didahulukan (preempt request) dari pengguna lain</li>
                            <li>Alasan permintaan didahulukan</li>
                            <li>Status tanggapan pemilik booking</li>
                            <li>Deadline untuk merespons permintaan</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-white mb-3">2.5 Informasi Teknis</h3>
                        <ul class="list-disc list-inside space-y-2 mb-4">
                            <li>Alamat IP</li>
                            <li>Browser dan perangkat yang digunakan</li>
                            <li>Waktu login terakhir</li>
                        </ul>
                    </div>

                    <h2 class="text-2xl font-bold text-white mb-4">3. Cara Kami Menggunakan Informasi</h2>
                    <div class="text-white/90 mb-6">
                        <p class="mb-4">Kami menggunakan informasi yang dikumpulkan untuk:</p>
                        <ul class="list-disc list-inside space-y-2">
                            <li>Menyediakan layanan pemesanan ruang meeting</li>
                            <li>Memverifikasi identitas pengguna melalui email verification</li>
                            <li>Mengelola dan memproses pemesanan ruang meeting</li>
                            <li>Mengirim notifikasi terkait pemesanan, undangan PIC, dan permintaan didahulukan</li>
                            <li>Mengelola undangan PIC ke meeting tertentu</li>
                            <li>Memproses permintaan didahulukan (preempt booking) antar pengguna</li>
                            <li>Mengatur visibilitas deskripsi meeting berdasarkan pengaturan pengguna</li>
                            <li>Meningkatkan keamanan sistem dengan role-based access control</li>
                            <li>Menyediakan dashboard dan kalender untuk monitoring booking</li>
                            <li>Menyediakan dukungan teknis</li>
                            <li>Mematuhi kewajiban hukum</li>
                        </ul>
                    </div>

                    <h2 class="text-2xl font-bold text-white mb-4">4. Keamanan Data</h2>
                    <div class="text-white/90 mb-6">
                        <p class="mb-4">Kami menerapkan berbagai langkah keamanan untuk melindungi informasi Anda:</p>
                        <ul class="list-disc list-inside space-y-2">
                            <li>Enkripsi password menggunakan hash yang aman (bcrypt)</li>
                            <li>Koneksi HTTPS untuk semua komunikasi</li>
                            <li>Verifikasi email wajib untuk akun baru sebelum dapat menggunakan sistem</li>
                            <li>Pembatasan akses berdasarkan peran pengguna (role-based access control)</li>
                            <li>Kontrol visibilitas deskripsi meeting (hanya admin, owner, atau PIC yang diundang dapat melihat deskripsi jika pengaturan "invited_pics_only")</li>
                            <li>Proteksi terhadap double booking dan konflik jadwal</li>
                            <li>Transaksi database untuk memastikan integritas data saat proses preempt booking</li>
                            <li>Validasi captcha untuk mencegah abuse saat membuat booking</li>
                            <li>Pencadangan data secara berkala</li>
                            <li>Session management yang aman dengan CSRF protection</li>
                        </ul>
                    </div>

                    <h2 class="text-2xl font-bold text-white mb-4">5. Berbagi Informasi</h2>
                    <div class="text-white/90 mb-6">
                        <p class="mb-4">Kami tidak menjual, menyewakan, atau membagikan informasi pribadi Anda kepada pihak ketiga, kecuali:</p>
                        <ul class="list-disc list-inside space-y-2 mb-4">
                            <li>Dengan persetujuan eksplisit dari Anda</li>
                            <li>Untuk mematuhi kewajiban hukum</li>
                            <li>Untuk melindungi hak dan keamanan kami atau pengguna lain</li>
                            <li>Dengan penyedia layanan tepercaya yang membantu operasional sistem</li>
                        </ul>
                        <h3 class="text-xl font-semibold text-white mb-3">5.1 Berbagi Informasi dalam Sistem</h3>
                        <p class="mb-4">Informasi berikut dibagikan kepada pengguna lain dalam sistem sesuai dengan fitur:</p>
                        <ul class="list-disc list-inside space-y-2">
                            <li><strong>Informasi Booking:</strong> Judul, waktu, ruang, unit kerja, dan nama PIC pemesan ditampilkan di kalender untuk semua pengguna yang memiliki akses (berdasarkan visibilitas)</li>
                            <li><strong>Deskripsi Meeting:</strong> Hanya dapat dilihat oleh admin, owner booking, atau PIC yang diundang (jika pengaturan visibilitas "invited_pics_only")</li>
                            <li><strong>PIC Invitations:</strong> PIC yang diundang dapat melihat informasi booking mereka di dashboard</li>
                            <li><strong>Preempt Booking:</strong> Informasi permintaan didahulukan dibagikan kepada pemilik booking yang diminta untuk didahulukan</li>
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
                            <li>Session cookies untuk autentikasi dan menjaga status login pengguna</li>
                            <li>CSRF tokens untuk melindungi dari serangan cross-site request forgery</li>
                            <li>Session storage untuk menyimpan preferensi pengguna sementara</li>
                        </ul>
                        <p class="mt-4">Anda dapat mengatur browser untuk menolak cookies, namun hal ini dapat mempengaruhi fungsionalitas sistem seperti kemampuan untuk tetap login dan menggunakan fitur-fitur yang memerlukan autentikasi.</p>
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
                            <p><strong>PUSDATIN</strong></p>
                            <p>Website: <a href="https://www.pusdatinbgn.web.id" class="text-blue-300 hover:text-blue-200">www.pusdatinbgn.web.id</a></p>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-white/20">
                        <p class="text-white/60 text-sm text-center">
                            <span onclick="showChangelogModal()" class="text-white/80 font-medium cursor-pointer hover:text-white underline transition-colors duration-300">Versi Aplikasi v2.1.1</span><br>
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

    <script>
        // Changelog Modal Functions
        window.showChangelogModal = function() {
            const modalHtml = `
                <div id="changelogModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-2 sm:p-4" onclick="closeChangelogModal()">
                    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
                        <div class="sticky top-0 bg-white border-b border-gray-200 z-10 p-4 sm:p-6 pb-4 flex justify-between items-center">
                            <h3 class="text-lg sm:text-xl font-bold text-gray-800">Changelog Aplikasi</h3>
                            <button type="button" onclick="closeChangelogModal()" class="text-gray-500 hover:text-gray-700 p-2 -mr-2">
                                <i class="fas fa-times text-xl sm:text-2xl"></i>
                            </button>
                        </div>
                        
                        <div class="p-4 sm:p-6">
                            <!-- v2.1.1 -->
                            <div class="mb-6">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-lg font-bold text-gray-800">v2.1.1 (2025) - Bug Fixes</h4>
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

                            <!-- v2.1.0 -->
                            <div class="mb-6 border-t border-gray-200 pt-6">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-lg font-bold text-gray-800">v2.1.0 (2025) - Feature Update</h4>
                                    <span class="text-sm text-gray-500">Januari 2025</span>
                                </div>
                                <ul class="space-y-2 text-sm text-gray-700">
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Export Excel</strong> - Menggantikan export CSV dengan format Excel (.xlsx) menggunakan SheetJS</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Sistem Preempt Request dengan SLA 1 Jam</strong> - Sistem ajukan pendahuluan meeting dengan deadline 1 jam</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Popup Warning Jadwal Bentrok Real-time</strong> - Popup modal muncul langsung saat deteksi konflik tanpa perlu submit form</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Perbaikan User Access Control</strong> - Kontrol akses deskripsi dan PDF berdasarkan checkbox invitation yang dicentang</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Perbaikan Responsive Design</strong> - Header mobile ditambahkan di semua halaman (admin dashboard, user dashboard)</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Perbaikan Popup Conflict Modal</strong> - Popup jadwal bentrok dapat ditutup dengan tombol X, button Tutup, atau ESC key</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Dokumentasi Lengkap</strong> - Dokumentasi lengkap skenario order bentrok dan sistem ajukan pendahuluan meeting</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- v2.0.0 -->
                            <div class="mb-6 border-t border-gray-200 pt-6">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-lg font-bold text-gray-800">v2.0.0 (2025) - Major Update</h4>
                                    <span class="text-sm text-gray-500">Januari 2025</span>
                                </div>
                                <ul class="space-y-2 text-sm text-gray-700">
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Dukungan Bahasa Indonesia Penuh</strong> - Semua pesan validasi dalam bahasa Indonesia</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Sistem Email Reminder</strong> - Email otomatis 30 menit sebelum meeting</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Smart Room Deletion Logic</strong> - Logika penghapusan ruang yang cerdas</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Auto Status Update</strong> - Booking otomatis selesai saat waktu habis</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>User Notification System</strong> - Sistem notifikasi in-app yang lengkap</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Room Maintenance Notices</strong> - Notifikasi maintenance untuk user</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Enhanced User Experience</strong> - Peringatan saat tidak ada ruang tersedia</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Automated Commands</strong> - Command untuk update status dan email reminder</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Beautiful Email Templates</strong> - Template HTML yang indah untuk email</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Comprehensive Logging</strong> - Logging lengkap untuk semua aktivitas</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- v1.0.0 -->
                            <div class="mb-4 border-t border-gray-200 pt-6">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-lg font-bold text-gray-800">v1.0.0 (2025) - Initial Release</h4>
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
