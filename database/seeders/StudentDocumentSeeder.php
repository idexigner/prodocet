<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class StudentDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all students
        $students = User::role('student')->get();
        
        if ($students->isEmpty()) {
            $this->command->warn('No students found. Please create students first.');
            return;
        }
        
        $documents = [];
        
        foreach ($students as $student) {
            // Each student gets at least one primary document
            $primaryDocument = $this->getRandomDocumentType();
            $documents[] = [
                'student_id' => $student->id,
                'document_type' => $primaryDocument,
                'document_number' => $this->generateDocumentNumber($primaryDocument),
                'document_name' => null,
                'is_primary' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            // Some students get additional documents (30% chance)
            if (rand(1, 100) <= 30) {
                $additionalDocument = $this->getRandomDocumentType();
                // Make sure it's different from primary
                while ($additionalDocument === $primaryDocument) {
                    $additionalDocument = $this->getRandomDocumentType();
                }
                
                $documents[] = [
                    'student_id' => $student->id,
                    'document_type' => $additionalDocument,
                    'document_number' => $this->generateDocumentNumber($additionalDocument),
                    'document_name' => null,
                    'is_primary' => false,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        
        // Insert all documents
        DB::table('student_documents')->insert($documents);
        
        $this->command->info('✅ Created ' . count($documents) . ' student documents');
        $this->command->info('👥 Students with documents: ' . $students->count());
        
        // Show some examples
        $this->command->info('📋 Example documents created:');
        $sampleDocuments = DB::table('student_documents')
            ->join('users', 'student_documents.student_id', '=', 'users.id')
            ->select('users.name', 'student_documents.document_type', 'student_documents.document_number', 'student_documents.is_primary')
            ->limit(5)
            ->get();
        
        foreach ($sampleDocuments as $doc) {
            $primary = $doc->is_primary ? ' (Primary)' : '';
            $this->command->line("  {$doc->name}: {$doc->document_type} - {$doc->document_number}{$primary}");
        }
    }
    
    private function getRandomDocumentType(): string
    {
        $types = ['C.C.', 'C.E.', 'T.I.', 'Pa.', 'Nit', 'other'];
        return $types[array_rand($types)];
    }
    
    private function generateDocumentNumber(string $documentType): string
    {
        switch ($documentType) {
            case 'C.C.':
                // Colombian ID: 8-10 digits
                return str_pad(rand(10000000, 999999999), rand(8, 10), '0', STR_PAD_LEFT);
                
            case 'C.E.':
                // Foreign ID: 8-10 digits
                return str_pad(rand(10000000, 999999999), rand(8, 10), '0', STR_PAD_LEFT);
                
            case 'T.I.':
                // Identity Card: 6-10 digits
                return str_pad(rand(100000, 999999999), rand(6, 10), '0', STR_PAD_LEFT);
                
            case 'Pa.':
                // Passport: Mix of letters and numbers (e.g., YB7716498)
                $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $prefix = $letters[rand(0, 25)] . $letters[rand(0, 25)];
                $numbers = str_pad(rand(1000000, 99999999), 8, '0', STR_PAD_LEFT);
                return $prefix . $numbers;
                
            case 'Nit':
                // NIT: 9 digits with dash (e.g., 900123456-7)
                $base = str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT);
                $checkDigit = rand(0, 9);
                return $base . '-' . $checkDigit;
                
            case 'other':
                // Other documents: random alphanumeric
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                $result = '';
                for ($i = 0; $i < rand(6, 12); $i++) {
                    $result .= $chars[rand(0, strlen($chars) - 1)];
                }
                return $result;
                
            default:
                return str_pad(rand(1000000, 99999999), 8, '0', STR_PAD_LEFT);
        }
    }
}