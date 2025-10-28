<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'unit_kerja')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('unit_kerja')->nullable()->after('department');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'unit_kerja')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('unit_kerja');
            });
        }
    }
};


