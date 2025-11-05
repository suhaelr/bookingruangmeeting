<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;

class BookingReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $minutesUntil;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking, int $minutesUntil = 60)
    {
        $this->booking = $booking;
        $this->minutesUntil = $minutesUntil;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // Subject berbeda sesuai waktu tersisa
        $timeText = $this->getTimeText();
        return new Envelope(
            subject: 'Pengingat Meeting - ' . $this->booking->title . ' (' . $timeText . ')',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-reminder',
            with: [
                'booking' => $this->booking,
                'user' => $this->booking->user,
                'meetingRoom' => $this->booking->meetingRoom,
                'minutesUntil' => $this->minutesUntil,
                'timeText' => $this->getTimeText(),
            ]
        );
    }
    
    /**
     * Get human-readable time text
     */
    private function getTimeText(): string
    {
        if ($this->minutesUntil >= 60) {
            $hours = floor($this->minutesUntil / 60);
            return $hours . ' jam lagi';
        } else {
            return $this->minutesUntil . ' menit lagi';
        }
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
