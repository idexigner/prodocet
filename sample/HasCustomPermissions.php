<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Spatie\Permission\Traits\HasRoles;

trait HasCustomPermissions
{
    use HasRoles;

    // Generic permission check methods
    public function _can($permission)
    {
        return _has_permission($permission);
    }

    public function _canAny($permissions)
    {
        return _has_any_permission($permissions);
    }

    public function _canAll($permissions)
    {
        return _has_all_permissions($permissions);
    }

    public function _is($role)
    {
        return _has_role($role);
    }

    public function _isAny($roles)
    {
        return _has_any_role($roles);
    }

    public function _getAllPermissions($userId = null)
    {
        $permissions = _get_cached_permissions($userId);
        return collect($permissions);
    }

    public function _getAllPermissionNames($userId = null)
    {
        return _get_cached_permissions($userId);
    }

    public function _getAllRoles($userId = null)
    {
        return collect(_get_cached_roles($userId));
    }

    public function _clearPermissionCache($userId = null)
    {
        _clear_permission_cache($userId);
    }
} 