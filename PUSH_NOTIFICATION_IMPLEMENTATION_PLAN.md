# Rencana Implementasi Push Notification untuk Booking Confirmed

## 📋 Strategi Implementasi

### 🎯 Tujuan
Mengirim **Web Push Notification** ke device (HP/Android/iOS/Windows) setiap kali booking dikonfirmasi, baik:
- ✅ **Konfirmasi Manual** oleh admin (`AdminController.updateBookingStatus()`)
- ✅ **Konfirmasi Otomatis** setelah preempt accepted (`UserController.respondPreempt()`)

### 📱 Fitur yang Akan Diterapkan
1. **Push Notification di Device**
   - Notifikasi muncul di HP/device meskipun browser ditutup
   - Berfungsi di Android, iOS, Windows, MacOS
   - Notifikasi berisi: Nama User, Ruang Meeting, Unit Kerja

2. **Deep Linking**
   - Klik notifikasi → Buka login (jika belum login)
   - Setelah login → Redirect ke dashboard dengan kalender untuk tanggal booking

3. **Trigger Points**
   - `AdminController.updateBookingStatus()` → saat admin konfirmasi manual
   - `UserController.respondPreempt()` → saat booking auto-confirm setelah preempt

---

## 🏗️ Arsitektur Implementasi

### 1. Komponen yang Dibutuhkan

```
┌─────────────────────────────────────┐
│  1. Database Schema                 │
│     - push_subscriptions table      │
│       * user_id, endpoint, keys     │
└─────────────────────────────────────┘
         │
         ▼
┌─────────────────────────────────────┐
│  2. Backend Components               │
│     - PushSubscription Model        │
│     - PushNotificationService       │
│     - NotificationController        │
│     - NotificationOpenController    │
└─────────────────────────────────────┘
         │
         ▼
┌─────────────────────────────────────┐
│  3. Frontend Components              │
│     - Service Worker (JS)           │
│     - Push Subscription Manager     │
│     - UI Toggle Subscribe/Unsub     │
└─────────────────────────────────────┘
         │
         ▼
┌─────────────────────────────────────┐
│  4. Integration Points               │
│     - AdminController (manual)      │
│     - UserController (auto)         │
└─────────────────────────────────────┘
```

---

## 📝 Langkah Implementasi

### **Phase 1: Setup & Infrastructure** (Foundation)
1. ✅ Install package `minishlink/web-push`
2. ✅ Generate VAPID keys (Public & Private)
3. ✅ Create database migration: `push_subscriptions`
4. ✅ Create `PushSubscription` model
5. ✅ Create config file: `config/push.php`

### **Phase 2: Backend Services** (Core Logic)
6. ✅ Create `PushNotificationService` class
7. ✅ Create `NotificationController` (subscribe/unsubscribe)
8. ✅ Create `NotificationOpenController` (deep linking)
9. ✅ Add routes untuk push API
10. ✅ Modify `AdminController.updateBookingStatus()` → trigger push saat confirmed
11. ✅ Modify `UserController.respondPreempt()` → trigger push saat auto-confirm

### **Phase 3: Frontend Components** (User Interface)
12. ✅ Create `service-worker.js` (receive & display notifications)
13. ✅ Create `push-notification.js` (subscription manager)
14. ✅ Create UI component untuk subscribe/unsubscribe toggle
15. ✅ Add push notification toggle ke dashboard/profile page

### **Phase 4: Integration & Testing** (Polish)
16. ✅ Test subscription flow
17. ✅ Test notification sending (manual & auto)
18. ✅ Test deep linking & authentication
19. ✅ Test pada multiple browsers/devices

---

## 🔧 Detail Implementasi

### **1. Database Schema**

```sql
CREATE TABLE `push_subscriptions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `endpoint` varchar(512) NOT NULL,
  `public_key` varchar(255) NOT NULL,
  `auth_token` varchar(255) NOT NULL,
  `user_agent` varchar(512) NULL,
  `device_info` varchar(255) NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `push_subscriptions_endpoint_unique` (`endpoint`),
  KEY `push_subscriptions_user_id_foreign` (`user_id`),
  CONSTRAINT `push_subscriptions_user_id_foreign` 
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### **2. Trigger Points untuk Push Notification**

#### **A. Manual Confirmation (Admin)**
**Lokasi**: `app/Http/Controllers/AdminController.php` → `updateBookingStatus()`

**Code yang perlu ditambahkan**:
```php
// Setelah booking status diubah menjadi 'confirmed'
if ($status === 'confirmed') {
    // ... existing notification code ...
    
    // Send push notification
    try {
        $pushService = new \App\Services\PushNotificationService();
        $pushService->sendBookingConfirmed($booking);
    } catch (\Throwable $e) {
        \Log::error('Failed to send push notification', [
            'booking_id' => $booking->id,
            'error' => $e->getMessage(),
        ]);
        // Don't fail the entire request if push fails
    }
}
```

#### **B. Auto Confirmation (Preempt)**
**Lokasi**: `app/Http/Controllers/UserController.php` → `respondPreempt()`

**Code yang perlu ditambahkan**:
```php
// Setelah booking baru dibuat dan dikonfirmasi otomatis
// Di dalam transaction setelah $new->save()

try {
    $pushService = new \App\Services\PushNotificationService();
    $pushService->sendBookingConfirmed($new);
} catch (\Throwable $e) {
    \Log::error('Failed to send push notification (auto-confirm)', [
        'booking_id' => $new->id,
        'error' => $e->getMessage(),
    ]);
}
```

