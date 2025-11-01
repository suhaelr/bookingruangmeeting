# Analisis Class Diagram - Sistem Pemesanan Ruang Meeting

## 1. Struktur Model/Class Utama

Project ini menggunakan **Laravel Eloquent ORM** dengan 5 model utama:

### 1.1. User Model
**File:** `app/Models/User.php`  
**Extends:** `Illuminate\Foundation\Auth\User` (Authenticatable)

#### Attributes:
- `id` (primary key)
- `name`, `username`, `email`, `password`
- `full_name`, `phone`, `unit_kerja`, `department`
- `role` (enum: 'admin', 'user')
- `avatar`, `last_login_at`
- `email_verified_at`, `email_verification_token`
- `google_id` (nullable)
- `created_at`, `updated_at` (timestamps)

#### Relationships:
- `hasMany(Booking)` → `bookings()`
- `hasMany(UserNotification)` → `notifications()`
- `belongsToMany(Booking)` → melalui `MeetingInvitation` (as invited PIC)

#### Methods:
- `isAdmin()`: boolean
- `isUser()`: boolean
- `getActiveBookings()`: Collection
- `getBookingStats()`: array
- `unreadNotifications()`: QueryBuilder

---

### 1.2. Booking Model
**File:** `app/Models/Booking.php`  
**Extends:** `Illuminate\Database\Eloquent\Model`

#### Attributes:
- `id` (primary key)
- `user_id` (foreign key → User)
- `meeting_room_id` (foreign key → MeetingRoom)
- `title`, `description`, `description_visibility`
- `start_time`, `end_time` (datetime)
- `status` (enum: 'pending', 'confirmed', 'cancelled', 'completed')
- `attendees_count`, `attendees` (array)
- `attachments` (array)
- `special_requirements`, `unit_kerja`
- `dokumen_perizinan`, `total_cost` (decimal)
- `cancelled_at`, `cancellation_reason`
- `preempt_status`, `preempt_requested_by`, `preempt_deadline_at`, `preempt_reason`
- `needs_reschedule`, `reschedule_deadline_at`
- `created_at`, `updated_at` (timestamps)

#### Relationships:
- `belongsTo(User)` → `user()`
- `belongsTo(MeetingRoom)` → `meetingRoom()`
- `hasMany(MeetingInvitation)` → `invitations()`
- `belongsToMany(User)` → `invitedPics()` (through meeting_invitations pivot table)
- `hasMany(UserNotification)` → implicit

#### Methods:
- `getDurationAttribute()`: int (hours)
- `getFormattedStartTimeAttribute()`: string
- `getFormattedEndTimeAttribute()`: string
- `isUpcoming()`: boolean
- `isPast()`: boolean
- `isOngoing()`: boolean
- `canBeCancelled()`: boolean
- `getStatusColorAttribute()`: string
- `getStatusTextAttribute()`: string
- `calculateTotalCost()`: float
- `isPreemptPending()`: boolean
- `startPreempt(int $requesterUserId, DateTimeInterface $deadlineAt, ?string $reason)`: void
- `closePreempt()`: void
- `updateStatus(string $status, ?string $reason)`: boolean

---

### 1.3. MeetingRoom Model
**File:** `app/Models/MeetingRoom.php`  
**Extends:** `Illuminate\Database\Eloquent\Model`

#### Attributes:
- `id` (primary key)
- `name`, `description`
- `capacity` (integer)
- `amenities` (array)
- `location`, `images` (array)
- `is_active` (boolean)
- `created_at`, `updated_at` (timestamps)

#### Relationships:
- `hasMany(Booking)` → `bookings()`

#### Methods:
- `getActiveBookings()`: Collection
- `isAvailable(DateTime $startTime, DateTime $endTime, ?int $excludeBookingId)`: boolean
- `getBookingStats()`: array
- `getAmenitiesList()`: array
- `getImagesList()`: array

---

### 1.4. MeetingInvitation Model
**File:** `app/Models/MeetingInvitation.php`  
**Extends:** `Illuminate\Database\Eloquent\Model`

