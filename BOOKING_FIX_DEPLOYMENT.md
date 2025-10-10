# Booking Fix Deployment

## Masalah yang Diperbaiki
❌ **Error saat user booking ruangan:**
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'hourly_rate' in 'SELECT'
```

## Root Cause
Kolom `hourly_rate` sudah dihapus dari tabel `meeting_rooms` melalui migrasi `2025_10_10_051815_remove_hourly_rate_from_meeting_rooms_table`, tetapi kode masih mencoba mengakses kolom tersebut.

## Perbaikan yang Dilakukan

### 1. Model Booking (`app/Models/Booking.php`)
- ✅ Hapus referensi `$this->meetingRoom->hourly_rate` dari method `calculateTotalCost()`
- ✅ Set `total_cost` ke `0.00` karena pricing sudah dihapus

### 2. UserController (`app/Http/Controllers/UserController.php`)
- ✅ Hapus referensi `$booking->meetingRoom->hourly_rate` dari method update
- ✅ Set `total_cost` ke `0.00`

### 3. DatabaseSeeder (`database/seeders/DatabaseSeeder.php`)
- ✅ Hapus semua referensi `hourly_rate` dari data seeder
- ✅ Set `total_cost` ke `0.00` untuk semua booking

## Langkah Deployment

### 1. Deploy ke Production
```bash
# SSH ke production server
ssh user@pusdatinbgn.web.id
cd /path/to/your/laravel/app

# Pull perubahan terbaru
git pull origin master

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart web server
sudo systemctl restart apache2
```

### 2. Verifikasi Deployment
```bash
# Cek apakah perubahan sudah ter-deploy
grep -n "hourly_rate" app/Models/Booking.php
grep -n "hourly_rate" app/Http/Controllers/UserController.php
grep -n "hourly_rate" database/seeders/DatabaseSeeder.php
```

### 3. Test Functionality
1. Buka `https://www.pusdatinbgn.web.id/user/bookings`
2. Login sebagai user
3. Coba booking ruangan baru
4. Upload PDF dan submit
5. Cek apakah error sudah teratasi

## Expected Results

### Sebelum Fix:
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'hourly_rate' in 'SELECT'
```

### Sesudah Fix:
- ✅ Booking berhasil dibuat
- ✅ Tidak ada error database
- ✅ PDF upload berfungsi
- ✅ Total cost = 0.00 (karena pricing dihapus)

## Test Data
```json
{
  "user_id": 2,
  "meeting_room_id": 4,
  "title": "Test Meeting",
  "description": "Test description",
  "start_time": "2025-10-11 00:05:00",
  "end_time": "2025-10-11 02:07:00",
  "attendees_count": 12,
  "attendees": ["suhaelr@gmail.com"],
  "special_requirements": "Test requirements",
  "unit_kerja": "Test unit",
  "dokumen_perizinan": "dokumen_perizinan/1760115977_visitor.pdf",
  "total_cost": 0.00
}
```

## Troubleshooting

### Jika Masih Ada Error:
1. Pastikan perubahan sudah ter-deploy
2. Cek apakah migrasi `remove_hourly_rate_from_meeting_rooms_table` sudah dijalankan
3. Cek log untuk error detail

### Jika Error Berbeda:
1. Catat error message yang muncul
2. Cek log untuk detail error
3. Update sesuai kebutuhan

## Status
✅ **Kode sudah diperbaiki di GitHub**
✅ **Test script berhasil**
🚨 **Perlu deploy ke production**

## Next Steps
1. **Deploy ke production sekarang!**
2. **Test booking functionality**
3. **Verifikasi tidak ada error**
4. **Report hasil**

## Catatan
- Pricing feature sudah dihapus dari sistem
- Semua booking akan memiliki `total_cost = 0.00`
- Fitur booking tetap berfungsi normal tanpa pricing
