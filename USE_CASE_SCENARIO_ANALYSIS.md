# Analisis Use Case Scenario - Sistem Pemesanan Ruang Meeting

## 1. Identifikasi Actor

### 1.1. Guest (Pengunjung)
- **Deskripsi:** Pengguna yang belum terautentikasi
- **Akses:** Terbatas pada autentikasi dan registrasi

### 1.2. User (Pengguna)
- **Deskripsi:** Pengguna terautentikasi dengan role 'user'
- **Akses:** Booking management, profile management, calendar view

### 1.3. Admin (Administrator)
- **Deskripsi:** Pengguna terautentikasi dengan role 'admin'
- **Akses:** Full access ke semua fitur termasuk user management, room management, booking approval

---

## 2. Use Case Diagram (Text Representation)

```
┌─────────────────────────────────────────────────────────────────────┐
│                         USE CASE DIAGRAM                             │
└─────────────────────────────────────────────────────────────────────┘

                          ┌─────────────┐
                          │   Guest     │
                          └──────┬──────┘
                                 │
                    ┌────────────┼────────────┐
                    │            │            │
          ┌─────────▼──────┐  ┌─▼──────┐  ┌─▼─────────┐
          │   Register     │  │  Login  │  │ Forgot    │
          │                │  │         │  │ Password  │
          └────────────────┘  └─────────┘  └───────────┘

                          ┌─────────────┐
                          │    User     │
                          └──────┬──────┘
                                 │
          ┌──────────────────────┼──────────────────────┐
          │                      │                      │
  ┌───────▼────────┐  ┌──────────▼──────────┐  ┌───────▼────────┐
  │ View Dashboard │  │  Manage Bookings    │  │ Manage Profile │
  │                │  │  • Create           │  │  • View        │
  │                │  │  • Update           │  │  • Update      │
  │                │  │  • Cancel           │  │  • Change Pass │
  │                │  │  • Request Preempt  │  └────────────────┘
  │                │  │  • Respond Preempt  │
  │                │  │  • Check Availability│  ┌──────────────┐
  │                │  └─────────────────────┘  │ View Calendar│
  │                │                            └──────────────┘
  │                │  ┌──────────────────────┐
  │                │  │ Manage Notifications │  ┌──────────────┐
  │                └──│  • View              │  │ Invite PICs  │
  │                   │  • Mark Read         │  │              │
  │                   └──────────────────────┘  └──────────────┘

                          ┌─────────────┐
                          │    Admin    │
                          └──────┬──────┘
                                 │
          ┌──────────────────────┼──────────────────────┐
          │                      │                      │
  ┌───────▼────────┐  ┌──────────▼──────────┐  ┌───────▼────────┐
  │ View Dashboard │  │  Manage Users        │  │ Manage Rooms   │
  │                │  │  • View List         │  │  • Create      │
  │                │  │  • Create            │  │  • Update      │
  │                │  │  • Update            │  │  • Delete      │
  │                │  │  • Delete            │  │  • Activate    │
  │                │  │  • Change Role       │  │                │
  │                │  └─────────────────────┘  └────────────────┘
  │                │
  │                │  ┌──────────────────────┐
  │                │  │  Manage Bookings      │  ┌──────────────┐
  │                │  │  • View All          │  │ Export Data  │
  │                │  │  • Approve/Reject    │  │  • Excel     │
  │                │  │  • Change Status     │  │  • PDF       │
  │                │  │  • Download Docs     │  └──────────────┘
  │                │  └──────────────────────┘
  │                │
  │                └──  ┌──────────────────────┐
                       │  Manage Notifications │
                       │  • View All           │
                       │  • Mark Read          │
                       │  • Clear All          │
                       └──────────────────────┘
```

---

## 3. Detail Use Case Scenario

### 3.1. Authentication & Registration

#### UC-001: Login
**Actor:** Guest, User, Admin  
**Precondition:** User belum login  
**Main Flow:**
1. User mengakses halaman login
2. User memasukkan username/email dan password
3. Sistem memvalidasi credentials
4. Jika valid:
   - Sistem membuat session
   - Sistem mengupdate `last_login_at`
   - Sistem redirect berdasarkan role:
     - Admin → `/admin/dashboard`
     - User → `/user/dashboard`
