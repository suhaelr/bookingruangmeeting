<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class UserNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'booking_id',
        'type',
        'title',
        'message',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'user_id' => 'integer',
        'booking_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function markAsUnread()
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    public static function createNotification($userId, $type, $title, $message, $bookingId = null)
    {
        $notification = self::create([
            'user_id' => $userId,
            'booking_id' => $bookingId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
        ]);

        // Send email notification
        try {
            $user = \App\Models\User::find($userId);
            if ($user && $user->email) {
                Mail::to($user->email)->send(new \App\Mail\NotificationEmail($user, $notification));

                Log::info('Notification email sent', [
                    'user_id' => $userId,
                    'user_email' => $user->email,
                    'notification_id' => $notification->id,
                    'type' => $type
                ]);
            }
        } catch (\Exception $e) {
            // Log error but don't fail notification creation
            Log::error('Failed to send notification email', [
                'user_id' => $userId,
                'notification_id' => $notification->id,
                'error' => $e->getMessage()
            ]);
        }

        return $notification;
    }
}
