# Laravel Scheduler Setup

## Automated Tasks Configuration

This application includes several automated tasks that need to be scheduled:

### 1. Booking Status Updates
- **Command**: `php artisan bookings:update-status`
- **Frequency**: Every 5 minutes
- **Purpose**: Automatically mark completed bookings as completed when their end time has passed

### 2. Booking Reminders
- **Command**: `php artisan bookings:send-reminders`
- **Frequency**: Every 15 minutes
- **Purpose**: Send 1-hour reminder emails to users with confirmed bookings

## Setup Instructions

### Option 1: Using Laravel Scheduler (Recommended)

1. Add the following to your server's crontab:
```bash
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

2. Add the following to `app/Console/Kernel.php` (if it exists) or create it:

```php
<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\UpdateBookingStatus::class,
        Commands\SendBookingReminders::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Update booking status every 5 minutes
        $schedule->command('bookings:update-status')->everyFiveMinutes();
        
        // Send booking reminders every 15 minutes
        $schedule->command('bookings:send-reminders')->everyFifteenMinutes();
    }
}
```

### Option 2: Direct Cron Jobs

Add these entries to your server's crontab:

```bash
# Update booking status every 5 minutes
*/5 * * * * cd /path/to/your/project && php artisan bookings:update-status >> /dev/null 2>&1

# Send booking reminders every 15 minutes
*/15 * * * * cd /path/to/your/project && php artisan bookings:send-reminders >> /dev/null 2>&1
```

### Option 3: Manual Testing

You can test the commands manually:

```bash
# Test booking status updates
php artisan bookings:update-status

# Test booking reminders
php artisan bookings:send-reminders
```

## Logging

All automated tasks are logged in the Laravel log files. Check `storage/logs/laravel.log` for execution details and any errors.

## Email Configuration

Make sure your email configuration in `.env` is properly set up for the reminder emails to work:

```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Meeting Room System"
```