5. Jika tidak valid, sistem menampilkan error message

**Postcondition:** User terautentikasi dan diarahkan ke dashboard sesuai role

**Alternative Flow:**
- 3a. Credentials salah → tampilkan error "Username/email atau password salah"
- 3b. Email belum diverifikasi → tampilkan error "Email belum diverifikasi"
- 3c. Hardcoded admin (username: 'admin', password: 'admin') → langsung login sebagai admin

**Special Requirements:**
- Hardcoded admin account: username='admin', password='admin'
- Session regeneration untuk security

---

#### UC-002: Register
**Actor:** Guest  
**Precondition:** User belum terdaftar  
**Main Flow:**
1. User mengakses halaman registrasi
2. User mengisi form:
   - Username (unique)
   - Full name
   - Email (unique)
   - Password (min 8 karakter, confirmed)
   - Phone (optional)
   - Department (optional)
3. Sistem memvalidasi input
4. Sistem membuat user baru dengan role 'user' (default)
5. Sistem generate email verification token
6. Sistem mengirim email verifikasi
7. Sistem redirect ke login dengan success message

**Postcondition:** User baru terdaftar, email verifikasi terkirim

**Alternative Flow:**
- 3a. Validation error → tampilkan error di form
- 3b. Email sudah terdaftar → tampilkan error "Email sudah digunakan"

**Business Rules:**
- Role default untuk registrasi adalah 'user'
- Email harus unique
- Username harus unique
- Password minimum 8 karakter

---

#### UC-003: Verify Email
**Actor:** User  
**Precondition:** User terdaftar tapi email belum diverifikasi  
**Main Flow:**
1. User mengklik link verifikasi di email
2. Sistem memvalidasi token
3. Jika valid:
   - Sistem mengupdate `email_verified_at`
   - Sistem redirect ke login dengan success message
4. Jika tidak valid:
   - Sistem menampilkan error message

**Postcondition:** Email user terverifikasi

---

#### UC-004: Forgot Password
**Actor:** User  
**Precondition:** User sudah terdaftar  
**Main Flow:**
1. User mengakses halaman forgot password
2. User memasukkan email
3. Sistem memvalidasi email terdaftar
4. Jika valid:
   - Sistem generate reset token
   - Sistem mengirim email reset password
   - Sistem menampilkan success message
5. User mengklik link di email
6. User memasukkan password baru
7. Sistem memvalidasi token
8. Sistem mengupdate password
9. Sistem redirect ke login

**Postcondition:** Password user berhasil direset

---

#### UC-005: Logout
**Actor:** User, Admin  
**Precondition:** User sudah login  
**Main Flow:**
1. User mengklik tombol logout
2. Sistem menghapus session data:
   - `user_logged_in`
   - `user_data`
   - `google_oauth_state` (jika ada)
3. Sistem invalidate session
4. Sistem regenerate CSRF token
5. Sistem redirect ke login

**Postcondition:** User logout, session dihapus

---

### 3.2. User Use Cases

#### UC-101: View Dashboard
**Actor:** User  
**Precondition:** User sudah login  
**Main Flow:**
1. User mengakses `/user/dashboard`
2. Sistem menampilkan:
   - Statistik booking (total, pending, confirmed, cancelled, this month)
   - Active bookings (5 terbaru)
   - Today's bookings
   - Calendar view dengan booking
   - Available rooms
   - Recent notifications (5 terbaru)
3. User dapat memilih bulan di datepicker untuk melihat calendar bulan tertentu

**Postcondition:** Dashboard ditampilkan dengan data terkini

**Special Requirements:**
- Calendar dapat dinavigasi berdasarkan bulan
- Datepicker format: `month` (YYYY-MM)

---

#### UC-102: View Profile
**Actor:** User  
**Precondition:** User sudah login  
**Main Flow:**
1. User mengakses `/user/profile`
2. Sistem menampilkan:
   - Username, full name, email
   - Phone, department, unit kerja
   - Role, avatar
   - Last login time

**Postcondition:** Profile user ditampilkan

---

