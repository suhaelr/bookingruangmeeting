<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

// Test route
Route::get('/test', function () {
    return response()->json(['status' => 'ok', 'message' => 'Laravel is working']);
});

// Fallback route
Route::fallback(function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Registration Routes
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Password Reset Routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Email Verification Routes
Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail'])->name('email.verify');
Route::post('/resend-verification', [AuthController::class, 'resendVerification'])->name('verification.resend');

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
});

// Legacy route for backward compatibility
Route::get('/dashboard', function () {
    $user = session('user_data');
    return $user['role'] === 'admin' 
        ? redirect()->route('admin.dashboard')
        : redirect()->route('user.dashboard');
})->middleware('admin.auth')->name('dashboard');
