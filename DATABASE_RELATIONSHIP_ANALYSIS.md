# Analisis Database Relationship - Sistem Pemesanan Ruang Meeting

## 1. Overview Database Schema

Sistem ini menggunakan **MySQL/MariaDB** dengan **5 tabel utama** yang memiliki hubungan foreign key dan constraints yang kompleks.

### 1.1. Tabel Utama

1. **users** - User accounts (admin/user)
2. **meeting_rooms** - Available meeting rooms
3. **bookings** - Booking records
4. **meeting_invitations** - PIC invitations (pivot table untuk many-to-many)
5. **user_notifications** - User notifications

---

## 2. Detail Struktur Tabel

### 2.1. Tabel: users

**Primary Key:** `id` (bigint, auto_increment)

**Columns:**
- `id` - Primary key
- `name` - Nama user (string)
- `username` - Username (string, unique)
- `email` - Email (string, unique)
- `email_verified_at` - Email verification timestamp (nullable)
- `password` - Password (hashed)
- `full_name` - Nama lengkap (string)
- `phone` - Nomor telepon (nullable)
- `department` - Department (nullable)
- `unit_kerja` - Unit kerja (nullable)
- `role` - Role: 'admin' atau 'user' (enum)
- `avatar` - Avatar path (nullable)
- `last_login_at` - Last login timestamp (nullable)
- `email_verification_token` - Email verification token (nullable)
- `google_id` - Google OAuth ID (nullable, unique)
- `remember_token` - Remember token (nullable)
- `created_at` - Created timestamp
- `updated_at` - Updated timestamp

**Constraints:**
- `email` UNIQUE
- `username` UNIQUE
- `google_id` UNIQUE (nullable)
- `role` ENUM('admin', 'user')

**Indexes:**
- PRIMARY KEY (`id`)
- UNIQUE (`email`)
- UNIQUE (`username`)
- UNIQUE (`google_id`)

**Foreign Keys:**
- None (parent table)

---

### 2.2. Tabel: meeting_rooms

**Primary Key:** `id` (bigint, auto_increment)

**Columns:**
- `id` - Primary key
- `name` - Nama ruang (string)
- `description` - Deskripsi ruang (text, nullable)
- `capacity` - Kapasitas (integer)
- `amenities` - Fasilitas (JSON, nullable)
- `location` - Lokasi (string)
- `images` - Gambar ruang (JSON, nullable)
- `is_active` - Status aktif (boolean, default true)
- `created_at` - Created timestamp
- `updated_at` - Updated timestamp

**Constraints:**
- `capacity` >= 1
- `is_active` BOOLEAN (default: true)

**Indexes:**
- PRIMARY KEY (`id`)

**Foreign Keys:**
- None (parent table)

---

### 2.3. Tabel: bookings

**Primary Key:** `id` (bigint, auto_increment)

**Columns:**
- `id` - Primary key
- `user_id` - **Foreign Key** → `users.id` (NOT NULL)
- `meeting_room_id` - **Foreign Key** → `meeting_rooms.id` (NOT NULL)
- `title` - Judul booking (string)
- `description` - Deskripsi (text, nullable)
- `description_visibility` - Visibility: 'all_users' atau 'invited_pics_only' (enum, nullable)
- `start_time` - Waktu mulai (datetime)
- `end_time` - Waktu selesai (datetime)
- `status` - Status: 'pending', 'confirmed', 'cancelled', 'completed' (enum, default 'pending')
- `attendees_count` - Jumlah peserta (integer, default 1)
- `attendees` - Daftar peserta (JSON, nullable)
- `attachments` - Lampiran (JSON, nullable)
- `special_requirements` - Kebutuhan khusus (text, nullable)
- `unit_kerja` - Unit kerja (string, nullable)
- `dokumen_perizinan` - Dokumen perizinan path (string, nullable)
- `total_cost` - Total biaya (decimal(8,2), default 0.00)
- `cancelled_at` - Waktu pembatalan (timestamp, nullable)
- `cancellation_reason` - Alasan pembatalan (text, nullable)
- `preempt_status` - Status preempt: 'none', 'pending', 'closed' (enum, default 'none')
- `preempt_requested_by` - **Foreign Key** → `users.id` (nullable, for preempt requester)
- `preempt_deadline_at` - Deadline preempt (datetime, nullable)
- `preempt_reason` - Alasan preempt (text, nullable)
- `needs_reschedule` - Perlu reschedule (boolean, nullable)
- `reschedule_deadline_at` - Deadline reschedule (datetime, nullable)
- `created_at` - Created timestamp
- `updated_at` - Updated timestamp