#### UC-103: Update Profile
**Actor:** User  
**Precondition:** User sudah login  
**Main Flow:**
1. User mengakses `/user/profile`
2. User mengubah data (full_name, phone, department, unit_kerja)
3. User submit form
4. Sistem memvalidasi input
5. Sistem mengupdate data user
6. Sistem menampilkan success message

**Postcondition:** Profile user terupdate

---

#### UC-104: Change Password
**Actor:** User  
**Precondition:** User sudah login  
**Main Flow:**
1. User mengakses `/user/profile`
2. User mengisi:
   - Current password
   - New password (min 8 karakter)
   - Confirm new password
3. User submit form
4. Sistem memvalidasi:
   - Current password benar
   - New password confirmed
5. Sistem mengupdate password
6. Sistem menampilkan success message

**Postcondition:** Password user terupdate

**Alternative Flow:**
- 4a. Current password salah → tampilkan error
- 4b. New password tidak confirmed → tampilkan error

---

#### UC-105: Create Booking
**Actor:** User  
**Precondition:** User sudah login, email terverifikasi  
**Main Flow:**
1. User mengakses `/user/bookings/create`
2. Sistem menampilkan form booking dan room availability grid
3. User memilih:
   - Meeting room
   - Date dan waktu (start_time, end_time)
   - Title
   - Description
   - Description visibility (all_users, invited_pics_only)
   - Invited PICs (multiple selection)
   - Attendees count
   - Special requirements
4. User submit form
5. Sistem memvalidasi:
   - Room available di waktu tersebut
   - Tidak ada conflict dengan booking lain
   - Start time < End time
6. Sistem membuat booking dengan status 'pending'
7. Sistem membuat MeetingInvitation untuk setiap PIC yang dipilih
8. Sistem membuat notification untuk user (success)
9. Sistem redirect ke booking list dengan success message

**Postcondition:** Booking baru dibuat dengan status 'pending', notification terkirim

**Alternative Flow:**
- 5a. Room tidak available → tampilkan error "Ruangan tidak tersedia pada waktu tersebut"
- 5b. Conflict dengan booking lain → tampilkan error dan opsi preempt

**Business Rules:**
- User tidak bisa booking yang overlap dengan booking miliknya sendiri
- Booking default status: 'pending' (menunggu approval admin)

---

#### UC-106: Check Availability
**Actor:** User  
**Precondition:** User sudah login  
**Main Flow:**
1. User memilih room dan tanggal di form booking
2. User klik "Check Availability"
3. Sistem menampilkan room availability grid:
   - Slot waktu per 30 menit (08:00 - 19:00)
   - Available (hijau)
   - Unavailable (merah)
   - Past time (abu-abu)
4. User dapat melihat booking yang conflict (jika ada)

**Postcondition:** Availability grid ditampilkan

**Special Requirements:**
- Grid menampilkan 30 menit intervals
- Past time slot tidak bisa dipilih
- Conflict bookings ditampilkan dengan detail

---

#### UC-107: Update Booking
**Actor:** User  
**Precondition:** User sudah login, booking milik user, status 'pending' atau 'confirmed'  
**Main Flow:**
1. User mengakses `/user/bookings/{id}`
2. User klik "Edit"
3. Sistem menampilkan form dengan data booking
4. User mengubah:
   - Title, description
   - Description visibility
   - Invited PICs (add/remove)
   - Special requirements
5. User submit form
6. Sistem memvalidasi perubahan
7. Sistem mengupdate booking
8. Sistem sync MeetingInvitation:
   - Hapus PIC yang di-unselect
   - Tambah PIC yang baru dipilih
9. Sistem menampilkan success message

**Postcondition:** Booking terupdate, PIC invitations ter-sync

**Alternative Flow:**
- 4a. Hanya mengubah PIC → hanya update invitations
- 4b. Booking sudah confirmed dan < 2 jam sebelum start → tidak bisa edit waktu/room

**Business Rules:**
- User hanya bisa edit booking miliknya sendiri
- Tidak bisa edit room/waktu jika booking sudah confirmed dan mendekati start time

---

