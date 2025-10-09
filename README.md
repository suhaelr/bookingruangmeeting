# Sistem Pemesanan Ruang Meeting

Sistem pemesanan ruang meeting yang modern dan responsif dengan fitur lengkap untuk manajemen booking ruang meeting. Dibangun dengan Laravel 11 dan menggunakan teknologi web modern.

## 🚀 Fitur Utama

### 👤 Fitur User
- **Dashboard User** dengan statistik booking personal
- **Pemesanan Ruang Meeting** dengan kalender interaktif
- **Edit dan Hapus Booking** dengan validasi cerdas
- **Manajemen Profile** lengkap
- **Sistem Notifikasi** real-time
- **Validasi Konflik** booking otomatis
- **Saran Waktu Kosong** cerdas

### 👨‍💼 Fitur Admin
- **Dashboard Admin** dengan statistik lengkap
- **Manajemen User** (tambah, edit, hapus)
- **Manajemen Ruang Meeting** 
- **Monitoring Booking** real-time
- **Sistem Notifikasi** admin
- **Export Data** dalam format CSV

### 🏢 Sistem Booking
- **Kalender Interaktif** untuk pemilihan tanggal
- **Validasi Double Booking** otomatis
- **Status Booking** (pending, confirmed, cancelled)
- **Upload Attachment** untuk booking
- **Sistem Konflik Cerdas** dengan saran alternatif
- **Notifikasi Real-time** untuk admin dan user

## 🛠️ Teknologi yang Digunakan

- **Backend**: Laravel 11.46.0
- **Frontend**: Tailwind CSS, JavaScript (Vanilla)
- **Database**: MySQL
- **Icons**: Font Awesome 6.0.0
- **Charts**: Chart.js
- **Authentication**: Session-based
- **Responsive Design**: Mobile-first approach

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

### 6. Compile Assets
```bash
# Development
npm run dev

# Production
npm run build
```

### 7. Jalankan Server
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
- Notifikasi real-time

### Dashboard Admin
- Statistik lengkap sistem
- Grafik booking bulanan
- Ruang meeting populer
- Notifikasi admin real-time

### Sistem Booking Cerdas
- **Validasi Konflik**: Sistem otomatis mendeteksi konflik jadwal
- **Saran Alternatif**: Memberikan saran waktu kosong terdekat
- **Informasi Detail**: Menampilkan siapa yang sudah booking dan kapan
- **Kapasitas Ruang**: Validasi jumlah peserta dengan kapasitas ruang

### Manajemen User (Admin)
- Lihat detail user
- Edit informasi user
- Hapus user (dengan validasi)
- Export data user

### Manajemen Booking (Admin)
- Lihat semua booking
- Update status booking
- Monitoring real-time
- Export data booking

### Profile Management
- Update informasi personal
- Ganti password
- Pengaturan notifikasi
- Download data personal

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
- total_cost, cancelled_at, cancellation_reason

## 🚀 Deployment

### Production Deployment
1. Set environment ke production
2. Optimize autoloader: `composer install --optimize-autoloader --no-dev`
3. Compile assets: `npm run build`
4. Cache configuration: `php artisan config:cache`
5. Cache routes: `php artisan route:cache`
6. Cache views: `php artisan view:cache`

### Web Server Configuration
Pastikan web server mengarahkan semua request ke `public/index.php`

## 🤝 Kontribusi

1. Fork repository
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📝 Changelog

### v1.0.0 (2024)
- ✅ Sistem authentication lengkap
- ✅ Dashboard user dan admin
- ✅ Sistem booking dengan validasi cerdas
- ✅ Manajemen user dan ruang meeting
- ✅ Sistem notifikasi real-time
- ✅ Export data dalam format CSV
- ✅ Responsive design
- ✅ Bahasa Indonesia

## 📞 Support

Jika mengalami masalah atau memiliki pertanyaan, silakan buat issue di repository ini.

## 📄 Lisensi

Dibuat dengan ❤️ oleh eL PUSDATIN

---

**Sistem Pemesanan Ruang Meeting** - Solusi modern untuk manajemen booking ruang meeting yang efisien dan user-friendly.