**Constraints:**
- `user_id` NOT NULL
- `meeting_room_id` NOT NULL
- `start_time` < `end_time` (application-level constraint)
- `status` ENUM('pending', 'confirmed', 'cancelled', 'completed')
- `preempt_status` ENUM('none', 'pending', 'closed')
- `description_visibility` ENUM('all_users', 'invited_pics_only')

**Indexes:**
- PRIMARY KEY (`id`)
- INDEX (`user_id`, `start_time`)
- INDEX (`meeting_room_id`, `start_time`, `end_time`)
- INDEX (`status`)
- INDEX (`preempt_status`)
- INDEX (`preempt_deadline_at`)

**Foreign Keys:**
- `bookings.user_id` → `users.id` **ON DELETE CASCADE**
- `bookings.meeting_room_id` → `meeting_rooms.id` **ON DELETE CASCADE**
- `bookings.preempt_requested_by` → `users.id` **ON DELETE SET NULL**

**Cascade Rules:**
- Jika user dihapus → semua bookings user tersebut dihapus (CASCADE)
- Jika meeting room dihapus → semua bookings untuk room tersebut dihapus (CASCADE)
- Jika preempt requester dihapus → `preempt_requested_by` diset NULL (SET NULL)

---

### 2.4. Tabel: meeting_invitations

**Primary Key:** `id` (bigint, auto_increment)

**Deskripsi:** Tabel pivot untuk many-to-many relationship antara `bookings` dan `users` (PIC invitations)

**Columns:**
- `id` - Primary key
- `booking_id` - **Foreign Key** → `bookings.id` (NOT NULL)
- `pic_id` - **Foreign Key** → `users.id` (NOT NULL, PIC yang diundang)
- `invited_by_pic_id` - **Foreign Key** → `users.id` (nullable, user yang mengundang)
- `status` - Status invitation: 'invited', 'accepted', 'declined' (enum, default 'invited')
- `invited_at` - Waktu diundang (datetime, default now())
- `responded_at` - Waktu merespons (datetime, nullable)

**Constraints:**
- `booking_id` NOT NULL
- `pic_id` NOT NULL
- `invited_by_pic_id` NULLABLE
- `status` ENUM('invited', 'accepted', 'declined')
- **NO timestamps** (`created_at`, `updated_at` tidak ada - manual tracking via `invited_at`, `responded_at`)

**Indexes:**
- PRIMARY KEY (`id`)
- INDEX (`booking_id`)
- INDEX (`pic_id`)
- INDEX (`invited_by_pic_id`)
- UNIQUE INDEX (`booking_id`, `pic_id`) - Mencegah duplicate invitation

**Foreign Keys:**
- `meeting_invitations.booking_id` → `bookings.id` **ON DELETE CASCADE**
- `meeting_invitations.pic_id` → `users.id` **ON DELETE CASCADE**
- `meeting_invitations.invited_by_pic_id` → `users.id` **ON DELETE SET NULL**

**Cascade Rules:**
- Jika booking dihapus → semua invitations untuk booking tersebut dihapus (CASCADE)
- Jika PIC (user) dihapus → semua invitations untuk PIC tersebut dihapus (CASCADE)
- Jika inviter dihapus → `invited_by_pic_id` diset NULL (SET NULL)

**Business Rules:**
- Satu booking bisa diundang ke banyak PIC (multiple invitations)
- Satu PIC bisa menerima banyak invitations dari berbagai bookings
- Tidak boleh ada duplicate invitation (booking_id + pic_id harus unique)

---

### 2.5. Tabel: user_notifications

**Primary Key:** `id` (bigint, auto_increment)

**Columns:**
- `id` - Primary key
- `user_id` - **Foreign Key** → `users.id` (NOT NULL)
- `booking_id` - **Foreign Key** → `bookings.id` (nullable, optional)
- `type` - Tipe notification: 'info', 'warning', 'error', 'success', 'booking_confirmed', 'booking_cancelled', etc. (string)
- `title` - Judul notification (string)
- `message` - Pesan notification (text)
- `is_read` - Status dibaca (boolean, default false)
- `read_at` - Waktu dibaca (timestamp, nullable)
- `created_at` - Created timestamp
- `updated_at` - Updated timestamp

**Constraints:**
- `user_id` NOT NULL
- `booking_id` NULLABLE (optional - bisa notifikasi tanpa booking)
- `is_read` BOOLEAN (default: false)

