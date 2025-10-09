<?php

namespace App\Interfaces;

use App\Models\Language;
use Illuminate\Http\Request;

interface LanguageInterface
{
    /**
     * Get all languages with pagination
     */
    public function getAllLanguages(Request $request);

    /**
     * Get language by ID
     */
    public function getLanguageById($id);

    /**
     * Create new language
     */
    public function createLanguage(array $data);

    /**
     * Update language
     */
    public function updateLanguage($id, array $data);

    /**
     * Delete language
     */
    public function deleteLanguage($id);

    /**
     * Get languages for DataTable
     */
    public function getLanguagesForDataTable(Request $request);

    /**
     * Search languages
     */
    public function searchLanguages($query);

    /**
     * Get active languages
     */
    public function getActiveLanguages();

    /**
     * Export languages to Excel
     */
    public function exportToExcel();

    /**
     * Export languages to PDF
     */
    public function exportToPDF();
}