#### UC-108: Cancel Booking
**Actor:** User  
**Precondition:** User sudah login, booking milik user  
**Main Flow:**
1. User mengakses booking detail
2. User klik "Cancel Booking"
3. Sistem validasi:
   - Booking status: 'pending' atau 'confirmed'
   - Jika 'confirmed', harus > 2 jam sebelum start time
4. Sistem mengupdate status booking menjadi 'cancelled'
5. Sistem set `cancelled_at` dan `cancellation_reason`
6. Sistem membuat notification untuk user
7. Sistem menampilkan success message

**Postcondition:** Booking dibatalkan, notification terkirim

**Alternative Flow:**
- 3a. Booking tidak bisa dibatalkan (confirmed < 2 jam) → tampilkan error

**Business Rules:**
- Pending booking: bisa dibatalkan kapan saja
- Confirmed booking: hanya bisa dibatalkan > 2 jam sebelum start time

---

#### UC-109: View Bookings List
**Actor:** User  
**Precondition:** User sudah login  
**Main Flow:**
1. User mengakses `/user/bookings`
2. Sistem menampilkan list booking user:
   - Filter berdasarkan status (all, pending, confirmed, cancelled)
   - Sort by date (terbaru dulu)
   - Pagination
3. User dapat:
   - View detail booking
   - Edit booking (jika pending/confirmed)
   - Cancel booking
   - Request preempt (jika ada conflict)

**Postcondition:** List booking ditampilkan

---

#### UC-110: Request Preempt (Didahulukan)
**Actor:** User  
**Precondition:** User sudah login, ada booking lain yang conflict  
**Main Flow:**
1. User memilih slot waktu yang sudah di-booking user lain
2. User klik "Request Preempt"
3. Sistem menampilkan form dengan:
   - Booking yang akan di-preempt
   - Alasan preempt (optional)
4. User submit form
5. Sistem validasi:
   - Booking tidak milik user sendiri
   - Booking tidak dalam status preempt pending
6. Sistem menghitung deadline SLA:
   - Jika > 2 jam sebelum start: 60 menit
   - Jika < 2 jam sebelum start: 15 menit
7. Sistem mengupdate booking target:
   - `preempt_status` = 'pending'
   - `preempt_requested_by` = user_id
   - `preempt_deadline_at` = deadline
   - `preempt_reason` = alasan
8. Sistem membuat notification untuk owner booking
9. Sistem menampilkan success message

**Postcondition:** Preempt request terkirim, owner mendapat notification

**Alternative Flow:**
- 5a. Booking milik sendiri → error "Tidak dapat meminta didahulukan pada booking milik sendiri"
- 5b. Preempt sudah pending → success message "Permintaan sudah dalam status menunggu tanggapan"

**Business Rules:**
- User tidak bisa preempt booking miliknya sendiri
- Deadline SLA: 60 menit (> 2 jam sebelum start) atau 15 menit (< 2 jam)

---

#### UC-111: Respond Preempt (Terima & Batalkan)
**Actor:** User (Owner Booking)  
**Precondition:** User sudah login, booking milik user, ada preempt request pending  
**Main Flow:**
1. User mendapat notification tentang preempt request
2. User mengakses booking detail
3. User klik "Terima & Batalkan"
4. Sistem validasi:
   - User adalah owner booking
   - Preempt status = 'pending'
5. Sistem memulai transaction:
   a. Batalkan booking lama:
      - Status = 'cancelled'
      - Close preempt
   b. Buat booking baru untuk requester:
      - Copy semua data dari booking lama
      - Title prefix: '[Didahulukan]'
      - Status = 'confirmed' (auto-confirm)
      - User ID = requester ID
   c. Buat notification:
      - Untuk requester: booking auto-confirmed
      - Untuk admin: preempt completed
6. Sistem commit transaction
7. Sistem menampilkan success message

**Postcondition:** Booking lama dibatalkan, booking baru untuk requester auto-confirmed

**Alternative Flow:**
- 4a. User bukan owner → error "Anda bukan pemilik booking ini"
- 4b. Preempt tidak pending → error "Tidak ada permintaan yang perlu ditanggapi"
- 5a. Transaction error → rollback, tampilkan error

