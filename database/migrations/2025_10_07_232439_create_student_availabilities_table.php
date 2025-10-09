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
        Schema::create('student_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('slot_id')->constrained('slots')->onDelete('cascade');
            $table->string('day_of_week'); // monday, tuesday, etc.
            $table->boolean('is_available')->default(true);
            $table->integer('priority')->default(1); // 1 = high priority, 2 = medium, 3 = low
            $table->date('effective_from')->default(now());
            $table->date('effective_until')->nullable(); // For temporary availability changes
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Ensure unique combination of student, slot, and day
            $table->unique(['student_id', 'slot_id', 'day_of_week']);
            
            // Index for efficient querying
            $table->index(['student_id', 'day_of_week', 'is_available']);
            $table->index(['slot_id', 'day_of_week']);
            $table->index(['is_available', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_availabilities');
    }
};