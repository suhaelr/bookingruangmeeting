# Meeting Room Booking System

Sistem booking meeting room yang lengkap dengan 2 jenis akun (Admin dan User) serta fitur-fitur modern.

## 🎯 Fitur Utama

### 👨‍💼 Admin Features
- **Dashboard Admin** dengan statistik lengkap
- **Manajemen User** - Lihat dan kelola semua user
- **Manajemen Ruang Meeting** - Kelola ruang meeting
- **Monitoring Booking** - Pantau semua booking dengan status management
- **Charts & Analytics** - Grafik interaktif untuk analisis

### 👤 User Features
- **Dashboard User** dengan statistik personal
- **Booking System** dengan kalender interaktif
- **Profile Management** - Kelola profil pribadi
- **My Bookings** - Lihat dan kelola booking sendiri
- **Room Availability** - Lihat ruang meeting yang tersedia

### 📅 Booking System
- **Kalender Interaktif** untuk pemilihan tanggal
- **Validasi Double Booking** otomatis
- **Status Management**: pending, confirmed, cancelled, completed
- **Cost Calculation** otomatis berdasarkan durasi
- **Special Requirements** dan attendees management
- **Export Functionality** untuk data booking

## 🔐 Kredensial Login

### Admin
- **Username:** `admin`
- **Password:** `admin`
- **Akses:** Dashboard admin dengan semua fitur manajemen

### User
- **Username:** `user`
- **Password:** `user`
- **Akses:** Dashboard user untuk booking meeting room

## 🚀 Cara Menggunakan

### 1. Akses Aplikasi
```
http://localhost:8000
```

### 2. Login sebagai Admin
1. Masukkan username: `admin`
2. Masukkan password: `admin`
3. Akan diarahkan ke dashboard admin
4. Akses fitur:
   - Dashboard dengan statistik lengkap
   - Manage Users
   - Manage Rooms
   - Manage Bookings

### 3. Login sebagai User
1. Masukkan username: `user`
2. Masukkan password: `user`
3. Akan diarahkan ke dashboard user
4. Akses fitur:
   - Dashboard dengan statistik personal
   - Book Meeting Room
   - My Bookings
   - Profile Management

## 🗄️ Database Structure

### Tabel Users
```sql
- id (Primary Key)
- name (Laravel default)
- username (Unique)
- email (Unique)
- password (Hashed)
- full_name
- phone
- department
- role (admin/user)
- avatar
- last_login_at
- created_at, updated_at
```

### Tabel Meeting Rooms
```sql
- id (Primary Key)
- name
- description
- capacity
- amenities (JSON)
- location
- hourly_rate
- images (JSON)
- is_active
- created_at, updated_at
```

### Tabel Bookings
```sql
- id (Primary Key)
- user_id (Foreign Key)
- meeting_room_id (Foreign Key)
- title
- description
- start_time
- end_time
- status (pending/confirmed/cancelled/completed)
- attendees_count
- attendees (JSON)
- attachments (JSON)
- special_requirements
- total_cost
- cancelled_at
- cancellation_reason
- created_at, updated_at
```

## 🎨 UI/UX Features

- **Modern Design** dengan glass morphism effect
- **Responsive** untuk semua device (desktop, tablet, mobile)
- **Interactive Charts** dengan Chart.js
- **Real-time Cost Calculation**
- **Status-based Color Coding**
- **Smooth Animations** dan transitions
- **Mobile-first Approach**
- **Dark Theme** dengan gradient background

## 📊 Sample Data

Aplikasi sudah dilengkapi dengan sample data:
- **4 Users** (1 admin, 3 users)
- **5 Meeting Rooms** dengan berbagai fasilitas
- **25 Sample Bookings** (past dan future)
- **Berbagai Status** booking untuk testing

### Sample Meeting Rooms:
1. **Conference Room A** - 20 seats, Rp 150,000/hour
2. **Meeting Room B** - 8 seats, Rp 100,000/hour
3. **Executive Boardroom** - 12 seats, Rp 250,000/hour
4. **Training Room** - 30 seats, Rp 200,000/hour
5. **Small Meeting Room** - 4 seats, Rp 75,000/hour

## 🔧 Technical Features

- **Laravel 11** dengan struktur MVC yang clean
- **Session-based Authentication**
- **Middleware Protection** untuk setiap role
- **Database Relationships** yang proper
- **Validation** yang comprehensive
- **Error Handling** yang user-friendly
- **CSRF Protection** dan security features
- **Pagination** untuk data besar
- **Export Functionality** (CSV)

## 📱 Responsive Design

### Desktop (1024px+)
- Layout 3 kolom untuk dashboard
- Sidebar navigation
- Full table view

### Tablet (768px - 1023px)
- Layout 2 kolom
- Collapsible navigation
- Responsive tables

### Mobile (< 768px)
- Single column layout
- Mobile navigation menu
- Card-based layout
- Touch-optimized buttons

## 🌐 Browser Support

- **Chrome** 90+
- **Firefox** 88+
- **Safari** 14+
- **Edge** 90+
- **Mobile browsers** (iOS Safari, Chrome Mobile)

## 📁 File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AdminController.php
│   │   ├── AuthController.php
│   │   └── UserController.php
│   └── Middleware/
│       ├── AdminAuth.php
│       └── UserAuth.php
├── Models/
│   ├── Booking.php
│   ├── MeetingRoom.php
│   └── User.php
resources/
├── views/
│   ├── admin/
│   │   ├── dashboard.blade.php
│   │   ├── bookings.blade.php
│   │   ├── users.blade.php
│   │   └── rooms.blade.php
│   ├── user/
│   │   ├── dashboard.blade.php
│   │   ├── bookings.blade.php
│   │   ├── create-booking.blade.php
│   │   └── profile.blade.php
│   └── auth/
│       └── login.blade.php
├── css/
│   └── app.css
└── js/
    └── app.js
routes/
└── web.php
database/
├── migrations/
└── seeders/
    └── DatabaseSeeder.php
```

## 🚀 Deployment

### 1. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 2. Database Setup
```bash
php artisan migrate
php artisan db:seed
```

### 3. Build Assets
```bash
npm install
npm run build
```

### 4. Run Server
```bash
php artisan serve
```

## 🔍 Troubleshooting

### Error: "View not found"
- Pastikan semua file view sudah dibuat
- Cek nama file dan path yang benar

### Error: "Class not found"
```bash
composer dump-autoload
```

### Error: "Database connection failed"
- Cek konfigurasi database di `.env`
- Pastikan database server berjalan

### Error: "Permission denied"
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## 📞 Support

Untuk pertanyaan atau masalah:
1. Cek file log di `storage/logs/laravel.log`
2. Pastikan semua dependencies terinstall
3. Cek konfigurasi database dan environment

---

**Dibuat dengan ❤️ menggunakan Laravel 11 dan Tailwind CSS**
