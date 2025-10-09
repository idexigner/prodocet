<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\StudentDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
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
                        
                        if (_has_permission('students.edit')) {
                            $actions .= '<button class="btn btn-sm btn-primary edit-btn" data-id="' . $student->id . '" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button> ';
                        }
                        
                        if (_has_permission('students.delete')) {
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

            return view('students.students');

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
                    'message' => _trans('students.validation_failed'),
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

            $student = User::create([
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
                'role' => 'student',
                'is_active' => $request->boolean('is_active', true),
                'documents' => $documentPaths
            ]);

            // Assign student role using Spatie Permission
            $student->assignRole('student');

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
                    'message' => _trans('students.validation_failed'),
                    'errors' => $validator->errors()
                ], 422);
            }

            // Handle document management
            $existingDocuments = $student->documents ?? [];
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

    /**
     * Delete a specific document for a student.
     */
    public function deleteDocument(Request $request, string $id)
    {
        try {
            $student = User::where('role', 'student')->findOrFail($id);
            $documentName = $request->input('document_name');
            
            if (!$documentName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document name is required'
                ], 400);
            }

            $documents = $student->documents;
            $documents = array_filter($documents, function($doc) use ($documentName) {
                return $doc !== $documentName;
            });

            // Delete file from storage
            $filePath = storage_path('app/public/documents/' . $documentName);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Update student documents
            $student->update(['documents' => array_values($documents)]);

            _log_info('Student document deleted', [
                'student_id' => $student->id,
                'document_name' => $documentName,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully'
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to delete student document', [
                'error' => $e->getMessage(),
                'student_id' => $id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete document'
            ], 500);
        }
    }

    /**
     * Get student documents.
     */
    public function getDocuments(string $id)
    {
        try {
            $student = User::where('role', 'student')->findOrFail($id);
            $documents = $student->studentDocuments()->active()->get();

            return response()->json([
                'success' => true,
                'data' => $documents
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to fetch student documents', [
                'error' => $e->getMessage(),
                'student_id' => $id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('student_documents.fetch_failed')
            ], 500);
        }
    }

    /**
     * Store a new student document.
     */
    public function storeDocument(Request $request, string $id)
    {
        try {
            $student = User::where('role', 'student')->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'document_type' => 'required|in:C.C.,C.E.,T.I.,Pa.,Nit,other',
                'document_number' => 'required|string|max:255',
                'document_name' => 'nullable|string|max:255',
                'is_primary' => 'in:0,1,true,false',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => _trans('student_documents.validation_failed'),
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if document already exists
            $existingDocument = StudentDocument::where('student_id', $student->id)
                ->where('document_type', $request->document_type)
                ->where('document_number', $request->document_number)
                ->first();

            if ($existingDocument) {
                return response()->json([
                    'success' => false,
                    'message' => _trans('student_documents.document_exists')
                ], 422);
            }

            // If setting as primary, unset other primary documents
            if ($request->boolean('is_primary')) {
                StudentDocument::where('student_id', $student->id)
                    ->update(['is_primary' => false]);
            }

            $document = StudentDocument::create([
                'student_id' => $student->id,
                'document_type' => $request->document_type,
                'document_number' => $request->document_number,
                'document_name' => $request->document_name,
                'is_primary' => $request->boolean('is_primary'),
                'notes' => $request->notes,
            ]);

            _log_info('Student document created', [
                'student_id' => $student->id,
                'document_id' => $document->id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => _trans('student_documents.document_added'),
                'data' => $document
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to create student document', [
                'error' => $e->getMessage(),
                'student_id' => $id,
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('student_documents.create_failed')
            ], 500);
        }
    }

    /**
     * Update a student document.
     */
    public function updateDocument(Request $request, string $id, string $documentId)
    {
        try {
            $student = User::where('role', 'student')->findOrFail($id);
            $document = StudentDocument::where('student_id', $student->id)
                ->findOrFail($documentId);

            $validator = Validator::make($request->all(), [
                'document_type' => 'required|in:C.C.,C.E.,T.I.,Pa.,Nit,other',
                'document_number' => 'required|string|max:255',
                'document_name' => 'nullable|string|max:255',
                'is_primary' => 'in:0,1,true,false',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => _trans('student_documents.validation_failed'),
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if document already exists (excluding current document)
            $existingDocument = StudentDocument::where('student_id', $student->id)
                ->where('document_type', $request->document_type)
                ->where('document_number', $request->document_number)
                ->where('id', '!=', $documentId)
                ->first();

            if ($existingDocument) {
                return response()->json([
                    'success' => false,
                    'message' => _trans('student_documents.document_exists')
                ], 422);
            }

            // If setting as primary, unset other primary documents
            if ($request->boolean('is_primary')) {
                StudentDocument::where('student_id', $student->id)
                    ->where('id', '!=', $documentId)
                    ->update(['is_primary' => false]);
            }

            $document->update([
                'document_type' => $request->document_type,
                'document_number' => $request->document_number,
                'document_name' => $request->document_name,
                'is_primary' => $request->boolean('is_primary'),
                'notes' => $request->notes,
            ]);

            _log_info('Student document updated', [
                'student_id' => $student->id,
                'document_id' => $document->id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => _trans('student_documents.document_updated'),
                'data' => $document
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to update student document', [
                'error' => $e->getMessage(),
                'student_id' => $id,
                'document_id' => $documentId,
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('student_documents.update_failed')
            ], 500);
        }
    }

    /**
     * Delete a student document.
     */
    public function deleteStudentDocument(string $id, string $documentId)
    {
        try {
            $student = User::where('role', 'student')->findOrFail($id);
            $document = StudentDocument::where('student_id', $student->id)
                ->findOrFail($documentId);

            $document->delete();

            _log_info('Student document deleted', [
                'student_id' => $student->id,
                'document_id' => $document->id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => _trans('student_documents.document_deleted')
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to delete student document', [
                'error' => $e->getMessage(),
                'student_id' => $id,
                'document_id' => $documentId,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('student_documents.delete_failed')
            ], 500);
        }
    }

    /**
     * Set a document as primary.
     */
    public function setPrimaryDocument(string $id, string $documentId)
    {
        try {
            $student = User::where('role', 'student')->findOrFail($id);
            $document = StudentDocument::where('student_id', $student->id)
                ->findOrFail($documentId);

            DB::transaction(function () use ($student, $document) {
                // Unset all other primary documents
                StudentDocument::where('student_id', $student->id)
                    ->update(['is_primary' => false]);

                // Set this document as primary
                $document->update(['is_primary' => true]);
            });

            _log_info('Student document set as primary', [
                'student_id' => $student->id,
                'document_id' => $document->id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => _trans('student_documents.document_set_primary')
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to set primary document', [
                'error' => $e->getMessage(),
                'student_id' => $id,
                'document_id' => $documentId,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('student_documents.set_primary_failed')
            ], 500);
        }
    }

    /**
     * Get student availability
     */
    public function getAvailability(string $id)
    {
        try {
            $student = User::findOrFail($id);
            
            // Get all slots
            $slots = \App\Models\Slot::where('is_active', true)
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->get();

            // Get student's current availability
            $studentAvailabilities = \App\Models\StudentAvailability::where('student_id', $id)
                ->get()
                ->keyBy('slot_id');

            // Group slots by day
            $availabilityByDay = [];
            foreach ($slots as $slot) {
                $day = $slot->day_of_week;
                if (!isset($availabilityByDay[$day])) {
                    $availabilityByDay[$day] = [];
                }
                
                $isAvailable = isset($studentAvailabilities[$slot->id]) 
                    ? $studentAvailabilities[$slot->id]->is_available 
                    : false;
                
                $availabilityByDay[$day][] = [
                    'id' => $slot->id,
                    'start_time' => $slot->start_time,
                    'end_time' => $slot->end_time,
                    'is_available' => $isAvailable,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'availability_by_day' => $availabilityByDay,
                    'student' => [
                        'id' => $student->id,
                        'name' => $student->first_name . ' ' . $student->last_name,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => _trans('student_availability.fetch_failed')
            ], 500);
        }
    }

    /**
     * Save student availability
     */
    public function saveAvailability(Request $request, string $id)
    {
        try {
            $student = User::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'availability' => 'required|array',
                'availability.*' => 'boolean',
                'notes' => 'string|nullable',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => _trans('student_availability.validation_failed'),
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::transaction(function () use ($student, $request) {
                // Clear existing availability
                \App\Models\StudentAvailability::where('student_id', $student->id)->delete();
                
                // Insert new availability
                $availabilityData = [];
                foreach ($request->availability as $slotId => $isAvailable) {
                    if ($isAvailable) {
                        $slot = \App\Models\Slot::find($slotId);
                        if ($slot) {
                            $availabilityData[] = [
                                'student_id' => $student->id,
                                'slot_id' => $slotId,
                                'day_of_week' => $slot->day_of_week,
                                'is_available' => true,
                                'priority' => 1,
                                'effective_from' => now(),
                                'notes' => $request->notes,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }
                }
                
                if (!empty($availabilityData)) {
                    \App\Models\StudentAvailability::insert($availabilityData);
                }
            });

            _log_info('Student availability saved', [
                'student_id' => $student->id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => _trans('student_availability.availability_saved')
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to save student availability', [
                'error' => $e->getMessage(),
                'student_id' => $id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('student_availability.save_failed')
            ], 500);
        }
    }

    /**
     * Clear student availability
     */
    public function clearAvailability(string $id)
    {
        try {
            $student = User::findOrFail($id);

            \App\Models\StudentAvailability::where('student_id', $student->id)->delete();

            _log_info('Student availability cleared', [
                'student_id' => $student->id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => _trans('student_availability.availability_cleared')
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to clear student availability', [
                'error' => $e->getMessage(),
                'student_id' => $id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => _trans('student_availability.clear_failed')
            ], 500);
        }
    }
}
