<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('id');
            $table->string('full_name')->after('username');
            $table->string('phone')->nullable()->after('full_name');
            $table->string('department')->nullable()->after('phone');
            $table->enum('role', ['admin', 'user'])->default('user')->after('department');
            $table->string('avatar')->nullable()->after('role');
            $table->timestamp('last_login_at')->nullable()->after('avatar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'full_name',
                'phone',
                'department',
                'role',
                'avatar',
                'last_login_at'
            ]);
        });
    }
};