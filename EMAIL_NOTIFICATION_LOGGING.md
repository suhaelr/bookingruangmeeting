# Dokumentasi Logging Email Notifikasi PIC

## Ringkasan
Sistem ini mencatat semua pengiriman email notifikasi ke PIC yang diundang saat booking dikonfirmasi. Semua log dicatat di file log Laravel.

## Lokasi File Log
File log berada di: `storage/logs/laravel.log`

## Cara Melihat File Log di Linux

### ⚠️ PENTING: Jangan eksekusi file log langsung!

**SALAH:**
```bash
storage/logs/laravel.log  # ❌ Ini akan error!
```

**BENAR - Gunakan command untuk membaca file:**
```bash
# Melihat seluruh isi log
cat storage/logs/laravel.log

# Melihat 50 baris terakhir (paling berguna)
tail -n 50 storage/logs/laravel.log

# Melihat log secara real-time (auto update)
tail -f storage/logs/laravel.log

# Mencari keyword tertentu
grep "PIC email notifications" storage/logs/laravel.log

# Mencari dengan konteks (5 baris sebelum dan sesudah)
grep -C 5 "Email notification sent to PIC" storage/logs/laravel.log

# Mencari untuk booking ID tertentu
grep "booking_id.*123" storage/logs/laravel.log

# Melihat log dengan pagination (tekan space untuk next page, q untuk quit)
less storage/logs/laravel.log
```

## Cara Melihat Bukti Email Terkirim

### 1. Melalui File Log
Gunakan command `grep` atau `tail` untuk mencari keyword:
- `"Email notification sent to PIC after booking confirmation"` - untuk email individual ke setiap PIC
- `"PIC email notifications summary after booking confirmation"` - untuk ringkasan semua email yang dikirim
- `"Notification email sent successfully"` - untuk konfirmasi email terkirim dari sistem

### 2. Informasi yang Dicatat dalam Log

#### Log Individual per PIC (setelah booking dikonfirmasi):
```json
{
  "booking_id": 123,
  "pic_id": 456,
  "pic_name": "Nama PIC",
  "pic_email": "pic@example.com",
  "invitation_id": 789
}
```

#### Log Ringkasan (summary):
```json
{
  "booking_id": 123,
  "total_invitations": 3,
  "emails_sent_count": 3,
  "emails_failed_count": 0,
  "emails_sent": [
    {
      "pic_id": 456,
      "pic_name": "Nama PIC 1",
      "pic_email": "pic1@example.com",
      "invitation_id": 789
    },
    {
      "pic_id": 457,
      "pic_name": "Nama PIC 2",
      "pic_email": "pic2@example.com",
      "invitation_id": 790
    }
  ],
  "emails_failed": []
}
```

### 3. Contoh Pencarian di Log File

#### Mencari semua email yang terkirim untuk booking tertentu:
```bash
grep "booking_id.*123" storage/logs/laravel.log | grep "Email notification sent to PIC"
```

#### Mencari ringkasan email untuk booking tertentu:
```bash
grep "PIC email notifications summary" storage/logs/laravel.log | grep "booking_id.*123"
```

#### Mencari semua email yang gagal terkirim:
```bash
grep "Failed to send email to PIC" storage/logs/laravel.log
```

#### Melihat log terbaru (50 baris terakhir):
```bash
tail -n 50 storage/logs/laravel.log
```

#### Melihat log real-time (auto update saat ada log baru):
```bash
tail -f storage/logs/laravel.log
# Tekan Ctrl+C untuk keluar
```

### 4. Alur Pengiriman Email

1. **Admin mengkonfirmasi booking** → `AdminController::updateBookingStatus()`
2. **Status booking diubah menjadi 'confirmed'** → `Booking::updateStatus()`
3. **Sistem mengambil semua PIC yang diundang** → `MeetingInvitation::where('booking_id', $booking->id)`
4. **Untuk setiap PIC:**
   - Sistem mengambil data user PIC
   - Membuat notifikasi di database
   - Mengirim email via `Mail::to($picEmail)->send()`
   - Mencatat log dengan detail lengkap (nama, email, PIC ID)

### 5. Bukti Email Terkirim

Setiap email yang berhasil dikirim akan memiliki log dengan format:
```
[YYYY-MM-DD HH:MM:SS] local.INFO: Email notification sent to PIC after booking confirmation
{
  "booking_id": 123,
  "pic_id": 456,
  "pic_name": "Nama PIC",
  "pic_email": "pic@example.com",
  "invitation_id": 789
}
```

Dan juga log dari `UserNotification::createNotification`:
```
[YYYY-MM-DD HH:MM:SS] local.INFO: Notification email sent successfully
{
  "user_id": 456,
  "user_name": "Nama PIC",
  "user_email": "pic@example.com",
  "notification_id": 999,
  "notification_title": "Undangan Meeting dari PIC",
  "notification_type": "info",
  "booking_id": 123,
  "sent_at": "2024-01-01 12:00:00"
}
```

### 6. Jika Email Gagal Terkirim

Jika email gagal terkirim, akan ada log error dengan detail:
```
[YYYY-MM-DD HH:MM:SS] local.ERROR: Error sending email notification to PIC
{
  "booking_id": 123,
  "pic_id": 456,
  "invitation_id": 789,
  "error": "Error message",
  "trace": "Stack trace..."
}
```

### 7. Catatan Penting

- Semua log dicatat di file `storage/logs/laravel.log`
- Log tidak menggunakan `console.log` sesuai aturan, semua dicatat di file
- Setiap email yang terkirim memiliki 2 log entries:
  1. Log dari `AdminController` (detail PIC)
  2. Log dari `UserNotification::createNotification` (detail email)
- Jika email gagal, akan ada log error dengan detail lengkap

## Troubleshooting

### Email tidak terkirim?
1. Cek log error di `storage/logs/laravel.log`
2. Pastikan konfigurasi email di `.env` sudah benar
3. Pastikan user PIC memiliki email yang valid
4. Cek apakah ada error di log dengan keyword "Failed to send"

### Tidak menemukan log?
1. Pastikan booking sudah dikonfirmasi (status = 'confirmed')
2. Pastikan ada PIC yang diundang saat membuat booking
3. Cek apakah log file sudah di-rotate (cek file dengan tanggal)

