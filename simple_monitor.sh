#!/bin/bash

echo "🔍 SIMPLE CRON MONITOR"
echo "====================="
echo "Time: $(date)"
echo ""

# 1. Cek crontab
echo "📋 Current crontab:"
crontab -l
echo ""

# 2. Test commands
echo "🧪 Testing commands:"
cd /home/dsvbpgpt/public_html

echo "1. Status Update:"
php artisan bookings:update-status
echo ""

echo "2. Email Reminder:"
php artisan bookings:send-reminders
echo ""

# 3. Cek log Laravel
echo "📊 Recent Laravel logs:"
tail -3 storage/logs/laravel.log
echo ""

echo "✅ Monitor complete!"
