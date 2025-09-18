<?php

use Illuminate\Container\Attributes\Log;
use Illuminate\Support\Facades\Auth;

function _is_json($string) {
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}

function _generate_random_string($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

function _format_date($date, $format = 'Y-m-d H:i:s') {
    return date($format, strtotime($date));
}

function _get_user_ip() {
    return request()->ip();
}

function _sanitize_input($input) {
    return htmlspecialchars(strip_tags($input));
} 

function _encrypt_id($id) {
    return base64_encode($id);
}

function _decrypt_id($id) {
    return base64_decode($id);
}

/**
    * Convert all string values in an array to uppercase (multibyte safe)
*/
function toUpperCaseArray($array)
{
    foreach ($array as $key => $value) {
        if (is_string($value)) {
            $array[$key] = mb_strtoupper($value);
        }
    }
    return $array;
}


// Permission caching functions
function _get_cached_permissions($userId = null) {
    $userId = $userId ?? auth()->id();
    $cacheKey = "user_permissions_{$userId}";
    
    return cache()->remember($cacheKey, 3, function() use ($userId) {
        $user = \App\Models\User::find($userId);
        if (!$user) {
            return collect([]);
        }
        return $user->getAllPermissions()->pluck('name')->toArray();
    });
}

function _get_cached_roles($userId = null) {
    $userId = $userId ?? auth()->id();
    $cacheKey = "user_roles_{$userId}";
    
    return cache()->remember($cacheKey, 3, function() use ($userId) {
        $user = \App\Models\User::find($userId);
        if (!$user) {
            return collect([]);
        }
        return $user->getRoleNames()->toArray();
    });
}

function _clear_permission_cache($userId = null) {
    $userId = $userId ?? auth()->id();
    cache()->forget("user_permissions_{$userId}");
    cache()->forget("user_roles_{$userId}");
}

function _has_permission($permission) {
    if (!auth()->check()) {
        return false;
    }
    
    $permissions = _get_cached_permissions();
    return in_array($permission, $permissions);
}

function _has_any_permission($permissions) {
    if (!auth()->check()) {
        return false;
    }
    
    $userPermissions = _get_cached_permissions();
    return !empty(array_intersect($permissions, $userPermissions));
}

function _has_all_permissions($permissions) {
    if (!auth()->check()) {
        return false;
    }
    
    $userPermissions = _get_cached_permissions();
    return empty(array_diff($permissions, $userPermissions));
}

function _has_role($role) {
    if (!auth()->check()) {
        return false;
    }
    
    $roles = _get_cached_roles();
    return in_array($role, $roles);
}

function _has_any_role($roles) {
    if (!auth()->check()) {
        return false;
    }
    
    $userRoles = _get_cached_roles();
    return !empty(array_intersect($roles, $userRoles));
}

// Client configuration functions
function _get_cached_client_config($clientName = null) {
    $clientName = $clientName ?? env('CLIENT_NAME', 'default');
    $cacheKey = "client_config_{$clientName}";
    
    return cache()->remember($cacheKey, 3, function() use ($clientName) {
        $config = \App\Modules\User\Models\ClientConfiguration::getByClientName($clientName);
        if (!$config) {
            return null;
        }
        
        // Build configuration array from relationships
        $configData = [
            'id' => $config->id,
            'client_name' => $config->client_name,
            'display_name' => $config->display_name,
            'description' => $config->description,
            'is_active' => $config->is_active,
            'menu_items' => []
        ];
        
        // Add menu items
        foreach ($config->menuItems as $menuItem) {
            $configData['menu_items'][$menuItem->key] = $menuItem->pivot->is_enabled;
        }
        
        return $configData;
    });
}

function _clear_client_config_cache($clientName = null) {
    if ($clientName) {
        cache()->forget("client_config_{$clientName}");
    } else {
        // Clear all client config caches by pattern (this is a simplified approach)
        $clientName = env('CLIENT_NAME', 'default');
        cache()->forget("client_config_{$clientName}");
    }
}

function client_config($key = null, $default = null) {
    $config = _get_cached_client_config();
    
    if (!$config) {
        return $default;
    }
    
    if ($key === null) {
        return $config;
    }
    
    return data_get($config, $key, $default);
}

function _is_client_menu_enabled($key) {
    $menuItems = client_config('menu_items', []);
    return isset($menuItems[$key]) && $menuItems[$key] === true;
}

function _get_client_name() {
    return client_config('display_name', env('CLIENT_NAME', 'Default Client'));
}

function client_customization($key, $default = false) {
    $clientName = env('CLIENT_NAME', 'default');
    $cacheKey = "client_customizations_{$clientName}";
    
    return cache()->remember($cacheKey, 3, function() use ($clientName) {
        $clientConfig = \App\Modules\User\Models\ClientConfiguration::getByClientName($clientName);
        
        if (!$clientConfig) {
            return [];
        }
        
        $customizations = [];
        
        foreach ($clientConfig->customizations as $customization) {
            if ($customization->is_active) {
                // For checkbox customizations (no value set), return true if enabled
                if ($customization->pivot->value === null) {
                    $customizations[$customization->key] = true;
                } else {
                    $value = $customization->pivot->value;
                    
                    // Handle boolean values
                    if (in_array(strtolower($value), ['true', '1', 'yes', 'on'])) {
                        $customizations[$customization->key] = true;
                    } elseif (in_array(strtolower($value), ['false', '0', 'no', 'off'])) {
                        $customizations[$customization->key] = false;
                    } else {
                        $customizations[$customization->key] = $value;
                    }
                }
            }
        }
        
        return $customizations;
    })[$key] ?? $default;
}

function _clear_client_customizations_cache($clientName = null) {
    if ($clientName) {
        cache()->forget("client_customizations_{$clientName}");
    } else {
        $clientName = env('CLIENT_NAME', 'default');
        cache()->forget("client_customizations_{$clientName}");
    }
}

// Menu helper functions
function _get_cached_sidebar_menu($clientName = null) {
    $clientName = $clientName ?? env('CLIENT_NAME', 'default');
    $cacheKey = "sidebar_menu_{$clientName}";
    
    return cache()->remember($cacheKey, 3, function() use ($clientName) {
        $clientConfig = \App\Modules\User\Models\ClientConfiguration::getByClientName($clientName);
        
        if (!$clientConfig) {
            return [];
        }
        
        // Get enabled menu items for this client
        $enabledMenuItems = $clientConfig->menuItems()
            ->where('menu_items.is_active', true)
            ->wherePivot('is_enabled', true)
            ->orderBy('menu_items.sort_order')
            ->orderBy('menu_items.name')
            ->get();
        
        // Build hierarchical menu structure
        return _build_menu_hierarchy($enabledMenuItems);
    });
}

function _build_menu_hierarchy($menuItems) {
    $menu = [];
    $itemsByKey = [];
    
    // Index items by key for easy lookup and filter by permission
    foreach ($menuItems as $item) {
        // Check if user has permission to view this menu item
        if ($item->permission && !_has_permission($item->permission)) {
            continue;
        }
        
        $itemsByKey[$item->key] = [
            'id' => $item->id,
            'key' => $item->key,
            'name' => $item->name,
            'icon' => $item->icon,
            'route' => $item->route,
            'parent_key' => $item->parent_key,
            'sort_order' => $item->sort_order,
            'permission' => $item->permission,
            'children' => []
        ];
    }
    
    // Build hierarchy
    foreach ($itemsByKey as $key => $item) {
        if ($item['parent_key'] && isset($itemsByKey[$item['parent_key']])) {
            $itemsByKey[$item['parent_key']]['children'][] = &$itemsByKey[$key];
        } else {
            $menu[] = &$itemsByKey[$key];
        }
    }
    
    // Remove parent items that have no children and no route
    $menu = array_filter($menu, function($item) {
        return !empty($item['children']) || !empty($item['route']);
    });
    
    return $menu;
}

function _clear_sidebar_menu_cache($clientName = null) {
    if ($clientName) {
        cache()->forget("sidebar_menu_{$clientName}");
    } else {
        $clientName = env('CLIENT_NAME', 'default');
        cache()->forget("sidebar_menu_{$clientName}");
    }
}

function _route_exists($route) {
    if (!$route) {
        return false;
    }
    
    try {
        route($route);
        return true;
    } catch (\Exception $e) {
        return false;
    }
}

function _is_menu_item_active($route, $currentRoute) {
    if (!$route) {
        return false;
    }
    
    // Check if route exists before using it
    if (!_route_exists($route)) {
        return false;
    }
    
    // Direct match
    if ($currentRoute === $route) {
        return true;
    }
    
    // Check if current route starts with menu route (for sub-routes)
    if (strpos($currentRoute, $route . '.') === 0) {
        return true;
    }
    
    return false;
}

function _has_active_child($menuItem, $currentRoute) {
    if (empty($menuItem['children'])) {
        return false;
    }
    
    foreach ($menuItem['children'] as $child) {
        if (_is_menu_item_active($child['route'], $currentRoute)) {
            return true;
        }
        
        if (_has_active_child($child, $currentRoute)) {
            return true;
        }
    }
    
    return false;
}

/**
 * Handle file attachments for vouchers
 * @param array $files Array of uploaded files
 * @param string $folder Folder name to store files
 * @param array $existingAttachments Existing attachments to merge with
 * @return array Array of attachment paths
 */
function _handle_attachments($files, $folder = 'voucher_attachments', $existingAttachments = []) {
    $attachments = $existingAttachments;
    
    // Log the incoming parameters for debugging
    \Illuminate\Support\Facades\Log::info('_handle_attachments called', [
        'folder_original' => $folder,
        'folder_type' => gettype($folder),
        'files_count' => is_array($files) ? count($files) : 'not_array',
        'existing_attachments_count' => count($existingAttachments)
    ]);
    
    if (!$files || !is_array($files)) {
        return $attachments;
    }
    
    // Check if we already have 5 attachments
    if (count($attachments) >= 5) {
        return $attachments;
    }
    
    // Validate folder parameter
    if (empty($folder) || !is_string($folder)) {
        \Illuminate\Support\Facades\Log::warning('Invalid folder parameter provided', [
            'folder' => $folder,
            'folder_type' => gettype($folder)
        ]);
        $folder = 'voucher_attachments'; // Use default folder if invalid
    }
    
    // Sanitize folder name - remove any invalid characters
    $folder = preg_replace('/[^a-zA-Z0-9_-]/', '', $folder);
    if (empty($folder)) {
        \Illuminate\Support\Facades\Log::warning('Folder parameter became empty after sanitization', [
            'original_folder' => $folder
        ]);
        $folder = 'voucher_attachments'; // Use default if sanitization results in empty string
    }
    
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'];
    $maxFileSize = 5 * 1024 * 1024; // 5MB
    
    // Create folder if it doesn't exist
    $storagePath = storage_path('app/public/' . $folder);
    if (!file_exists($storagePath)) {
        mkdir($storagePath, 0755, true);
        \Illuminate\Support\Facades\Log::info('Folder not exists', [
            'folder' => $folder,
            'storage_path' => $storagePath
        ]);
    }else{
        \Illuminate\Support\Facades\Log::info('Folder already exists', [
            'folder' => $folder,
            'storage_path' => $storagePath
        ]);
    }
    
    foreach ($files as $file) {
        // Check if we've reached the limit
        if (count($attachments) >= 5) {
            break;
        }
        
        if (!$file || !$file->isValid()) {
            continue;
        }
        
        // Check file size
        if ($file->getSize() > $maxFileSize) {
            continue;
        }
        
        // Check file extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $allowedExtensions)) {
            continue;
        }
        
        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $extension;
        
        try {
            \Illuminate\Support\Facades\Log::info('Storing file', [
                'folder' => $folder,
                'filename' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'trace' =>''
            ]);
            // Store file
            $path = $file->storeAs($folder, $filename, 'public');
            \Illuminate\Support\Facades\Log::info('File stored', [
                'folder' => $folder,
                'filename' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'trace' => ''
            ]);
            $attachments[] = [
                'original_name' => $file->getClientOriginalName(),
                'path' => $path,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_at' => now()->toDateTimeString()
            ];
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error uploading attachment: ' . $e->getMessage(), [
                'folder' => $folder,
                'filename' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'trace' => $e->getTraceAsString()
            ]);
            continue;
        }
    }
    
    return $attachments;
}

