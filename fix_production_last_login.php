<?php
/**
 * Script untuk memperbaiki masalah last_login_at di production
 */

echo "=== Fix Production last_login_at Issue ===\n\n";

echo "Langkah-langkah untuk memperbaiki di production:\n\n";

echo "1. SSH ke production server:\n";
echo "   ssh user@pusdatinbgn.web.id\n";
echo "   cd /path/to/your/laravel/app\n\n";

echo "2. Pull perubahan terbaru:\n";
echo "   git pull origin master\n\n";

echo "3. Jalankan migration:\n";
echo "   php artisan migrate\n\n";

echo "4. Jika migration gagal, jalankan manual SQL:\n";
echo "   mysql -u username -p database_name\n";
echo "   ALTER TABLE users ADD COLUMN last_login_at TIMESTAMP NULL AFTER avatar;\n";
echo "   exit\n\n";

echo "5. Verifikasi kolom sudah ada:\n";
echo "   php artisan tinker --execute=\"\\DB::select('DESCRIBE users');\"\n\n";

echo "6. Test login untuk memastikan tidak ada error lagi\n\n";

echo "=== Alternative: Manual Fix ===\n\n";
echo "Jika tidak bisa SSH, jalankan SQL langsung di database:\n";
echo "```sql\n";
echo "ALTER TABLE users ADD COLUMN last_login_at TIMESTAMP NULL AFTER avatar;\n";
echo "```\n\n";

echo "=== Verification ===\n\n";
echo "Setelah fix, pastikan:\n";
echo "✅ Kolom last_login_at ada di tabel users\n";
echo "✅ Login berfungsi tanpa error\n";
echo "✅ last_login_at terupdate saat login\n";
?>
