#!/bin/bash

echo "🔍 CHECKING BOOKING DATA"
echo "========================"

# Cek booking yang confirmed
echo "📋 Confirmed Bookings:"
mysql -u dsvbpgpt -p'password_here' dsvbpgpt_booking -e "
SELECT 
    id, 
    title, 
    status, 
    start_time, 
    end_time,
    user_id
FROM bookings 
WHERE status = 'confirmed' 
ORDER BY start_time;
"

echo ""
echo "⏰ Current Time:"
date

echo ""
echo "🎯 Bookings starting in next 2 hours:"
mysql -u dsvbpgpt -p'password_here' dsvbpgpt_booking -e "
SELECT 
    id, 
    title, 
    start_time,
    TIMESTAMPDIFF(MINUTE, NOW(), start_time) as minutes_from_now
FROM bookings 
WHERE status = 'confirmed' 
AND start_time BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 2 HOUR)
ORDER BY start_time;
"
