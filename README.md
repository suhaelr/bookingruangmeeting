# Sistem Pemesanan Ruang Meeting

Sistem pemesanan ruang meeting yang modern dan responsif dengan fitur lengkap untuk manajemen booking ruang meeting. Dibangun dengan Laravel 11 dan menggunakan teknologi web modern dengan dukungan bahasa Indonesia penuh.

## 🚀 Fitur Utama

### 👤 Fitur User
- **Dashboard User** dengan statistik booking personal
- **Pemesanan Ruang Meeting** dengan kalender interaktif
- **Edit dan Hapus Booking** dengan validasi cerdas
- **Manajemen Profile** lengkap
- **Sistem Notifikasi** real-time dengan berbagai jenis notifikasi
- **Validasi Konflik** booking otomatis
- **Saran Waktu Kosong** cerdas
- **Email Reminder** 1 jam sebelum meeting
- **Notifikasi Maintenance** ruang meeting

### 👨‍💼 Fitur Admin
- **Dashboard Admin** dengan statistik lengkap
- **Manajemen User** (tambah, edit, hapus)
- **Manajemen Ruang Meeting** dengan logika penghapusan cerdas
- **Monitoring Booking** real-time
- **Sistem Notifikasi** admin
- **Export Data** dalam format CSV
- **Auto Status Update** booking otomatis
- **Smart Room Deletion** dengan auto-cancellation

### 🏢 Sistem Booking
- **Kalender Interaktif** untuk pemilihan tanggal
- **Validasi Double Booking** otomatis
- **Status Booking** (pending, confirmed, cancelled, completed)
- **Upload Attachment** untuk booking dengan validasi file
- **Sistem Konflik Cerdas** dengan saran alternatif
- **Notifikasi Real-time** untuk admin dan user
- **Auto Completion** booking otomatis saat waktu selesai
- **Email Reminder System** dengan template HTML yang indah

## 🛠️ Teknologi yang Digunakan

- **Backend**: Laravel 11.46.0
- **Frontend**: Tailwind CSS, JavaScript (Vanilla)
- **Database**: MySQL
- **Icons**: Font Awesome 6.0.0
- **Charts**: Chart.js
- **Authentication**: Session-based
- **Responsive Design**: Mobile-first approach
- **Email System**: Laravel Mail dengan template HTML
- **Scheduler**: Laravel Task Scheduler dengan Cron Jobs
- **Language**: Full Indonesian Language Support
- **Notifications**: In-app notification system

## 📋 Persyaratan Sistem

- PHP >= 8.2
- Composer
- MySQL >= 5.7
- Node.js & NPM (untuk asset compilation)
- Web Server (Apache/Nginx)

## 🔧 Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/suhaelr/bookingruangmeeting.git
cd bookingruangmeeting
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Konfigurasi Environment
```bash
# Copy file environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Konfigurasi Database
Edit file `.env` dan sesuaikan dengan database Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=booking_ruang_meeting
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Migrasi Database
```bash
# Jalankan migrasi
php artisan migrate

# Jalankan seeder (opsional)
php artisan db:seed
```

### 6. Konfigurasi Email (Opsional)
Untuk fitur email reminder, edit file `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Meeting Room System"
```

### 7. Setup Cron Jobs (Opsional)
Untuk fitur otomatis, tambahkan ke crontab server:
```bash
# Update booking status setiap 5 menit
*/5 * * * * cd /path/to/your/project && php artisan bookings:update-status >> /dev/null 2>&1

# Kirim email reminder setiap 15 menit
*/15 * * * * cd /path/to/your/project && php artisan bookings:send-reminders >> /dev/null 2>&1
```

### 8. Compile Assets
```bash
# Development
npm run dev

# Production
npm run build
```

### 9. Jalankan Server
```bash
# Development server
php artisan serve