**Business Rules:**
- Hanya owner booking yang bisa respond preempt
- Booking baru untuk requester auto-confirmed (tidak perlu approval admin)
- `unit_kerja` dijamin tidak null (fallback ke booking lama, requester unit, atau 'Tidak diketahui')

---

#### UC-112: View Calendar
**Actor:** User  
**Precondition:** User sudah login  
**Main Flow:**
1. User mengakses dashboard
2. Sistem menampilkan calendar view:
   - Bulan saat ini
   - Hari dengan booking ditandai
   - Jumlah booking per hari
3. User memilih bulan di datepicker
4. Sistem menampilkan calendar untuk bulan tersebut
5. User klik tanggal
6. Sistem menampilkan detail booking untuk tanggal tersebut

**Postcondition:** Calendar ditampilkan dengan booking

**Special Requirements:**
- Datepicker format: `month` (YYYY-MM)
- Calendar navigate ke bulan yang dipilih
- Hari dengan booking ditandai dengan jumlah booking

---

#### UC-113: Invite PICs
**Actor:** User  
**Precondition:** User sudah login  
**Main Flow:**
1. User membuat atau edit booking
2. User memilih PICs dari dropdown:
   - List semua user yang registered
   - Multiple selection
3. User submit booking
4. Sistem membuat MeetingInvitation untuk setiap PIC:
   - `booking_id`
   - `pic_id`
   - `invited_by_pic_id` = user_id
   - `status` = 'invited'
   - `invited_at` = now()
5. PIC dapat melihat:
   - Booking di dashboard (jika invited)
   - Description (jika `description_visibility` = 'invited_pics_only')

**Postcondition:** PICs terundang, invitations dibuat

**Business Rules:**
- Hanya PIC yang diundang bisa melihat description (jika visibility = 'invited_pics_only')
- Admin bisa melihat semua description
- Owner booking bisa melihat description

---

#### UC-114: View Notifications
**Actor:** User  
**Precondition:** User sudah login  
**Main Flow:**
1. User mengakses `/user/notifications` atau melihat di dashboard
2. Sistem menampilkan:
   - List notifications (unread first)
   - Type (info, warning, error, success)
   - Title, message
   - Created at
   - Booking link (jika ada)
3. User dapat:
   - Mark as read
   - Mark all as read
   - View booking detail (jika linked)

**Postcondition:** Notifications ditampilkan

---

#### UC-115: Update Notification Settings
**Actor:** User  
**Precondition:** User sudah login  
**Main Flow:**
1. User mengakses profile settings
2. User mengatur notification preferences
3. User submit form
4. Sistem menyimpan preferences

**Postcondition:** Notification settings terupdate

---

### 3.3. Admin Use Cases

#### UC-201: View Admin Dashboard
**Actor:** Admin  
**Precondition:** Admin sudah login  
**Main Flow:**
1. Admin mengakses `/admin/dashboard`
2. Sistem menampilkan:
   - Statistik umum:
     * Total users
     * Total rooms
     * Total bookings
     * Pending bookings
     * Confirmed bookings
     * Cancelled bookings
     * Active rooms
   - Recent bookings (10 terbaru)
   - Today's bookings
   - Monthly stats (6 bulan terakhir)
   - Popular rooms (top 5)

**Postcondition:** Admin dashboard ditampilkan dengan statistik

---

#### UC-202: Manage Users - View List
**Actor:** Admin  
**Precondition:** Admin sudah login  
**Main Flow:**
1. Admin mengakses `/admin/users`
2. Sistem menampilkan:
   - List semua users (table)
   - Columns: Nama, Email, Unit Kerja, Role, Terakhir Login, Bergabung, Aksi
   - Search/filter functionality
3. Admin dapat:
   - View user detail
   - Change user role
   - Delete user (kecuali admin default)

**Postcondition:** User list ditampilkan

---

#### UC-203: Manage Users - Create
**Actor:** Admin  
**Precondition:** Admin sudah login  
**Main Flow:**
1. Admin mengakses `/admin/users/create`
2. Admin mengisi form:
   - Username (unique)
   - Full name
   - Email (unique)
   - Password (min 8 karakter)
   - Phone (optional)
   - Department (optional)
   - Role (admin/user)