**Indexes:**
- PRIMARY KEY (`id`)
- INDEX (`user_id`, `is_read`)
- INDEX (`type`, `created_at`)

**Foreign Keys:**
- `user_notifications.user_id` → `users.id` **ON DELETE CASCADE**
- `user_notifications.booking_id` → `bookings.id` **ON DELETE CASCADE**

**Cascade Rules:**
- Jika user dihapus → semua notifications user tersebut dihapus (CASCADE)
- Jika booking dihapus → semua notifications terkait booking tersebut dihapus (CASCADE)

---

## 3. Database Relationships (Foreign Keys)

### 3.1. User ↔ Booking (One-to-Many)

**Relationship Type:** One-to-Many (1:N)

**Foreign Key:**
- `bookings.user_id` → `users.id`

**Constraint:**
```sql
ALTER TABLE `bookings`
ADD CONSTRAINT `bookings_user_id_foreign`
FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
ON DELETE CASCADE
ON UPDATE CASCADE;
```

**Cascade Behavior:**
- **ON DELETE CASCADE:** Jika user dihapus, semua bookings milik user tersebut **otomatis dihapus**
- **ON UPDATE CASCADE:** Jika user.id diubah, bookings.user_id **otomatis terupdate**

**Business Logic:**
- Satu user dapat memiliki banyak bookings
- Satu booking hanya dimiliki oleh satu user
- User tidak bisa dihapus jika masih memiliki bookings aktif (application-level validation)

---

### 3.2. MeetingRoom ↔ Booking (One-to-Many)

**Relationship Type:** One-to-Many (1:N)

**Foreign Key:**
- `bookings.meeting_room_id` → `meeting_rooms.id`

**Constraint:**
```sql
ALTER TABLE `bookings`
ADD CONSTRAINT `bookings_meeting_room_id_foreign`
FOREIGN KEY (`meeting_room_id`) REFERENCES `meeting_rooms` (`id`)
ON DELETE CASCADE
ON UPDATE CASCADE;
```

**Cascade Behavior:**
- **ON DELETE CASCADE:** Jika meeting room dihapus, semua bookings untuk room tersebut **otomatis dihapus**
- **ON UPDATE CASCADE:** Jika meeting_rooms.id diubah, bookings.meeting_room_id **otomatis terupdate**

**Business Logic:**
- Satu meeting room dapat memiliki banyak bookings
- Satu booking hanya untuk satu meeting room
- Room tidak bisa dihapus jika masih ada bookings aktif (application-level validation)

---

### 3.3. Booking ↔ MeetingInvitation (One-to-Many)

**Relationship Type:** One-to-Many (1:N)

**Foreign Key:**
- `meeting_invitations.booking_id` → `bookings.id`

