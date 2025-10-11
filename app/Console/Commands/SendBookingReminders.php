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
    protected $description = 'Send 30-minute reminder emails for confirmed bookings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting booking reminder process...');
        
        // Find bookings that start in 30 minutes and are confirmed
        $reminderTime = now()->addMinutes(30);
        $startOfHour = $reminderTime->copy()->startOfHour();
        $endOfHour = $reminderTime->copy()->endOfHour();
        
        $bookingsToRemind = Booking::with(['user', 'meetingRoom'])
            ->where('status', 'confirmed')
            ->whereBetween('start_time', [$startOfHour, $endOfHour])
            ->get();
        
        $sentCount = 0;
        $failedCount = 0;
        
        foreach ($bookingsToRemind as $booking) {
            try {
                // Check if reminder was already sent (you might want to add a field to track this)
                // For now, we'll send reminders for all bookings in the time range
                
                Mail::to($booking->user->email)->send(new BookingReminderMail($booking));
                $sentCount++;
                
                $this->info("Reminder sent for booking {$booking->id} to {$booking->user->email}");
                
                \Log::info('Booking reminder sent', [
                    'booking_id' => $booking->id,
                    'user_id' => $booking->user_id,
                    'user_email' => $booking->user->email,
                    'start_time' => $booking->start_time,
                    'sent_at' => now()
                ]);
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
}
