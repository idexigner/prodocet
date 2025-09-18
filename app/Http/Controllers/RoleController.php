<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $roles = Role::withTrashed()
                    ->select(['id', 'name', 'display_name', 'description', 'permissions', 'is_active', 'created_at', 'deleted_at']);

                return DataTables::of($roles)
                    ->addColumn('permissions_count', function ($role) {
                        return count($role->permissions ?? []);
                    })
                    ->addColumn('status', function ($role) {
                        if ($role->deleted_at) {
                            return '<span class="badge bg-danger">Deleted</span>';
                        }
                        return $role->is_active 
                            ? '<span class="badge bg-success">Active</span>'
                            : '<span class="badge bg-warning">Inactive</span>';
                    })
                    ->addColumn('action', function ($role) {
                        $actions = '';
                        
                        if (_has_permission(auth()->user(), 'roles.edit')) {
                            $actions .= '<button class="btn btn-sm btn-primary edit-btn" data-id="' . $role->id . '" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button> ';
                        }
                        
                        if (_has_permission(auth()->user(), 'roles.delete')) {
                            if ($role->deleted_at) {
                                $actions .= '<button class="btn btn-sm btn-success restore-btn" data-id="' . $role->id . '" title="Restore">
                                    <i class="fas fa-undo"></i>
                                </button>';
                            } else {
                                $actions .= '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $role->id . '" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>';
                            }
                        }
                        
                        return $actions;
                    })
                    ->rawColumns(['status', 'action'])
                    ->make(true);
            }

            return view('roles.index');

        } catch (\Exception $e) {
            _log_error('Failed to fetch roles', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => _trans('roles.fetch_failed')
                ], 500);
            }

            return redirect()->back()->with('error', _trans('roles.fetch_failed'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $permissions = $this->getAllPermissions();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'permissions' => $permissions
                ]
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to load role creation form', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('roles.create_form_failed')
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:roles,name',
                'display_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'permissions' => 'nullable|array',
                'permissions.*' => 'string|exists:permissions,name',
                'is_active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => _trans('roles.validation_failed'),
                    'errors' => $validator->errors()
                ], 422);
            }

            $role = Role::create([
                'name' => Str::slug($request->name),
                'guard_name' => 'web',
                'display_name' => $request->display_name,
                'description' => $request->description,
                'permissions' => $request->permissions ?? [],
                'is_active' => $request->boolean('is_active', true)
            ]);

            _log_info('Role created successfully', [
                'role_id' => $role->id,
                'role_name' => $role->name,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => _trans('roles.created_successfully'),
                'data' => $role
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to create role', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('roles.create_failed')
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // Using plain ID
            $role = Role::withTrashed()->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'role' => $role,
                    'permissions' => $this->getAllPermissions()
                ]
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to fetch role details', [
                'role_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('roles.fetch_failed')
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            // Using plain ID
            $role = Role::withTrashed()->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'role' => $role,
                    'permissions' => $this->getAllPermissions()
                ]
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to load role edit form', [
                'role_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('roles.edit_form_failed')
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Using plain ID
            $role = Role::withTrashed()->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:roles,name,' . $roleId,
                'display_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'permissions' => 'nullable|array',
                'permissions.*' => 'string|exists:permissions,name',
                'is_active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => _trans('roles.validation_failed'),
                    'errors' => $validator->errors()
                ], 422);
            }

            $role->update([
                'name' => Str::slug($request->name),
                'guard_name' => 'web',
                'display_name' => $request->display_name,
                'description' => $request->description,
                'permissions' => $request->permissions ?? [],
                'is_active' => $request->boolean('is_active', true)
            ]);

            _log_info('Role updated successfully', [
                'role_id' => $role->id,
                'role_name' => $role->name,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => _trans('roles.updated_successfully'),
                'data' => $role
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to update role', [
                'role_id' => $id,
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('roles.update_failed')
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Using plain ID
            $role = Role::findOrFail($roleId);

            // Check if role is assigned to any users
            $usersWithRole = User::where('role', $role->name)->count();
            if ($usersWithRole > 0) {
                return response()->json([
                    'success' => false,
                    'message' => _trans('roles.cannot_delete_assigned')
                ], 422);
            }

            $role->delete();

            _log_info('Role deleted successfully', [
                'role_id' => $role->id,
                'role_name' => $role->name,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => _trans('roles.deleted_successfully')
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to delete role', [
                'role_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('roles.delete_failed')
            ], 500);
        }
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id)
    {
        try {
            $role = Role::withTrashed()->findOrFail($id);

            $role->restore();

            _log_info('Role restored successfully', [
                'role_id' => $role->id,
                'role_name' => $role->name,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => _trans('roles.restored_successfully')
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to restore role', [
                'role_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('roles.restore_failed')
            ], 500);
        }
    }

    /**
     * Get all available permissions.
     */
    private function getAllPermissions()
    {
        return [
            'dashboard.view',
            'groups.view', 'groups.create', 'groups.edit', 'groups.delete',
            'students.view', 'students.create', 'students.edit', 'students.delete',
            'teachers.view', 'teachers.create', 'teachers.edit', 'teachers.delete',
            'calendar.view', 'calendar.create', 'calendar.edit', 'calendar.delete',
            'attendance.view', 'attendance.create', 'attendance.edit',
            'reports.view', 'reports.generate',
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'settings.view', 'settings.edit',
            'analytics.view',
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
            'permissions.view', 'permissions.assign',
        ];
    }
}
