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
        Schema::create('teacher_course', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->date('assigned_date')->default(now());
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Ensure unique combination of teacher and course
            $table->unique(['teacher_id', 'course_id']);
            
            // Index for efficient querying
            $table->index(['teacher_id', 'is_active']);
            $table->index(['course_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_course');
    }
};