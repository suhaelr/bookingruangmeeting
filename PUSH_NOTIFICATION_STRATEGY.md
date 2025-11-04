# Strategi Implementasi Web Push Notification

## 📋 Daftar Isi

1. [Overview](#overview)
2. [Arsitektur & Flow](#arsitektur--flow)
3. [Teknologi yang Digunakan](#teknologi-yang-digunakan)
4. [Komponen yang Dibutuhkan](#komponen-yang-dibutuhkan)
5. [Alur Proses](#alur-proses)
6. [Database Schema](#database-schema)
7. [Backend Implementation](#backend-implementation)
8. [Frontend Implementation](#frontend-implementation)
9. [Service Worker](#service-worker)
10. [Deep Linking & Authentication](#deep-linking--authentication)
11. [Browser Compatibility](#browser-compatibility)
12. [Security Considerations](#security-considerations)
13. [Testing Strategy](#testing-strategy)

---

## 1. Overview

### Tujuan
Implementasi **Web Push Notification** yang akan mengirim notifikasi ke browser (Android, iOS, Windows, dll) setiap kali **booking terkonfirmasi**.

### Fitur
- ✅ Notifikasi muncul di device user (Android, iOS, Windows, MacOS)
- ✅ Berfungsi meskipun browser/tab ditutup
- ✅ Notifikasi berisi: siapa yang meeting, dimana meetingnya, unit kerja
- ✅ Ketika user klik notifikasi, otomatis buka halaman login
- ✅ Setelah login, redirect ke dashboard dengan kalender meeting

---

## 2. Arsitektur & Flow

### 2.1. Komponen Arsitektur

```
┌─────────────────┐
│   User Browser  │ (Chrome, Firefox, Edge, Safari)
│                 │
│  ┌───────────┐  │
│  │Service    │  │ <- Receives & displays notifications
│  │Worker     │  │ <- Handles notification clicks
│  └───────────┘  │
└─────────────────┘
         ▲
         │ Push Notification (Web Push Protocol)
         │
┌─────────────────┐
│   Push Service  │ (Browser vendor: Chrome Push Service, Mozilla Push Service, etc.)
│                 │
│  - Receives     │
│    notification │
│  - Delivers to  │
│    browser      │
└─────────────────┘
         ▲
         │ HTTP POST (encrypted)
         │
┌─────────────────┐
│  Laravel App    │
│                 │
│  - VAPID keys   │
│  - Subscription │
│    management   │
│  - Sends push   │
│    when booking │
│    confirmed    │
└─────────────────┘
```

### 2.2. Flow Diagram

```
1. User visits website
   └─> Browser prompts "Allow notifications?"
       └─> User allows
           └─> Subscribe to push notifications
               └─> Save subscription to database

2. Admin confirms booking
   └─> AdminController.updateBookingStatus()
       └─> Check: booking.status = 'confirmed'
           └─> SendPushNotification service
               └─> Get user's push subscription
                   └─> Create notification payload
                       └─> Send to browser push service
                           └─> Browser receives notification
                               └─> Service Worker displays notification

3. User clicks notification
   └─> Service Worker handles click event
       └─> Open URL: /notification/open/{booking_id}?token={temp_token}
           └─> Check authentication
               └─> If not authenticated:
                   └─> Redirect to login?redirect=/user/dashboard?date={booking_date}
                   └─> After login: Redirect to dashboard with calendar for booking date
               └─> If authenticated:
                   └─> Redirect to dashboard with calendar for booking date
```

---

## 3. Teknologi yang Digunakan

### 3.1. Web Push API
- **Standard**: W3C Push API
- **Library**: `minishlink/web-push` (PHP) atau Laravel package
- **Browser Support**: Chrome, Firefox, Edge, Safari (iOS 16.4+), Opera

### 3.2. Service Worker
- **File**: `public/service-worker.js`
- **Function**: Receive push notifications, display notifications, handle notification clicks

### 3.3. VAPID (Voluntary Application Server Identification)
- **Purpose**: Authenticate server to push service
- **Components**: Public key & Private key
- **Security**: Prevents unauthorized servers from sending notifications

---

## 4. Komponen yang Dibutuhkan

### 4.1. Database
1. **Tabel `push_subscriptions`**
   - Menyimpan push subscription endpoint untuk setiap user
   - Mendukung multiple devices per user

### 4.2. Backend Components
1. **PushSubscription Model** - Eloquent model untuk subscriptions
2. **PushNotificationService** - Service class untuk sending notifications
3. **NotificationController** - Controller untuk subscription management
4. **AdminController** modification - Trigger push saat booking confirmed
5. **VAPID Key Generation** - Generate & store VAPID keys

### 4.3. Frontend Components
1. **Service Worker** (`public/service-worker.js`)
2. **Push Subscription Manager** (JavaScript)
3. **Notification Permission UI** - Button untuk request permission
4. **Notification Click Handler** - Handle deep linking

---

## 5. Alur Proses

### 5.1. Subscription Flow (User Mendaftar Push)

```
1. User mengakses halaman dashboard/profile
2. Sistem cek: Browser support push? → Yes
3. Sistem cek: User sudah subscribe? → No
4. Tampilkan prompt: "Aktifkan notifikasi untuk menerima update booking"
5. User klik "Aktifkan"
6. Browser request permission: "pusdatinbgn.web.id ingin mengirim notifikasi"
7. User klik "Allow"
8. JavaScript: requestPushSubscription()
   └─> navigator.serviceWorker.ready
       └─> serviceWorkerRegistration.pushManager.subscribe()
           └─> Generate subscription (endpoint + keys)
               └─> POST /api/push/subscribe
                   └─> Save to database (push_subscriptions table)
9. Success: User subscribed
```

### 5.2. Notification Sending Flow (Booking Confirmed)

```
1. Admin mengkonfirmasi booking
   └─> AdminController.updateBookingStatus()
       └─> booking.update(['status' => 'confirmed'])

2. Create in-app notification
   └─> UserNotification::createNotification(...)

3. Trigger push notification
   └─> PushNotificationService::sendBookingConfirmed($booking)
       └─> Get booking owner's push subscriptions
           └─> For each subscription:
               └─> Create notification payload
                   └─> Send to push service (browser vendor)
                       └─> Push service delivers to browser
                           └─> Service Worker receives
                               └─> Display notification
```

### 5.3. Notification Click Flow

```
1. User melihat notification di device
   └─> Notification shows: "Booking Dikonfirmasi: Meeting ABC di Ruang XYZ"

2. User klik notification
   └─> Service Worker: notificationclick event
       └─> Open URL: /notification/open/{booking_id}?ref=push

3. NotificationOpenController.handle()
   └─> Check: User authenticated?
       └─> NO:
           └─> Generate temporary token (expires in 5 minutes)
           └─> Redirect to: /login?redirect=/user/dashboard&date={booking_date}&token={temp_token}
           └─> After login:
               └─> Check temp_token valid
               └─> Redirect to: /user/dashboard?date={booking_date}
       └─> YES:
           └─> Redirect to: /user/dashboard?date={booking_date}

4. Dashboard loads with calendar for booking date
   └─> Calendar highlights booking
```

---

## 6. Database Schema

### 6.1. Tabel `push_subscriptions`

```sql
CREATE TABLE `push_subscriptions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `endpoint` varchar(512) NOT NULL,
  `public_key` varchar(255) NOT NULL,
  `auth_token` varchar(255) NOT NULL,
  `user_agent` varchar(512) NULL,
  `device_info` varchar(255) NULL COMMENT 'Android, iOS, Windows, etc.',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `push_subscriptions_endpoint_unique` (`endpoint`),
  KEY `push_subscriptions_user_id_foreign` (`user_id`),
  KEY `push_subscriptions_user_id_is_active_index` (`user_id`, `is_active`),
  CONSTRAINT `push_subscriptions_user_id_foreign` 
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Penjelasan Kolom:**
- `endpoint`: URL push service dari browser vendor
- `public_key`: Public key dari browser (P256DH)
- `auth_token`: Authentication secret dari browser
- `user_agent`: Browser & OS info untuk debugging
- `device_info`: Device type (Android, iOS, Windows, etc.)
- `is_active`: Status subscription (false jika unsubscribe)

### 6.2. Tabel `notification_tokens` (Optional - untuk deep linking)

```sql
CREATE TABLE `notification_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NULL,
  `token` varchar(64) NOT NULL,
  `booking_id` bigint(20) unsigned NULL,
  `redirect_url` varchar(512) NULL,
  `expires_at` timestamp NOT NULL,
  `used_at` timestamp NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `notification_tokens_token_unique` (`token`),
  KEY `notification_tokens_user_id_foreign` (`user_id`),
  KEY `notification_tokens_expires_at_index` (`expires_at`),
  CONSTRAINT `notification_tokens_user_id_foreign` 
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 7. Backend Implementation

### 7.1. Migration: Create Push Subscriptions Table

```php
// database/migrations/xxxx_create_push_subscriptions_table.php
Schema::create('push_subscriptions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('endpoint', 512)->unique();
    $table->string('public_key', 255);
    $table->string('auth_token', 255);
    $table->string('user_agent', 512)->nullable();
    $table->string('device_info', 255)->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    
    $table->index(['user_id', 'is_active']);
});
```

### 7.2. PushSubscription Model

```php
// app/Models/PushSubscription.php
class PushSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'endpoint',
        'public_key',
        'auth_token',
        'user_agent',
        'device_info',
        'is_active',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

### 7.3. PushNotificationService

```php
// app/Services/PushNotificationService.php
namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use App\Models\PushSubscription;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    private $vapidPublicKey;
    private $vapidPrivateKey;
    
    public function __construct()
    {
        $this->vapidPublicKey = config('push.vapid.public_key');
        $this->vapidPrivateKey = config('push.vapid.private_key');
    }
    
    /**
     * Send booking confirmed notification
     */
    public function sendBookingConfirmed(Booking $booking): bool
    {
        $user = $booking->user;
        
        // Get all active subscriptions for user
        $subscriptions = PushSubscription::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();
        
        if ($subscriptions->isEmpty()) {
            Log::info('No push subscriptions found for user', [
                'user_id' => $user->id,
                'booking_id' => $booking->id,
            ]);
            return false;
        }
        
        // Prepare notification payload
        $payload = $this->createBookingConfirmedPayload($booking);
        
        $successCount = 0;
        $failureCount = 0;
        
        foreach ($subscriptions as $subscription) {
            try {
                $this->sendPush($subscription, $payload);
                $successCount++;
            } catch (\Exception $e) {
                Log::error('Failed to send push notification', [
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);
                
                // Mark subscription as inactive if error indicates invalid subscription
                if ($this->isInvalidSubscriptionError($e)) {
                    $subscription->update(['is_active' => false]);
                }
                
                $failureCount++;
            }
        }
        
        Log::info('Push notifications sent', [
            'user_id' => $user->id,
            'booking_id' => $booking->id,
            'success' => $successCount,
            'failures' => $failureCount,
        ]);
        
        return $successCount > 0;
    }
    
    /**
     * Create notification payload for booking confirmed
     */
    private function createBookingConfirmedPayload(Booking $booking): array
    {
        $roomName = $booking->meetingRoom->name ?? 'Tidak diketahui';
        $unitKerja = $booking->unit_kerja ?? 'Tidak diketahui';
        $userName = $booking->user->full_name ?? $booking->user->name;
        
        return [
            'title' => 'Booking Dikonfirmasi',
            'body' => "{$userName} - {$roomName} ({$unitKerja})",
            'icon' => asset('logo-bgn.png'),
            'badge' => asset('logo-bgn.png'),
            'tag' => "booking-{$booking->id}",
            'data' => [
                'url' => route('notification.open', [
                    'booking_id' => $booking->id,
                    'ref' => 'push'
                ]),
                'booking_id' => $booking->id,
                'booking_date' => $booking->start_time->format('Y-m-d'),
                'type' => 'booking_confirmed',
            ],
            'requireInteraction' => false,
            'vibrate' => [200, 100, 200],
            'timestamp' => time(),
        ];
    }
    
    /**
     * Send push notification to subscription
     */
    private function sendPush(PushSubscription $subscription, array $payload): void
    {
        $webPush = new WebPush([
            'VAPID' => [
                'subject' => config('app.url'),
                'publicKey' => $this->vapidPublicKey,
                'privateKey' => $this->vapidPrivateKey,
            ],
        ]);
        
        $pushSubscription = Subscription::create([
            'endpoint' => $subscription->endpoint,
            'keys' => [
                'p256dh' => $subscription->public_key,
                'auth' => $subscription->auth_token,
            ],
        ]);
        
        $webPush->queueNotification($pushSubscription, json_encode($payload));
        
        $result = $webPush->flush();
        
        if ($result->isSuccess() === false) {
            throw new \Exception('Failed to send push: ' . $result->getReason());
        }
    }
    
    /**
     * Check if error indicates invalid subscription
     */
    private function isInvalidSubscriptionError(\Exception $e): bool
    {
        $invalidErrors = [
            'Invalid subscription',
            'Subscription expired',
            '410',
            '404',
        ];
        
        foreach ($invalidErrors as $error) {
            if (str_contains($e->getMessage(), $error)) {
                return true;
            }
        }
        
        return false;
    }
}
```

### 7.4. NotificationController (Subscription Management)

```php
// app/Http/Controllers/NotificationController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PushSubscription;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Subscribe user to push notifications
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url|max:512',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);
        
        $user = session('user_data');
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu.',
            ], 401);
        }
        
        try {
            // Check if subscription already exists
            $subscription = PushSubscription::where('endpoint', $request->endpoint)
                ->where('user_id', $user['id'])
                ->first();
            
            if ($subscription) {
                // Update existing subscription
                $subscription->update([
                    'public_key' => $request->keys['p256dh'],
                    'auth_token' => $request->keys['auth'],
                    'user_agent' => $request->userAgent(),
                    'device_info' => $this->detectDevice($request),
                    'is_active' => true,
                ]);
            } else {
                // Create new subscription
                $subscription = PushSubscription::create([
                    'user_id' => $user['id'],
                    'endpoint' => $request->endpoint,
                    'public_key' => $request->keys['p256dh'],
                    'auth_token' => $request->keys['auth'],
                    'user_agent' => $request->userAgent(),
                    'device_info' => $this->detectDevice($request),
                ]);
            }
            
            Log::info('Push subscription created/updated', [
                'user_id' => $user['id'],
                'subscription_id' => $subscription->id,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Berhasil berlangganan notifikasi push.',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to subscribe push notification', [
                'error' => $e->getMessage(),
                'user_id' => $user['id'] ?? null,
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal berlangganan notifikasi push.',
            ], 500);
        }
    }
    
    /**
     * Unsubscribe user from push notifications
     */
    public function unsubscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url|max:512',
        ]);
        
        $user = session('user_data');
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu.',
            ], 401);
        }
        
        try {
            PushSubscription::where('endpoint', $request->endpoint)
                ->where('user_id', $user['id'])
                ->update(['is_active' => false]);
            
            return response()->json([
                'success' => true,
                'message' => 'Berhasil berhenti berlangganan notifikasi push.',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to unsubscribe push notification', [
                'error' => $e->getMessage(),
                'user_id' => $user['id'] ?? null,
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal berhenti berlangganan notifikasi push.',
            ], 500);
        }
    }
    
    /**
     * Detect device type from user agent
     */
    private function detectDevice(Request $request): string
    {
        $userAgent = $request->userAgent();
        
        if (str_contains($userAgent, 'Android')) {
            return 'Android';
        } elseif (str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad')) {
            return 'iOS';
        } elseif (str_contains($userAgent, 'Windows')) {
            return 'Windows';
        } elseif (str_contains($userAgent, 'Mac')) {
            return 'macOS';
        } elseif (str_contains($userAgent, 'Linux')) {
            return 'Linux';
        }
        
        return 'Unknown';
    }
}
```

### 7.5. NotificationOpenController (Deep Linking)

```php
// app/Http/Controllers/NotificationOpenController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Session;

class NotificationOpenController extends Controller
{
    /**
     * Handle notification click - deep linking
     */
    public function open(Request $request, $bookingId)
    {
        try {
            $booking = Booking::findOrFail($bookingId);
            $bookingDate = $booking->start_time->format('Y-m-d');
            
            // Check if user is authenticated
            if (!Session::has('user_logged_in') || !Session::get('user_logged_in')) {
                // Generate temporary token for post-login redirect
                $tempToken = $this->generateTempToken($bookingId, $bookingDate);
                
                // Store in session (alternative: database table notification_tokens)
                Session::put("redirect_after_login", [
                    'url' => route('user.dashboard', ['date' => $bookingDate]),
                    'booking_id' => $bookingId,
                    'token' => $tempToken,
                    'expires_at' => now()->addMinutes(10),
                ]);
                
                // Redirect to login with redirect parameter
                return redirect()->route('login', [
                    'redirect' => route('user.dashboard', ['date' => $bookingDate]),
                    'ref' => 'push',
                ])->with('info', 'Silakan login untuk melihat detail booking.');
            }
            
            // User is authenticated - check if user owns this booking or is admin
            $user = Session::get('user_data');
            $userModel = \App\Models\User::find($user['id']);
            
            if (!$userModel) {
                return redirect()->route('login')
                    ->with('error', 'Session tidak valid. Silakan login kembali.');
            }
            
            // Check ownership or admin access
            if ($booking->user_id !== $userModel->id && $userModel->role !== 'admin') {
                return redirect()->route('user.dashboard')
                    ->with('error', 'Anda tidak memiliki akses ke booking ini.');
            }
            
            // Redirect to dashboard with calendar for booking date
            return redirect()->route('user.dashboard', [
                'date' => $bookingDate,
                'highlight' => $bookingId,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to open notification', [
                'booking_id' => $bookingId,
                'error' => $e->getMessage(),
            ]);
            
            return redirect()->route('login')
                ->with('error', 'Terjadi kesalahan saat membuka notifikasi.');
        }
    }
    
    /**
     * Generate temporary token for post-login redirect
     */
    private function generateTempToken($bookingId, $bookingDate): string
    {
        return hash('sha256', $bookingId . $bookingDate . now()->timestamp . uniqid());
    }
}
```

### 7.6. Modify AdminController (Trigger Push)

```php
// In AdminController.updateBookingStatus()
// After updating booking status to 'confirmed':

if ($status === 'confirmed') {
    // ... existing notification code ...
    
    // Send push notification
    try {
        $pushService = new \App\Services\PushNotificationService();
        $pushService->sendBookingConfirmed($booking);
    } catch (\Throwable $e) {
        Log::error('Failed to send push notification', [
            'booking_id' => $booking->id,
            'error' => $e->getMessage(),
        ]);
        // Don't fail the entire request if push fails
    }
}
```

---

## 8. Frontend Implementation

### 8.1. Push Subscription Manager (JavaScript)

```javascript
// public/js/push-notification.js
class PushNotificationManager {
    constructor() {
        this.registration = null;
        this.subscription = null;
    }
    
    /**
     * Initialize push notifications
     */
    async init() {
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
            console.log('Push notifications are not supported in this browser.');
            return false;
        }
        
        try {
            // Register service worker
            this.registration = await navigator.serviceWorker.register('/service-worker.js');
            console.log('Service Worker registered');
            
            // Check current subscription
            this.subscription = await this.registration.pushManager.getSubscription();
            
            if (this.subscription) {
                console.log('Already subscribed to push notifications');
                return true;
            }
            
            return false;
        } catch (error) {
            console.error('Failed to initialize push notifications:', error);
            return false;
        }
    }
    
    /**
     * Request push subscription permission and subscribe
     */
    async subscribe() {
        try {
            // Request permission
            const permission = await Notification.requestPermission();
            
            if (permission !== 'granted') {
                alert('Izin notifikasi ditolak. Silakan aktifkan di pengaturan browser.');
                return false;
            }
            
            // Get VAPID public key from server
            const response = await fetch('/api/push/vapid-public-key');
            const data = await response.json();
            const vapidPublicKey = data.publicKey;
            
            // Subscribe to push
            this.subscription = await this.registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: this.urlBase64ToUint8Array(vapidPublicKey),
            });
            
            // Send subscription to server
            const subscribeResponse = await fetch('/api/push/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({
                    endpoint: this.subscription.endpoint,
                    keys: {
                        p256dh: this.arrayBufferToBase64(this.subscription.getKey('p256dh')),
                        auth: this.arrayBufferToBase64(this.subscription.getKey('auth')),
                    },
                }),
            });
            
            const result = await subscribeResponse.json();
            
            if (result.success) {
                console.log('Successfully subscribed to push notifications');
                return true;
            } else {
                console.error('Failed to subscribe:', result.message);
                return false;
            }
        } catch (error) {
            console.error('Failed to subscribe to push notifications:', error);
            return false;
        }
    }
    
    /**
     * Unsubscribe from push notifications
     */
    async unsubscribe() {
        try {
            if (this.subscription) {
                await this.subscription.unsubscribe();
                
                // Notify server
                await fetch('/api/push/unsubscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        endpoint: this.subscription.endpoint,
                    }),
                });
                
                this.subscription = null;
                console.log('Successfully unsubscribed from push notifications');
                return true;
            }
            return false;
        } catch (error) {
            console.error('Failed to unsubscribe:', error);
            return false;
        }
    }
    
    /**
     * Convert VAPID key from base64 to Uint8Array
     */
    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/\-/g, '+')
            .replace(/_/g, '/');
        
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        
        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }
    
    /**
     * Convert ArrayBuffer to base64
     */
    arrayBufferToBase64(buffer) {
        const bytes = new Uint8Array(buffer);
        let binary = '';
        for (let i = 0; i < bytes.byteLength; i++) {
            binary += String.fromCharCode(bytes[i]);
        }
        return window.btoa(binary);
    }
}

