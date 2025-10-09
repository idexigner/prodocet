<?php

namespace App\Interfaces;

use App\Models\CourseLevel;
use Illuminate\Http\Request;

interface CourseLevelInterface
{
    /**
     * Get all course levels with pagination
     */
    public function getAllCourseLevels(Request $request);

    /**
     * Get course level by ID
     */
    public function getCourseLevelById($id);

    /**
     * Create new course level
     */
    public function createCourseLevel(array $data);

    /**
     * Update course level
     */
    public function updateCourseLevel($id, array $data);

    /**
     * Delete course level
     */
    public function deleteCourseLevel($id);

    /**
     * Get course levels for DataTable
     */
    public function getCourseLevelsForDataTable(Request $request);

    /**
     * Search course levels
     */
    public function searchCourseLevels($query);

    /**
     * Get active course levels
     */
    public function getActiveCourseLevels();

    /**
     * Get course levels by language
     */
    public function getCourseLevelsByLanguage($languageId);

    /**
     * Export course levels to Excel
     */
    public function exportToExcel();

    /**
     * Export course levels to PDF
     */
    public function exportToPDF();
}
