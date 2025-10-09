<?php

namespace App\Repositories;

use App\Interfaces\RoleInterface;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RolesExport;
use Barryvdh\DomPDF\Facade\Pdf;

class RoleRepository implements RoleInterface
{
    public function getAllRoles(Request $request)
    {
        $query = Role::with(['permissions', 'users'])
            ->orderBy('created_at', 'desc');

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        return $query->paginate($request->get('per_page', 15));
    }

    public function getRoleById($id)
    {
        return Role::with(['permissions', 'users'])->findOrFail($id);
    }

    public function createRole(array $data)
    {
        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'guard_name' => 'web'
            ]);

            if (isset($data['permissions']) && is_array($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            DB::commit();
            return $role;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateRole($id, array $data)
    {
        DB::beginTransaction();
        try {
            $role = Role::findOrFail($id);
            
            $role->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
            ]);

            if (isset($data['permissions']) && is_array($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            DB::commit();
            return $role;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteRole($id)
    {
        $role = Role::findOrFail($id);
        
        // Don't allow deletion of super-admin role
        if ($role->name === 'super-admin') {
            throw new \Exception('Cannot delete super-admin role');
        }

        return $role->delete();
    }

    public function getRolesForDataTable(Request $request)
    {
        $query = Role::with(['permissions', 'users']);

        return DataTables::of($query)
            ->addColumn('permissions_count', function ($role) {
                return $role->permissions->count();
            })
            ->addColumn('users_count', function ($role) {
                return $role->users->count();
            })
            ->addColumn('permissions_list', function ($role) {
                return $role->permissions->pluck('name')->implode(', ');
            })
            ->addColumn('actions', function ($role) {
                $actions = '<div class="btn-group" role="group">';
                
                if (_has_permission('roles.view')) {
                    $actions .= '<button type="button" class="btn btn-sm btn-info view-btn" data-id="' . $role->id . '" title="View"><i class="fas fa-eye"></i></button>';
                }
                
                if (_has_permission('roles.edit')) {
                    $actions .= '<button type="button" class="btn btn-sm btn-primary edit-btn" data-id="' . $role->id . '" title="Edit"><i class="fas fa-edit"></i></button>';
                }
                
                if (_has_permission('roles.delete') && $role->name !== 'super-admin') {
                    $actions .= '<button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $role->id . '" title="Delete"><i class="fas fa-trash"></i></button>';
                }
                
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function searchRoles($query)
    {
        return Role::where('name', 'like', '%' . $query . '%')
            ->orWhere('description', 'like', '%' . $query . '%')
            ->limit(10)
            ->get();
    }

    public function getRolePermissions($roleId)
    {
        $role = Role::with('permissions')->findOrFail($roleId);
        return $role->permissions->pluck('name')->toArray();
    }

    public function assignPermissionsToRole($roleId, array $permissions)
    {
        $role = Role::findOrFail($roleId);
        $role->syncPermissions($permissions);
        return $role;
    }

    public function removePermissionsFromRole($roleId, array $permissions)
    {
        $role = Role::findOrFail($roleId);
        $role->revokePermissionTo($permissions);
        return $role;
    }

    public function getAllPermissions()
    {
        return Permission::orderBy('name')->get();
    }

    public function getPermissionsGroupedByModule()
    {
        return Permission::all()->groupBy(function ($permission) {
            $parts = explode('.', $permission->name);
            return $parts[0] ?? 'general';
        });
    }

    public function exportToExcel()
    {
        return Excel::download(new RolesExport, 'roles.xlsx');
    }

    public function exportToPDF()
    {
        $roles = Role::with(['permissions', 'users'])->get();
        $pdf = Pdf::loadView('exports.roles-pdf', compact('roles'));
        return $pdf->download('roles.pdf');
    }
}