// Initialize on page load
let pushManager = null;
document.addEventListener('DOMContentLoaded', async function() {
    pushManager = new PushNotificationManager();
    const isSubscribed = await pushManager.init();
    
    // Show/hide subscribe button based on subscription status
    const subscribeBtn = document.getElementById('enable-push-btn');
    const unsubscribeBtn = document.getElementById('disable-push-btn');
    
    if (subscribeBtn && unsubscribeBtn) {
        if (isSubscribed) {
            subscribeBtn.style.display = 'none';
            unsubscribeBtn.style.display = 'block';
        } else {
            subscribeBtn.style.display = 'block';
            unsubscribeBtn.style.display = 'none';
        }
    }
});
```

### 8.2. UI Component untuk Subscribe/Unsubscribe

```html
<!-- resources/views/components/push-notification-toggle.blade.php -->
<div class="glass-effect rounded-lg p-4 mb-4">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-bold text-white mb-1">Notifikasi Push</h3>
            <p class="text-white/60 text-sm">Terima notifikasi di device Anda saat booking dikonfirmasi</p>
        </div>
        <div>
            <button id="enable-push-btn" onclick="enablePushNotifications()" 
                    class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-300">
                <i class="fas fa-bell mr-2"></i>Aktifkan
            </button>
            <button id="disable-push-btn" onclick="disablePushNotifications()" 
                    class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors duration-300" 
                    style="display: none;">
                <i class="fas fa-bell-slash mr-2"></i>Nonaktifkan
            </button>
        </div>
    </div>
