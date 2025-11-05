# Skenario Order Bentrok dan Sistem Ajukan Pendahuluan Meeting

## 📋 Ringkasan Sistem

Sistem ini memiliki mekanisme untuk menangani **konflik jadwal (order bentrok)** dan memberikan opsi **ajukan pendahuluan (preempt request)** kepada user yang mengalami konflik dengan booking lain.

---

## 🔍 1. DETECTION ORDER BENTROK (Konflik Jadwal)

### 1.1. Real-time Conflict Detection

Sistem melakukan pengecekan konflik secara real-time saat user memilih ruang, tanggal, dan waktu di halaman **Pesan Ruang Meeting** (`/user/bookings/create`).

#### Bukti Kode: `app/Http/Controllers/UserController.php`

```php
public function checkAvailability(Request $request)
{
    $request->validate([
        'room_id' => 'required|exists:meeting_rooms,id',
        'start_time' => 'required|date',
        'end_time' => 'required|date|after:start_time',
        'exclude_booking_id' => 'nullable|integer|exists:bookings,id',
    ]);

    $user = session('user_data');
    $userModel = User::find($user['id']);
    if (!$userModel) {
        return response()->json([
            'available' => false,
            'message' => 'User session invalid. Please login again.'
        ], 401);
    }

    $room = MeetingRoom::findOrFail($request->room_id);
    $startTime = Carbon::parse($request->start_time);
    $endTime = Carbon::parse($request->end_time);
    $excludeBookingId = $request->input('exclude_booking_id');
    
    $conflictingBookings = $this->getConflictingBookings($room->id, $startTime, $endTime, $excludeBookingId);
    
    if ($conflictingBookings->count() > 0) {
        $conflictDetails = $this->formatConflictDetails($conflictingBookings, $room);

        // For preempt UI purposes, only return conflicts owned by other users
        $otherUserConflicts = $conflictingBookings->filter(function($booking) use ($userModel) {
            return $booking->user_id !== $userModel->id;
        });

        return response()->json([
            'available' => false,
            'message' => $conflictDetails,
            'conflicts' => $otherUserConflicts->map(function($booking) {
                return [
                    'id' => $booking->id,
                    'title' => $booking->title,
                    'user' => $booking->user->full_name,
                    'start_time' => $booking->start_time->format('d M Y H:i'),
                    'end_time' => $booking->end_time->format('H:i'),
                ];
            })
        ]);
    }

    return response()->json([
        'available' => true,
        'message' => 'Ruang tersedia untuk waktu yang dipilih!'
    ]);
}
```

#### Bukti Kode: Method `getConflictingBookings()`

```php
private function getConflictingBookings($roomId, $startTime, $endTime, $excludeBookingId = null)
{
    $query = Booking::where('meeting_room_id', $roomId)
        ->whereIn('status', ['pending', 'confirmed'])
        ->where('start_time', '<', $endTime)
        ->where('end_time', '>', $startTime);

    if ($excludeBookingId) {
        $query->where('id', '!=', $excludeBookingId);
    }

    return $query->with('user')->get();
}
```

### 1.2. Popup Warning Konflik di Frontend

Saat sistem mendeteksi konflik, **popup modal** langsung muncul menampilkan warning dan daftar booking yang bentrok.

#### Bukti Kode: `resources/views/user/create-booking.blade.php`

