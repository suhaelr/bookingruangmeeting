<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;

class AutoExpirePreempt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-expire-preempt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-expire preempt-pending bookings by deadline and release slots';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();

        $expired = Booking::where('preempt_status', 'pending')
            ->whereNotNull('preempt_deadline_at')
            ->where('preempt_deadline_at', '<=', $now)
            ->get();

        $count = 0;
        foreach ($expired as $booking) {
            try {
                // Cancel booking and close preempt atomically
                \DB::transaction(function () use ($booking) {
                    $booking->updateStatus('cancelled', 'Auto-cancel due to preempt SLA expired');
                    $booking->closePreempt();
                });
                $count++;

                // Notify owner
                try {
                    \App\Models\UserNotification::createNotification(
                        $booking->user_id,
                        'warning',
                        'Booking Dibatalkan Otomatis',
                        'Booking Anda dibatalkan karena melewati batas waktu tanggapan permintaan didahulukan.',
                        $booking->id
                    );
                } catch (\Throwable $e) {
                    \Log::error('Failed to notify owner after auto-expire', ['error' => $e->getMessage()]);
                }

                \Log::info('Auto-expired preempt booking', [
                    'booking_id' => $booking->id,
                    'deadline_at' => optional($booking->preempt_deadline_at)->toDateTimeString(),
                ]);
            } catch (\Throwable $e) {
                \Log::error('Failed to auto-expire preempt booking', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Auto-cancel for owners who didn't reschedule in time
        $rescheduleExpired = Booking::where('needs_reschedule', true)
            ->whereNotNull('reschedule_deadline_at')
            ->where('reschedule_deadline_at', '<=', $now)
            ->get();

        $cancelledReschedules = 0;
        foreach ($rescheduleExpired as $booking) {
            try {
                \DB::transaction(function () use ($booking) {
                    $booking->updateStatus('cancelled', 'Auto-cancel due to reschedule deadline expired');
                    $booking->needs_reschedule = false;
                    $booking->reschedule_deadline_at = null;
                    $booking->save();
                });
                $cancelledReschedules++;
                try {
                    \App\Models\UserNotification::createNotification(
                        $booking->user_id,
                        'warning',
                        'Booking Dibatalkan Otomatis',
                        'Booking Anda dibatalkan karena melewati batas waktu reschedule.',
                        $booking->id
                    );
                } catch (\Throwable $e) { \Log::error('Failed to notify owner after reschedule auto-cancel', ['e'=>$e->getMessage()]); }
            } catch (\Throwable $e) {
                \Log::error('Failed to auto-cancel after reschedule deadline', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Processed {$count} expired preempt bookings and {$cancelledReschedules} reschedule timeouts.");
        return self::SUCCESS;
    }
}
