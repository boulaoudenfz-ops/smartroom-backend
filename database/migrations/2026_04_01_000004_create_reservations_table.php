<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->integer('attendees_count')->default(1);
            $table->enum('status', ['pending','approved','rejected','cancelled','completed'])->default('pending');
            $table->string('qr_code')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->boolean('recurring')->default(false);
            $table->string('recurrence_pattern', 50)->nullable();
            $table->timestamps();
            $table->index(['room_id', 'start_datetime', 'end_datetime']);
            $table->index('status');
        });
    }
    public function down(): void {
        Schema::dropIfExists('reservations');
    }
};