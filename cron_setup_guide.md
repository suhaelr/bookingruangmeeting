# Panduan Setup Cron Job untuk Email Reminder

## ❌ **Yang SALAH (yang Anda lakukan):**
```bash
# JANGAN jalankan ini langsung di terminal!
*/15 * * * * cd public_html && php artisan bookings:send-reminders >> /dev/null 2>&1
```

## ✅ **Yang BENAR - Cara Setup Cron Job:**

### **1. Edit Crontab**
```bash
# Buka editor crontab
crontab -e
```

### **2. Tambahkan Command Cron**
Di dalam editor crontab, tambahkan baris berikut:
```bash
# Update booking status setiap 5 menit
*/5 * * * * cd /home/dsvbpgpt/public_html && php artisan bookings:update-status >> /dev/null 2>&1

# Kirim email reminder setiap 15 menit
*/15 * * * * cd /home/dsvbpgpt/public_html && php artisan bookings:send-reminders >> /dev/null 2>&1
```

### **3. Simpan dan Keluar**
- Tekan `Ctrl + X`
- Tekan `Y` untuk save
- Tekan `Enter` untuk confirm

### **4. Verifikasi Cron Job**
```bash
# Cek apakah cron job sudah terdaftar
crontab -l
```

## 🔧 **Alternatif Setup (jika crontab tidak tersedia):**

### **Option 1: Manual Testing**
```bash
# Test command secara manual
cd /home/dsvbpgpt/public_html
php artisan bookings:send-reminders
php artisan bookings:update-status
```

### **Option 2: Setup via cPanel (jika ada)**
1. Login ke cPanel
2. Cari "Cron Jobs" atau "Scheduled Tasks"
3. Tambahkan:
   - **Minute**: */15
   - **Hour**: *
   - **Day**: *
   - **Month**: *
   - **Weekday**: *
   - **Command**: `cd /home/dsvbpgpt/public_html && php artisan bookings:send-reminders >> /dev/null 2>&1`

### **Option 3: Setup via Web Cron**
Jika server tidak support cron, gunakan web cron service seperti:
- https://cron-job.org
- https://www.easycron.com

## 🧪 **Testing Commands:**

### **Test Email Reminder:**
```bash
cd /home/dsvbpgpt/public_html
php artisan bookings:send-reminders
```

### **Test Status Update:**
```bash
cd /home/dsvbpgpt/public_html
php artisan bookings:update-status
```

### **Check Logs:**
```bash
# Cek log Laravel
tail -f storage/logs/laravel.log
```

## ⚠️ **Troubleshooting:**

### **Jika Command Tidak Ditemukan:**
```bash
# Pastikan path PHP benar
which php
# Output: /usr/bin/php atau /usr/local/bin/php

# Gunakan full path di cron job
*/15 * * * * cd /home/dsvbpgpt/public_html && /usr/bin/php artisan bookings:send-reminders >> /dev/null 2>&1
```

### **Jika Permission Error:**
```bash
# Set permission untuk file
chmod +x /home/dsvbpgpt/public_html/artisan
```

### **Jika Path Salah:**
```bash
# Cek path yang benar
pwd
# Pastikan path di cron job sesuai dengan output pwd
```

## 📝 **Cron Job Format:**
```
* * * * * command
│ │ │ │ │
│ │ │ │ └─── Day of week (0-7, Sunday = 0 or 7)
│ │ │ └───── Month (1-12)
│ │ └─────── Day of month (1-31)
│ └───────── Hour (0-23)
└─────────── Minute (0-59)
```

## 🎯 **Contoh Cron Job Lengkap:**
```bash
# Setiap 5 menit - update status booking
*/5 * * * * cd /home/dsvbpgpt/public_html && /usr/bin/php artisan bookings:update-status >> /dev/null 2>&1

# Setiap 15 menit - kirim email reminder
*/15 * * * * cd /home/dsvbpgpt/public_html && /usr/bin/php artisan bookings:send-reminders >> /dev/null 2>&1

# Setiap hari jam 2 pagi - cleanup log lama
0 2 * * * cd /home/dsvbpgpt/public_html && /usr/bin/php artisan log:clear >> /dev/null 2>&1
```
