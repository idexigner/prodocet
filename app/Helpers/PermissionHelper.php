<?php

namespace App\Helpers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Log;

class PermissionHelper
{
    /**
     * Check if user has specific permission
     */
    public static function hasPermission(User $user, string $permission): bool
    {
        try {
            // Check if user has direct permission
            if ($user->hasPermissionTo($permission)) {
                return true;
            }

            // Check if user has permission through role
            if ($user->hasRole('super-admin')) {
                return true; // Super admin has all permissions
            }

            return false;
        } catch (\Exception $e) {
            self::logError('Permission check failed', [
                'user_id' => $user->id,
                'permission' => $permission,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get all permissions for a role
     */
    public static function getRolePermissions(string $roleName): array
    {
        try {
            $role = Role::where('name', $roleName)->first();
            if (!$role) {
                return [];
            }

            return $role->permissions->pluck('name')->toArray();
        } catch (\Exception $e) {
            self::logError('Get role permissions failed', [
                'role' => $roleName,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Get all available permissions grouped by module
     */
    public static function getAllPermissionsGrouped(): array
    {
        try {
            $permissions = Permission::all()->groupBy(function ($permission) {
                $parts = explode('.', $permission->name);
                return $parts[0] ?? 'general';
            });

            return $permissions->toArray();
        } catch (\Exception $e) {
            self::logError('Get all permissions failed', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Create permission if not exists
     */
    public static function createPermissionIfNotExists(string $permissionName, string $guardName = 'web'): Permission
    {
        try {
            return Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => $guardName],
                ['name' => $permissionName, 'guard_name' => $guardName]
            );
        } catch (\Exception $e) {
            self::logError('Create permission failed', [
                'permission' => $permissionName,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Assign permissions to role
     */
    public static function assignPermissionsToRole(string $roleName, array $permissions): bool
    {
        try {
            $role = Role::where('name', $roleName)->first();
            if (!$role) {
                throw new \Exception("Role {$roleName} not found");
            }

            $role->syncPermissions($permissions);
            return true;
        } catch (\Exception $e) {
            self::logError('Assign permissions to role failed', [
                'role' => $roleName,
                'permissions' => $permissions,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get user's effective permissions
     */
    public static function getUserEffectivePermissions(User $user): array
    {
        try {
            $permissions = [];

            // Get direct permissions
            $directPermissions = $user->getDirectPermissions()->pluck('name')->toArray();
            $permissions = array_merge($permissions, $directPermissions);

            // Get permissions through roles
            $rolePermissions = $user->getPermissionsViaRoles()->pluck('name')->toArray();
            $permissions = array_merge($permissions, $rolePermissions);

            // Remove duplicates
            return array_unique($permissions);
        } catch (\Exception $e) {
            self::logError('Get user effective permissions failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Check if user can access specific module
     */
    public static function canAccessModule(User $user, string $module): bool
    {
        $modulePermissions = [
            'dashboard' => ['dashboard.view'],
            'groups' => ['groups.view', 'groups.create', 'groups.edit', 'groups.delete'],
            'students' => ['students.view', 'students.create', 'students.edit', 'students.delete'],
            'teachers' => ['teachers.view', 'teachers.create', 'teachers.edit', 'teachers.delete'],
            'calendar' => ['calendar.view', 'calendar.create', 'calendar.edit', 'calendar.delete'],
            'attendance' => ['attendance.view', 'attendance.create', 'attendance.edit'],
            'reports' => ['reports.view', 'reports.generate'],
            'users' => ['users.view', 'users.create', 'users.edit', 'users.delete'],
            'settings' => ['settings.view', 'settings.edit'],
            'analytics' => ['analytics.view'],
        ];

        if (!isset($modulePermissions[$module])) {
            return false;
        }

        foreach ($modulePermissions[$module] as $permission) {
            if (self::hasPermission($user, $permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Log error with context
     */
    private static function logError(string $message, array $context = []): void
    {
        Log::error("PermissionHelper: {$message}", $context);
    }
}
