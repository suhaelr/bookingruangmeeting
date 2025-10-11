#!/bin/bash

echo "🔧 SETTING UP CRON JOB MONITORING"
echo "=================================="

# 1. Buat direktori log
echo "📁 Creating log directory..."
mkdir -p /home/dsvbpgpt/cron_logs

# 2. Buat file log kosong
echo "📝 Creating log files..."
touch /home/dsvbpgpt/cron_logs/status.log
touch /home/dsvbpgpt/cron_logs/reminder.log
touch /home/dsvbpgpt/cron_logs/cron.log

# 3. Set permission
chmod 755 /home/dsvbpgpt/cron_logs
chmod 644 /home/dsvbpgpt/cron_logs/*.log

echo "✅ Log directory created: /home/dsvbpgpt/cron_logs/"
echo ""

# 4. Update crontab dengan logging
echo "🔄 Updating crontab with logging..."
echo "Current crontab:"
crontab -l
echo ""

# 5. Buat crontab baru dengan log
cat > /tmp/new_crontab << EOF
# Meeting Room Booking System Cron Jobs
# Update booking status every 5 minutes
*/5 * * * * cd /home/dsvbpgpt/public_html && echo "\$(date): Running status update" >> /home/dsvbpgpt/cron_logs/cron.log && php artisan bookings:update-status >> /home/dsvbpgpt/cron_logs/status.log 2>&1

# Send email reminders every 15 minutes  
*/15 * * * * cd /home/dsvbpgpt/public_html && echo "\$(date): Running email reminder" >> /home/dsvbpgpt/cron_logs/cron.log && php artisan bookings:send-reminders >> /home/dsvbpgpt/cron_logs/reminder.log 2>&1
EOF

# 6. Install crontab baru
crontab /tmp/new_crontab

echo "✅ Crontab updated with logging!"
echo ""

# 7. Test commands
echo "🧪 Testing commands..."
cd /home/dsvbpgpt/public_html

echo "Testing status update:"
php artisan bookings:update-status >> /home/dsvbpgpt/cron_logs/status.log 2>&1

echo "Testing email reminder:"
php artisan bookings:send-reminders >> /home/dsvbpgpt/cron_logs/reminder.log 2>&1

echo "✅ Commands tested!"
echo ""

# 8. Show log files
echo "📋 Log files created:"
ls -la /home/dsvbpgpt/cron_logs/

echo ""
echo "📊 Recent log content:"
echo "=== STATUS LOG ==="
tail -5 /home/dsvbpgpt/cron_logs/status.log

echo ""
echo "=== REMINDER LOG ==="
tail -5 /home/dsvbpgpt/cron_logs/reminder.log

echo ""
echo "=== CRON LOG ==="
tail -5 /home/dsvbpgpt/cron_logs/cron.log

echo ""
echo "🎯 Monitoring setup complete!"
echo "To monitor: tail -f /home/dsvbpgpt/cron_logs/cron.log"
