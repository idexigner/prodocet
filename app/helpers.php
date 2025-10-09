<?php

use App\Models\User;
use App\Helpers\PermissionHelper;

if (!function_exists('_has_permission')) {
    /**
     * Check if current user has specific permission
     * 
     * @param string $permission
     * @return bool
     */
    function _has_permission(string $permission): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }
        return PermissionHelper::hasPermission($user, $permission);
    }
}

if (!function_exists('_can_access_module')) {
    /**
     * Check if current user can access specific module
     * 
     * @param string $module
     * @return bool
     */
    function _can_access_module(string $module): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }
        return PermissionHelper::canAccessModule($user, $module);
    }
}

if (!function_exists('_get_user_permissions')) {
    /**
     * Get user's effective permissions
     * 
     * @param User $user
     * @return array
     */
    function _get_user_permissions(User $user): array
    {
        return PermissionHelper::getUserEffectivePermissions($user);
    }
}

if (!function_exists('_get_role_permissions')) {
    /**
     * Get permissions for a role
     * 
     * @param string $roleName
     * @return array
     */
    function _get_role_permissions(string $roleName): array
    {
        return PermissionHelper::getRolePermissions($roleName);
    }
}

if (!function_exists('_log_error')) {
    /**
     * Log error with context
     * 
     * @param string $message
     * @param array $context
     * @param string $level
     * @return void
     */
    function _log_error(string $message, array $context = [], string $level = 'error'): void
    {
        \Illuminate\Support\Facades\Log::log($level, $message, $context);
    }
}

if (!function_exists('_log_info')) {
    /**
     * Log info message with context
     * 
     * @param string $message
     * @param array $context
     * @return void
     */
    function _log_info(string $message, array $context = []): void
    {
        \Illuminate\Support\Facades\Log::info($message, $context);
    }
}

if (!function_exists('_log_warning')) {
    /**
     * Log warning message with context
     * 
     * @param string $message
     * @param array $context
     * @return void
     */
    function _log_warning(string $message, array $context = []): void
    {
        \Illuminate\Support\Facades\Log::warning($message, $context);
    }
}

if (!function_exists('_log_debug')) {
    /**
     * Log debug message with context
     * 
     * @param string $message
     * @param array $context
     * @return void
     */
    function _log_debug(string $message, array $context = []): void
    {
        \Illuminate\Support\Facades\Log::debug($message, $context);
    }
}

if (!function_exists('_trans')) {
    /**
     * Get translation with fallback
     * 
     * @param string $key
     * @param array $replace
     * @param string $locale
     * @return string
     */
    function _trans(string $key, array $replace = [], string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $translation = trans($key, $replace, $locale);
        
        // If translation not found, try English fallback
        if ($translation === $key && $locale !== 'en') {
            $translation = trans($key, $replace, 'en');
        }
        
        return $translation;
    }
}

if (!function_exists('_get_current_user')) {
    /**
     * Get current authenticated user
     * 
     * @return User|null
     */
    function _get_current_user(): ?User
    {
        return auth()->user();
    }
}

if (!function_exists('_is_super_admin')) {
    /**
     * Check if current user is super admin
     * 
     * @return bool
     */
    function _is_super_admin(): bool
    {
        $user = _get_current_user();
        return $user ? $user->hasRole('super-admin') : false;
    }
}

if (!function_exists('_get_user_role')) {
    /**
     * Get current user's primary role
     * 
     * @return string|null
     */
    function _get_user_role(): ?string
    {
        $user = _get_current_user();
        return $user ? $user->primary_role?->name : null;
    }
}


if (!function_exists('_encrypt_id')) {
    function _encrypt_id($id) {
        // return $id;
        return base64_encode($id);
    }
}

if (!function_exists('_decrypt_id')) {
    function _decrypt_id($id) {
        return base64_decode($id);
    }
}