3. Admin submit form
4. Sistem memvalidasi input
5. Sistem membuat user baru
6. Sistem redirect ke user list dengan success message

**Postcondition:** User baru dibuat

**Business Rules:**
- Role default: 'user' (kecuali admin memilih 'admin')
- Email dan username harus unique
- Email auto-verified untuk user yang dibuat admin

---

#### UC-204: Manage Users - Update
**Actor:** Admin  
**Precondition:** Admin sudah login  
**Main Flow:**
1. Admin mengakses user detail
2. Admin klik "Edit"
3. Admin mengubah data user
4. Admin submit form
5. Sistem memvalidasi dan mengupdate user
6. Sistem menampilkan success message

**Postcondition:** User terupdate

---

#### UC-205: Manage Users - Change Role
**Actor:** Admin  
**Precondition:** Admin sudah login  
**Main Flow:**
1. Admin mengakses user list
2. Admin klik "Change Role" pada user tertentu
3. Sistem menampilkan modal:
   - Current role
   - New role (toggle admin/user)
4. Admin konfirmasi perubahan
5. Sistem mengupdate user role
6. Sistem menampilkan success message
7. Jika role berubah menjadi 'admin':
   - Sistem redirect user tersebut ke admin dashboard (jika sedang login)

**Postcondition:** User role terupdate

**Business Rules:**
- Tidak bisa delete atau change role admin default (username='admin')
- Admin bisa mengubah role user lain

---

#### UC-206: Manage Users - Delete
**Actor:** Admin  
**Precondition:** Admin sudah login  
**Main Flow:**
1. Admin mengakses user list
2. Admin klik "Delete" pada user tertentu
3. Sistem validasi:
   - User bukan admin default
   - Tidak ada active bookings terkait
4. Sistem menampilkan konfirmasi dialog
5. Admin konfirmasi
6. Sistem menghapus user
7. Sistem menampilkan success message

**Postcondition:** User dihapus

**Alternative Flow:**
- 3a. User adalah admin default → error "Tidak bisa menghapus admin default"
- 3b. User memiliki active bookings → warning atau prevent deletion

---

#### UC-207: Manage Rooms - View List
**Actor:** Admin  
**Precondition:** Admin sudah login  
**Main Flow:**
1. Admin mengakses `/admin/rooms`
2. Sistem menampilkan:
   - List semua meeting rooms
   - Columns: Name, Location, Capacity, Status, Bookings Count, Aksi
   - Filter active/inactive rooms

**Postcondition:** Room list ditampilkan

---

#### UC-208: Manage Rooms - Create
**Actor:** Admin  
**Precondition:** Admin sudah login  
**Main Flow:**
1. Admin mengakses `/admin/rooms/create`
2. Admin mengisi form:
   - Name
   - Description
   - Capacity
   - Location
   - Amenities (array)
   - Images (array)
   - Is Active (boolean)
3. Admin submit form
4. Sistem memvalidasi input
5. Sistem membuat room baru
6. Sistem redirect ke room list dengan success message

**Postcondition:** Room baru dibuat

---

#### UC-209: Manage Rooms - Update
**Actor:** Admin  
**Precondition:** Admin sudah login  
**Main Flow:**
1. Admin mengakses room detail
2. Admin klik "Edit"
3. Admin mengubah data room
4. Admin submit form
5. Sistem memvalidasi dan mengupdate room
6. Sistem menampilkan success message

**Postcondition:** Room terupdate

**Business Rules:**
- Jika room di-deactivate, semua active bookings bisa dibatalkan otomatis
- Update capacity tidak boleh < jumlah attendees di active bookings

---

#### UC-210: Manage Rooms - Delete
**Actor:** Admin  
**Precondition:** Admin sudah login  
**Main Flow:**
1. Admin mengakses room detail
2. Admin klik "Delete"
3. Sistem validasi:
   - Tidak ada active bookings
   - Atau semua bookings sudah cancelled
4. Sistem menampilkan konfirmasi dialog
5. Admin konfirmasi
6. Jika ada active bookings:
   - Sistem membatalkan semua active bookings
   - Sistem membuat notification untuk users
7. Sistem menghapus room
8. Sistem menampilkan success message