**Constraint:**
```sql
ALTER TABLE `meeting_invitations`
ADD CONSTRAINT `meeting_invitations_booking_id_foreign`
FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`)
ON DELETE CASCADE
ON UPDATE CASCADE;
```

**Cascade Behavior:**
- **ON DELETE CASCADE:** Jika booking dihapus, semua invitations untuk booking tersebut **otomatis dihapus**
- **ON UPDATE CASCADE:** Jika bookings.id diubah, meeting_invitations.booking_id **otomatis terupdate**

**Business Logic:**
- Satu booking dapat memiliki banyak invitations (untuk multiple PICs)
- Satu invitation hanya untuk satu booking
- Invitation dihapus otomatis jika booking dihapus

---

### 3.4. User ↔ MeetingInvitation (as PIC) (One-to-Many)

**Relationship Type:** One-to-Many (1:N)

**Foreign Key:**
- `meeting_invitations.pic_id` → `users.id`

**Constraint:**
```sql
ALTER TABLE `meeting_invitations`
ADD CONSTRAINT `meeting_invitations_pic_id_foreign`
FOREIGN KEY (`pic_id`) REFERENCES `users` (`id`)
ON DELETE CASCADE
ON UPDATE CASCADE;
```

**Cascade Behavior:**
- **ON DELETE CASCADE:** Jika PIC (user) dihapus, semua invitations untuk PIC tersebut **otomatis dihapus**
- **ON UPDATE CASCADE:** Jika users.id diubah, meeting_invitations.pic_id **otomatis terupdate**

**Business Logic:**
- Satu user (PIC) dapat menerima banyak invitations
- Satu invitation hanya untuk satu PIC
- Invitation dihapus otomatis jika PIC dihapus

---

### 3.5. User ↔ MeetingInvitation (as Inviter) (One-to-Many)

**Relationship Type:** One-to-Many (1:N)

**Foreign Key:**
- `meeting_invitations.invited_by_pic_id` → `users.id`

**Constraint:**
```sql
ALTER TABLE `meeting_invitations`
ADD CONSTRAINT `meeting_invitations_invited_by_pic_id_foreign`
FOREIGN KEY (`invited_by_pic_id`) REFERENCES `users` (`id`)
ON DELETE SET NULL
ON UPDATE CASCADE;
```

**Cascade Behavior:**
- **ON DELETE SET NULL:** Jika inviter (user) dihapus, `invited_by_pic_id` **diset NULL** (invitation tetap ada, tapi tidak tahu siapa yang mengundang)
- **ON UPDATE CASCADE:** Jika users.id diubah, meeting_invitations.invited_by_pic_id **otomatis terupdate**

**Business Logic:**
- Satu user dapat mengundang banyak PICs
- Satu invitation hanya punya satu inviter
- Jika inviter dihapus, invitation tetap ada tapi `invited_by_pic_id` = NULL

---

### 3.6. Booking ↔ User (Many-to-Many via MeetingInvitation)

**Relationship Type:** Many-to-Many (N:N)

**Pivot Table:** `meeting_invitations`

**Foreign Keys:**
- `meeting_invitations.booking_id` → `bookings.id`
- `meeting_invitations.pic_id` → `users.id`

**Constraint:**
```sql
-- Unique constraint untuk mencegah duplicate invitation
ALTER TABLE `meeting_invitations`
ADD UNIQUE INDEX `unique_booking_pic` (`booking_id`, `pic_id`);
```

**Business Logic:**
- Satu booking dapat diundang ke banyak PICs (multiple invitations)
- Satu PIC dapat menerima banyak invitations dari berbagai bookings
- **Tidak boleh ada duplicate invitation** (booking_id + pic_id harus unique)

**Pivot Columns:**
- `status` - Status invitation ('invited', 'accepted', 'declined')
- `invited_at` - Waktu diundang
- `responded_at` - Waktu merespons
- `invited_by_pic_id` - Siapa yang mengundang

---

### 3.7. User ↔ UserNotification (One-to-Many)

**Relationship Type:** One-to-Many (1:N)

**Foreign Key:**
- `user_notifications.user_id` → `users.id`

**Constraint:**
```sql
ALTER TABLE `user_notifications`
ADD CONSTRAINT `user_notifications_user_id_foreign`
FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
ON DELETE CASCADE
ON UPDATE CASCADE;
```

**Cascade Behavior:**
- **ON DELETE CASCADE:** Jika user dihapus, semua notifications user tersebut **otomatis dihapus**
- **ON UPDATE CASCADE:** Jika users.id diubah, user_notifications.user_id **otomatis terupdate**

**Business Logic:**
- Satu user dapat memiliki banyak notifications
- Satu notification hanya untuk satu user
- Notification dihapus otomatis jika user dihapus

---

### 3.8. Booking ↔ UserNotification (One-to-Many, Optional)

**Relationship Type:** One-to-Many (1:N, Optional)

**Foreign Key:**
- `user_notifications.booking_id` → `bookings.id` (nullable)

**Constraint:**
```sql
ALTER TABLE `user_notifications`
ADD CONSTRAINT `user_notifications_booking_id_foreign`
FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`)
ON DELETE CASCADE
ON UPDATE CASCADE;
```

**Cascade Behavior:**
- **ON DELETE CASCADE:** Jika booking dihapus, semua notifications terkait booking tersebut **otomatis dihapus**
- **ON UPDATE CASCADE:** Jika bookings.id diubah, user_notifications.booking_id **otomatis terupdate**

**Business Logic:**
- Satu booking dapat memiliki banyak notifications (untuk berbagai user)
- Satu notification bisa terkait dengan booking (nullable) atau tidak terkait booking
- Notification dihapus otomatis jika booking dihapus (jika linked)

---

### 3.9. User ↔ Booking (as Preempt Requester) (One-to-Many, Optional)

**Relationship Type:** One-to-Many (1:N, Optional)

**Foreign Key:**
- `bookings.preempt_requested_by` → `users.id` (nullable)

**Constraint:**
```sql
ALTER TABLE `bookings`
ADD CONSTRAINT `bookings_preempt_requested_by_foreign`
FOREIGN KEY (`preempt_requested_by`) REFERENCES `users` (`id`)
ON DELETE SET NULL
ON UPDATE CASCADE;
```

