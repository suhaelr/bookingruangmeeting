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
        Schema::table('bookings', function (Blueprint $table) {
            // Track reminder emails yang sudah dikirim
            $table->boolean('reminder_1h_sent')->default(false)->after('reschedule_deadline_at');
            $table->boolean('reminder_30m_sent')->default(false)->after('reminder_1h_sent');
            $table->boolean('reminder_15m_sent')->default(false)->after('reminder_30m_sent');
            $table->timestamp('reminder_1h_sent_at')->nullable()->after('reminder_15m_sent');
            $table->timestamp('reminder_30m_sent_at')->nullable()->after('reminder_1h_sent_at');
            $table->timestamp('reminder_15m_sent_at')->nullable()->after('reminder_30m_sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'reminder_1h_sent',
                'reminder_30m_sent',
                'reminder_15m_sent',
                'reminder_1h_sent_at',
                'reminder_30m_sent_at',
                'reminder_15m_sent_at'
            ]);
        });
    }
};
