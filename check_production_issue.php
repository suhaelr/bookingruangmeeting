<?php
/**
 * Script untuk memeriksa masalah production
 */

echo "=== Production Issue Analysis ===\n";

echo "Error yang terjadi:\n";
echo "SQLSTATE[42S22]: Column not found: 1054 Unknown column 'last_login_at' in 'SET'\n";
echo "SQL: update `users` set `last_login_at` = 2025-10-10 18:15:40, `users`.`updated_at` = 2025-10-10 18:15:40 where `id` = 2\n\n";

echo "Kemungkinan penyebab:\n";
echo "1. ❌ Kolom last_login_at tidak ada di database production\n";
echo "2. ❌ Migration belum dijalankan di production\n";
echo "3. ❌ Ada masalah dengan database connection di production\n";
echo "4. ❌ Ada perbedaan struktur database antara local dan production\n\n";

echo "Solusi yang disarankan:\n";
echo "1. ✅ Jalankan migration di production: php artisan migrate\n";
echo "2. ✅ Periksa struktur tabel: DESCRIBE users;\n";
echo "3. ✅ Pastikan migration sudah dijalankan: php artisan migrate:status\n";
echo "4. ✅ Jika perlu, jalankan migration manual untuk menambahkan kolom\n\n";

echo "Script untuk production:\n";
echo "```bash\n";
echo "# SSH ke production server\n";
echo "ssh user@pusdatinbgn.web.id\n";
echo "cd /path/to/your/laravel/app\n\n";
echo "# Pull perubahan terbaru\n";
echo "git pull origin master\n\n";
echo "# Jalankan migration\n";
echo "php artisan migrate\n\n";
echo "# Periksa status migration\n";
echo "php artisan migrate:status\n\n";
echo "# Periksa struktur tabel\n";
echo "php artisan tinker --execute=\"\\DB::select('DESCRIBE users');\"\n";
echo "```\n\n";

echo "Jika migration gagal, jalankan manual:\n";
echo "```sql\n";
echo "ALTER TABLE users ADD COLUMN last_login_at TIMESTAMP NULL AFTER avatar;\n";
echo "```\n";
?>
