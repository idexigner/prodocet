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
        Schema::create('slots', function (Blueprint $table) {
            $table->id();
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->integer('duration')->default(30); // Duration in minutes (30, 60, or 90)
            $table->string('day_of_week'); // monday, tuesday, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Index for efficient querying
            $table->index(['day_of_week', 'start_time']);
            $table->index(['is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slots');
    }
};