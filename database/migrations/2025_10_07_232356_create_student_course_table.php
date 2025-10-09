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
        Schema::create('student_course', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed', 'dropped'])->default('pending');
            $table->date('enrollment_date')->default(now());
            $table->date('start_date')->nullable(); // When the student can start attending classes
            $table->date('end_date')->nullable(); // When the course ends for this student
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Ensure unique combination of student and course
            $table->unique(['student_id', 'course_id']);
            
            // Index for efficient querying
            $table->index(['student_id', 'status']);
            $table->index(['course_id', 'status']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_course');
    }
};