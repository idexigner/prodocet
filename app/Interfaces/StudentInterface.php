<?php

namespace App\Interfaces;

use App\Models\Student;
use Illuminate\Http\Request;

interface StudentInterface
{
    /**
     * Get all students with pagination
     */
    public function getAllStudents(Request $request);

    /**
     * Get student by ID
     */
    public function getStudentById($id);

    /**
     * Create new student
     */
    public function createStudent(array $data);

    /**
     * Update student
     */
    public function updateStudent($id, array $data);

    /**
     * Delete student (soft delete)
     */
    public function deleteStudent($id);

    /**
     * Restore soft deleted student
     */
    public function restoreStudent($id);

    /**
     * Get students for DataTable
     */
    public function getStudentsForDataTable(Request $request);

    /**
     * Search students
     */
    public function searchStudents($query);

    /**
     * Get students by status
     */
    public function getStudentsByStatus($status);

    /**
     * Export students to Excel
     */
    public function exportToExcel();

    /**
     * Export students to PDF
     */
    public function exportToPDF();

    /**
     * Get student statistics
     */
    public function getStudentStatistics();
}