# Atau untuk production
php artisan serve --host=0.0.0.0 --port=8000
```

## 👥 Akun Default

### Super Admin
- **Username**: admin
- **Password**: admin
- **Akses**: Full access ke semua fitur

### User Biasa
- **Username**: user
- **Password**: user
- **Akses**: Booking dan manajemen profile

## 📱 Fitur Detail

### Dashboard User
- Statistik booking personal
- Booking aktif dan hari ini
- Ruang meeting tersedia
- Notifikasi real-time dengan berbagai jenis
- Email reminder notifications

### Dashboard Admin
- Statistik lengkap sistem
- Grafik booking bulanan
- Ruang meeting populer
- Notifikasi admin real-time
- Auto status update monitoring

### Sistem Booking Cerdas
- **Validasi Konflik**: Sistem otomatis mendeteksi konflik jadwal
- **Saran Alternatif**: Memberikan saran waktu kosong terdekat
- **Informasi Detail**: Menampilkan siapa yang sudah booking dan kapan
- **Kapasitas Ruang**: Validasi jumlah peserta dengan kapasitas ruang
- **Auto Completion**: Booking otomatis selesai saat waktu habis
- **Email Reminder**: Notifikasi 1 jam sebelum meeting

### Manajemen User (Admin)
- Lihat detail user
- Edit informasi user
- Hapus user (dengan validasi)
- Export data user
- Notifikasi maintenance untuk user

### Manajemen Ruang Meeting (Admin)
- **Smart Deletion Logic**: 
  - ✅ Bisa hapus ruang jika tidak ada booking
  - ✅ Bisa hapus ruang jika sudah dinonaktifkan (auto-cancel booking aktif)
  - ✅ Bisa hapus ruang jika semua booking sudah selesai
  - ❌ Tidak bisa hapus ruang aktif dengan booking aktif
- **Auto-Cancellation**: Booking aktif otomatis dibatalkan dengan notifikasi maintenance
- **Maintenance Notice**: User mendapat notifikasi saat ruang dinonaktifkan

### Manajemen Booking (Admin)
- Lihat semua booking
- Update status booking dengan notifikasi user
- Monitoring real-time
- Export data booking
- Auto status update (completed saat waktu selesai)

### Profile Management
- Update informasi personal
- Ganti password
- Pengaturan notifikasi
- Download data personal
- Notifikasi maintenance ruang

## 🆕 Fitur Baru v2.0

### 🌐 Dukungan Bahasa Indonesia Penuh
- Semua pesan validasi dalam bahasa Indonesia
- Error messages yang user-friendly
- Interface yang konsisten dengan bahasa Indonesia

### 📧 Sistem Email Reminder
- **Template HTML Indah**: Email reminder dengan design modern
- **Timing Otomatis**: Kirim 1 jam sebelum meeting
- **Informasi Lengkap**: Detail meeting, ruang, waktu, dan peserta
- **Tips Meeting**: Saran untuk persiapan meeting

### 🔔 Sistem Notifikasi Cerdas
- **Jenis Notifikasi**:
  - `booking_confirmed`: Booking dikonfirmasi admin
  - `booking_cancelled`: Booking dibatalkan
  - `booking_completed`: Meeting selesai
  - `room_maintenance`: Ruang dalam maintenance
- **Real-time Display**: Notifikasi muncul di dashboard user
- **Mark as Read**: User bisa menandai notifikasi sudah dibaca

### ⚡ Auto Status Update
- **Command**: `php artisan bookings:update-status`
- **Fungsi**: Booking otomatis selesai saat waktu habis
- **Scheduling**: Berjalan setiap 5 menit
- **Logging**: Semua perubahan tercatat di log

### 🏢 Smart Room Management
- **Logika Penghapusan Cerdas**:
  - Ruang kosong → Bisa dihapus
  - Ruang nonaktif → Bisa dihapus (booking aktif auto-cancel)
  - Ruang dengan booking selesai → Bisa dihapus
  - Ruang aktif dengan booking aktif → Tidak bisa dihapus
- **Auto-Cancellation**: Booking aktif dibatalkan dengan alasan maintenance
- **User Notification**: User mendapat notifikasi maintenance

### 📱 Enhanced User Experience
- **No Rooms Warning**: Peringatan saat tidak ada ruang tersedia
- **Maintenance Notice**: Notifikasi jelas saat ruang dalam maintenance
- **Email Integration**: Konfirmasi booking dengan info email reminder 1 jam

## 🎨 Tampilan

- **Design**: Modern glass-morphism design
- **Responsive**: Mobile-first approach
- **Color Scheme**: Dark theme dengan aksen biru
- **Icons**: Font Awesome 6.0.0
- **Typography**: Clean dan readable

## 🔒 Keamanan

- **CSRF Protection**: Semua form dilindungi CSRF
- **Session Authentication**: Sistem login berbasis session
- **Input Validation**: Validasi input server-side dan client-side
- **SQL Injection Protection**: Menggunakan Eloquent ORM
- **XSS Protection**: Output escaping

## 📊 Database Schema

### Tabel Users
- id, name, username, email, password
- full_name, phone, department, role
- avatar, last_login_at, timestamps

### Tabel Meeting Rooms
- id, name, description, capacity
- amenities, location, hourly_rate
- images, is_active, timestamps

### Tabel Bookings
- id, user_id, meeting_room_id
- title, description, start_time, end_time
- status, attendees_count, attendees
- attachments, special_requirements
- unit_kerja, dokumen_perizinan
- total_cost, cancelled_at, cancellation_reason

### Tabel User Notifications (Baru)
- id, user_id, booking_id
- type, title, message
- is_read, read_at, timestamps

## ⚙️ Automated Commands

### Testing Commands
```bash
# Test booking status updates
php artisan bookings:update-status

