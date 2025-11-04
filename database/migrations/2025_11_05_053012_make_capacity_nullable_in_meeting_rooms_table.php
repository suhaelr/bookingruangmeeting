<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to modify column to nullable
        // This works for both MySQL and PostgreSQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `meeting_rooms` MODIFY COLUMN `capacity` INTEGER NULL');
        } else {
            Schema::table('meeting_rooms', function (Blueprint $table) {
                $table->integer('capacity')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Use raw SQL to modify column to not nullable
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `meeting_rooms` MODIFY COLUMN `capacity` INTEGER NOT NULL');
        } else {
            Schema::table('meeting_rooms', function (Blueprint $table) {
                $table->integer('capacity')->nullable(false)->change();
            });
        }
    }
};
