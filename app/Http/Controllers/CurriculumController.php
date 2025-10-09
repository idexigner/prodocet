<?php

namespace App\Http\Controllers;

use App\Models\CurriculumTopic;
use App\Models\Language;
use App\Models\CourseLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\CurriculumTopicsExport;

class CurriculumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $curriculumTopics = CurriculumTopic::withTrashed()
                    ->with(['language', 'level'])
                    ->select(['id', 'title', 'description', 'content', 'documents', 'order_index', 'language_id', 'level_id', 'is_active', 'created_at', 'deleted_at']);

                // Apply filters
                if ($request->has('language_id') && $request->language_id) {
                    $curriculumTopics->where('language_id', $request->language_id);
                }
                
                if ($request->has('level_id') && $request->level_id) {
                    $curriculumTopics->where('level_id', $request->level_id);
                }
                
                if ($request->has('status') && $request->status) {
                    if ($request->status === 'active') {
                        $curriculumTopics->where('is_active', true);
                    } elseif ($request->status === 'inactive') {
                        $curriculumTopics->where('is_active', false);
                    }
                }

                return DataTables::of($curriculumTopics)
                    ->addColumn('language_name', function ($topic) {
                        return $topic->language ? $topic->language->name : 'N/A';
                    })
                    ->addColumn('course_level_name', function ($topic) {
                        return $topic->level ? $topic->level->name : 'N/A';
                    })
                    ->addColumn('status', function ($topic) {
                        if ($topic->deleted_at) {
                            return '<span class="badge bg-danger">Deleted</span>';
                        }
                        return $topic->is_active 
                            ? '<span class="badge bg-success">Active</span>'
                            : '<span class="badge bg-warning">Inactive</span>';
                    })
                    ->addColumn('actions', function ($topic) {
                        $actions = '';
                        
                        if (_has_permission('curriculum.edit')) {
                            $actions .= '<button class="btn btn-sm btn-primary edit-btn" data-id="' . $topic->id . '" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button> ';
                        }
                        
                        if (_has_permission('curriculum.delete')) {
                            if ($topic->deleted_at) {
                                $actions .= '<button class="btn btn-sm btn-success restore-btn" data-id="' . $topic->id . '" title="Restore">
                                    <i class="fas fa-undo"></i>
                                </button>';
                            } else {
                                $actions .= '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $topic->id . '" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>';
                            }
                        }
                        
                        return $actions;
                    })
                    ->rawColumns(['status', 'actions'])
                    ->make(true);
            }

            $languages = \App\Models\Language::all();
            $levels = \App\Models\CourseLevel::all();
            
            return view('curriculum.index', compact('languages', 'levels'));

        } catch (\Exception $e) {
            _log_error('Failed to fetch curriculum topics', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('curriculum.messages.fetch_failed')
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->json([
            'success' => true,
            'message' => __('curriculum.messages.create_form_loaded')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:500',
                'content' => 'nullable|string',
                'order_index' => 'nullable|integer|min:0',
                'language_id' => 'required|exists:languages,id',
                'level_id' => 'required|exists:course_levels,id',
                'documents' => 'nullable|array',
                'documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
                'is_active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => __('curriculum.messages.validation_failed'),
                    'errors' => $validator->errors()
                ], 422);
            }

            // Handle document uploads
            $documentPaths = [];
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $document) {
                    $filename = time() . '_' . $document->getClientOriginalName();
                    $document->storeAs('public/curriculum_documents', $filename);
                    $documentPaths[] = $filename;
                }
            }

            $curriculumTopic = CurriculumTopic::create([
                'title' => $request->title,
                'description' => $request->description,
                'content' => $request->content,
                'documents' => $documentPaths,
                'order_index' => $request->order_index ?? 0,
                'language_id' => $request->language_id,
                'level_id' => $request->level_id,
                'is_active' => $request->boolean('is_active', true)
            ]);

            _log_info('Curriculum topic created successfully', [
                'curriculum_topic_id' => $curriculumTopic->id,
                'title' => $curriculumTopic->title,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('curriculum.messages.created'),
                'data' => $curriculumTopic
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to create curriculum topic', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('curriculum.messages.create_failed')
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $curriculumTopic = CurriculumTopic::withTrashed()->with(['language', 'level'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $curriculumTopic
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('curriculum.messages.not_found')
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $curriculumTopic = CurriculumTopic::withTrashed()->with(['language', 'level'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $curriculumTopic
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('curriculum.messages.not_found')
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:500',
                'content' => 'nullable|string',
                'order_index' => 'nullable|integer|min:0',
                'language_id' => 'required|exists:languages,id',
                'level_id' => 'required|exists:course_levels,id',
                'documents' => 'nullable|array',
                'documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
                'is_active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => __('curriculum.messages.validation_failed'),
                    'errors' => $validator->errors()
                ], 422);
            }

            $curriculumTopic = CurriculumTopic::findOrFail($id);
            
            // Handle document uploads and removals
            $existingDocuments = $curriculumTopic->documents;
            $newDocuments = [];
            $documentsToRemove = [];
            
            // Get documents to remove
            if ($request->has('documents_to_remove') && $request->documents_to_remove) {
                $documentsToRemove = explode(',', $request->documents_to_remove);
            }
            
            // Filter out removed documents
            $remainingDocuments = array_filter($existingDocuments, function($doc) use ($documentsToRemove) {
                return !in_array($doc, $documentsToRemove);
            });
            
            // Handle new document uploads
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $document) {
                    $filename = time() . '_' . $document->getClientOriginalName();
                    $document->storeAs('public/curriculum_documents', $filename);
                    $newDocuments[] = $filename;
                }
            }
            
            // Merge remaining and new documents
            $allDocuments = array_merge($remainingDocuments, $newDocuments);
            
            // Delete removed documents from storage
            foreach ($documentsToRemove as $docToRemove) {
                $filePath = storage_path('app/public/curriculum_documents/' . $docToRemove);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            
            $curriculumTopic->update([
                'title' => $request->title,
                'description' => $request->description,
                'content' => $request->content,
                'documents' => $allDocuments,
                'order_index' => $request->order_index ?? 0,
                'language_id' => $request->language_id,
                'level_id' => $request->level_id,
                'is_active' => $request->boolean('is_active', true)
            ]);

            _log_info('Curriculum topic updated successfully', [
                'curriculum_topic_id' => $curriculumTopic->id,
                'title' => $curriculumTopic->title,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('curriculum.messages.updated'),
                'data' => $curriculumTopic
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to update curriculum topic', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('curriculum.messages.update_failed')
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $curriculumTopic = CurriculumTopic::findOrFail($id);
            $curriculumTopic->delete();
            
            _log_info('Curriculum topic deleted successfully', [
                'curriculum_topic_id' => $curriculumTopic->id,
                'title' => $curriculumTopic->title,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('curriculum.messages.deleted')
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to delete curriculum topic', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('curriculum.messages.delete_failed')
            ], 500);
        }
    }

    /**
     * Restore the specified resource.
     */
    public function restore($id)
    {
        try {
            $curriculumTopic = CurriculumTopic::withTrashed()->findOrFail($id);
            $curriculumTopic->restore();
            
            _log_info('Curriculum topic restored successfully', [
                'curriculum_topic_id' => $curriculumTopic->id,
                'title' => $curriculumTopic->title,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('curriculum.messages.restored')
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to restore curriculum topic', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('curriculum.messages.restore_failed')
            ], 500);
        }
    }

    /**
     * Export curriculum topics to Excel
     */
    public function exportExcel()
    {
        try {
            $curriculumTopics = CurriculumTopic::withTrashed()
                ->with(['language', 'level'])
                ->get();

            $filename = 'curriculum_topics_' . date('Y-m-d_H-i-s') . '.xlsx';
            
            return Excel::download(new CurriculumTopicsExport($curriculumTopics), $filename);
        } catch (\Exception $e) {
            _log_error('Failed to export curriculum topics to Excel', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return redirect()->back()->with('error', __('curriculum.messages.export_error'));
        }
    }

    /**
     * Export curriculum topics to PDF
     */
    public function exportPdf()
    {
        try {
            $curriculumTopics = CurriculumTopic::withTrashed()
                ->with(['language', 'level'])
                ->get();

            $pdf = PDF::loadView('exports.curriculum-pdf', compact('curriculumTopics'));
            $filename = 'curriculum_topics_' . date('Y-m-d_H-i-s') . '.pdf';
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            _log_error('Failed to export curriculum topics to PDF', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return redirect()->back()->with('error', __('curriculum.messages.export_error'));
        }
    }

    /**
     * Get curriculum topics grouped by language and level for accordion view
     */
    public function getGroupedCurriculum(Request $request)
    {
        try {
            $curriculumTopics = CurriculumTopic::withTrashed()
                ->with(['language', 'level'])
                ->select(['id', 'title', 'description', 'content', 'documents', 'order_index', 'language_id', 'level_id', 'is_active', 'created_at', 'deleted_at'])
                ->orderBy('order_index')
                ->get();

            // Group by language and level
            $grouped = [];
            foreach ($curriculumTopics as $topic) {
                $languageName = $topic->language ? $topic->language->name : 'Unknown Language';
                $levelName = $topic->level ? $topic->level->name : 'Unknown Level';
                $key = $topic->language_id . '_' . $topic->level_id;
                
                if (!isset($grouped[$key])) {
                    $grouped[$key] = [
                        'language_id' => $topic->language_id,
                        'level_id' => $topic->level_id,
                        'language_name' => $languageName,
                        'level_name' => $levelName,
                        'topics' => []
                    ];
                }
                
                $grouped[$key]['topics'][] = $topic;
            }

            return response()->json([
                'success' => true,
                'data' => array_values($grouped)
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to fetch grouped curriculum topics', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('curriculum.messages.fetch_failed')
            ], 500);
        }
    }

    /**
     * Search curriculum topics
     */
    public function search(Request $request)
    {
        // Implementation for search functionality
        return response()->json(['message' => 'Search functionality']);
    }
}