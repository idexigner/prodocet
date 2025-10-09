<?php

namespace App\Interfaces;

use App\Models\RateScheme;
use Illuminate\Http\Request;

interface RateSchemeInterface
{
    /**
     * Get all rate schemes with pagination
     */
    public function getAllRateSchemes(Request $request);

    /**
     * Get rate scheme by ID
     */
    public function getRateSchemeById($id);

    /**
     * Create new rate scheme
     */
    public function createRateScheme(array $data);

    /**
     * Update rate scheme
     */
    public function updateRateScheme($id, array $data);

    /**
     * Delete rate scheme
     */
    public function deleteRateScheme($id);

    /**
     * Get rate schemes for DataTable
     */
    public function getRateSchemesForDataTable(Request $request);

    /**
     * Search rate schemes
     */
    public function searchRateSchemes($query);

    /**
     * Get rate scheme by letter code
     */
    public function getRateSchemeByLetterCode($letterCode);

    /**
     * Export rate schemes to Excel
     */
    public function exportToExcel();

    /**
     * Export rate schemes to PDF
     */
    public function exportToPDF();
}
