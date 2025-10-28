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
            $table->boolean('needs_reschedule')->default(false)->after('preempt_reason');
            $table->dateTime('reschedule_deadline_at')->nullable()->after('needs_reschedule');

            $table->index('needs_reschedule', 'idx_bookings_needs_reschedule');
            $table->index('reschedule_deadline_at', 'idx_bookings_reschedule_deadline_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            try { $table->dropIndex('idx_bookings_needs_reschedule'); } catch (\Throwable $e) {}
            try { $table->dropIndex('idx_bookings_reschedule_deadline_at'); } catch (\Throwable $e) {}
            try { $table->dropColumn('reschedule_deadline_at'); } catch (\Throwable $e) {}
            try { $table->dropColumn('needs_reschedule'); } catch (\Throwable $e) {}
        });
    }
};
