# Coding Standards - Sistem Pemesanan Ruang Meeting

## 📋 Daftar Isi

1. [Pengenalan](#pengenalan)
2. [PHP & Laravel Standards](#php--laravel-standards)
3. [JavaScript Standards](#javascript-standards)
4. [CSS & Styling Standards](#css--styling-standards)
5. [File Structure & Naming Conventions](#file-structure--naming-conventions)
6. [Database Standards](#database-standards)
7. [Security Standards](#security-standards)
8. [Documentation Standards](#documentation-standards)
9. [Testing Standards](#testing-standards)

---

## 1. Pengenalan

Dokumen ini menjelaskan standar coding yang harus diikuti oleh semua developer yang bekerja pada **Sistem Pemesanan Ruang Meeting**. Standar ini dibuat untuk memastikan konsistensi, maintainability, dan kualitas kode di seluruh proyek.

### Teknologi Stack
- **Backend**: PHP 8.2+, Laravel 11+
- **Frontend**: Blade Templates, JavaScript (ES6+), Tailwind CSS
- **Database**: MySQL 5.7+
- **Version Control**: Git

---

## 2. PHP & Laravel Standards

### 2.1. Code Style

Proyek ini mengikuti **PSR-12** coding standard untuk PHP.

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function dashboard(Request $request)
    {
        // Implementation
    }
}
```

### 2.2. Naming Conventions

#### Classes
- **PascalCase** untuk class names
- Harus deskriptif dan jelas
- Contoh: `UserController`, `BookingService`, `EmailNotification`

```php
class UserController extends Controller
{
    // ✅ Good
}

class usrCtrl extends Controller
{
    // ❌ Bad - tidak jelas dan tidak mengikuti konvensi
}
```

#### Methods & Functions
- **camelCase** untuk method names
- Harus deskriptif dan verb-based
- Contoh: `getUserBookings()`, `createBooking()`, `updateProfile()`

```php
public function getUserBookings($userId)
{
    // ✅ Good - jelas dan deskriptif
}

public function get()
{
    // ❌ Bad - terlalu generic
}
```

#### Variables
- **camelCase** untuk variables
- Harus deskriptif
- Hindari singkatan yang tidak jelas
- Contoh: `$userData`, `$bookingList`, `$isActive`

```php
$userData = session('user_data');  // ✅ Good
$ud = session('user_data');       // ❌ Bad - tidak jelas
```

#### Constants
- **UPPER_SNAKE_CASE** untuk constants
- Contoh: `MAX_FILE_SIZE`, `DEFAULT_PAGE_SIZE`

```php
const MAX_FILE_SIZE = 2048;  // 2MB in KB
const DEFAULT_PAGE_SIZE = 10;
```

### 2.3. Controller Standards

#### Structure
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Method description
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function methodName(Request $request)
    {
        try {
            // Validation
            $validated = $request->validate([
                'field' => 'required|string|max:255',
            ]);
            
            // Business logic
            $result = $this->processData($validated);
            
            // Response
            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in methodName', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses permintaan.',
            ], 500);
        }
    }
}
```

#### Best Practices

1. **Single Responsibility**: Setiap controller method harus memiliki satu tanggung jawab
2. **Error Handling**: Selalu wrap business logic dalam try-catch
3. **Logging**: Log semua error dengan context yang jelas
4. **Validation**: Validasi input di awal method
5. **Eager Loading**: Gunakan eager loading untuk menghindari N+1 queries

```php
// ✅ Good - Eager loading
$bookings = Booking::with(['meetingRoom', 'user'])->get();

// ❌ Bad - N+1 queries
$bookings = Booking::all();
foreach ($bookings as $booking) {
    echo $booking->meetingRoom->name; // Query setiap loop
}
```

### 2.4. Model Standards

#### Structure
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'meeting_room_id',
        'title',
        'description',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Relationship: Booking belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Get confirmed bookings
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Accessor: Get formatted status
     */
    public function getStatusTextAttribute()
    {
        return ucfirst($this->status);
    }
}
```

#### Best Practices

1. **Fillable vs Guarded**: Gunakan `$fillable`, hindari `$guarded` kecuali perlu
2. **Relationships**: Definisikan semua relationships di model
3. **Scopes**: Gunakan query scopes untuk query yang sering digunakan
4. **Accessors/Mutators**: Gunakan untuk format data
5. **Events**: Gunakan model events untuk side effects

### 2.5. Validation

#### Validation Rules
```php
// ✅ Good - Validation rules di request class atau controller
$validated = $request->validate([
    'email' => 'required|email|max:255|unique:users,email',
    'phone' => 'nullable|string|max:20',
    'unit_kerja' => 'required|string|max:255',
]);

// ❌ Bad - Validasi manual tanpa rules
if (empty($request->email)) {
    // ...
}
```

#### Custom Validation Messages
```php
$request->validate([
    'email' => 'required|email',
], [
    'email.required' => 'Alamat email wajib diisi.',
    'email.email' => 'Format email tidak valid.',
]);
```

### 2.6. Error Handling & Logging

```php
try {
    $booking = Booking::create($data);
    Log::info('Booking created successfully', [
        'booking_id' => $booking->id,
        'user_id' => $booking->user_id,
    ]);
} catch (\Exception $e) {
    Log::error('Failed to create booking', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
        'data' => $data,
    ]);
    
    throw new \Exception('Gagal membuat booking. Silakan coba lagi.');
}
```

---

## 3. JavaScript Standards

### 3.1. Code Style

#### ES6+ Features
```javascript
// ✅ Good - Arrow functions, const/let
const getUserBookings = async (userId) => {
    const response = await fetch(`/api/users/${userId}/bookings`);
    return response.json();
};

// ❌ Bad - Old JavaScript
var getUserBookings = function(userId) {
    // ...
};
```

#### Variable Declarations
- Gunakan `const` untuk nilai yang tidak berubah
- Gunakan `let` untuk nilai yang berubah
- Hindari `var`

```javascript
const API_URL = '/api/bookings';  // ✅ Constant
let currentBookingId = null;      // ✅ Mutable
var globalVar = 'value';           // ❌ Avoid
```

### 3.2. Naming Conventions

#### Variables & Functions
- **camelCase** untuk variables dan functions
- Contoh: `getUserData()`, `bookingList`, `isActive`

#### Constants
- **UPPER_SNAKE_CASE** untuk constants
- Contoh: `API_BASE_URL`, `MAX_RETRIES`

```javascript
const API_BASE_URL = 'https://api.example.com';
const MAX_RETRIES = 3;
```

#### Classes
- **PascalCase** untuk class names
- Contoh: `BookingManager`, `NotificationService`

### 3.3. Async/Await

```javascript
// ✅ Good - Async/await
async function fetchBookings() {
    try {
        const response = await fetch('/api/bookings');
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching bookings:', error);
        throw error;
    }
}

// ❌ Bad - Callbacks
function fetchBookings(callback) {
    fetch('/api/bookings')
        .then(response => response.json())
        .then(data => callback(data))
        .catch(error => callback(null, error));
}
```

### 3.4. DOM Manipulation

```javascript
// ✅ Good - Modern DOM API
document.addEventListener('DOMContentLoaded', function() {
    const submitBtn = document.getElementById('submit-btn');
    if (submitBtn) {
        submitBtn.addEventListener('click', handleSubmit);
    }
});

// ❌ Bad - jQuery (tidak digunakan di project ini)
$('#submit-btn').click(function() {
    // ...
});
```

### 3.5. Event Handling

```javascript
// ✅ Good - Event delegation untuk dynamic content
document.addEventListener('click', function(e) {
    if (e.target.matches('.booking-item')) {
        handleBookingClick(e.target);
    }
});

// ✅ Good - Debouncing untuk frequent events
let debounceTimer;
input.addEventListener('input', function() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        handleInputChange(this.value);
    }, 300);
});
```

### 3.6. Error Handling

```javascript
async function loadBookingData(bookingId) {
    try {
        const response = await fetch(`/api/bookings/${bookingId}`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error loading booking:', error);
        showErrorMessage('Gagal memuat data booking. Silakan coba lagi.');
        return null;
    }
}
```

---

## 4. CSS & Styling Standards

### 4.1. Tailwind CSS

Proyek ini menggunakan **Tailwind CSS** sebagai primary styling framework.

#### Utility Classes
```html
<!-- ✅ Good - Tailwind utility classes -->
<div class="bg-white rounded-lg shadow-lg p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Title</h2>
</div>

<!-- ❌ Bad - Inline styles -->
<div style="background: white; border-radius: 8px; padding: 24px;">
    <h2 style="font-size: 24px; font-weight: bold;">Title</h2>
</div>
```

#### Responsive Design
```html
<!-- Mobile-first approach -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <!-- Content -->
</div>
```

### 4.2. Custom Components

#### Glass Effect
```html
<!-- ✅ Standard glass-effect component -->
<div class="glass-effect rounded-2xl p-8 shadow-2xl">
    <!-- Content -->
</div>
```

#### Gradient Background
```html
<!-- ✅ Standard gradient background -->
<body class="gradient-bg min-h-screen">
    <!-- Content -->
</body>
```

### 4.3. Custom CSS

Custom styles harus ditulis di `resources/css/app.css` menggunakan Tailwind's `@layer` directive:

```css
@layer components {
    .btn-primary {
        @apply bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors duration-300;
    }
    
    .glass-effect {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
}
```

### 4.4. Color Scheme

#### Primary Colors
- **Purple Gradient**: `linear-gradient(135deg, #667eea 0%, #764ba2 100%)`
- **Blue**: `#667eea` (primary actions)
- **Green**: `#10b981` (success states)
- **Red**: `#ef4444` (error/danger states)
- **Yellow**: `#f59e0b` (warning states)

#### Usage
```html
<!-- Success state -->
<div class="bg-green-500 text-white">Success</div>

<!-- Error state -->
<div class="bg-red-500 text-white">Error</div>

<!-- Warning state -->
<div class="bg-yellow-500 text-white">Warning</div>
```

---

## 5. File Structure & Naming Conventions

### 5.1. Directory Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AdminController.php
│   │   ├── AuthController.php
│   │   └── UserController.php
│   ├── Middleware/
│   │   ├── AdminAuth.php
│   │   └── UserAuth.php
│   └── Requests/
├── Models/
│   ├── User.php
│   ├── Booking.php
│   └── MeetingRoom.php
├── Mail/
├── Services/
└── Helpers/

resources/
├── views/
│   ├── admin/
│   ├── user/
│   ├── auth/
│   └── components/
├── css/
│   └── app.css
└── js/
    └── app.js

routes/
└── web.php

database/
├── migrations/
└── seeders/
```

### 5.2. File Naming

#### PHP Files
- **PascalCase** untuk class files
- Contoh: `UserController.php`, `BookingService.php`

#### Blade Templates
- **kebab-case** untuk view files
- Contoh: `user-dashboard.blade.php`, `create-booking.blade.php`

#### JavaScript Files
- **kebab-case** untuk JS files
- Contoh: `booking-manager.js`, `form-validator.js`

#### CSS Files
- **kebab-case** untuk CSS files
- Contoh: `app.css`, `admin-dashboard.css`

### 5.3. Component Naming

```html
<!-- Blade components -->
@include('components.mobile-sidebar')
@include('components.seo-meta')
@include('components.whatsapp-float')

<!-- File structure -->
resources/views/components/
├── mobile-sidebar.blade.php
├── seo-meta.blade.php
└── whatsapp-float.blade.php
```

---

## 6. Database Standards

### 6.1. Table Naming

- **snake_case** untuk table names
- Plural nouns
- Contoh: `users`, `meeting_rooms`, `bookings`

### 6.2. Column Naming

- **snake_case** untuk column names
- Deskriptif dan jelas
- Contoh: `user_id`, `start_time`, `unit_kerja`

### 6.3. Foreign Keys

- Naming: `{referenced_table}_id`
- Contoh: `user_id`, `meeting_room_id`

### 6.4. Indexes

- Index pada foreign keys
- Index pada columns yang sering di-query
- Composite indexes untuk multi-column queries

```php
Schema::table('bookings', function (Blueprint $table) {
    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    $table->index(['status', 'start_time']);
});
```

### 6.5. Migrations

```php
// ✅ Good - Descriptive migration name
2024_01_15_100000_create_bookings_table.php

// ❌ Bad - Generic name
2024_01_15_100000_create_table.php
```

---

## 7. Security Standards

### 7.1. Input Validation

```php
// ✅ Good - Always validate input
$validated = $request->validate([
    'email' => 'required|email|max:255',
    'password' => 'required|min:8',
]);

// ❌ Bad - No validation
$email = $request->input('email');
```

### 7.2. SQL Injection Prevention

```php
// ✅ Good - Use Eloquent or Query Builder
Booking::where('user_id', $userId)->get();

// ❌ Bad - Raw SQL
DB::select("SELECT * FROM bookings WHERE user_id = $userId");
```

### 7.3. XSS Prevention

```php
// ✅ Good - Blade automatically escapes
{{ $user->name }}

// ✅ Good - Use {!! !!} only for trusted HTML
{!! $sanitizedHtml !!}

// ❌ Bad - Unescaped output
echo $user->name;
```

### 7.4. CSRF Protection

```html
<!-- ✅ Good - Include CSRF token -->
<form method="POST">
    @csrf
    <!-- Form fields -->
</form>

<!-- ✅ Good - Include in AJAX -->
<meta name="csrf-token" content="{{ csrf_token() }}">
```

### 7.5. Authentication & Authorization

```php
// ✅ Good - Check authentication
if (!session('user_logged_in')) {
    return redirect()->route('login');
}

// ✅ Good - Role-based authorization
if ($user['role'] !== 'admin') {
    abort(403, 'Unauthorized access');
}
```

---

## 8. Documentation Standards

### 8.1. PHP DocBlocks

```php
/**
 * Get user bookings with pagination
 * 
 * @param int $userId
 * @param int $page
 * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
 */
public function getUserBookings($userId, $page = 1)
{
    // Implementation
}
```

### 8.2. Function Comments

```php
// Calculate booking duration in hours
// @param Carbon $startTime
// @param Carbon $endTime
// @return float Duration in hours
function calculateDuration($startTime, $endTime)
{
    // Implementation
}
```

### 8.3. Inline Comments

```php
// ✅ Good - Explain WHY, not WHAT
// Check if booking overlaps with existing bookings
// This prevents double booking in the same time slot
if ($this->hasOverlappingBooking($booking)) {
    // ...
}

// ❌ Bad - Obvious comment
// Get user data
$user = User::find($userId);
```

---

## 9. Testing Standards

### 9.1. Unit Tests

```php
class BookingTest extends TestCase
{
    public function test_can_create_booking()
    {
        $user = User::factory()->create();
        
        $booking = Booking::create([
            'user_id' => $user->id,
            'title' => 'Test Booking',
        ]);
        
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'title' => 'Test Booking',
        ]);
    }
}
```

### 9.2. Feature Tests

```php
class BookingFeatureTest extends TestCase
{
    public function test_user_can_create_booking()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->post('/user/bookings', [
                'title' => 'Test Booking',
                // ... other fields
            ]);
        
        $response->assertStatus(200);
        $this->assertDatabaseHas('bookings', [
            'title' => 'Test Booking',
        ]);
    }
}
```

---

## 10. Commit Message Standards

### Format
```
Type: Short description (50 chars max)

Longer description if needed (wrap at 72 chars)

- Bullet points for multiple changes
- Use present tense ("add" not "added")
- Use imperative mood ("move" not "moves")
```

### Types
- **feat**: New feature
- **fix**: Bug fix
- **docs**: Documentation changes
- **style**: Code style changes (formatting, etc.)
- **refactor**: Code refactoring
- **test**: Adding/updating tests
- **chore**: Maintenance tasks

### Examples
```
feat: Add booking cancellation feature

- Add cancelBooking method in UserController
- Add cancellation_reason field to bookings table
- Update booking status handling

fix: Resolve calendar display issue for selected month

- Fix month parameter parsing in dashboard method
- Normalize month string to YYYY-MM-01 format
```

---

## 11. Best Practices Summary

1. ✅ **Follow PSR-12** untuk PHP code
2. ✅ **Use Eloquent** untuk database queries
3. ✅ **Validate all input** di controller
4. ✅ **Handle errors** dengan try-catch dan logging
5. ✅ **Use eager loading** untuk relationships
6. ✅ **Write descriptive names** untuk functions dan variables
7. ✅ **Document complex logic** dengan comments
8. ✅ **Use Tailwind CSS** untuk styling
9. ✅ **Mobile-first** responsive design
10. ✅ **Write tests** untuk critical features

---

**Last Updated**: 2025-01-XX
**Version**: 1.0.0

