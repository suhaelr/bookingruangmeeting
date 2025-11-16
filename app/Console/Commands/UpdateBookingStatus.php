<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;

class UpdateBookingStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update booking status automatically - mark completed bookings as completed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting booking status update...');
        
        // Find all confirmed bookings that have passed their end time
        $expiredBookings = Booking::where('status', 'confirmed')
            ->where('end_time', '<', now())
            ->get();
        
        $updatedCount = 0;
        
        foreach ($expiredBookings as $booking) {
            try {
                $booking->updateStatus('completed');
                $updatedCount++;
                
                $this->info("Updated booking {$booking->id} to completed status");
                
                \Log::info('Booking automatically completed', [
                    'booking_id' => $booking->id,
                    'user_id' => $booking->user_id,
                    'end_time' => $booking->end_time,
                    'updated_at' => now()
                ]);
            } catch (\Exception $e) {
                $this->error("Failed to update booking {$booking->id}: " . $e->getMessage());
                \Log::error('Failed to auto-update booking status', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        $this->info("Updated {$updatedCount} bookings to completed status");
        
        return Command::SUCCESS;
    }
}
