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
        Schema::create('student_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->enum('document_type', ['C.C.', 'C.E.', 'T.I.', 'Pa.', 'Nit', 'other'])->comment('Document type: C.C.=Cédula de Ciudadanía, C.E.=Cédula de Extranjería, T.I.=Tarjeta de Identidad, Pa.=Pasaporte, Nit=NIT, other=Other');
            $table->string('document_number'); // Can contain numbers and letters
            $table->string('document_name')->nullable(); // For 'other' type, specify the document name
            $table->boolean('is_primary')->default(false); // Mark the primary document
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Ensure unique combination of student and document type+number
            $table->unique(['student_id', 'document_type', 'document_number'], 'student_doc_unique');
            
            // Index for efficient querying
            $table->index(['student_id', 'is_primary']);
            $table->index(['document_type', 'document_number']);
            $table->index(['is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_documents');
    }
};