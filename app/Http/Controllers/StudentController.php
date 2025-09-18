<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $students = User::where('role', 'student')
                    ->withTrashed()
                    ->select(['id', 'first_name', 'last_name', 'email', 'phone', 'document_id', 'is_active', 'created_at', 'deleted_at']);

                return DataTables::of($students)
                    ->addColumn('full_name', function ($student) {
                        return $student->first_name . ' ' . $student->last_name;
                    })
                    ->addColumn('status', function ($student) {
                        if ($student->deleted_at) {
                            return '<span class="badge bg-danger">Deleted</span>';
                        }
                        return $student->is_active 
                            ? '<span class="badge bg-success">Active</span>'
                            : '<span class="badge bg-warning">Inactive</span>';
                    })
                    ->addColumn('action', function ($student) {
                        $actions = '';
                        
                        if (_has_permission(auth()->user(), 'students.edit')) {
                            $actions .= '<button class="btn btn-sm btn-primary edit-btn" data-id="' . $student->id . '" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button> ';
                        }
                        
                        if (_has_permission(auth()->user(), 'students.delete')) {
                            if ($student->deleted_at) {
                                $actions .= '<button class="btn btn-sm btn-success restore-btn" data-id="' . $student->id . '" title="Restore">
                                    <i class="fas fa-undo"></i>
                                </button>';
                            } else {
                                $actions .= '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $student->id . '" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>';
                            }
                        }
                        
                        return $actions;
                    })
                    ->rawColumns(['status', 'action'])
                    ->make(true);
            }

            return view('students.index');

        } catch (\Exception $e) {
            _log_error('Failed to fetch students', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => _trans('students.fetch_failed')
                ], 500);
            }

            return redirect()->back()->with('error', _trans('students.fetch_failed'));
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
            _log_error('Failed to load student creation form', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('students.create_form_failed')
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
                    'message' => _trans('students.validation_failed'),
                    'errors' => $validator->errors()
                ], 422);
            }

            $student = User::create([
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
                'role' => 'student',
                'is_active' => $request->boolean('is_active', true)
            ]);

            _log_info('Student created successfully', [
                'student_id' => $student->id,
                'student_email' => $student->email,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => _trans('students.created_successfully'),
                'data' => $student
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to create student', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('students.create_failed')
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $student = User::where('role', 'student')->withTrashed()->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'student' => $student
                ]
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to fetch student details', [
                'student_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('students.fetch_failed')
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $student = User::where('role', 'student')->withTrashed()->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'student' => $student
                ]
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to load student edit form', [
                'student_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('students.edit_form_failed')
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $student = User::where('role', 'student')->withTrashed()->findOrFail($id);
   
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
                    'message' => _trans('students.validation_failed'),
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

            $student->update($updateData);

            _log_info('Student updated successfully', [
                'student_id' => $student->id,
                'student_email' => $student->email,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => _trans('students.updated_successfully'),
                'data' => $student
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to update student', [
                'student_id' => $id,
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('students.update_failed')
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $student = User::where('role', 'student')->findOrFail($id);

            $student->delete();

            _log_info('Student deleted successfully', [
                'student_id' => $student->id,
                'student_email' => $student->email,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => _trans('students.deleted_successfully')
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to delete student', [
                'student_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('students.delete_failed')
            ], 500);
        }
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id)
    {
        try {
            $student = User::withTrashed()->where('role', 'student')->findOrFail($id);

            $student->restore();

            _log_info('Student restored successfully', [
                'student_id' => $student->id,
                'student_email' => $student->email,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => _trans('students.restored_successfully')
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to restore student', [
                'student_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('students.restore_failed')
            ], 500);
        }
    }
}
