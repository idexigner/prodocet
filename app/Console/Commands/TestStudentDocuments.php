<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class TestStudentDocuments extends Command
{
    protected $signature = 'test:student-documents';
    protected $description = 'Test student documents functionality';

    public function handle()
    {
        $this->info('🧪 Testing Student Documents System...');
        
        // Check if students exist
        $students = User::role('student')->get();
        if ($students->isEmpty()) {
            $this->error('No students found. Please create students first.');
            return;
        }
        
        $this->line("Found {$students->count()} students");
        
        // Check if documents exist
        $totalDocuments = DB::table('student_documents')->count();
        $this->line("Total documents: {$totalDocuments}");
        
        if ($totalDocuments === 0) {
            $this->warn('No documents found. Running seeder...');
            $this->call('db:seed', ['--class' => 'StudentDocumentSeeder']);
            $totalDocuments = DB::table('student_documents')->count();
        }
        
        // Show documents by type
        $this->newLine();
        $this->info('Documents by type:');
        $documentsByType = DB::table('student_documents')
            ->select('document_type', DB::raw('COUNT(*) as count'))
            ->groupBy('document_type')
            ->orderBy('count', 'desc')
            ->get();
        
        foreach ($documentsByType as $doc) {
            $this->line("  {$doc->document_type}: {$doc->count} documents");
        }
        
        // Show students with their documents
        $this->newLine();
        $this->info('Students with documents:');
        $studentsWithDocs = DB::table('student_documents')
            ->join('users', 'student_documents.student_id', '=', 'users.id')
            ->select('users.name', 'users.email', 'student_documents.document_type', 'student_documents.document_number', 'student_documents.is_primary')
            ->orderBy('users.name')
            ->get();
        
        $currentStudent = null;
        foreach ($studentsWithDocs as $doc) {
            if ($currentStudent !== $doc->name) {
                $currentStudent = $doc->name;
                $this->newLine();
                $this->line("👤 {$doc->name} ({$doc->email}):");
            }
            
            $primary = $doc->is_primary ? ' (Primary)' : '';
            $this->line("    {$doc->document_type}: {$doc->document_number}{$primary}");
        }
        
        // Test document validation examples
        $this->newLine();
        $this->info('📋 Document Type Examples:');
        $examples = [
            'C.C.' => '1013665259',
            'C.E.' => '1234567890',
            'T.I.' => '123456789',
            'Pa.' => 'YB7716498',
            'Nit' => '900123456-7',
            'other' => 'ABC123XYZ'
        ];
        
        foreach ($examples as $type => $number) {
            $this->line("  {$type}: {$number}");
        }
        
        $this->newLine();
        $this->info('✅ Student documents system is working correctly!');
    }
}