**Cascade Behavior:**
- **ON DELETE SET NULL:** Jika preempt requester dihapus, `preempt_requested_by` **diset NULL** (booking tetap ada, tapi tidak tahu siapa yang request preempt)
- **ON UPDATE CASCADE:** Jika users.id diubah, bookings.preempt_requested_by **otomatis terupdate**

**Business Logic:**
- Satu user dapat request preempt untuk banyak bookings
- Satu booking hanya punya satu preempt requester (nullable)
- Jika requester dihapus, booking tetap ada tapi `preempt_requested_by` = NULL

---

## 4. Entity Relationship Diagram (ERD) - Text Representation

```
┌─────────────────────────────────────────────────────────────────┐
│                    ENTITY RELATIONSHIP DIAGRAM                   │
└─────────────────────────────────────────────────────────────────┘

┌──────────────────────┐
│       users          │
│ ──────────────────── │
│ PK: id               │
│      username (UK)   │
│      email (UK)       │
│      password         │
│      role            │
│      ...             │
└──────────┬───────────┘
           │
           │ 1
           │
           │ hasMany (CASCADE)
           │
           ▼ *                    ┌──────────────────────┐
┌──────────────────────┐          │   meeting_rooms     │
│      bookings        │          │ ──────────────────── │
│ ──────────────────── │          │ PK: id               │
│ PK: id               │          │      name            │
│ FK: user_id ────────┼───┐      │      capacity        │
│ FK: meeting_room_id ─┼───┼──────┤      location        │
│      title           │   │  1   │      is_active       │
│      status          │   │      │      ...             │
│      start_time      │   │      └──────────────────────┘
│      end_time        │   │
│      preempt_...     │   │
│ FK: preempt_req_by ──┼───┼───┐
│      ...             │   │   │
└──────────┬───────────┘   │   │
           │ 1             │   │
           │               │   │
           │ hasMany       │   │
           │ (CASCADE)     │   │
           ▼ *             │   │
┌──────────────────────┐   │   │
│ meeting_invitations  │   │   │
│ ──────────────────── │   │   │
│ PK: id               │   │   │
│ FK: booking_id ──────┼───┘   │
│ FK: pic_id ───────────┼───────┼───┐
│ FK: invited_by_id ────┼───────┘   │
│      status          │             │
│      invited_at      │             │
│      responded_at    │             │
└──────────────────────┘             │
                                      │
                                      │
           ┌──────────────────────┐   │
           │   user_notifications │   │
           │ ──────────────────── │   │
           │ PK: id               │   │
           │ FK: user_id ─────────┼───┼───┐
           │ FK: booking_id ──────┼───┘   │
           │      type            │       │
           │      title           │       │
           │      message         │       │
           │      is_read         │       │
           └──────────────────────┘       │
                                          │
                                          │
                    ┌─────────────────────┘
                    │
                    │ Many-to-Many
                    │ (via meeting_invitations)
                    │
                    ▼
        Booking ↔ User (PIC)
```

---

## 5. Foreign Key Constraints Summary

### 5.1. Cascade Delete Rules

| Foreign Key | Parent Table | Child Table | ON DELETE | Description |
|-------------|--------------|-------------|-----------|-------------|
| `bookings.user_id` | `users` | `bookings` | CASCADE | Hapus user → hapus semua bookings |
| `bookings.meeting_room_id` | `meeting_rooms` | `bookings` | CASCADE | Hapus room → hapus semua bookings |
| `bookings.preempt_requested_by` | `users` | `bookings` | SET NULL | Hapus requester → set NULL (booking tetap) |
| `meeting_invitations.booking_id` | `bookings` | `meeting_invitations` | CASCADE | Hapus booking → hapus semua invitations |
| `meeting_invitations.pic_id` | `users` | `meeting_invitations` | CASCADE | Hapus PIC → hapus semua invitations |
| `meeting_invitations.invited_by_pic_id` | `users` | `meeting_invitations` | SET NULL | Hapus inviter → set NULL (invitation tetap) |
| `user_notifications.user_id` | `users` | `user_notifications` | CASCADE | Hapus user → hapus semua notifications |
| `user_notifications.booking_id` | `bookings` | `user_notifications` | CASCADE | Hapus booking → hapus semua notifications |

### 5.2. Cascade Update Rules

Semua foreign keys menggunakan **ON UPDATE CASCADE** untuk menjaga referential integrity jika primary key diubah.

