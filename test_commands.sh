#!/bin/bash

# =====================================================
# Script untuk Testing Commands Manual
# Meeting Room Booking System v2.0
# =====================================================

echo "🚀 Testing Meeting Room Booking System Commands"
echo "================================================"

# Set working directory
cd /home/dsvbpgpt/public_html

echo "📁 Current directory: $(pwd)"
echo ""

# Test 1: Check if artisan exists
echo "🔍 Testing 1: Check if artisan exists"
if [ -f "artisan" ]; then
    echo "✅ artisan file found"
else
    echo "❌ artisan file not found"
    exit 1
fi
echo ""

# Test 2: Check PHP version
echo "🔍 Testing 2: Check PHP version"
php --version
echo ""

# Test 3: Test booking status update
echo "🔍 Testing 3: Update booking status"
echo "Running: php artisan bookings:update-status"
php artisan bookings:update-status
echo ""

# Test 4: Test email reminders
echo "🔍 Testing 4: Send email reminders"
echo "Running: php artisan bookings:send-reminders"
php artisan bookings:send-reminders
echo ""

# Test 5: Check Laravel logs
echo "🔍 Testing 5: Check recent logs"
if [ -f "storage/logs/laravel.log" ]; then
    echo "📋 Recent log entries:"
    tail -10 storage/logs/laravel.log
else
    echo "❌ Log file not found"
fi
echo ""

echo "✅ Testing completed!"
echo ""
echo "📝 Next steps:"
echo "1. If tests passed, setup cron jobs"
echo "2. Run: crontab -e"
echo "3. Add the cron job lines"
echo "4. Save and exit"