/**
 * Remove attachment from voucher
 * @param array $attachments Current attachments
 * @param int $index Index of attachment to remove
 * @return array Updated attachments array
 */
function _remove_attachment($attachments, $index) {
    if (!is_array($attachments) || !isset($attachments[$index])) {
        return $attachments;
    }
    
    $attachment = $attachments[$index];
    
    // Delete file from storage
    if (isset($attachment['path'])) {
        try {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($attachment['path']);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error deleting attachment file: ' . $e->getMessage());
        }
    }
    
    // Remove from array
    unset($attachments[$index]);
    
    // Re-index array
    return array_values($attachments);
}

/**
 * Get attachment URL for download
 * @param string $path File path
 * @return string|null URL or null if file doesn't exist
 */
function _get_attachment_url($path) {
    if (!$path) {
        return null;
    }
    
    if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
        return null;
    }
    
    return asset('storage/' . $path);
}

/**
 * Calculate opening balance for a specific date, transaction head, and voucher type
 * @param string $date Date in Y-m-d format
 * @param int $transactionHeadId Transaction head ID
 * @param string $voucherType Voucher type
 * @return float Opening balance amount
 */
function _calculate_opening_balance($date, $transactionHeadId, $voucherType) {
    return \App\Services\OpeningBalanceService::calculateOpeningBalance($date, $transactionHeadId, $voucherType);
}