```javascript
// Real-time availability check and submit button control
function checkAvailability() {
    // ... validation code ...
    
    if (roomId && startWaktu && endWaktu) {
        fetch('{{ route("user.check-availability") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                room_id: roomId,
                start_time: startWaktu,
                end_time: endWaktu
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.available) {
                // Hide conflict modal if exists
                window.closeConflictModal();
                
                // Show success message
                // Enable submit button
            } else {
                // Show conflict modal/popup
                showConflictModal(data.message, data.conflicts || []);
                // Disable submit button
            }
        });
    }
}

// Conflict Modal Functions
function showConflictModal(message, conflicts) {
    let conflictContent = '';
    if (conflicts && conflicts.length > 0) {
        conflictContent = `
            <div class="space-y-3 mt-4">
                <p class="text-sm text-gray-700 mb-3">Pilih booking yang ingin Anda minta didahulukan:</p>
                ${conflicts.map(conflict => `
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-800">${conflict.title}</h4>
                                <p class="text-sm text-gray-600 mt-1">oleh ${conflict.user}</p>
                                <p class="text-xs text-gray-500 mt-1">${conflict.start_time} - ${conflict.end_time}</p>
                            </div>
                        </div>
                        <button type="button" onclick="requestPreemptFromModal(${conflict.id})" 
                                class="w-full mt-3 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-colors duration-300 flex items-center justify-center">
                            <i class="fas fa-handshake mr-2"></i>
                            Minta Didahulukan
                        </button>
                    </div>
                `).join('')}
            </div>
        `;
    }
    
    // Modal HTML with conflict warning and preempt buttons
    const modalHtml = `
        <div id="conflictModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-2 sm:p-4">
            <div class="bg-white rounded-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
                <div class="sticky top-0 bg-white border-b border-gray-200 z-10 p-4 sm:p-6 pb-4">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-800">Jadwal Bentrok</h3>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <p class="text-sm text-red-800 whitespace-pre-line">${message}</p>
                    </div>
                    ${conflictContent}
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
}
```

---

## 🚀 2. AJUKAN PENDAHULUAN (Preempt Request)

### 2.1. User Mengajukan Preempt Request

Saat user mengalami konflik, mereka dapat mengklik tombol **"Minta Didahulukan"** pada booking yang bentrok.

#### Bukti Kode: Frontend Request Function

```javascript
// Preempt request function
window.requestPreempt = function(bookingId, reason = null) {
    if (reason === null) {
        reason = prompt('Masukkan alasan mengapa Anda perlu didahulukan (opsional):');
        if (reason === null) return; // User cancelled
    }
    
    fetch(`/user/bookings/${bookingId}/preempt-request`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            reason: reason || null
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Permintaan didahulukan berhasil dikirim!');
            // Refresh availability check after preempt request
            setTimeout(() => {
                checkAvailability();
            }, 1000);
        } else {
            alert(data.message || 'Gagal mengirim permintaan didahulukan.');
        }
    });
};
```

### 2.2. Backend Processing Preempt Request

#### Bukti Kode: `app/Http/Controllers/UserController.php`

```php
public function requestPreempt(Request $request, $id)
{
    $request->validate([
        'reason' => 'nullable|string|max:500',
    ]);

    $user = session('user_data');
    $requesterId = $user['id'] ?? null;

    try {
        $target = Booking::with('user')->findOrFail($id);

        // Cannot preempt own booking
        if ($target->user_id === $requesterId) {
            return response()->json([
                'success' => false, 
                'message' => 'Tidak dapat meminta didahulukan pada booking milik sendiri.'
            ], 400);
        }

        // If already pending, do nothing (idempotent)
        if ($target->preempt_status === 'pending') {
            return response()->json([
                'success' => true, 
                'message' => 'Permintaan sudah dalam status menunggu tanggapan.'
            ]);
        }

        // Compute SLA - Fixed 1 hour
        $now = now();
        $deadline = $now->copy()->addHour(); // 1 hour SLA

        // Start preempt
        $target->startPreempt($requesterId, $deadline, $request->input('reason'));

        \Log::info('Preempt requested', [
            'booking_id' => $target->id,
            'requested_by' => $requesterId,
            'deadline_at' => $deadline->toDateTimeString(),
        ]);

        // Notify owner (simple DB notification)
        try {
            \App\Models\UserNotification::createNotification(
                $target->user_id,
                'info',
                'Permintaan Didahulukan',
                'Booking Anda diminta untuk didahulukan oleh pengguna lain. Mohon tanggapi segera.',
                $target->id
            );
        } catch (\Throwable $e) {
            \Log::error('Failed to create preempt notification to owner', ['error' => $e->getMessage()]);
        }

        return response()->json([
            'success' => true, 
            'message' => 'Permintaan dikirim. Menunggu tanggapan pemilik booking.'
        ]);
    } catch (\Exception $e) {
        \Log::error('Error in requestPreempt', [
            'error' => $e->getMessage(),
            'booking_id' => $id,
            'user_id' => $requesterId,
        ]);
        return response()->json(['success' => false, 'message' => 'Gagal mengirim permintaan.'], 500);
    }
}
```

### 2.3. Model Method: `startPreempt()`

#### Bukti Kode: `app/Models/Booking.php`

```php
public function startPreempt(int $requesterUserId, \DateTimeInterface $deadlineAt, ?string $reason = null): void
{
    $this->preempt_status = 'pending';
    $this->preempt_requested_by = $requesterUserId;
    $this->preempt_deadline_at = $deadlineAt;
    $this->preempt_reason = $reason;
    $this->save();
}
```

### 2.4. Database Schema untuk Preempt

#### Bukti Kode: Migration `2025_10_28_215854_add_preempt_columns_to_bookings_table.php`

```php
Schema::table('bookings', function (Blueprint $table) {
    // Preempt fields for request-first (minta didahulukan) flow
    $table->enum('preempt_status', ['none', 'pending', 'closed'])->default('none')->after('status');
    $table->foreignId('preempt_requested_by')->nullable()->after('preempt_status')->constrained('users')->nullOnDelete();
    $table->dateTime('preempt_deadline_at')->nullable()->after('preempt_requested_by');
    $table->text('preempt_reason')->nullable()->after('preempt_deadline_at');

    // Indexes to speed up queries and scheduler
    $table->index('preempt_status', 'idx_bookings_preempt_status');
    $table->index('preempt_deadline_at', 'idx_bookings_preempt_deadline_at');
});
```

---

## ⏰ 3. SLA (Service Level Agreement) - 1 Jam

### 3.1. SLA Fixed 1 Jam

Sistem menetapkan **SLA 1 jam** untuk owner merespons preempt request. Jika tidak direspon dalam 1 jam, **booking pertama tetap dikonfirmasi**.

#### Bukti Kode: SLA Calculation

```php
// Compute SLA - Fixed 1 hour
$now = now();
$deadline = $now->copy()->addHour(); // 1 hour SLA
```

### 3.2. Auto-Expire Preempt Request

Sistem memiliki **scheduled command** yang secara otomatis menutup preempt request yang telah melewati deadline.

#### Bukti Kode: `app/Console/Commands/AutoExpirePreempt.php`

```php
public function handle()
{
    $now = now();

    $expired = Booking::where('preempt_status', 'pending')
        ->whereNotNull('preempt_deadline_at')
        ->where('preempt_deadline_at', '<=', $now)
        ->get();

    $count = 0;
    foreach ($expired as $booking) {
        try {
            // Close preempt but keep booking confirmed (original booking stays)
            // SLA expired means owner didn't respond, so original booking remains valid
            \DB::transaction(function () use ($booking) {
                $booking->closePreempt();
            });
            $count++;

            // Notify owner that preempt request expired and booking stays confirmed
            try {
                \App\Models\UserNotification::createNotification(
                    $booking->user_id,
                    'info',
                    'Permintaan Didahulukan Expired',
                    'Permintaan didahulukan telah expired karena tidak ditanggapi dalam 1 jam. Booking Anda tetap dikonfirmasi.',
                    $booking->id
                );
            } catch (\Throwable $e) {
                \Log::error('Failed to notify owner after auto-expire', ['error' => $e->getMessage()]);
            }

            \Log::info('Auto-expired preempt booking', [
                'booking_id' => $booking->id,
                'deadline_at' => optional($booking->preempt_deadline_at)->toDateTimeString(),
            ]);
        } catch (\Throwable $e) {
            \Log::error('Failed to auto-expire preempt booking', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    $this->info("Processed {$count} expired preempt bookings and {$cancelledReschedules} reschedule timeouts.");
    return self::SUCCESS;
}
```

#### Bukti Kode: Model Method `closePreempt()`

```php
public function closePreempt(): void
{
    $this->preempt_status = 'closed';
    $this->preempt_requested_by = null;
    $this->preempt_deadline_at = null;
    $this->preempt_reason = null;
    $this->save();
}
```

---

## ✅ 4. RESPOND PREEMPT (Owner Merespons)

### 4.1. Owner Menerima & Membatalkan Booking

Ketika owner menerima preempt request, sistem akan:
1. **Membatalkan booking lama** (milik owner)
2. **Membuat booking baru otomatis** untuk peminta pada slot yang sama
3. **Mengkonfirmasi booking baru** secara otomatis

#### Bukti Kode: `app/Http/Controllers/UserController.php`

```php
public function respondPreempt(Request $request, $id)
{
    $request->validate([
        'action' => 'required|in:accept_cancel',
    ]);

    $user = session('user_data');
    $ownerId = $user['id'] ?? null;

    try {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id !== $ownerId) {
            return response()->json(['success' => false, 'message' => 'Anda bukan pemilik booking ini.'], 403);
        }

        if ($booking->preempt_status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Tidak ada permintaan yang perlu ditanggapi.'], 400);
        }

        $action = $request->input('action');

        if ($action === 'accept_cancel') {
            $requesterId = $booking->preempt_requested_by;

            try {
                \DB::beginTransaction();
                
                // 1) Batalkan booking lama & tutup preempt
                $booking->updateStatus('cancelled', 'Cancelled due to preempt request');
                $booking->closePreempt();

                // 2) Auto-create & confirm booking baru untuk peminta pada slot yang sama
                if ($requesterId) {
                    $requester = \App\Models\User::find($requesterId);
                    if ($requester) {
                        $new = new \App\Models\Booking();
                        $new->user_id = $requester->id;
                        $new->meeting_room_id = $booking->meeting_room_id;
                        $new->title = '[Didahulukan] ' . ($booking->title ?? 'Meeting');
                        $new->description = 'Dibuat otomatis setelah disetujui didahulukan.';
                        $new->description_visibility = $booking->description_visibility ?: 'invited_pics_only';
                        $new->start_time = $booking->start_time;
                        $new->end_time = $booking->end_time;
                        $new->status = 'confirmed';
                        $new->attendees_count = max(1, (int)($booking->attendees_count ?? 1));
                        $new->attendees = $booking->attendees ?? [];
                        $new->special_requirements = $booking->special_requirements;
                        $new->unit_kerja = $booking->unit_kerja
                            ?? $requester->unit_kerja
                            ?? $requester->department
                            ?? 'Tidak diketahui';
                        $new->total_cost = 0;
                        $new->save();

                        // Notifikasi ke peminta
                        \App\Models\UserNotification::createNotification(
                            $requester->id,
                            'success',
                            'Booking Anda Otomatis Dikonfirmasi',
                            'Permintaan didahulukan disetujui. Booking baru telah dibuat dan dikonfirmasi pada slot tersebut.',
                            $new->id
                        );

                        // Notifikasi ke admin
                        try {
                            $adminUser = \App\Models\User::where('role', 'admin')->orderBy('id')->first();
                            if ($adminUser) {
                                \App\Models\UserNotification::createNotification(
                                    $adminUser->id,
                                    'info',
                                    'Auto-Confirm Setelah Didahulukan',
                                    "Booking #{$booking->id} dibatalkan oleh pemilik; booking baru #{$new->id} untuk peminta telah dikonfirmasi.",
                                    $new->id
                                );
                            }
                        } catch (\Throwable $e) { 
                            \Log::error('Notify admin auto-confirm failed', ['e'=>$e->getMessage()]); 
                        }
                    }
                }
                \DB::commit();
            } catch (\Throwable $t) {
                \DB::rollBack();
                \Log::error('respondPreempt transaction failed', [
                    'booking_id' => $booking->id,
                    'requester_id' => $requesterId,
                    'error' => $t->getMessage(),
                ]);
                throw $t;
            }

            return response()->json([
                'success' => true, 
                'message' => 'Booking dibatalkan. Permintaan didahulukan disetujui dan booking peminta dikonfirmasi otomatis.'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Aksi tidak dikenal.'], 400);
    } catch (\Exception $e) {
        \Log::error('Error in respondPreempt', [
            'error' => $e->getMessage(),
            'booking_id' => $id,
            'user_id' => $ownerId,
        ]);
        return response()->json(['success' => false, 'message' => 'Gagal memproses tanggapan.'], 500);
    }
}
```

---

## 📊 5. FLOW DIAGRAM SISTEM

```
┌─────────────────────────────────────────────────────────────┐
│  USER A: Memesan Ruang Meeting                            │
│  - Pilih ruang, tanggal, waktu                            │
│  - Sistem cek availability                                │
│  - Booking berhasil dibuat (CONFIRMED)                    │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│  USER B: Memesan Ruang Meeting (SAME SLOT)                │
│  - Pilih ruang, tanggal, waktu yang sama                   │
│  - Sistem detect CONFLICT                                   │
│  - Popup warning muncul dengan daftar booking bentrok     │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│  USER B: Klik "Minta Didahulukan"                          │
│  - Sistem kirim preempt request ke USER A                  │
│  - Booking USER A: preempt_status = 'pending'              │
│  - SLA: deadline = now() + 1 hour                          │
│  - Notification ke USER A                                  │
└─────────────────────────────────────────────────────────────┘
                          ↓
        ┌───────────────────┴───────────────────┐
        │                                       │
        ↓                                       ↓
┌───────────────────────┐          ┌───────────────────────┐
│  USER A RESPOND       │          │  USER A TIDAK RESPOND  │
│  (dalam 1 jam)        │          │  (> 1 jam)            │
│  - Klik "Terima &     │          │  - Auto-expire command │
│    Batalkan"          │          │  - closePreempt()      │
│  - Booking A dibatalkan│          │  - Booking A tetap     │
│  - Booking B auto-    │          │    CONFIRMED           │
│    created & confirmed│          │  - Preempt closed      │
└───────────────────────┘          └───────────────────────┘
```

---

## 🔑 6. KEY FEATURES

### 6.1. Real-time Conflict Detection
- ✅ Pengecekan konflik dilakukan secara real-time saat user memilih waktu
- ✅ Popup modal muncul langsung tanpa harus submit form
- ✅ Daftar booking yang bentrok ditampilkan dengan detail

### 6.2. Preempt Request System
- ✅ User dapat mengajukan preempt request pada booking orang lain
- ✅ Tidak bisa preempt booking milik sendiri
- ✅ Idempotent: jika sudah pending, tidak akan membuat duplicate request

### 6.3. SLA (Service Level Agreement)
- ✅ **Fixed 1 hour SLA** untuk owner merespons
- ✅ Jika tidak direspon dalam 1 jam, booking pertama tetap dikonfirmasi
- ✅ Auto-expire command menutup preempt yang expired

### 6.4. Auto-Confirm untuk Requester
- ✅ Jika owner menerima preempt, sistem **otomatis membuat dan mengkonfirmasi** booking baru untuk requester
- ✅ Booking baru dibuat dengan prefix `[Didahulukan]` pada title
- ✅ Notifikasi dikirim ke requester dan admin

### 6.5. Notification System
- ✅ Owner mendapat notifikasi saat ada preempt request
- ✅ Requester mendapat notifikasi saat preempt diterima
- ✅ Owner mendapat notifikasi saat preempt expired
- ✅ Admin mendapat notifikasi saat terjadi auto-confirm

---

## 📝 7. ROUTES

### Bukti Kode: `routes/web.php`

```php
// Preempt routes
Route::post('/bookings/{id}/preempt-request', [UserController::class, 'requestPreempt'])
    ->name('user.bookings.preempt.request');
Route::post('/bookings/{id}/respond-preempt', [UserController::class, 'respondPreempt'])
    ->name('user.bookings.preempt.respond');
Route::post('/check-availability', [UserController::class, 'checkAvailability'])
    ->name('user.check-availability');
```

---

## 🎯 8. BUSINESS RULES

1. **User tidak bisa preempt booking miliknya sendiri**
2. **SLA fixed 1 jam** - tidak tergantung waktu start booking
3. **Jika SLA expired**: Booking pertama tetap dikonfirmasi (tidak dibatalkan)
4. **Jika owner menerima**: Booking lama dibatalkan, booking baru dibuat otomatis untuk requester
5. **Idempotent**: Preempt request yang sudah pending tidak akan dibuat duplicate
6. **Real-time detection**: Konflik terdeteksi langsung tanpa perlu submit form

---

## 🔧 9. SCHEDULED COMMAND

Untuk menjalankan auto-expire preempt, perlu menambahkan ke **Kernel.php**:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('app:auto-expire-preempt')
        ->everyMinute(); // Run every minute to check expired preempts
}
```

---

## ✅ 10. KESIMPULAN

Sistem ini memiliki mekanisme lengkap untuk menangani konflik jadwal dengan:
- ✅ **Real-time conflict detection** yang user-friendly
- ✅ **Preempt request system** yang memungkinkan user mengajukan pendahuluan
- ✅ **SLA 1 jam** yang fair untuk owner merespons
- ✅ **Auto-expire** jika owner tidak merespons
- ✅ **Auto-confirm** untuk requester jika owner menerima
- ✅ **Notification system** yang menginformasikan semua pihak

Semua fitur ini terintegrasi dengan baik dan menggunakan transaction untuk memastikan data consistency.