</div>

<script>
async function enablePushNotifications() {
    if (pushManager) {
        const success = await pushManager.subscribe();
        if (success) {
            alert('Notifikasi push berhasil diaktifkan!');
            location.reload();
        }
    }
}

async function disablePushNotifications() {
    if (pushManager) {
        const success = await pushManager.unsubscribe();
        if (success) {
            alert('Notifikasi push berhasil dinonaktifkan.');
            location.reload();
        }
    }
}
</script>
```

---

## 9. Service Worker

### 9.1. Service Worker File

```javascript
// public/service-worker.js
const CACHE_NAME = 'booking-system-v1';
const STATIC_ASSETS = [
    '/',
    '/login',
    '/logo-bgn.png',
];

// Install event - cache static assets
self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(function(cache) {
                return cache.addAll(STATIC_ASSETS);
            })
    );
    self.skipWaiting();
});

// Activate event - clean up old caches
self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames.map(function(cacheName) {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    return self.clients.claim();
});

// Push event - receive push notification
self.addEventListener('push', function(event) {
    let data = {};
    
    if (event.data) {
        try {
            data = event.data.json();
        } catch (e) {
            data = {
                title: 'Booking Dikonfirmasi',
                body: event.data.text(),
            };
        }
    }
    
    const title = data.title || 'Booking Dikonfirmasi';
    const options = {
        body: data.body || 'Ada update pada booking Anda',
        icon: data.icon || '/logo-bgn.png',
        badge: data.badge || '/logo-bgn.png',
        tag: data.tag || 'booking-notification',
        data: data.data || {},
        requireInteraction: data.requireInteraction || false,
        vibrate: data.vibrate || [200, 100, 200],
        timestamp: data.timestamp || Date.now(),
        actions: [
            {
                action: 'open',
                title: 'Buka Dashboard',
            },
            {
                action: 'close',
                title: 'Tutup',
            },
        ],
    };
    
    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// Notification click event - handle notification click
self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    
    const action = event.action;
    const notificationData = event.notification.data;
    
    if (action === 'close') {
        return;
    }
    
    // Default action or 'open' action
    let url = notificationData.url || '/user/dashboard';
    
    // If URL contains booking_id, use notification.open route
    if (notificationData.booking_id) {
        url = `/notification/open/${notificationData.booking_id}?ref=push`;
    }
    
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(function(clientList) {
                // Check if there's already a window/tab open with the target URL
                for (let i = 0; i < clientList.length; i++) {
                    const client = clientList[i];
                    if (client.url === url && 'focus' in client) {
                        return client.focus();
                    }
                }
                
                // If not, open new window/tab
                if (clients.openWindow) {
                    return clients.openWindow(url);
                }
            })
    );
});
```

---

## 10. Deep Linking & Authentication

### 10.1. Flow Deep Linking

```
User clicks notification
    ↓
