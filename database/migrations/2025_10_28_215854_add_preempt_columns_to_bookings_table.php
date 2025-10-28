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
            // Preempt fields for request-first (minta didahulukan) flow
            $table->enum('preempt_status', ['none', 'pending', 'closed'])->default('none')->after('status');
            $table->foreignId('preempt_requested_by')->nullable()->after('preempt_status')->constrained('users')->nullOnDelete();
            $table->dateTime('preempt_deadline_at')->nullable()->after('preempt_requested_by');
            $table->text('preempt_reason')->nullable()->after('preempt_deadline_at');

            // Indexes to speed up queries and scheduler
            $table->index('preempt_status', 'idx_bookings_preempt_status');
            $table->index('preempt_deadline_at', 'idx_bookings_preempt_deadline_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Drop indexes first (if exist)
            try { $table->dropIndex('idx_bookings_preempt_status'); } catch (\Throwable $e) {}
            try { $table->dropIndex('idx_bookings_preempt_deadline_at'); } catch (\Throwable $e) {}

            // Drop foreign key then columns
            try { $table->dropConstrainedForeignId('preempt_requested_by'); } catch (\Throwable $e) {
                // Fallback if platform doesn't support dropConstrainedForeignId
                try { $table->dropForeign(['preempt_requested_by']); } catch (\Throwable $e2) {}
                try { $table->dropColumn('preempt_requested_by'); } catch (\Throwable $e3) {}
            }

            // Remaining columns
            try { $table->dropColumn('preempt_status'); } catch (\Throwable $e) {}
            try { $table->dropColumn('preempt_deadline_at'); } catch (\Throwable $e) {}
            try { $table->dropColumn('preempt_reason'); } catch (\Throwable $e) {}
        });
    }
};
