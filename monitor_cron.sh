#!/bin/bash

echo "🔍 CRON JOB MONITOR"
echo "==================="
echo "Time: $(date)"
echo ""

# 1. Cek crontab
echo "📋 Current crontab:"
crontab -l
echo ""

# 2. Test commands manual
echo "🧪 Testing commands manually:"
cd /home/dsvbpgpt/public_html

echo "1. Status Update:"
php artisan bookings:update-status
echo ""

echo "2. Email Reminder:"
php artisan bookings:send-reminders
echo ""

# 3. Cek log Laravel
echo "📊 Recent Laravel logs:"
tail -5 storage/logs/laravel.log
echo ""

# 4. Cek database
echo "🗄️ Database check:"
mysql -u dsvbpgpt -p'password_here' dsvbpgpt_booking -e "
SELECT 
    COUNT(*) as total_bookings,
    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_bookings,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_bookings
FROM bookings;
" 2>/dev/null || echo "Database connection failed"

echo ""
echo "✅ Monitor complete!"
echo "To check if cron is working, run this script every few minutes"
echo "and see if the numbers change."
