<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\UserNotification;
use App\Models\Booking;

class NotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $notification;
    public $booking;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, UserNotification $notification)
    {
        $this->user = $user;
        $this->notification = $notification;
        $this->booking = $notification->booking;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->notification->title . ' - Sistem Pemesanan Ruang Meeting',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notification',
            with: [
                'user' => $this->user,
                'notification' => $this->notification,
                'booking' => $this->booking,
                'typeIcon' => $this->getTypeIcon(),
            ]
        );
    }

    /**
     * Get icon based on notification type
     */
    private function getTypeIcon(): string
    {
        return match($this->notification->type) {
            'booking_confirmed' => '✅',
            'booking_cancelled' => '❌',
            'booking_completed' => '✔️',
            'room_maintenance' => '🔧',
            'info' => 'ℹ️',
            'success' => '✅',
            'warning' => '⚠️',
            'error' => '❌',
            default => '📢'
        };
    }


    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

