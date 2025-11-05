<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Mail\BookingReminderMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendBookingReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails for confirmed bookings (1 hour, 30 minutes, 15 minutes before)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting booking reminder process...');
        
        $now = now();
        $sentCount = 0;
        $failedCount = 0;
        
        // Get all confirmed bookings that haven't started yet
        $upcomingBookings = Booking::with(['user', 'meetingRoom'])
            ->where('status', 'confirmed')
            ->where('start_time', '>', $now)
            ->get();
        
        foreach ($upcomingBookings as $booking) {
            try {
                $minutesUntil = $now->diffInMinutes($booking->start_time, false);
                
                // Reminder 1 jam (60 menit) sebelum meeting
                if ($minutesUntil >= 55 && $minutesUntil <= 65 && !$booking->reminder_1h_sent) {
                    $this->sendReminder($booking, 60, '1h');
                    $booking->reminder_1h_sent = true;
                    $booking->reminder_1h_sent_at = $now;
                    $booking->save();
                    $sentCount++;
                }
                // Reminder 30 menit sebelum meeting
                elseif ($minutesUntil >= 25 && $minutesUntil <= 35 && !$booking->reminder_30m_sent) {
                    $this->sendReminder($booking, 30, '30m');
                    $booking->reminder_30m_sent = true;
                    $booking->reminder_30m_sent_at = $now;
                    $booking->save();
                    $sentCount++;
                }
                // Reminder 15 menit sebelum meeting
                elseif ($minutesUntil >= 10 && $minutesUntil <= 20 && !$booking->reminder_15m_sent) {
                    $this->sendReminder($booking, 15, '15m');
                    $booking->reminder_15m_sent = true;
                    $booking->reminder_15m_sent_at = $now;
                    $booking->save();
                    $sentCount++;
                }
            } catch (\Exception $e) {
                $failedCount++;
                $this->error("Failed to send reminder for booking {$booking->id}: " . $e->getMessage());
                
                \Log::error('Failed to send booking reminder', [
                    'booking_id' => $booking->id,
                    'user_id' => $booking->user_id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        $this->info("Reminder process completed. Sent: {$sentCount}, Failed: {$failedCount}");
        
        return Command::SUCCESS;
    }
    
    /**
     * Send reminder email to user
     */
    private function sendReminder(Booking $booking, int $minutesUntil, string $reminderType): void
    {
        Mail::to($booking->user->email)->send(new BookingReminderMail($booking, $minutesUntil));
        
        $this->info("Reminder ({$reminderType}) sent for booking {$booking->id} to {$booking->user->email}");
        
        \Log::info('Booking reminder sent', [
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
            'user_email' => $booking->user->email,
            'start_time' => $booking->start_time,
            'reminder_type' => $reminderType,
            'minutes_until' => $minutesUntil,
            'sent_at' => now()
        ]);
    }
}
