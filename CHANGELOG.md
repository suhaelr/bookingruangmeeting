# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.1.3] - 2025-01-XX

### Added
- **Notifikasi 2 Arah Real-time untuk Preempt Request**
  - Notifikasi ke owner saat preempt request diterima
  - Notifikasi ke requester saat preempt request diterima
  - Notifikasi ke owner saat preempt request ditolak
  - Notifikasi ke requester saat preempt request ditolak
  - Notifikasi ke requester saat preempt request expired
  - Notifikasi konfirmasi ke requester saat request berhasil dikirim
  - Fungsi reject/decline untuk owner menolak preempt request
  - Tombol "Tolak" di UI untuk owner menolak preempt request

### Changed
- **UserController.php**
  - Menambahkan aksi `reject` di method `respondPreempt()`
  - Menambahkan notifikasi 2 arah untuk semua skenario preempt
  - Notifikasi ke owner dan requester saat preempt diterima
  - Notifikasi ke owner dan requester saat preempt ditolak

- **AutoExpirePreempt.php**
  - Menambahkan notifikasi ke requester saat preempt expired
  - Tidak hanya owner yang mendapat notifikasi saat expired

- **resources/views/user/bookings.blade.php**
  - Menambahkan tombol "Tolak" untuk owner menolak preempt
  - Mengubah warna tombol "Terima & Batalkan" menjadi hijau

### Fixed
- Sistem notifikasi sekarang 2 arah secara real-time untuk owner dan requester
- Semua skenario preempt (accept, reject, expired) sekarang mengirim notifikasi ke kedua belah pihak

### Technical Details
- Semua notifikasi menggunakan polling real-time setiap 10 detik
- Notifikasi tersimpan di database dan muncul otomatis di dashboard
- Logging ditambahkan untuk semua notifikasi preempt

---

## [2.1.2] - 2025-01-XX

### Added
- Real-time notifications system (polling every 10 seconds)
- Auto-refresh notifications without browser refresh
- Admin notification system
- User notification system

### Changed
- Improved notification responsiveness (max 10 seconds delay)
- Better error handling for notifications

---

## [2.1.1] - 2025-01-XX

### Added
- Preempt booking system (permintaan didahulukan)
- Auto-expire preempt command
- SLA 1 hour untuk preempt requests
- Auto-confirm booking untuk requester setelah preempt diterima

---

## [2.1.0] - 2025-01-XX

### Added
- Initial release dengan fitur lengkap
- User management
- Room management
- Booking system
- Notification system

