<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TeacherAvailability;
use App\Models\Slot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
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
                    ->filter(function ($query) use ($request) {
                        $searchValue = $request->get('search')['value'] ?? '';
                        if (!empty($searchValue)) {
                            $query->where(function ($q) use ($searchValue) {
                                $q->where('first_name', 'like', "%{$searchValue}%")
                                  ->orWhere('last_name', 'like', "%{$searchValue}%")
                                  ->orWhere('email', 'like', "%{$searchValue}%")
                                  ->orWhere('phone', 'like', "%{$searchValue}%")
                                  ->orWhere('document_id', 'like', "%{$searchValue}%")
                                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$searchValue}%"]);
                            });
                        }
                    })
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
                        
                        if (_has_permission('teachers.edit')) {
                            $actions .= '<button class="btn btn-sm btn-primary edit-btn" data-id="' . $teacher->id . '" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button> ';
                        }
                        
                        if (_has_permission('teachers.delete')) {
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

            return view('teachers.teachers');

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
                'passport_number' => 'nullable|string|max:50|regex:/^[A-Za-z0-9]+$/',
                'birth_date' => 'nullable|date',
                'address' => 'nullable|string',
                'emergency_contact' => 'nullable|string|max:255',
                'emergency_phone' => 'nullable|string|max:20',
                'language_preference' => 'in:es,en',
                'is_active' => 'boolean',
                'documents' => 'nullable|array',
                'documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => _trans('teachers.validation_failed'),
                    'errors' => $validator->errors()
                ], 422);
            }

            // Handle document uploads
            $documentPaths = [];
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $document) {
                    $filename = time() . '_' . $document->getClientOriginalName();
                    $path = $document->storeAs('documents', $filename, 'public');
                    $documentPaths[] = $filename;
                }
            }

            $teacher = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'document_id' => $request->document_id,
                'passport_number' => $request->passport_number,
                'birth_date' => $request->birth_date,
                'address' => $request->address,
                'emergency_contact' => $request->emergency_contact,
                'emergency_phone' => $request->emergency_phone,
                'language_preference' => $request->language_preference ?? 'es',
                'role' => 'teacher',
                'is_active' => $request->boolean('is_active', true),
                'documents' => $documentPaths
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
                'passport_number' => 'nullable|string|max:50|regex:/^[A-Za-z0-9]+$/',
                'birth_date' => 'nullable|date',
                'address' => 'nullable|string',
                'emergency_contact' => 'nullable|string|max:255',
                'emergency_phone' => 'nullable|string|max:20',
                'language_preference' => 'in:es,en',
                'is_active' => 'boolean',
                'documents' => 'nullable|array',
                'documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => _trans('teachers.validation_failed'),
                    'errors' => $validator->errors()
                ], 422);
            }

            // Handle document management
            $existingDocuments = $teacher->documents ?? [];
            $newDocuments = [];
            
            // Process new document uploads
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $document) {
                    $filename = time() . '_' . $document->getClientOriginalName();
                    $path = $document->storeAs('documents', $filename, 'public');
                    $newDocuments[] = $filename;
                }
            }
            
            // Handle document removal (from frontend)
            $documentsToKeep = $existingDocuments;
            if ($request->has('documents_to_remove')) {
                $documentsToRemove = explode(',', $request->input('documents_to_remove'));
                $documentsToKeep = array_diff($existingDocuments, $documentsToRemove);
                
                // Delete removed files from storage
                foreach ($documentsToRemove as $docToRemove) {
                    if (!empty($docToRemove)) {
                        $filePath = storage_path('app/public/documents/' . $docToRemove);
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                    }
                }
            }
            
            // Combine existing (kept) documents with new documents
            $finalDocuments = array_merge(array_values($documentsToKeep), $newDocuments);

            $updateData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'document_id' => $request->document_id,
                'passport_number' => $request->passport_number,
                'birth_date' => $request->birth_date,
                'address' => $request->address,
                'emergency_contact' => $request->emergency_contact,
                'emergency_phone' => $request->emergency_phone,
                'language_preference' => $request->language_preference ?? 'es',
                'is_active' => $request->boolean('is_active', true),
                'documents' => $finalDocuments
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

    /**
     * Delete a specific document for a teacher.
     */
    public function deleteDocument(Request $request, string $id)
    {
        try {
            $teacher = User::where('role', 'teacher')->findOrFail($id);
            $documentName = $request->input('document_name');
            
            if (!$documentName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document name is required'
                ], 400);
            }

            $documents = $teacher->documents;
            $documents = array_filter($documents, function($doc) use ($documentName) {
                return $doc !== $documentName;
            });

            // Delete file from storage
            $filePath = storage_path('app/public/documents/' . $documentName);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Update teacher documents
            $teacher->update(['documents' => array_values($documents)]);

            _log_info('Teacher document deleted', [
                'teacher_id' => $teacher->id,
                'document_name' => $documentName,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully'
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to delete teacher document', [
                'error' => $e->getMessage(),
                'teacher_id' => $id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete document'
            ], 500);
        }
    }

    /**
     * Get teacher assigned courses.
     */
    public function getCourses(string $id)
    {
        try {
            $teacher = User::where('role', 'teacher')->findOrFail($id);
            
            // Get teacher's assigned courses
            $assignedCourses = DB::table('teacher_course')
                ->where('teacher_id', $teacher->id)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $assignedCourses
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to fetch teacher courses', [
                'error' => $e->getMessage(),
                'teacher_id' => $id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch teacher courses'
            ], 500);
        }
    }

    /**
     * Save teacher course assignments.
     */
    public function saveCourses(Request $request, string $id)
    {
        try {
            $teacher = User::where('role', 'teacher')->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'course_ids' => 'required|array',
                'course_ids.*' => 'integer|exists:courses,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::transaction(function () use ($teacher, $request) {
                // Clear existing course assignments
                DB::table('teacher_course')->where('teacher_id', $teacher->id)->delete();
                
                // Insert new course assignments
                $courseAssignments = [];
                foreach ($request->course_ids as $courseId) {
                    $courseAssignments[] = [
                        'teacher_id' => $teacher->id,
                        'course_id' => $courseId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                
                if (!empty($courseAssignments)) {
                    DB::table('teacher_course')->insert($courseAssignments);
                }
            });

            _log_info('Teacher courses saved', [
                'teacher_id' => $teacher->id,
                'courses_count' => count($request->course_ids),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Teacher courses saved successfully'
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to save teacher courses', [
                'error' => $e->getMessage(),
                'teacher_id' => $id,
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save teacher courses'
            ], 500);
        }
    }

    /**
     * Get teacher availability data.
     */
    public function getAvailability(string $id)
    {
        try {
            $teacher = User::where('role', 'teacher')->findOrFail($id);
            
            // Get all slots grouped by day
            $slots = Slot::orderBy('day_of_week')
                ->orderBy('start_time')
                ->get()
                ->groupBy('day_of_week');
            
            // Get teacher's current availability
            $availability = TeacherAvailability::where('teacher_id', $teacher->id)
                ->pluck('is_available', 'slot_id')
                ->toArray();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'teacher' => $teacher,
                    'slots' => $slots,
                    'availability' => $availability
                ]
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to fetch teacher availability', [
                'error' => $e->getMessage(),
                'teacher_id' => $id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('teacher_availability.fetch_failed')
            ], 500);
        }
    }

    /**
     * Save teacher availability.
     */
    public function saveAvailability(Request $request, string $id)
    {
        try {
            $teacher = User::where('role', 'teacher')->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'availability' => 'required|array',
                'availability.*' => 'in:0,1,true,false',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => _trans('teacher_availability.validation_failed'),
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::transaction(function () use ($teacher, $request) {
                // Clear existing availability
                TeacherAvailability::where('teacher_id', $teacher->id)->delete();
                
                // Insert new availability
                $availabilityData = [];
                foreach ($request->availability as $slotId => $isAvailable) {
                    if ($isAvailable) {
                        // Get the slot to get the day_of_week
                        $slot = Slot::find($slotId);
                        if ($slot) {
                            $availabilityData[] = [
                                'teacher_id' => $teacher->id,
                                'slot_id' => $slotId,
                                'day_of_week' => $slot->day_of_week,
                                'is_available' => true,
                                'effective_from' => now()->toDateString(),
                                'notes' => $request->notes,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }
                }
                
                if (!empty($availabilityData)) {
                    TeacherAvailability::insert($availabilityData);
                }
            });

            _log_info('Teacher availability saved', [
                'teacher_id' => $teacher->id,
                'slots_count' => count($request->availability),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => _trans('teacher_availability.availability_saved')
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to save teacher availability', [
                'error' => $e->getMessage(),
                'teacher_id' => $id,
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('teacher_availability.save_failed')
            ], 500);
        }
    }

    /**
     * Clear teacher availability.
     */
    public function clearAvailability(string $id)
    {
        try {
            $teacher = User::where('role', 'teacher')->findOrFail($id);

            TeacherAvailability::where('teacher_id', $teacher->id)->delete();

            _log_info('Teacher availability cleared', [
                'teacher_id' => $teacher->id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => _trans('teacher_availability.availability_cleared')
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to clear teacher availability', [
                'error' => $e->getMessage(),
                'teacher_id' => $id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('teacher_availability.clear_failed')
            ], 500);
        }
    }
}