#### Attributes:
- `id` (primary key)
- `booking_id` (foreign key → Booking)
- `pic_id` (foreign key → User)
- `invited_by_pic_id` (foreign key → User, nullable)
- `status` (enum: 'invited', 'accepted', 'declined')
- `invited_at` (datetime)
- `responded_at` (datetime, nullable)
- **NO timestamps** (`public $timestamps = false`)

#### Relationships:
- `belongsTo(Booking)` → `booking()`
- `belongsTo(User, 'pic_id')` → `pic()` (invited PIC)
- `belongsTo(User, 'invited_by_pic_id')` → `invitedByPic()` (who invited)

#### Methods:
- `accept()`: void
- `decline()`: void

---

### 1.5. UserNotification Model
**File:** `app/Models/UserNotification.php`  
**Extends:** `Illuminate\Database\Eloquent\Model`

#### Attributes:
- `id` (primary key)
- `user_id` (foreign key → User)
- `booking_id` (foreign key → Booking, nullable)
- `type` (string: 'info', 'warning', 'error', 'success', etc.)
- `title`, `message`
- `is_read` (boolean)
- `read_at` (datetime, nullable)
- `created_at`, `updated_at` (timestamps)

#### Relationships:
- `belongsTo(User)` → `user()`
- `belongsTo(Booking)` → `booking()` (nullable)

#### Methods:
- `markAsRead()`: void
- `markAsUnread()`: void
- `static createNotification(int $userId, string $type, string $title, string $message, ?int $bookingId)`: self

---

## 2. Class Diagram (UML Format - Text Representation)

```
┌─────────────────────────────────────────────────────────────────────┐
│                           CLASS DIAGRAM                              │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────┐
│       User          │
├─────────────────────┤
│ +id: int            │
│ +username: string   │
│ +name: string       │
│ +email: string      │
│ +password: string   │
│ +full_name: string  │
│ +phone: string      │
│ +unit_kerja: string │
│ +role: enum         │
│ +last_login_at: dt  │
│ +email_verified_at  │
│ +google_id: string? │
├─────────────────────┤
│ +bookings()         │
│ +notifications()    │
│ +unreadNotifications()│
│ +isAdmin(): bool    │
│ +isUser(): bool     │
│ +getActiveBookings()│
│ +getBookingStats()  │
└─────────────────────┘
         │ 1
         │
         │ hasMany
         │
         ▼ *
┌─────────────────────┐         ┌─────────────────────┐
│      Booking        │────────▶│   MeetingRoom       │
├─────────────────────┤         ├─────────────────────┤
│ +id: int            │ belongsTo│ +id: int            │
│ +user_id: int (FK)  │         │ +name: string       │
│ +meeting_room_id:FK │         │ +description: str  │
│ +title: string      │         │ +capacity: int      │
│ +description: str   │         │ +amenities: array  │
│ +start_time: dt     │         │ +location: string   │
│ +end_time: dt       │         │ +images: array      │
│ +status: enum       │         │ +is_active: bool    │
│ +attendees: array   │         ├─────────────────────┤
│ +total_cost: decimal│         │ +bookings()         │
│ +preempt_status: str│         │ +getActiveBookings()│
│ +preempt_requested_by│         │ +isAvailable()      │
├─────────────────────┤         │ +getBookingStats()  │
│ +user()             │         └─────────────────────┘
│ +meetingRoom()      │                    │ 1
│ +invitations()      │                    │
│ +invitedPics()      │                    │ hasMany
│ +getDuration()      │                    │
│ +isUpcoming()       │                    │
│ +isPast()           │                    │
│ +isOngoing()        │                    │
│ +canBeCancelled()   │                    │ *
│ +startPreempt()     │                    │
│ +closePreempt()     │                    │
│ +updateStatus()     │                    │
└─────────────────────┘                    │
         │ 1                                │
         │                                  │
         │ hasMany                          │
         │                                  │
         ▼ *                                │
┌─────────────────────┐                    │
│ MeetingInvitation   │─────────────────────┘
├─────────────────────┤
│ +id: int            │
│ +booking_id: int(FK)│
│ +pic_id: int (FK)   │
│ +invited_by_pic_id  │
│ +status: enum       │
│ +invited_at: dt     │
│ +responded_at: dt?  │
├─────────────────────┤
│ +booking()          │
│ +pic()              │
│ +invitedByPic()     │
│ +accept()           │
│ +decline()          │
└─────────────────────┘
         │
         │ belongsTo (pic_id)
         │
         ▼
┌─────────────────────┐
│       User          │ (as PIC)
└─────────────────────┘

         │ 1
         │ hasMany
         │
         ▼ *
┌─────────────────────┐
│  UserNotification   │
├─────────────────────┤
│ +id: int            │
│ +user_id: int (FK)  │
│ +booking_id: int? FK│
│ +type: string       │
│ +title: string      │
│ +message: string    │
│ +is_read: bool      │
│ +read_at: dt?       │
├─────────────────────┤
│ +user()             │
│ +booking()          │
│ +markAsRead()       │
│ +markAsUnread()     │
│ +createNotification()│
└─────────────────────┘
         │
         │ belongsTo (user_id)
         │
         ▼
┌─────────────────────┐
│       User          │
└─────────────────────┘
```

