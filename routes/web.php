<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\RateLimitMiddleware;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

// Test route
Route::get('/test', function () {
    return response()->json(['status' => 'ok', 'message' => 'Laravel is working']);
});

// CSS and JS asset routes
Route::get('/build/assets/{file}', function ($file) {
    $path = public_path("build/assets/{$file}");
    
    if (!file_exists($path)) {
        abort(404);
    }
    
    $mimeType = pathinfo($file, PATHINFO_EXTENSION) === 'css' 
        ? 'text/css' 
        : 'application/javascript';
    
    return response()->file($path, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('file', '.*');

// Fallback route
Route::fallback(function () {
    return redirect()->route('login');
});

// Authentication Routes with rate limiting
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware(RateLimitMiddleware::class);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Registration Routes with rate limiting
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware(RateLimitMiddleware::class);

// Password Reset Routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Email Verification Routes
Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail'])->name('email.verify');
Route::post('/resend-verification', [AuthController::class, 'resendVerification'])->name('verification.resend');

// Google OAuth Routes with Cloudflare bypass
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google')->middleware('cloudflare.bypass');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback')->middleware('cloudflare.bypass');
Route::post('/auth/google/revoke', [AuthController::class, 'revokeGoogleToken'])->name('auth.google.revoke');

// Debug route for session checking
Route::get('/debug/session', function() {
    return response()->json([
        'session_id' => session()->getId(),
        'user_logged_in' => session('user_logged_in'),
        'user_data' => session('user_data'),
        'all_session' => session()->all(),
        'csrf_token' => csrf_token(),
        'request_url' => request()->url(),
        'request_method' => request()->method()
    ]);
})->name('debug.session');

// Debug route for Google OAuth callback
Route::get('/debug/oauth', function() {
    return response()->json([
        'session_id' => session()->getId(),
        'user_logged_in' => session('user_logged_in'),
        'user_data' => session('user_data'),
        'google_oauth_state' => session('google_oauth_state'),
        'google_refresh_token' => session('google_refresh_token'),
        'all_session' => session()->all()
    ]);
})->name('debug.oauth');

// Test OAuth callback route
Route::get('/test/oauth', function() {
    return response()->json([
        'message' => 'OAuth test endpoint working',
        'timestamp' => now(),
        'session_id' => session()->getId()
    ]);
})->name('test.oauth');

// OAuth callback debug route
Route::get('/oauth/debug', function(Request $request) {
    return response()->json([
        'message' => 'OAuth callback debug endpoint',
        'timestamp' => now(),
        'session_id' => session()->getId(),
        'request_data' => $request->all(),
        'headers' => $request->headers->all(),
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent(),
        'url' => $request->url(),
        'method' => $request->method()
    ]);
})->name('oauth.debug');

// User Management Routes (Admin only)
Route::middleware(['admin.auth', 'web'])->group(function () {
    Route::get('/admin/users/api', function() {
        \Log::info('Route /admin/users/api called', [
            'timestamp' => now(),
            'session_id' => session()->getId()
        ]);
        return app(AuthController::class)->getAllUsers();
    })->name('admin.users.api');
    Route::put('/admin/users/{userId}/role', [AuthController::class, 'updateUserRole'])->name('admin.users.role.update');
});

// Privacy Policy Route
Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy.policy');

// Terms of Service Route
Route::get('/terms-of-service', function () {
    return view('terms-of-service');
})->name('terms.service');

// SEO Routes
Route::get('/sitemap.xml', [App\Http\Controllers\SeoController::class, 'generateSitemap'])->name('sitemap');
Route::get('/robots.txt', [App\Http\Controllers\SeoController::class, 'generateRobots'])->name('robots');

// Admin Routes
Route::prefix('admin')->middleware('admin.auth')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // User Management
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{id}/delete', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    
    // Room Management
    Route::get('/rooms', [AdminController::class, 'rooms'])->name('admin.rooms');
    Route::get('/rooms/create', [AdminController::class, 'createRoom'])->name('admin.rooms.create');
    Route::post('/rooms', [AdminController::class, 'storeRoom'])->name('admin.rooms.store');
    Route::put('/rooms/{id}', [AdminController::class, 'updateRoom'])->name('admin.rooms.update');
    Route::delete('/rooms/{id}', [AdminController::class, 'deleteRoom'])->name('admin.rooms.delete');
    
    // Booking Management
    Route::get('/bookings', [AdminController::class, 'bookings'])->name('admin.bookings');
    Route::post('/bookings/{id}/status', [AdminController::class, 'updateBookingStatus'])->name('admin.bookings.status');
    Route::get('/bookings/{id}/download', [AdminController::class, 'downloadDokumenPerizinan'])->name('admin.bookings.download');
    Route::get('/notifications', [AdminController::class, 'getNotifications'])->name('admin.notifications');
    Route::delete('/notifications/clear', [AdminController::class, 'clearAllNotifications'])->name('admin.notifications.clear');
});

// User Routes
Route::prefix('user')->middleware('user.auth')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
    Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
    Route::post('/profile', [UserController::class, 'updateProfile'])->name('user.profile.update');
    Route::post('/change-password', [UserController::class, 'changePassword'])->name('user.change-password');
    Route::post('/notification-settings', [UserController::class, 'updateNotificationSettings'])->name('user.notification-settings');
    Route::get('/bookings', [UserController::class, 'bookings'])->name('user.bookings');
    Route::get('/bookings/create', [UserController::class, 'createBooking'])->name('user.bookings.create');
    Route::post('/bookings', [UserController::class, 'storeBooking'])->name('user.bookings.store');
    Route::put('/bookings/{id}', [UserController::class, 'updateBooking'])->name('user.bookings.update');
    Route::post('/bookings/{id}/cancel', [UserController::class, 'cancelBooking'])->name('user.bookings.cancel');
    Route::post('/check-availability', [UserController::class, 'checkAvailability'])->name('user.check-availability');
    
    // Notification routes
    Route::get('/notifications', [UserController::class, 'notifications'])->name('user.notifications');
    Route::post('/notifications/{id}/mark-read', [UserController::class, 'markNotificationRead'])->name('user.notifications.mark-read');
    Route::post('/notifications/mark-all-read', [UserController::class, 'markAllNotificationsRead'])->name('user.notifications.mark-all-read');
});

// Legacy route for backward compatibility
Route::get('/dashboard', function () {
    $user = session('user_data');
    return $user['role'] === 'admin' 
        ? redirect()->route('admin.dashboard')
        : redirect()->route('user.dashboard');
})->middleware('admin.auth')->name('dashboard');
