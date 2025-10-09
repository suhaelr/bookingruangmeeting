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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('meeting_room_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->integer('attendees_count')->default(1);
            $table->json('attendees')->nullable(); // ['email1@example.com', 'email2@example.com']
            $table->json('attachments')->nullable(); // ['file1.pdf', 'file2.docx']
            $table->text('special_requirements')->nullable();
            $table->decimal('total_cost', 8, 2)->default(0);
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
            
            // Index untuk optimasi query
            $table->index(['meeting_room_id', 'start_time', 'end_time']);
            $table->index(['user_id', 'start_time']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
