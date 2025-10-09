<?php

namespace App\Interfaces;

use App\Models\Role;
use Illuminate\Http\Request;

interface RoleInterface
{
    /**
     * Get all roles with pagination
     */
    public function getAllRoles(Request $request);

    /**
     * Get role by ID
     */
    public function getRoleById($id);

    /**
     * Create new role
     */
    public function createRole(array $data);

    /**
     * Update role
     */
    public function updateRole($id, array $data);

    /**
     * Delete role
     */
    public function deleteRole($id);

    /**
     * Get roles for DataTable
     */
    public function getRolesForDataTable(Request $request);

    /**
     * Search roles
     */
    public function searchRoles($query);

    /**
     * Get role permissions
     */
    public function getRolePermissions($roleId);

    /**
     * Assign permissions to role
     */
    public function assignPermissionsToRole($roleId, array $permissions);

    /**
     * Remove permissions from role
     */
    public function removePermissionsFromRole($roleId, array $permissions);

    /**
     * Get all available permissions
     */
    public function getAllPermissions();

    /**
     * Get permissions grouped by module
     */
    public function getPermissionsGroupedByModule();

    /**
     * Export roles to Excel
     */
    public function exportToExcel();

    /**
     * Export roles to PDF
     */
    public function exportToPDF();
}