**Postcondition:** Room dihapus, bookings terkait dibatalkan (jika ada)

---

#### UC-211: Manage Bookings - View List
**Actor:** Admin  
**Precondition:** Admin sudah login  
**Main Flow:**
1. Admin mengakses `/admin/bookings`
2. Sistem menampilkan:
   - List semua bookings
   - Filter berdasarkan status
   - Search functionality
   - Hide cancelled bookings yang sudah di-replace oleh preempt auto-confirmed booking
3. Admin dapat:
   - View booking detail
   - Approve/reject booking
   - Change booking status
   - Download dokumen perizinan

**Postcondition:** Booking list ditampilkan

**Special Requirements:**
- Hide cancelled bookings (preempt) jika ada confirmed booking di slot yang sama
- Menampilkan auto-confirmed booking dari preempt

---

#### UC-212: Manage Bookings - Approve/Reject
**Actor:** Admin  
**Precondition:** Admin sudah login, booking status 'pending'  
**Main Flow:**
1. Admin mengakses booking detail
2. Admin klik "Approve" atau "Reject"
3. Sistem validasi booking status
4. Sistem mengupdate booking status:
   - Approve → status = 'confirmed'
   - Reject → status = 'cancelled'
5. Sistem membuat notification untuk user
6. Sistem menampilkan success message

**Postcondition:** Booking status terupdate, user mendapat notification

---

#### UC-213: Manage Bookings - Change Status
**Actor:** Admin  
**Precondition:** Admin sudah login  
**Main Flow:**
1. Admin mengakses booking detail
2. Admin mengubah status booking:
   - pending
   - confirmed
   - cancelled
   - completed
3. Admin memasukkan reason (optional)
4. Admin submit form
5. Sistem memvalidasi status baru
6. Sistem mengupdate booking status
7. Sistem membuat notification untuk user
8. Sistem menampilkan success message

**Postcondition:** Booking status terupdate

---

#### UC-214: Manage Bookings - Download Dokumen Perizinan
**Actor:** Admin  
**Precondition:** Admin sudah login, booking memiliki dokumen perizinan  
**Main Flow:**
1. Admin mengakses booking detail
2. Admin klik "Download Dokumen Perizinan"
3. Sistem validasi dokumen ada
4. Sistem men-download file PDF

**Postcondition:** Dokumen terunduh

**Alternative Flow:**
- 3a. Dokumen tidak ada → error "Dokumen perizinan tidak ditemukan"

---

#### UC-215: Manage Notifications - View All
**Actor:** Admin  
**Precondition:** Admin sudah login  
**Main Flow:**
1. Admin mengakses `/admin/notifications`
2. Sistem menampilkan:
   - All notifications (admin notifications + user notifications)
   - Filter unread/read
   - Group by user atau booking

**Postcondition:** All notifications ditampilkan

**Business Rules:**
- Admin melihat semua notifications:
  - Notifications dengan `user_id` = NULL (admin notifications)
  - Notifications untuk admin users
  - User notifications (optional)

---

#### UC-216: Manage Notifications - Mark Read/Clear
**Actor:** Admin  
**Precondition:** Admin sudah login  
**Main Flow:**
1. Admin mengakses notifications list
2. Admin dapat:
   - Mark individual notification as read
   - Mark all as read
   - Clear all notifications
3. Sistem mengupdate status notifications

**Postcondition:** Notifications terupdate

---

#### UC-217: Export Data - Excel
**Actor:** Admin  
**Precondition:** Admin sudah login  
**Main Flow:**
1. Admin mengakses bookings list
2. Admin klik "Export Excel"
3. Sistem generate Excel file dengan:
   - All bookings data
   - Columns: ID, User, Room, Title, Start Time, End Time, Status, etc.
4. Sistem download file Excel

**Postcondition:** Excel file terunduh

---

#### UC-218: Export Data - PDF
**Actor:** Admin  
**Precondition:** Admin sudah login  
**Main Flow:**
1. Admin mengakses bookings list
2. Admin klik "Export PDF"
3. Sistem generate PDF file dengan bookings report
4. Sistem download file PDF

**Postcondition:** PDF file terunduh

---

