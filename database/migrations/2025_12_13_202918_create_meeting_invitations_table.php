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
        Schema::create('meeting_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('pic_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('invited_by_pic_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['invited', 'accepted', 'declined'])->default('invited');
            $table->timestamp('invited_at')->useCurrent();
            $table->timestamp('responded_at')->nullable();
            $table->enum('attendance_status', ['pending', 'confirmed', 'declined', 'absent'])->default('pending');
            $table->timestamp('attendance_confirmed_at')->nullable();
            $table->timestamp('attendance_declined_at')->nullable();
            
            // Unique constraint to prevent duplicate invitations
            $table->unique(['booking_id', 'pic_id'], 'unique_booking_pic');
            
            // Indexes for better query performance
            $table->index('booking_id');
            $table->index('pic_id');
            $table->index('status');
            $table->index('attendance_status');
            $table->index(['booking_id', 'attendance_status'], 'idx_booking_attendance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_invitations');
    }
};
