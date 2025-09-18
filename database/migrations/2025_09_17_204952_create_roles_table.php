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
        // Add additional columns to existing roles table (created by Spatie)
        Schema::table('roles', function (Blueprint $table) {
            $table->string('display_name')->nullable()->after('name');
            $table->text('description')->nullable()->after('display_name');
            $table->json('permissions')->nullable()->after('description'); // Store permissions as JSON
            $table->boolean('is_active')->default(true)->after('permissions');
            $table->softDeletes()->after('updated_at');
            
            // Indexes
            $table->index(['name', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropIndex(['name', 'is_active']);
            $table->dropColumn([
                'display_name',
                'description',
                'permissions',
                'is_active',
                'deleted_at'
            ]);
        });
    }
};
