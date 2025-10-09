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
        Schema::create('course_curriculum', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('curriculum_topic_id')->constrained('curriculum_topics')->onDelete('cascade');
            $table->integer('order_index')->default(0); // Order of topics in the course
            $table->boolean('is_required')->default(true); // Whether this topic is mandatory
            $table->integer('estimated_hours')->default(1); // Estimated hours for this topic
            $table->timestamps();
            
            // Ensure unique combination of course and curriculum topic
            $table->unique(['course_id', 'curriculum_topic_id']);
            
            // Index for efficient querying
            $table->index(['course_id', 'order_index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_curriculum');
    }
};