Service Worker opens: /notification/open/{booking_id}?ref=push
    ↓
NotificationOpenController.handle()
    ↓
Check: Session authenticated?
    ├─ NO:
    │   ├─ Generate temp_token
    │   ├─ Store redirect info in session
    │   └─ Redirect to: /login?redirect=/user/dashboard&date={booking_date}&ref=push
    │       ↓
    │   User login
    │       ↓
    │   AuthController.login() - after successful login:
    │       ├─ Check: redirect parameter exists?
    │       ├─ YES: Redirect to redirect URL with date parameter
    │       └─ Dashboard loads calendar for booking date
    │
    └─ YES:
        ├─ Check: User owns booking OR is admin?
        ├─ YES: Redirect to dashboard with date
        └─ NO: Redirect to dashboard with error message
```

### 10.2. Modify AuthController Login

```php
// In AuthController.login() - after successful login:

$redirect = $request->input('redirect');
$date = $request->input('date');

if ($redirect) {
    // Add date parameter if exists
    if ($date) {
        $redirect .= (str_contains($redirect, '?') ? '&' : '?') . 'date=' . urlencode($date);
    }
    
    return redirect($redirect)
        ->with('success', 'Login berhasil!');
}

// Default redirect based on role
return redirect()->route('user.dashboard')
    ->with('success', 'Login berhasil!');