# Test email reminders
php artisan bookings:send-reminders
```

### Command Descriptions
- **`bookings:update-status`**: Mengupdate status booking yang sudah selesai menjadi "completed"
- **`bookings:send-reminders`**: Mengirim email reminder 1 jam sebelum meeting dimulai

### Manual Testing
```bash
# Test email reminder system
php artisan tinker
>>> $booking = App\Models\Booking::where('status', 'confirmed')->first();
>>> Mail::to($booking->user->email)->send(new App\Mail\BookingReminderMail($booking));
```

## 🚀 Deployment

### Production Deployment
1. Set environment ke production
2. Optimize autoloader: `composer install --optimize-autoloader --no-dev`
3. Compile assets: `npm run build`
4. Cache configuration: `php artisan config:cache`
5. Cache routes: `php artisan route:cache`
6. Cache views: `php artisan view:cache`
7. **Setup Cron Jobs** (penting untuk fitur otomatis):
   ```bash
   */5 * * * * cd /path/to/project && php artisan bookings:update-status >> /dev/null 2>&1
   */15 * * * * cd /path/to/project && php artisan bookings:send-reminders >> /dev/null 2>&1
   ```

### Web Server Configuration
Pastikan web server mengarahkan semua request ke `public/index.php`

## 🤝 Kontribusi

1. Fork repository
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📝 Changelog

### v2.1.1 (2025) - Bug Fixes
- ✅ **Perbaikan Bug Notifikasi Admin** - Teks "Admin Notifikasis" diperbaiki menjadi "Admin Notifikasi"
- ✅ **Perbaikan Mark as Read** - Badge count berkurang dengan benar setelah klik notifikasi
- ✅ **Perbaikan Mark All as Read** - Fungsi mark all as read sekarang bekerja dengan benar
- ✅ **Perbaikan Badge Count** - Badge count ter-update secara real-time setelah mark as read
- ✅ **Perbaikan Error Handling** - Menambahkan error handling dan logging yang lebih baik untuk notifikasi

### v2.1.0 (2025) - Feature Update
- ✅ **Export Excel** - Menggantikan export CSV dengan format Excel (.xlsx) menggunakan SheetJS
- ✅ **Sistem Preempt Request dengan SLA 1 Jam** - Sistem ajukan pendahuluan meeting dengan deadline 1 jam
- ✅ **Popup Warning Jadwal Bentrok Real-time** - Popup modal muncul langsung saat deteksi konflik tanpa perlu submit form
- ✅ **Perbaikan User Access Control** - Kontrol akses deskripsi dan PDF berdasarkan checkbox invitation yang dicentang
- ✅ **Perbaikan Responsive Design** - Header mobile ditambahkan di semua halaman (admin dashboard, user dashboard)
- ✅ **Perbaikan Popup Conflict Modal** - Popup jadwal bentrok dapat ditutup dengan tombol X, button Tutup, atau ESC key
- ✅ **Dokumentasi Lengkap** - Dokumentasi lengkap skenario order bentrok dan sistem ajukan pendahuluan meeting

### v2.0.0 (2025) - Major Update
- ✅ **Dukungan Bahasa Indonesia Penuh** - Semua pesan validasi dalam bahasa Indonesia
- ✅ **Sistem Email Reminder** - Email otomatis 30 menit sebelum meeting
- ✅ **Smart Room Deletion Logic** - Logika penghapusan ruang yang cerdas
- ✅ **Auto Status Update** - Booking otomatis selesai saat waktu habis
- ✅ **User Notification System** - Sistem notifikasi in-app yang lengkap
- ✅ **Room Maintenance Notices** - Notifikasi maintenance untuk user
- ✅ **Enhanced User Experience** - Peringatan saat tidak ada ruang tersedia
- ✅ **Automated Commands** - Command untuk update status dan email reminder
- ✅ **Beautiful Email Templates** - Template HTML yang indah untuk email
- ✅ **Comprehensive Logging** - Logging lengkap untuk semua aktivitas

### v1.0.0 (2025) - Initial Release
- ✅ Project dimulai sejak Oktober 2025
- ✅ Sistem authentication lengkap
- ✅ Dashboard user dan admin
- ✅ Sistem booking dengan validasi cerdas
- ✅ Manajemen user dan ruang meeting
- ✅ Sistem notifikasi real-time
- ✅ Export data dalam format CSV
- ✅ Responsive design
- ✅ Bahasa Indonesia dasar

## 📞 Support

Jika mengalami masalah atau memiliki pertanyaan, silakan buat issue di repository ini.

## 📄 Lisensi

Dibuat dengan ❤️ oleh eL PUSDATIN

---

**Sistem Pemesanan Ruang Meeting** - Solusi modern untuk manajemen booking ruang meeting yang efisien dan user-friendly.