---

## 6. Indexes untuk Optimasi Query

### 6.1. Primary Keys
- Semua tabel memiliki `id` sebagai PRIMARY KEY

### 6.2. Foreign Key Indexes
- Semua foreign keys otomatis memiliki index untuk performa join

### 6.3. Composite Indexes

**Table: bookings**
- `(meeting_room_id, start_time, end_time)` - Untuk query availability
- `(user_id, start_time)` - Untuk query user bookings
- `status` - Untuk filter bookings by status
- `preempt_status` - Untuk query preempt requests
- `preempt_deadline_at` - Untuk auto-expire preempt

**Table: user_notifications**
- `(user_id, is_read)` - Untuk query unread notifications
- `(type, created_at)` - Untuk filter notifications by type

**Table: meeting_invitations**
- `(booking_id, pic_id)` - UNIQUE INDEX untuk mencegah duplicate invitations

---

## 7. Referential Integrity Rules

### 7.1. Rules yang Diterapkan

1. **Cascade Delete:**
   - User deleted → Bookings deleted → Invitations deleted → Notifications deleted
   - Room deleted → Bookings deleted → Invitations deleted → Notifications deleted
   - Booking deleted → Invitations deleted → Notifications deleted

2. **Set Null Delete:**
   - Requester deleted → `preempt_requested_by` = NULL (booking tetap)
   - Inviter deleted → `invited_by_pic_id` = NULL (invitation tetap)

3. **Prevent Delete (Application-level):**
   - User tidak bisa dihapus jika masih ada bookings aktif
   - Room tidak bisa dihapus jika masih ada bookings aktif
   - Validation dilakukan di controller sebelum delete

### 7.2. Data Consistency

- Foreign keys menjamin tidak ada orphan records
- Cascade delete menjaga referential integrity
- Set null delete menjaga data historical (tidak menghapus, hanya nullify)

---

## 8. Query Optimization

### 8.1. Eager Loading Relationships

```php
// Efficient query dengan eager loading
Booking::with(['user', 'meetingRoom', 'invitations.pic'])->get();

// Menghindari N+1 query problem
User::with(['bookings', 'notifications'])->get();
```

### 8.2. Index Usage

- Composite indexes digunakan untuk query availability
- Foreign key indexes digunakan untuk join operations
- Status indexes digunakan untuk filtering

### 8.3. Query Patterns

**Availability Check:**
```php
Booking::where('meeting_room_id', $roomId)
    ->where('start_time', '>=', $startTime)
    ->where('end_time', '<=', $endTime)
    ->whereIn('status', ['pending', 'confirmed'])
    ->exists();
```
→ Menggunakan index `(meeting_room_id, start_time, end_time)`

**User Bookings:**
```php
Booking::where('user_id', $userId)
    ->where('start_time', '>=', now())
    ->orderBy('start_time')
    ->get();
```
→ Menggunakan index `(user_id, start_time)`

**Unread Notifications:**
```php
UserNotification::where('user_id', $userId)
    ->where('is_read', false)
    ->orderBy('created_at', 'desc')
    ->get();
```
→ Menggunakan index `(user_id, is_read)`

---

## 9. Kesimpulan

### 9.1. Relationship Summary

- **One-to-Many (1:N):** 7 relationships
  - User → Bookings
  - MeetingRoom → Bookings
  - Booking → MeetingInvitations
  - User (PIC) → MeetingInvitations
  - User (Inviter) → MeetingInvitations
  - User → UserNotifications
  - Booking → UserNotifications

- **Many-to-Many (N:N):** 1 relationship
  - Booking ↔ User (via `meeting_invitations` pivot table)

### 9.2. Foreign Key Constraints

- **8 Foreign Keys** dengan berbagai cascade rules:
  - 6 CASCADE DELETE
  - 2 SET NULL DELETE
  - Semua ON UPDATE CASCADE

### 9.3. Data Integrity

- Foreign keys menjaga referential integrity
- Cascade delete mencegah orphan records
- Set null delete menjaga historical data
- Unique constraints mencegah duplicate data
- Application-level validation mencegah invalid operations

### 9.4. Performance

- Composite indexes untuk query optimization
- Foreign key indexes untuk join operations
- Eager loading untuk menghindari N+1 queries
- Query patterns menggunakan indexes yang tepat

Sistem ini memiliki **database relationship yang solid** dengan **referential integrity yang kuat** dan **optimasi query yang baik**.