```

---

## 11. Browser Compatibility

### 11.1. Supported Browsers

| Browser | Desktop | Mobile | Notes |
|---------|---------|--------|-------|
| Chrome | ✅ | ✅ Android | Full support |
| Firefox | ✅ | ✅ Android | Full support |
| Edge | ✅ | ✅ Android | Full support |
| Safari | ✅ | ✅ iOS 16.4+ | Requires HTTPS |
| Opera | ✅ | ✅ Android | Full support |
| Samsung Internet | ❌ | ✅ Android | Limited support |

### 11.2. Requirements

- **HTTPS**: Wajib untuk Web Push API (kecuali localhost)
- **User Permission**: User harus grant notification permission
- **Service Worker**: Browser harus support Service Worker
- **Push API**: Browser harus support Push API

---

## 12. Security Considerations

### 12.1. VAPID Keys

```php
// config/push.php (new config file)
return [
    'vapid' => [
        'subject' => env('PUSH_VAPID_SUBJECT', 'mailto:admin@pusdatinbgn.web.id'),
        'public_key' => env('PUSH_VAPID_PUBLIC_KEY'),
        'private_key' => env('PUSH_VAPID_PRIVATE_KEY'),
    ],
];
```

**Generate VAPID Keys:**
```bash
# Using web-push CLI
npx web-push generate-vapid-keys