### **3. Notification Payload Structure**

```json
{
  "title": "Booking Dikonfirmasi",
  "body": "Suhael Rizqullah - Ruang Meeting A (PUSDATIN BGN)",
  "icon": "https://pusdatinbgn.web.id/logo-bgn.png",
  "badge": "https://pusdatinbgn.web.id/logo-bgn.png",
  "tag": "booking-47",
  "data": {
    "url": "/notification/open/47?ref=push",
    "booking_id": 47,
    "booking_date": "2025-11-15",
    "type": "booking_confirmed"
  },
  "vibrate": [200, 100, 200],
  "timestamp": 1734278400
}
```

**Isi Notifikasi**:
- **Title**: "Booking Dikonfirmasi"
- **Body**: "{Nama User} - {Nama Ruang} ({Unit Kerja})"
- **Action**: Klik untuk membuka dashboard dengan kalender untuk tanggal booking

### **4. Deep Linking Flow**

```
User clicks notification
    ↓
Service Worker opens: /notification/open/{booking_id}?ref=push
    ↓
NotificationOpenController.handle()
    ↓
Check: User authenticated?
    ├─ NO:
    │   └─ Redirect to: /login?redirect=/user/dashboard&date={booking_date}
    │       ↓
    │   User login
    │       ↓
    │   After login: Redirect to dashboard with date parameter
    │
    └─ YES:
        └─ Redirect to: /user/dashboard?date={booking_date}
            ↓
        Dashboard loads calendar for booking date
```

---

## 🔐 Security Considerations

### **1. VAPID Keys**
- Generate Public & Private keys
- Store di `.env`:
  ```
  PUSH_VAPID_SUBJECT=mailto:admin@pusdatinbgn.web.id
  PUSH_VAPID_PUBLIC_KEY=your_public_key_here
  PUSH_VAPID_PRIVATE_KEY=your_private_key_here
  ```

### **2. Requirements**
- ✅ **HTTPS**: Wajib untuk Web Push API (production)
- ✅ **User Permission**: User harus grant notification permission
- ✅ **Service Worker**: Browser harus support Service Worker

---

## 📦 Dependencies

### **Composer Package**
```bash
composer require minishlink/web-push
```

### **Package.json** (tidak diperlukan)
- Tidak perlu package npm, menggunakan vanilla JavaScript

---

## 🌐 Browser Compatibility

| Browser | Desktop | Mobile | Notes |
|---------|---------|--------|-------|
| Chrome | ✅ | ✅ Android | Full support |
| Firefox | ✅ | ✅ Android | Full support |
| Edge | ✅ | ✅ Android | Full support |
| Safari | ✅ | ✅ iOS 16.4+ | Requires HTTPS |
| Opera | ✅ | ✅ Android | Full support |

---

## 📍 File yang Akan Dibuat/Dimodifikasi

### **Files Baru**:
1. `database/migrations/xxxx_create_push_subscriptions_table.php`
2. `app/Models/PushSubscription.php`
3. `app/Services/PushNotificationService.php`
4. `app/Http/Controllers/NotificationController.php`
5. `app/Http/Controllers/NotificationOpenController.php`
6. `public/service-worker.js`
7. `public/js/push-notification.js`
8. `resources/views/components/push-notification-toggle.blade.php`
9. `config/push.php`
10. `routes/web.php` (add routes)

### **Files yang Dimodifikasi**:
1. `app/Http/Controllers/AdminController.php` → tambah trigger push saat confirmed
2. `app/Http/Controllers/UserController.php` → tambah trigger push saat auto-confirm
3. `resources/views/user/dashboard.blade.php` → tambah push notification toggle
4. `resources/views/user/profile.blade.php` → tambah push notification toggle
5. `.env` → tambah VAPID keys

---

## ✅ Keuntungan Implementasi Ini

1. **Real-time Notification**: User langsung tahu saat booking dikonfirmasi
2. **Cross-platform**: Bekerja di Android, iOS, Windows, MacOS
3. **Offline Support**: Notifikasi muncul meskipun browser ditutup
4. **User-friendly**: Deep linking dengan authentication flow yang smooth
5. **Secure**: Menggunakan VAPID untuk authentication

---

## ⚠️ Challenges & Considerations

1. **Browser Support**: Safari iOS memerlukan versi 16.4+
2. **HTTPS Required**: Wajib HTTPS di production
3. **User Permission**: User harus grant permission (tidak bisa dipaksa)
4. **Service Worker**: Browser harus support Service Worker

---

## 🚀 Next Steps

**Apakah Anda ingin saya langsung implementasi lengkap sekarang?**

Jika ya, saya akan membuat:
1. ✅ Migration untuk push_subscriptions table
2. ✅ PushSubscription model
3. ✅ PushNotificationService
4. ✅ NotificationController (subscribe/unsubscribe)
5. ✅ NotificationOpenController (deep linking)
6. ✅ Service Worker
7. ✅ Frontend JavaScript (push-notification.js)
8. ✅ UI Component untuk subscribe/unsubscribe
9. ✅ Modify AdminController & UserController untuk trigger push
10. ✅ Routes untuk push API

**Atau Anda ingin saya jelaskan lebih detail terlebih dahulu?**


