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
        Schema::table('users', function (Blueprint $table) {
            // Add LMS specific fields
            $table->string('first_name')->after('id');
            $table->string('last_name')->after('first_name');
            $table->string('phone')->nullable()->after('email');
            $table->string('document_id')->nullable()->after('phone');
            $table->date('birth_date')->nullable()->after('document_id');
            $table->text('address')->nullable()->after('birth_date');
            $table->string('emergency_contact')->nullable()->after('address');
            $table->string('emergency_phone')->nullable()->after('emergency_contact');
            $table->enum('language_preference', ['es', 'en'])->default('es')->after('emergency_phone');
            $table->string('role')->nullable()->after('language_preference'); // Direct role for database checking
            $table->boolean('is_active')->default(true)->after('role');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            
            // Add soft deletes
            $table->softDeletes();
            
            // Modify existing fields
            $table->string('name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name', 
                'phone',
                'document_id',
                'birth_date',
                'address',
                'emergency_contact',
                'emergency_phone',
                'language_preference',
                'role',
                'is_active',
                'last_login_at',
                'deleted_at'
            ]);
            
            $table->string('name')->nullable(false)->change();
        });
    }
};