---

## 3. Relasi Detail Antar Class

### 3.1. User ↔ Booking
- **Type:** One-to-Many (1:N)
- **User → Booking:** `hasMany(Booking)`
- **Booking → User:** `belongsTo(User)`
- **Foreign Key:** `bookings.user_id`
- **Description:** Satu user dapat memiliki banyak booking

### 3.2. MeetingRoom ↔ Booking
- **Type:** One-to-Many (1:N)
- **MeetingRoom → Booking:** `hasMany(Booking)`
- **Booking → MeetingRoom:** `belongsTo(MeetingRoom)`
- **Foreign Key:** `bookings.meeting_room_id`
- **Description:** Satu ruang meeting dapat memiliki banyak booking

### 3.3. Booking ↔ MeetingInvitation
- **Type:** One-to-Many (1:N)
- **Booking → MeetingInvitation:** `hasMany(MeetingInvitation)`
- **MeetingInvitation → Booking:** `belongsTo(Booking)`
- **Foreign Key:** `meeting_invitations.booking_id`
- **Description:** Satu booking dapat memiliki banyak invitation (untuk multiple PICs)

### 3.4. User ↔ MeetingInvitation (as PIC)
- **Type:** One-to-Many (1:N)
- **User → MeetingInvitation:** tidak ada method langsung
- **MeetingInvitation → User:** `belongsTo(User, 'pic_id')` → `pic()`
- **Foreign Key:** `meeting_invitations.pic_id`
- **Description:** Satu user (PIC) dapat menerima banyak invitation

### 3.5. User ↔ MeetingInvitation (as Inviter)
- **Type:** One-to-Many (1:N)
- **User → MeetingInvitation:** tidak ada method langsung
- **MeetingInvitation → User:** `belongsTo(User, 'invited_by_pic_id')` → `invitedByPic()`
- **Foreign Key:** `meeting_invitations.invited_by_pic_id`
- **Description:** Satu user dapat mengundang banyak PIC

### 3.6. Booking ↔ User (Many-to-Many via MeetingInvitation)
- **Type:** Many-to-Many (N:N)
- **Booking → User:** `belongsToMany(User)` → `invitedPics()`
- **Pivot Table:** `meeting_invitations`
- **Pivot Columns:** `status`, `invited_at`, `responded_at`
- **Description:** Satu booking dapat diundang ke banyak PIC, satu PIC dapat menerima banyak invitation

### 3.7. User ↔ UserNotification
- **Type:** One-to-Many (1:N)
- **User → UserNotification:** `hasMany(UserNotification)` → `notifications()`
- **UserNotification → User:** `belongsTo(User)`
- **Foreign Key:** `user_notifications.user_id`
- **Description:** Satu user dapat memiliki banyak notification