## 4. Use Case Scenario - Flow Sequence

### 4.1. Normal Booking Flow

```
User → Create Booking → Admin Dashboard
  │                          │
  │                          ▼
  │                    View Pending Booking
  │                          │
  │                          ▼
  │                    Approve Booking
  │                          │
  │                          ▼
  │                    Notify User
  │                          │
  └──────────────────────────┘
         User receives notification
```

### 4.2. Preempt Booking Flow

```
User A → Request Preempt → Booking Owner (User B)
  │                            │
  │                            ▼
  │                      Receive Notification
  │                            │
  │                            ▼
  │                      Respond: Accept & Cancel
  │                            │
  │                            ▼
  │                      System Transaction:
  │                      • Cancel Booking A
  │                      • Auto-Confirm Booking B (for User A)
  │                      • Notify Both Users
  │                      • Notify Admin
  │                            │
  └────────────────────────────┘
    User A receives auto-confirmed booking
```

### 4.3. PIC Invitation Flow

```
User → Create Booking → Select PICs
  │                          │
  │                          ▼
  │                    Create Invitations
  │                          │
  │                          ▼
  │                    PICs can see:
  │                    • Booking in dashboard
  │                    • Description (if invited)
  │                          │
  └──────────────────────────┘
```

---

## 5. Business Rules Summary

### 5.1. Authentication
- Default admin: username='admin', password='admin'
- Email verification required untuk login
- Password minimum 8 karakter
- Session regeneration untuk security

### 5.2. Booking
- Default status: 'pending' (menunggu approval admin)
- User tidak bisa booking yang overlap dengan booking miliknya sendiri
- Confirmed booking hanya bisa dibatalkan > 2 jam sebelum start time
- Pending booking bisa dibatalkan kapan saja

### 5.3. Preempt
- User tidak bisa preempt booking miliknya sendiri
- Deadline SLA: 60 menit (> 2 jam sebelum start) atau 15 menit (< 2 jam)
- Auto-confirmed booking untuk requester (tidak perlu approval admin)
- `unit_kerja` guaranteed not null

### 5.4. PIC Invitation
- PIC yang diundang bisa melihat booking
- Description visibility:
  - 'all_users': semua user bisa lihat
  - 'invited_pics_only': hanya owner, admin, dan invited PICs bisa lihat

### 5.5. User Management
- Role default untuk registrasi: 'user'
- Admin bisa create user dengan role 'admin' atau 'user'
- Tidak bisa delete admin default (username='admin')

### 5.6. Room Management
- Room harus active untuk bisa dibooking
- Delete room akan cancel semua active bookings
- Capacity tidak boleh < jumlah attendees di active bookings

---

## 6. Use Case Priority

### High Priority
- UC-001: Login
- UC-105: Create Booking
- UC-107: Update Booking
- UC-108: Cancel Booking
- UC-212: Approve/Reject Booking
- UC-110: Request Preempt
- UC-111: Respond Preempt

### Medium Priority
- UC-101: View Dashboard
- UC-102: View Profile
- UC-103: Update Profile
- UC-109: View Bookings List
- UC-201: View Admin Dashboard
- UC-202: Manage Users - View List
- UC-207: Manage Rooms - View List

### Low Priority
- UC-002: Register
- UC-003: Verify Email
- UC-004: Forgot Password
- UC-217: Export Data - Excel
- UC-218: Export Data - PDF

---

## 7. Kesimpulan

Sistem ini memiliki **45+ use cases** yang mencakup:

1. **Authentication & Authorization** (5 use cases)
2. **User Management** (15 use cases)
3. **Admin Management** (18 use cases)
4. **Booking Management** (6 use cases)
5. **Preempt System** (2 use cases)
6. **Notification System** (3 use cases)
7. **Data Export** (2 use cases)

Fitur utama:
- **Booking Management** dengan approval workflow
- **Preempt System** untuk prioritas booking
- **PIC Invitation System** untuk kolaborasi
- **Notification System** terpusat
- **Role-based Access Control** (User/Admin)

Setiap use case memiliki:
- Actor yang jelas
- Precondition dan Postcondition
- Main flow dan Alternative flow
- Business rules terkait

