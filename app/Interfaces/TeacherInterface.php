<?php

namespace App\Interfaces;

use App\Models\Teacher;
use Illuminate\Http\Request;

interface TeacherInterface
{
    /**
     * Get all teachers with pagination
     */
    public function getAllTeachers(Request $request);

    /**
     * Get teacher by ID
     */
    public function getTeacherById($id);

    /**
     * Create new teacher
     */
    public function createTeacher(array $data);

    /**
     * Update teacher
     */
    public function updateTeacher($id, array $data);

    /**
     * Delete teacher (soft delete)
     */
    public function deleteTeacher($id);

    /**
     * Restore soft deleted teacher
     */
    public function restoreTeacher($id);

    /**
     * Get teachers for DataTable
     */
    public function getTeachersForDataTable(Request $request);

    /**
     * Search teachers
     */
    public function searchTeachers($query);

    /**
     * Get teachers by status
     */
    public function getTeachersByStatus($status);

    /**
     * Export teachers to Excel
     */
    public function exportToExcel();

    /**
     * Export teachers to PDF
     */
    public function exportToPDF();

    /**
     * Get teacher statistics
     */
    public function getTeacherStatistics();

    /**
     * Get available teachers for scheduling
     */
    public function getAvailableTeachers($date, $time);
}
