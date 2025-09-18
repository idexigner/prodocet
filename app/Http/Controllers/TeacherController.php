<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $teachers = User::where('role', 'teacher')
                    ->withTrashed()
                    ->select(['id', 'first_name', 'last_name', 'email', 'phone', 'document_id', 'is_active', 'created_at', 'deleted_at']);

                return DataTables::of($teachers)
                    ->addColumn('full_name', function ($teacher) {
                        return $teacher->first_name . ' ' . $teacher->last_name;
                    })
                    ->addColumn('status', function ($teacher) {
                        if ($teacher->deleted_at) {
                            return '<span class="badge bg-danger">Deleted</span>';
                        }
                        return $teacher->is_active 
                            ? '<span class="badge bg-success">Active</span>'
                            : '<span class="badge bg-warning">Inactive</span>';
                    })
                    ->addColumn('action', function ($teacher) {
                        $actions = '';
                        
                        if (_has_permission(auth()->user(), 'teachers.edit')) {
                            $actions .= '<button class="btn btn-sm btn-primary edit-btn" data-id="' . $teacher->id . '" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button> ';
                        }
                        
                        if (_has_permission(auth()->user(), 'teachers.delete')) {
                            if ($teacher->deleted_at) {
                                $actions .= '<button class="btn btn-sm btn-success restore-btn" data-id="' . $teacher->id . '" title="Restore">
                                    <i class="fas fa-undo"></i>
                                </button>';
                            } else {
                                $actions .= '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $teacher->id . '" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>';
                            }
                        }
                        
                        return $actions;
                    })
                    ->rawColumns(['status', 'action'])
                    ->make(true);
            }

            return view('teachers.index');

        } catch (\Exception $e) {
            _log_error('Failed to fetch teachers', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => _trans('teachers.fetch_failed')
                ], 500);
            }

            return redirect()->back()->with('error', _trans('teachers.fetch_failed'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            return response()->json([
                'success' => true,
                'data' => []
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to load teacher creation form', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('teachers.create_form_failed')
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
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'phone' => 'nullable|string|max:20',
                'document_id' => 'nullable|string|max:50',
                'birth_date' => 'nullable|date',
                'address' => 'nullable|string',
                'emergency_contact' => 'nullable|string|max:255',
                'emergency_phone' => 'nullable|string|max:20',
                'language_preference' => 'in:es,en',
                'is_active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => _trans('teachers.validation_failed'),
                    'errors' => $validator->errors()
                ], 422);
            }

            $teacher = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'document_id' => $request->document_id,
                'birth_date' => $request->birth_date,
                'address' => $request->address,
                'emergency_contact' => $request->emergency_contact,
                'emergency_phone' => $request->emergency_phone,
                'language_preference' => $request->language_preference ?? 'es',
                'role' => 'teacher',
                'is_active' => $request->boolean('is_active', true)
            ]);

            _log_info('Teacher created successfully', [
                'teacher_id' => $teacher->id,
                'teacher_email' => $teacher->email,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => _trans('teachers.created_successfully'),
                'data' => $teacher
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to create teacher', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('teachers.create_failed')
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
            $teacher = User::where('role', 'teacher')->withTrashed()->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'teacher' => $teacher
                ]
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to fetch teacher details', [
                'teacher_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('teachers.fetch_failed')
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
            $teacher = User::where('role', 'teacher')->withTrashed()->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'teacher' => $teacher
                ]
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to load teacher edit form', [
                'teacher_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('teachers.edit_form_failed')
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
            $teacher = User::where('role', 'teacher')->withTrashed()->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'password' => 'nullable|string|min:8|confirmed',
                'phone' => 'nullable|string|max:20',
                'document_id' => 'nullable|string|max:50',
                'birth_date' => 'nullable|date',
                'address' => 'nullable|string',
                'emergency_contact' => 'nullable|string|max:255',
                'emergency_phone' => 'nullable|string|max:20',
                'language_preference' => 'in:es,en',
                'is_active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => _trans('teachers.validation_failed'),
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'document_id' => $request->document_id,
                'birth_date' => $request->birth_date,
                'address' => $request->address,
                'emergency_contact' => $request->emergency_contact,
                'emergency_phone' => $request->emergency_phone,
                'language_preference' => $request->language_preference ?? 'es',
                'is_active' => $request->boolean('is_active', true)
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $teacher->update($updateData);

            _log_info('Teacher updated successfully', [
                'teacher_id' => $teacher->id,
                'teacher_email' => $teacher->email,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => _trans('teachers.updated_successfully'),
                'data' => $teacher
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to update teacher', [
                'teacher_id' => $id,
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('teachers.update_failed')
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
            $teacher = User::where('role', 'teacher')->findOrFail($id);

            $teacher->delete();

            _log_info('Teacher deleted successfully', [
                'teacher_id' => $teacher->id,
                'teacher_email' => $teacher->email,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => _trans('teachers.deleted_successfully')
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to delete teacher', [
                'teacher_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('teachers.delete_failed')
            ], 500);
        }
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id)
    {
        try {
            $teacher = User::withTrashed()->where('role', 'teacher')->findOrFail($id);

            $teacher->restore();

            _log_info('Teacher restored successfully', [
                'teacher_id' => $teacher->id,
                'teacher_email' => $teacher->email,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => _trans('teachers.restored_successfully')
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to restore teacher', [
                'teacher_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('teachers.restore_failed')
            ], 500);
        }
    }
}