/**
 * Calculate opening balance with billed amounts consideration
 * @param string $date Date in Y-m-d format
 * @param int $transactionHeadId Transaction head ID
 * @param string $voucherType Voucher type
 * @return float Opening balance amount
 */
function _calculate_opening_balance_with_billed_amounts($date, $transactionHeadId, $voucherType) {
    return \App\Services\OpeningBalanceService::calculateOpeningBalanceWithBilledAmounts($date, $transactionHeadId, $voucherType);
}

/**
 * Get opening balance for multiple transaction heads and voucher types
 * @param string $date Date in Y-m-d format
 * @param array $filters Array of filters with transaction_head_id and voucher_type
 * @return array Array of opening balances
 */
function _get_multiple_opening_balances($date, $filters = []) {
    return \App\Services\OpeningBalanceService::getMultipleOpeningBalances($date, $filters);
}

/**
 * Get opening balance for a date range
 * @param string $startDate Start date in Y-m-d format
 * @param string $endDate End date in Y-m-d format
 * @param int $transactionHeadId Transaction head ID
 * @param string $voucherType Voucher type
 * @return array Array of daily opening balances
 */
function _get_opening_balance_range($startDate, $endDate, $transactionHeadId, $voucherType) {
    return \App\Services\OpeningBalanceService::getOpeningBalanceRange($startDate, $endDate, $transactionHeadId, $voucherType);
}