### 3.8. Booking ↔ UserNotification
- **Type:** One-to-Many (1:N, optional)
- **Booking → UserNotification:** tidak ada method langsung (implicit)
- **UserNotification → Booking:** `belongsTo(Booking)` (nullable)
- **Foreign Key:** `user_notifications.booking_id` (nullable)
- **Description:** Satu booking dapat memiliki banyak notification (optional)

---

## 4. Controller Classes

### 4.1. AuthController
- **File:** `app/Http/Controllers/AuthController.php`
- **Responsibilities:**
  - Authentication (login, logout, register)
  - Email verification
  - Password reset
  - Google OAuth (deprecated/removed)
  - User management API (`getAllUsers()`)
  - Role management (`updateUserRole()`)

### 4.2. UserController
- **File:** `app/Http/Controllers/UserController.php`
- **Responsibilities:**
  - User dashboard
  - User profile management
  - Booking management (create, update, cancel)
  - Preempt booking (request, respond)
  - Availability checking
  - Calendar view

### 4.3. AdminController
- **File:** `app/Http/Controllers/AdminController.php`
- **Responsibilities:**
  - Admin dashboard
  - User management (CRUD)
  - Meeting room management (CRUD)
  - Booking management (approval, status update)
  - Notification management

### 4.4. Other Controllers
- **ExportController:** Export data (Excel, PDF)
- **SeoController:** SEO sitemap, robots.txt
- **CaptchaController:** CAPTCHA handling

---

## 5. Pola Desain yang Digunakan

### 5.1. Active Record Pattern
- Semua model menggunakan Laravel Eloquent (Active Record)
- Model encapsulate data dan behavior

### 5.2. Repository Pattern (Implicit)
- Controller menggunakan model langsung
- Business logic ada di model methods

### 5.3. Factory Pattern
- Laravel factories untuk model seeding/testing

### 5.4. Observer Pattern
- Laravel events/observers (jika ada)

---

## 6. Constraints & Business Rules

### 6.1. User
- `role` hanya bisa 'admin' atau 'user'
- `email` harus unique
- `username` harus unique

### 6.2. Booking
- `status` hanya bisa: 'pending', 'confirmed', 'cancelled', 'completed'
- `start_time` harus sebelum `end_time`
- `preempt_status` hanya bisa: 'pending', 'accepted', 'rejected', 'closed', null

### 6.3. MeetingInvitation
- `status` hanya bisa: 'invited', 'accepted', 'declined'
- Tidak ada timestamps (manual tracking via `invited_at`, `responded_at`)

### 6.4. MeetingRoom
- `is_active` menentukan apakah ruang bisa dibooking
- `capacity` harus >= 1

---

## 7. Database Schema Summary

### Tables:
1. **users** - User accounts (admin/user)
2. **meeting_rooms** - Available meeting rooms
3. **bookings** - Booking records
4. **meeting_invitations** - PIC invitations (pivot table)
5. **user_notifications** - User notifications

### Key Foreign Keys:
- `bookings.user_id` → `users.id`
- `bookings.meeting_room_id` → `meeting_rooms.id`
- `meeting_invitations.booking_id` → `bookings.id`
- `meeting_invitations.pic_id` → `users.id`
- `meeting_invitations.invited_by_pic_id` → `users.id`
- `user_notifications.user_id` → `users.id`
- `user_notifications.booking_id` → `bookings.id` (nullable)

---

## 8. Kesimpulan

Sistem ini menggunakan arsitektur **MVC (Model-View-Controller)** dengan Laravel Framework:

- **5 Model utama** dengan relasi yang kompleks
- **One-to-Many** dominan untuk relasi utama
- **Many-to-Many** untuk Booking ↔ User (via MeetingInvitation pivot)
- **Controller** meng-handle HTTP requests dan business logic
- **Model methods** encapsulate business rules dan queries

Struktur relasi yang solid memungkinkan:
- User dapat membuat banyak booking
- Booking dapat diundang ke banyak PIC (Person In Charge)
- Notifikasi terpusat untuk user dan booking
- Preempt booking untuk prioritas