# Or using PHP
php artisan push:generate-vapid-keys
```

### 12.2. Security Best Practices

1. **HTTPS Only**: Push notifications hanya bekerja dengan HTTPS
2. **VAPID Keys**: Store private key di `.env`, jangan commit ke git
3. **Subscription Validation**: Validate subscription endpoint sebelum save
4. **Rate Limiting**: Limit push notification sending per user
5. **Error Handling**: Handle invalid subscriptions gracefully

---

## 13. Testing Strategy

### 13.1. Development Testing

1. **Local Testing**: Use localhost (bypasses HTTPS requirement)
2. **Browser DevTools**: Test service worker registration
3. **Push Testing**: Use Chrome DevTools → Application → Service Workers → Push

### 13.2. Production Testing

1. **Test on Multiple Browsers**: Chrome, Firefox, Edge
2. **Test on Multiple Devices**: Android, iOS (if supported), Windows
3. **Test Notification Click**: Verify deep linking works
4. **Test Authentication Flow**: Verify redirect after login

---

## 14. Implementation Steps

### Phase 1: Setup & Infrastructure
1. ✅ Install `minishlink/web-push` package
2. ✅ Generate VAPID keys
3. ✅ Create database migration (push_subscriptions)
4. ✅ Create PushSubscription model

### Phase 2: Backend Implementation
5. ✅ Create PushNotificationService
6. ✅ Create NotificationController (subscribe/unsubscribe)
7. ✅ Create NotificationOpenController (deep linking)
8. ✅ Add routes for push API
9. ✅ Modify AdminController to trigger push on booking confirmed

### Phase 3: Frontend Implementation
10. ✅ Create service-worker.js
11. ✅ Create push-notification.js (manager)
12. ✅ Create UI component for subscribe/unsubscribe
13. ✅ Add push notification toggle to dashboard/profile

### Phase 4: Integration & Testing
14. ✅ Test subscription flow
15. ✅ Test notification sending
16. ✅ Test deep linking & authentication
17. ✅ Test on multiple browsers/devices

---

## 15. Notification Payload Structure

### 15.1. Booking Confirmed Notification

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
  "requireInteraction": false,
  "vibrate": [200, 100, 200],
  "timestamp": 1734278400,
  "actions": [
    {
      "action": "open",
      "title": "Buka Dashboard"
    },
    {
      "action": "close",
      "title": "Tutup"
    }
  ]
}
```

