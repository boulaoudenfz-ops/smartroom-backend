<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->integer('total_reservations')->default(0);
            $table->decimal('total_hours', 5, 2)->default(0);
            $table->decimal('occupancy_rate', 5, 2)->default(0);
            $table->unique(['room_id','date']);
        });
    }
    public function down(): void { Schema::dropIfExists('analytics'); }
};