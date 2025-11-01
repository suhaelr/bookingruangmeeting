# Security Standards - Sistem Pemesanan Ruang Meeting

## 📋 Daftar Isi

1. [Pengenalan](#pengenalan)
2. [Security Principles](#security-principles)
3. [Authentication & Authorization](#authentication--authorization)
4. [Input Validation & Sanitization](#input-validation--sanitization)
5. [SQL Injection Prevention](#sql-injection-prevention)
6. [XSS (Cross-Site Scripting) Prevention](#xss-cross-site-scripting-prevention)
7. [CSRF Protection](#csrf-protection)
8. [Session Security](#session-security)
9. [Password Security](#password-security)
10. [File Upload Security](#file-upload-security)
11. [Security Headers](#security-headers)
12. [Rate Limiting](#rate-limiting)
13. [Error Handling & Information Disclosure](#error-handling--information-disclosure)
14. [Database Security](#database-security)
15. [API Security](#api-security)
16. [Logging & Monitoring](#logging--monitoring)
17. [Dependency Security](#dependency-security)
18. [SSL/TLS & HTTPS](#ssltls--https)
19. [Security Checklist](#security-checklist)
20. [Incident Response](#incident-response)

---

## 1. Pengenalan

Dokumen ini menjelaskan standar keamanan yang harus diikuti oleh semua developer yang bekerja pada **Sistem Pemesanan Ruang Meeting**. Standar ini dibuat untuk memastikan aplikasi aman dari berbagai serangan cyber dan memenuhi best practices keamanan web application.

### Scope
- Authentication & Authorization
- Input Validation & Sanitization
- SQL Injection Prevention
- XSS Prevention
- CSRF Protection
- Session Management
- Password Security
- File Upload Security
- Security Headers
- Rate Limiting
- Error Handling
- Database Security
- API Security
- Logging & Monitoring

---

## 2. Security Principles

### 2.1. Principle of Least Privilege

Setiap user hanya memiliki akses minimum yang diperlukan untuk menjalankan tugasnya.

```php
// ✅ Good - Role-based access control
if ($user['role'] !== 'admin') {
    abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
}

// ❌ Bad - No authorization check
public function deleteUser($id) {
    User::destroy($id); // Anyone can delete users
}
```

### 2.2. Defense in Depth

Implementasi multiple layers of security untuk melindungi aplikasi.

```php
// Layer 1: Middleware authentication
Route::middleware(['user.auth'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard']);
});

// Layer 2: Controller authorization check
public function dashboard(Request $request) {
    $user = session('user_data');
    if (!$user || $user['role'] !== 'user') {
        abort(403);
    }
    // ...
}

// Layer 3: Database-level constraints
Schema::table('bookings', function (Blueprint $table) {
    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
});
```

### 2.3. Fail Secure

Sistem harus gagal dalam kondisi yang aman, default ke deny access.

```php
// ✅ Good - Default deny
public function authorize($action) {
    if (!in_array($action, $this->allowedActions)) {
        return false; // Default deny
    }
    return $this->checkPermission($action);
}

// ❌ Bad - Default allow
public function authorize($action) {
    if ($this->isAdmin()) {
        return true; // Risky if check fails
    }
    return false;
}
```

### 2.4. Secure by Default

Semua fitur baru harus secure by default, tidak memerlukan konfigurasi khusus untuk mengaktifkan security.

---

## 3. Authentication & Authorization

### 3.1. Authentication Standards

#### Session-Based Authentication

```php
// ✅ Good - Secure session management
public function login(Request $request) {
    $user = User::where('email', $request->email)->first();
    
    if ($user && Hash::check($request->password, $user->password)) {
        Session::regenerate(true); // Regenerate session ID
        Session::put('user_logged_in', true);
        Session::put('user_data', [
            'id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
        ]);
        
        Log::info('User logged in', [
            'user_id' => $user->id,
            'ip' => $request->ip(),
        ]);
        
        return redirect()->route('user.dashboard');
    }
    
    return back()->withErrors(['email' => 'Email atau password salah.']);
}
```

#### Password Verification

```php
// ✅ Good - Use Hash::check()
if (!Hash::check($request->password, $user->password)) {
    return back()->withErrors(['password' => 'Password salah.']);
}

// ❌ Bad - Plain text comparison
if ($request->password !== $user->password) {
    // Never do this!
}
```

#### Middleware Authentication

```php
// ✅ Good - Middleware authentication check
class UserAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!Session::has('user_logged_in') || !Session::get('user_logged_in')) {
            Log::warning('Unauthorized access attempt', [
                'url' => $request->url(),
                'ip' => $request->ip(),
            ]);
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu!');
        }
        
        return $next($request);
    }
}
```

### 3.2. Authorization Standards

#### Role-Based Access Control (RBAC)

```php
// ✅ Good - Role check in middleware
class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!Session::has('user_logged_in') || !Session::get('user_logged_in')) {
            return redirect()->route('login');
        }
        
        $user = Session::get('user_data');
        
        // Validate user exists in database
        $userModel = User::find($user['id']);
        if (!$userModel || $userModel->role !== 'admin') {
            Log::warning('Unauthorized admin access attempt', [
                'user_id' => $user['id'] ?? null,
                'url' => $request->url(),
                'ip' => $request->ip(),
            ]);
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }
        
        return $next($request);
    }
}
```

#### Resource Ownership Check

```php
// ✅ Good - Check ownership before operations
public function updateBooking(Request $request, $id)
{
    $booking = Booking::findOrFail($id);
    $user = session('user_data');
    
    // Check ownership or admin role
    if ($booking->user_id !== $user['id'] && $user['role'] !== 'admin') {
        abort(403, 'Anda tidak memiliki izin untuk mengedit booking ini.');
    }
    
    // Update booking
    $booking->update($request->validated());
    
    return redirect()->back()->with('success', 'Booking berhasil diperbarui.');
}

// ❌ Bad - No ownership check
public function updateBooking(Request $request, $id)
{
    $booking = Booking::findOrFail($id);
    $booking->update($request->all()); // Anyone can update any booking
}
```

---

## 4. Input Validation & Sanitization

### 4.1. Validation Standards

#### Laravel Validation Rules

```php
// ✅ Good - Validate at controller level
public function createBooking(Request $request)
{
    $validated = $request->validate([
        'meeting_room_id' => 'required|exists:meeting_rooms,id',
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'start_time' => 'required|date|after:now',
        'end_time' => 'required|date|after:start_time',
        'unit_kerja' => 'required|string|max:255',
        'captcha_answer' => 'required|string|size:4',
    ], [
        'title.required' => 'Judul wajib diisi.',
        'start_time.after' => 'Waktu mulai harus setelah waktu sekarang.',
        'end_time.after' => 'Waktu selesai harus setelah waktu mulai.',
    ]);
    
    // Process validated data
}

// ❌ Bad - No validation
public function createBooking(Request $request)
{
    Booking::create($request->all()); // Dangerous!
}
```

#### Input Sanitization Middleware

```php
// ✅ Good - Global input sanitization
class InputValidationMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $input = $request->all();
        $sanitized = $this->sanitizeInput($input);
        $request->replace($sanitized);
        
        return $next($request);
    }
    
    private function sanitizeInput($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeInput'], $data);
        }
        
        if (is_string($data)) {
            // Remove potential SQL injection patterns
            $data = preg_replace('/(\b(SELECT|INSERT|UPDATE|DELETE|DROP|CREATE|ALTER|EXEC|UNION|SCRIPT)\b)/i', '', $data);
            // Remove potential XSS patterns
            $data = strip_tags($data);
            // Escape special characters
            $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        }
        
        return $data;
    }
}
```

### 4.2. Data Type Validation

```php
// ✅ Good - Validate data types
$request->validate([
    'user_id' => 'required|integer|exists:users,id',
    'price' => 'required|numeric|min:0|max:999999',
    'email' => 'required|email|max:255',
    'phone' => 'nullable|string|regex:/^[0-9+\-\s()]+$/',
    'start_time' => 'required|date|date_format:Y-m-d H:i:s',
]);
```

### 4.3. Length Validation

```php
// ✅ Good - Enforce length limits
$request->validate([
    'title' => 'required|string|min:3|max:255',
    'description' => 'nullable|string|max:1000',
    'unit_kerja' => 'required|string|max:255',
]);

// Prevent DoS attacks with excessive input
if (strlen($request->input('description')) > 10000) {
    return back()->withErrors(['description' => 'Deskripsi terlalu panjang.']);
}
```

---

## 5. SQL Injection Prevention

### 5.1. Use Eloquent ORM

```php
// ✅ Good - Eloquent ORM (safe)
$bookings = Booking::where('user_id', $userId)
    ->where('status', 'confirmed')
    ->get();

// ✅ Good - Query Builder (safe)
$bookings = DB::table('bookings')
    ->where('user_id', $userId)
    ->where('status', 'confirmed')
    ->get();

// ❌ Bad - Raw SQL (vulnerable to SQL injection)
$bookings = DB::select("SELECT * FROM bookings WHERE user_id = $userId");
```

### 5.2. Parameterized Queries

```php
// ✅ Good - Parameter binding
DB::select('SELECT * FROM bookings WHERE user_id = ? AND status = ?', [$userId, $status]);

// ❌ Bad - String concatenation
DB::select("SELECT * FROM bookings WHERE user_id = $userId AND status = '$status'");
```

### 5.3. Raw Queries (When Necessary)

```php
// ✅ Good - Use parameter binding even in raw queries
DB::raw('SELECT * FROM bookings WHERE user_id = ?', [$userId]);

// ❌ Bad - Never use user input directly in raw queries
DB::raw("SELECT * FROM bookings WHERE user_id = {$request->user_id}");
```

---

## 6. XSS (Cross-Site Scripting) Prevention

### 6.1. Blade Template Escaping

```php
// ✅ Good - Automatic escaping in Blade
{{ $user->name }} // Automatically escaped
{{ $booking->title }} // Automatically escaped

// ✅ Good - Raw output only for trusted HTML
{!! $sanitizedHtml !!} // Only use for trusted content

// ❌ Bad - Raw output of user input
{!! $user->description !!} // Dangerous if contains user input
```

### 6.2. JavaScript Escaping

```javascript
// ✅ Good - Escape user input in JavaScript
const userName = {{ Js::from($user->name) }};
const bookingTitle = @json($booking->title); // Laravel's @json helper escapes automatically

// ❌ Bad - Direct injection
const userName = "<?php echo $user->name; ?>"; // Vulnerable to XSS
```

### 6.3. HTML Purification

```php
// ✅ Good - Use HTML purifier for user-generated HTML
use HTMLPurifier;

$purifier = new HTMLPurifier();
$cleanHtml = $purifier->purify($userInput);
```

### 6.4. Content Security Policy (CSP)

```php
// ✅ Good - CSP headers in middleware
$csp = "default-src 'self'; " .
       "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net; " .
       "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; " .
       "img-src 'self' data: https:; " .
       "frame-ancestors 'none';";

$response->headers->set('Content-Security-Policy', $csp);
```

---

## 7. CSRF Protection

### 7.1. CSRF Token in Forms

```html
<!-- ✅ Good - Include CSRF token -->
<form method="POST" action="{{ route('user.bookings.store') }}">
    @csrf
    <!-- Form fields -->
</form>
```

### 7.2. CSRF Token in AJAX Requests

```html
<!-- ✅ Good - Include CSRF token meta tag -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
// ✅ Good - Include CSRF token in AJAX headers
fetch('/api/bookings', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify(data)
});
</script>
```

### 7.3. Verify CSRF Token

```php
// ✅ Good - Laravel automatically verifies CSRF tokens for POST/PUT/DELETE requests

// If you need to manually verify:
if (!Hash::check($request->header('X-CSRF-TOKEN'), session()->token())) {
    abort(403, 'Invalid CSRF token.');
}
```

---

## 8. Session Security

### 8.1. Session Configuration

```php
// config/session.php
'same_site' => 'strict', // Prevent CSRF
'secure' => env('SESSION_SECURE_COOKIE', true), // HTTPS only
'http_only' => true, // Prevent JavaScript access
'lifetime' => 120, // 2 hours
```

### 8.2. Session Regeneration

```php
// ✅ Good - Regenerate session ID after login
Session::regenerate(true);

// ✅ Good - Regenerate session ID periodically
if (Session::get('last_regeneration') < now()->subMinutes(30)) {
    Session::regenerate(true);
    Session::put('last_regeneration', now());
}
```

### 8.3. Session Fixation Prevention

```php
// ✅ Good - Regenerate session ID on authentication
public function login(Request $request) {
    // Clear old session data
    Session::flush();
    
    // Regenerate session ID
    Session::regenerate(true);
    
    // Set new session data
    Session::put('user_logged_in', true);
    Session::put('user_data', $userData);
}
```

### 8.4. Session Timeout

```php
// ✅ Good - Check session timeout
$lastActivity = Session::get('last_activity');
if ($lastActivity && $lastActivity < now()->subHours(2)) {
    Session::flush();
    return redirect()->route('login')->with('error', 'Session expired. Silakan login kembali.');
}

Session::put('last_activity', now());
```

---

## 9. Password Security

### 9.1. Password Hashing

```php
// ✅ Good - Use bcrypt (default Laravel hasher)
$user->password = Hash::make($password);

// ✅ Good - Check password
if (Hash::check($inputPassword, $user->password)) {
    // Password correct
}

// ❌ Bad - Never store plain text passwords
$user->password = $password; // Never do this!
```

### 9.2. Password Requirements

```php
// ✅ Good - Enforce strong passwords
$request->validate([
    'password' => [
        'required',
        'string',
        'min:8',              // Minimum 8 characters
        'regex:/[a-z]/',      // At least one lowercase
        'regex:/[A-Z]/',      // At least one uppercase
        'regex:/[0-9]/',      // At least one digit
        'regex:/[@$!%*#?&]/', // At least one special character
        'confirmed',          // Must match password_confirmation
    ],
], [
    'password.min' => 'Password harus minimal 8 karakter.',
    'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan karakter khusus.',
]);
```

### 9.3. Password Reset Security

```php
// ✅ Good - Secure password reset
public function resetPassword(Request $request)
{
    $request->validate([
        'token' => 'required',
        'email' => 'required|email|exists:users,email',
        'password' => 'required|string|min:8|confirmed',
    ]);
    
    // Verify token (store in database with expiration)
    $resetToken = PasswordReset::where('email', $request->email)
        ->where('token', Hash::make($request->token))
        ->where('created_at', '>', now()->subHours(1))
        ->first();
    
    if (!$resetToken) {
        return back()->withErrors(['token' => 'Token reset password tidak valid atau sudah kadaluarsa.']);
    }
    
    // Update password
    $user = User::where('email', $request->email)->first();
    $user->password = Hash::make($request->password);
    $user->save();
    
    // Delete reset token
    $resetToken->delete();
    
    return redirect()->route('login')->with('success', 'Password berhasil direset.');
}
```

---

## 10. File Upload Security

### 10.1. File Type Validation

```php
// ✅ Good - Validate file type and size
$request->validate([
    'dokumen_perizinan' => [
        'nullable',
        'file',
        'mimes:pdf',           // Only PDF files
        'max:2048',            // Max 2MB
    ],
]);

// Additional validation
$file = $request->file('dokumen_perizinan');
if ($file) {
    // Validate MIME type (not just extension)
    $allowedMimes = ['application/pdf'];
    if (!in_array($file->getMimeType(), $allowedMimes)) {
        return back()->withErrors(['dokumen_perizinan' => 'File harus berformat PDF.']);
    }
    
    // Check file size
    if ($file->getSize() > 2 * 1024 * 1024) { // 2MB
        return back()->withErrors(['dokumen_perizinan' => 'Ukuran file maksimal 2MB.']);
    }
}
```

### 10.2. File Storage Security

```php
// ✅ Good - Store files outside web root or with random names
$fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
$path = $file->storeAs('uploads/documents', $fileName, 'private'); // Outside public

// ❌ Bad - Store with original filename
$path = $file->storeAs('uploads', $file->getClientOriginalName()); // Dangerous
```

### 10.3. File Content Validation

```php
// ✅ Good - Scan uploaded files (if antivirus available)
// Or validate file structure
$pdf = new \Smalot\PdfParser\Parser();
try {
    $pdfDocument = $pdf->parseFile($file->getRealPath());
    // File is valid PDF
} catch (\Exception $e) {
    return back()->withErrors(['dokumen_perizinan' => 'File PDF tidak valid.']);
}
```

---

## 11. Security Headers

### 11.1. Security Headers Middleware

```php
// ✅ Good - Comprehensive security headers
class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'DENY');
        
        // XSS Protection (legacy browsers)
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Referrer policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Permissions policy
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        
        // Content Security Policy
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net; " .
               "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; " .
               "img-src 'self' data: https:; " .
               "frame-ancestors 'none';";
        $response->headers->set('Content-Security-Policy', $csp);
        
        // HSTS for HTTPS
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }
        
        return $response;
    }
}
```

### 11.2. Cache Control Headers

```php
// ✅ Good - Prevent caching of sensitive pages
$response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0');
$response->headers->set('Pragma', 'no-cache');
$response->headers->set('Expires', '0');
```

---

## 12. Rate Limiting

### 12.1. Rate Limiting Middleware

```php
// ✅ Good - Rate limiting per IP
class RateLimitMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();
        $key = 'rate_limit_' . $ip;
        $maxRequests = 100; // Maximum requests per minute
        $decayMinutes = 1;
        
        $currentRequests = Cache::get($key, 0);
        
        if ($currentRequests >= $maxRequests) {
            Log::warning('Rate limit exceeded', [
                'ip' => $ip,
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
            ]);
            
            return response()->json([
                'error' => 'Too Many Requests',
                'message' => 'Rate limit exceeded. Please try again later.',
                'retry_after' => 60
            ], 429);
        }
        
        Cache::put($key, $currentRequests + 1, now()->addMinutes($decayMinutes));
        
        return $next($request);
    }
}
```

### 12.2. Route-Level Rate Limiting

```php
// ✅ Good - Rate limit specific routes
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1'); // 5 attempts per minute

Route::post('/bookings', [UserController::class, 'store'])
    ->middleware(['user.auth', 'throttle:10,1']); // 10 attempts per minute
```

---

## 13. Error Handling & Information Disclosure

### 13.1. Error Messages

```php
// ✅ Good - Generic error messages in production
if (app()->environment('production')) {
    Log::error('Booking creation failed', [
        'user_id' => $user->id,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ]);
    
    return back()->withErrors(['error' => 'Terjadi kesalahan saat membuat booking. Silakan coba lagi.']);
} else {
    // Show detailed errors in development
    return back()->withErrors(['error' => $e->getMessage()]);
}

// ❌ Bad - Expose internal details
return back()->withErrors(['error' => "Database error: {$e->getMessage()}"]);
```

### 13.2. Exception Handling

```php
// ✅ Good - Proper exception handling
try {
    $booking = Booking::create($data);
} catch (\Illuminate\Database\QueryException $e) {
    Log::error('Database error', [
        'error' => $e->getMessage(),
        'code' => $e->getCode(),
    ]);
    
    if ($e->getCode() == 23000) { // Integrity constraint violation
        return back()->withErrors(['error' => 'Data yang dimasukkan tidak valid.']);
    }
    
    return back()->withErrors(['error' => 'Terjadi kesalahan database. Silakan coba lagi.']);
} catch (\Exception $e) {
    Log::error('Unexpected error', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ]);
    
    return back()->withErrors(['error' => 'Terjadi kesalahan. Silakan coba lagi.']);
}
```

---

## 14. Database Security

### 14.1. Database Credentials

```php
// ✅ Good - Store credentials in .env file
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=booking_system
DB_USERNAME=app_user
DB_PASSWORD=secure_password_here

// ❌ Bad - Hardcode credentials
$db = new PDO('mysql:host=localhost;dbname=booking_system', 'admin', 'password123');
```

### 14.2. Database User Permissions

```sql
-- ✅ Good - Limited permissions for application user
GRANT SELECT, INSERT, UPDATE, DELETE ON booking_system.* TO 'app_user'@'localhost';
FLUSH PRIVILEGES;

-- ❌ Bad - Full admin access
GRANT ALL PRIVILEGES ON *.* TO 'app_user'@'localhost';
```

### 14.3. Prepared Statements

```php
// ✅ Good - Always use prepared statements
DB::statement('INSERT INTO bookings (user_id, title) VALUES (?, ?)', [$userId, $title]);

// ❌ Bad - String concatenation
DB::statement("INSERT INTO bookings (user_id, title) VALUES ($userId, '$title')");
```

---

## 15. API Security

### 15.1. API Authentication

```php
// ✅ Good - Token-based authentication for API
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/api/bookings', [UserController::class, 'apiBookings']);
});
```

### 15.2. API Rate Limiting

```php
// ✅ Good - Rate limit API endpoints
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/api/bookings', [UserController::class, 'apiBookings']);
});
```

### 15.3. API Response Security

```php
// ✅ Good - Sanitize API responses
public function apiBookings()
{
    $bookings = Booking::where('user_id', auth()->id())->get();
    
    return response()->json([
        'success' => true,
        'data' => $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'title' => $booking->title,
                'start_time' => $booking->start_time,
                // Don't expose sensitive data
                // 'internal_notes' => $booking->internal_notes, // ❌ Don't expose
            ];
        }),
    ]);
}
```

---

## 16. Logging & Monitoring

### 16.1. Security Event Logging

```php
// ✅ Good - Log security events
Log::warning('Unauthorized access attempt', [
    'url' => $request->url(),
    'method' => $request->method(),
    'ip' => $request->ip(),
    'user_agent' => $request->userAgent(),
    'user_id' => $user['id'] ?? null,
]);

Log::info('User logged in', [
    'user_id' => $user->id,
    'ip' => $request->ip(),
    'timestamp' => now(),
]);

Log::error('Security violation', [
    'event' => 'SQL injection attempt',
    'input' => $request->all(),
    'ip' => $request->ip(),
]);
```

### 16.2. Log Monitoring

```php
// ✅ Good - Monitor suspicious activities
if ($failedAttempts > 5) {
    Log::critical('Multiple failed login attempts', [
        'ip' => $request->ip(),
        'attempts' => $failedAttempts,
        'timestamp' => now(),
    ]);
    
    // Block IP temporarily
    Cache::put('blocked_ip_' . $request->ip(), true, now()->addMinutes(30));
}
```

---

## 17. Dependency Security

### 17.1. Dependency Updates

```bash
# ✅ Good - Regularly update dependencies
composer update --no-dev
npm update

# Check for security vulnerabilities
composer audit
npm audit
```

### 17.2. Dependency Verification

```php
// ✅ Good - Verify dependencies are from trusted sources
// composer.json should only include trusted repositories
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://repo.packagist.org"
        }
    ]
}
```

---

## 18. SSL/TLS & HTTPS

### 18.1. Force HTTPS

```php
// ✅ Good - Force HTTPS in production
// AppServiceProvider.php
public function boot()
{
    if (app()->environment('production')) {
        URL::forceScheme('https');
    }
}

// .htaccess or server config
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### 18.2. HSTS Header

```php
// ✅ Good - HSTS header (already in SecurityHeadersMiddleware)
if ($request->secure()) {
    $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
}
```

---

## 19. Security Checklist

### Pre-Deployment Checklist

- [ ] All passwords are hashed with bcrypt
- [ ] All user inputs are validated and sanitized
- [ ] All database queries use prepared statements/Eloquent
- [ ] CSRF tokens are included in all forms
- [ ] XSS protection is enabled (CSP headers)
- [ ] Security headers are configured
- [ ] Session security is configured (http_only, secure, same_site)
- [ ] Authentication middleware is applied to protected routes
- [ ] Authorization checks are in place
- [ ] File uploads are validated (type, size, content)
- [ ] Rate limiting is enabled
- [ ] Error messages don't expose sensitive information
- [ ] Logging is enabled for security events
- [ ] Dependencies are up to date
- [ ] HTTPS is enforced
- [ ] Database credentials are in .env (not hardcoded)
- [ ] .env file is not in version control
- [ ] API endpoints are secured
- [ ] SQL injection prevention is verified
- [ ] XSS prevention is verified
- [ ] CSRF protection is verified

### Code Review Checklist

- [ ] No hardcoded credentials
- [ ] No SQL injection vulnerabilities
- [ ] No XSS vulnerabilities
- [ ] Proper error handling
- [ ] Input validation on all endpoints
- [ ] Authorization checks on all protected resources
- [ ] Secure session management
- [ ] Proper password handling
- [ ] Secure file uploads (if applicable)
- [ ] Rate limiting on sensitive endpoints

---

## 20. Incident Response

### 20.1. Security Incident Procedure

1. **Identify**: Detect security incident
2. **Contain**: Isolate affected systems
3. **Eradicate**: Remove threat
4. **Recover**: Restore systems
5. **Lessons Learned**: Document and improve

### 20.2. Incident Logging

```php
// ✅ Good - Log security incidents
Log::critical('Security incident detected', [
    'type' => 'SQL injection attempt',
    'ip' => $request->ip(),
    'user_agent' => $request->userAgent(),
    'url' => $request->fullUrl(),
    'payload' => $request->all(),
    'timestamp' => now(),
    'action_taken' => 'Request blocked',
]);
```

### 20.3. Incident Response Contacts

- **Security Team**: security@example.com
- **System Admin**: admin@example.com
- **Emergency**: +62-xxx-xxxx-xxxx

---

## 21. Best Practices Summary

1. ✅ **Always validate and sanitize user input**
2. ✅ **Use Eloquent ORM or prepared statements** for database queries
3. ✅ **Hash all passwords** with bcrypt
4. ✅ **Implement CSRF protection** on all forms
5. ✅ **Enable XSS protection** with CSP headers
6. ✅ **Configure security headers** properly
7. ✅ **Use secure session management** (http_only, secure, same_site)
8. ✅ **Implement rate limiting** on sensitive endpoints
9. ✅ **Check authorization** on all protected resources
10. ✅ **Log security events** for monitoring
11. ✅ **Handle errors** without exposing sensitive information
12. ✅ **Keep dependencies** up to date
13. ✅ **Force HTTPS** in production
14. ✅ **Regular security audits** and penetration testing

---

## 22. Security Tools & Resources

### Recommended Tools

- **Laravel Security Checker**: `composer require sensiolabs/security-checker`
- **PHP Security Audit**: Static code analysis
- **OWASP ZAP**: Web application security scanner
- **Burp Suite**: Web vulnerability scanner
- **Nmap**: Network security scanner

### Security Resources

- **OWASP Top 10**: https://owasp.org/www-project-top-ten/
- **Laravel Security**: https://laravel.com/docs/security
- **PHP Security**: https://www.php.net/manual/en/security.php
- **Web Security Best Practices**: https://developer.mozilla.org/en-US/docs/Web/Security

---

**Last Updated**: 2025-01-XX
**Version**: 1.0.0

**Note**: Security standards ini harus di-review dan di-update secara berkala mengikuti perkembangan threats dan best practices terbaru.