**Isi Notifikasi:**
- **Title**: "Booking Dikonfirmasi"
- **Body**: "{Nama User} - {Nama Ruang} ({Unit Kerja})"
- **Icon**: Logo BGN
- **Action**: Klik untuk membuka dashboard

---

## 16. Routes yang Dibutuhkan

```php
// routes/web.php

// Push subscription routes
Route::prefix('api/push')->group(function () {
    Route::get('/vapid-public-key', [NotificationController::class, 'getVapidPublicKey']);
    Route::post('/subscribe', [NotificationController::class, 'subscribe'])->middleware('user.auth');
    Route::post('/unsubscribe', [NotificationController::class, 'unsubscribe'])->middleware('user.auth');
});

// Notification deep link route
Route::get('/notification/open/{booking_id}', [NotificationOpenController::class, 'open'])
    ->name('notification.open');
```

---

## 17. Configuration

### 17.1. .env Variables

```env
# Push Notification Configuration
PUSH_VAPID_SUBJECT=mailto:admin@pusdatinbgn.web.id
PUSH_VAPID_PUBLIC_KEY=your_public_key_here
PUSH_VAPID_PRIVATE_KEY=your_private_key_here
```

---

## 18. Dependencies

### 18.1. Composer Package

```json
{
    "require": {
        "minishlink/web-push": "^10.0"
    }
}
```

```bash
composer require minishlink/web-push
```

---

## 19. Summary

### Keuntungan Implementasi ini:
1. ✅ **Cross-platform**: Bekerja di Android, iOS, Windows, MacOS
2. ✅ **Offline**: Notifikasi muncul meskipun browser ditutup
3. ✅ **Real-time**: User langsung tahu saat booking dikonfirmasi
4. ✅ **Secure**: Menggunakan VAPID untuk authentication
5. ✅ **User-friendly**: Deep linking dengan authentication flow yang smooth

### Challenges:
1. ⚠️ **Browser Support**: Safari iOS memerlukan versi 16.4+
2. ⚠️ **HTTPS Required**: Wajib HTTPS di production
3. ⚠️ **User Permission**: User harus grant permission
4. ⚠️ **Service Worker**: Browser harus support Service Worker

---

**Apakah Anda ingin saya lanjutkan dengan implementasi lengkap?**

Jika ya, saya akan membuat:
1. Migration untuk push_subscriptions table
2. PushSubscription model
3. PushNotificationService
4. NotificationController
5. NotificationOpenController
6. Service Worker
7. Frontend JavaScript
8. UI Components
9. Modify AdminController



