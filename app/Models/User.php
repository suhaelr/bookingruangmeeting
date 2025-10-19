<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Disable model caching
     */
    public $timestamps = true;
    protected $rememberTokenName = false;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'full_name',
        'phone',
        'unit_kerja',
        'role',
        'avatar',
        'last_login_at',
        'email_verified_at',
        'email_verification_token',
        'google_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function notifications()
    {
        return $this->hasMany(UserNotification::class);
    }

    public function unreadNotifications()
    {
        return $this->notifications()->where('is_read', false);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }

    public function getActiveBookings()
    {
        return $this->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->get();
    }

    public function getBookingStats()
    {
        return [
            'total' => $this->bookings()->count(),
            'pending' => $this->bookings()->where('status', 'pending')->count(),
            'confirmed' => $this->bookings()->where('status', 'confirmed')->count(),
            'cancelled' => $this->bookings()->where('status', 'cancelled')->count(),
            'this_month' => $this->bookings()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
    }
}