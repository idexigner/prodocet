<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'display_name' => $this->display_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'document_id' => $this->document_id,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'address' => $this->address,
            'emergency_contact' => $this->emergency_contact,
            'emergency_phone' => $this->emergency_phone,
            'language_preference' => $this->language_preference,
            'is_active' => $this->is_active,
            'last_login_at' => $this->last_login_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            
            // Relationships
            'roles' => $this->whenLoaded('roles', function () {
                return $this->roles->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'display_name' => _trans("roles.{$role->name}"),
                        'permissions' => $role->permissions->pluck('name')
                    ];
                });
            }),
            
            'permissions' => $this->whenLoaded('permissions', function () {
                return $this->permissions->pluck('name');
            }),
            
            'primary_role' => $this->when($this->relationLoaded('roles'), function () {
                $primaryRole = $this->primary_role;
                return $primaryRole ? [
                    'id' => $primaryRole->id,
                    'name' => $primaryRole->name,
                    'display_name' => _trans("roles.{$primaryRole->name}")
                ] : null;
            }),
            
            // Computed fields
            'effective_permissions' => $this->when($this->relationLoaded('roles'), function () {
                return _get_user_permissions($this->resource);
            }),
            
            'can_access_modules' => $this->when($this->relationLoaded('roles'), function () {
                $modules = ['dashboard', 'groups', 'students', 'teachers', 'calendar', 'attendance', 'reports', 'users', 'settings', 'analytics'];
                $access = [];
                
                foreach ($modules as $module) {
                    $access[$module] = _can_access_module($this->resource, $module);
                }
                
                return $access;
            }),
            
            // Status indicators
            'status' => [
                'is_active' => $this->is_active,
                'is_online' => $this->last_login_at && $this->last_login_at->isAfter(now()->subMinutes(5)),
                'is_super_admin' => $this->hasRole('super-admin'),
            ],
            
            // Language and localization
            'localization' => [
                'language_preference' => $this->language_preference,
                'language_display' => _trans("languages.{$this->language_preference}"),
            ],
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'timestamp' => now()->toISOString(),
                'version' => '1.0',
            ],
        ];
    }
}
