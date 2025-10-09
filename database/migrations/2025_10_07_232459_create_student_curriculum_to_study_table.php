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
        Schema::create('student_curriculum_to_study', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_course_id')->constrained('student_course')->onDelete('cascade');
            $table->foreignId('course_curriculum_id')->constrained('course_curriculum')->onDelete('cascade');
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'skipped'])->default('not_started');
            $table->date('started_at')->nullable();
            $table->date('completed_at')->nullable();
            $table->integer('hours_studied')->default(0); // Actual hours spent on this topic
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Ensure unique combination of student_course and course_curriculum
            $table->unique(['student_course_id', 'course_curriculum_id'], 'sc_study_unique');
            
            // Index for efficient querying
            $table->index(['student_course_id', 'status']);
            $table->index(['course_curriculum_id', 'status']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_curriculum_to_study');
    }
};