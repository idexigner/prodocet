<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Log;

class StudentPermissionController extends Controller
{
    /**
     * Display the student permissions management page.
     */
    public function index()
    {
        $students = User::role('student')->with('permissions')->get();
        $studentPermissions = Permission::where('name', 'like', 'student.%')->get();
        
        return view('admin.student-permissions', compact('students', 'studentPermissions'));
    }

    /**
     * Update student permissions.
     */
    public function updatePermissions(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        try {
            $student = User::findOrFail($request->student_id);
            
            // Ensure user is a student
            if (!$student->hasRole('student')) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not a student'
                ], 400);
            }

            // Get all student permissions
            $allStudentPermissions = Permission::where('name', 'like', 'student.%')->pluck('name')->toArray();
            
            // Revoke all student permissions first
            $student->revokePermissionTo($allStudentPermissions);
            
            // Grant selected permissions
            if ($request->has('permissions')) {
                $student->givePermissionTo($request->permissions);
            }

            Log::info('Student permissions updated', [
                'student_id' => $student->id,
                'student_email' => $student->email,
                'permissions' => $request->permissions ?? []
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Student permissions updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating student permissions', [
                'error' => $e->getMessage(),
                'student_id' => $request->student_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating permissions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get student permissions for AJAX requests.
     */
    public function getStudentPermissions($studentId)
    {
        try {
            $student = User::findOrFail($studentId);
            
            if (!$student->hasRole('student')) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not a student'
                ], 400);
            }

            $permissions = $student->permissions->pluck('name')->toArray();

            return response()->json([
                'success' => true,
                'permissions' => $permissions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching permissions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update permissions for multiple students.
     */
    public function bulkUpdatePermissions(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        try {
            $updatedCount = 0;
            $allStudentPermissions = Permission::where('name', 'like', 'student.%')->pluck('name')->toArray();

            foreach ($request->student_ids as $studentId) {
                $student = User::findOrFail($studentId);
                
                if ($student->hasRole('student')) {
                    // Revoke all student permissions first
                    $student->revokePermissionTo($allStudentPermissions);
                    
                    // Grant selected permissions
                    if ($request->has('permissions')) {
                        $student->givePermissionTo($request->permissions);
                    }
                    
                    $updatedCount++;
                }
            }

            Log::info('Bulk student permissions updated', [
                'updated_count' => $updatedCount,
                'permissions' => $request->permissions ?? []
            ]);

            return response()->json([
                'success' => true,
                'message' => "Permissions updated for {$updatedCount} students"
            ]);

        } catch (\Exception $e) {
            Log::error('Error in bulk update student permissions', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating permissions: ' . $e->getMessage()
            ], 500);
        }
    }
}