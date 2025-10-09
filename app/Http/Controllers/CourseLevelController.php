<?php

namespace App\Http\Controllers;

use App\Models\CourseLevel;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class CourseLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $courseLevels = CourseLevel::withTrashed()
                    ->with('language')
                    ->select(['id', 'code', 'name', 'description', 'order_index', 'language_id', 'status', 'is_active', 'created_at', 'deleted_at']);

                return DataTables::of($courseLevels)
                    ->addColumn('language_name', function ($courseLevel) {
                        return $courseLevel->language ? $courseLevel->language->name : 'N/A';
                    })
                    ->addColumn('status', function ($courseLevel) {
                        if ($courseLevel->deleted_at) {
                            return '<span class="badge bg-danger">Deleted</span>';
                        }
                        return $courseLevel->is_active 
                            ? '<span class="badge bg-success">Active</span>'
                            : '<span class="badge bg-warning">Inactive</span>';
                    })
                    ->addColumn('actions', function ($courseLevel) {
                        $actions = '';
                        
                        if (_has_permission('course-levels.edit')) {
                            $actions .= '<button class="btn btn-sm btn-primary edit-btn" data-id="' . $courseLevel->id . '" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button> ';
                        }
                        
                        if (_has_permission('course-levels.delete')) {
                            if ($courseLevel->deleted_at) {
                                $actions .= '<button class="btn btn-sm btn-success restore-btn" data-id="' . $courseLevel->id . '" title="Restore">
                                    <i class="fas fa-undo"></i>
                                </button>';
                            } else {
                                $actions .= '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $courseLevel->id . '" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>';
                            }
                        }
                        
                        return $actions;
                    })
                    ->rawColumns(['status', 'actions'])
                    ->make(true);
            }

            return view('settings.course-levels.course-levels');

        } catch (\Exception $e) {
            _log_error('Failed to fetch course levels', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('settings.course_levels.messages.fetch_failed')
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
            'message' => __('settings.course_levels.messages.create_form_loaded')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|max:10|unique:course_levels,code',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:500',
                'order_index' => 'nullable|integer|min:0',
                'language_id' => 'nullable|exists:languages,id',
                'status' => 'required|in:active,inactive',
                'is_active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => __('settings.course_levels.messages.validation_failed'),
                    'errors' => $validator->errors()
                ], 422);
            }

            $courseLevel = CourseLevel::create([
                'code' => $request->code,
                'name' => $request->name,
                'description' => $request->description,
                'order_index' => $request->order_index ?? 0,
                'language_id' => $request->language_id,
                'status' => $request->status,
                'is_active' => $request->boolean('is_active', true)
            ]);

            _log_info('Course level created successfully', [
                'level_id' => $courseLevel->id,
                'code' => $courseLevel->code,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('settings.course_levels.messages.created'),
                'data' => $courseLevel
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to create course level', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('settings.course_levels.messages.create_failed')
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $courseLevel = CourseLevel::withTrashed()->with('language')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $courseLevel
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('settings.course_levels.messages.not_found')
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $courseLevel = CourseLevel::withTrashed()->with('language')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $courseLevel
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('settings.course_levels.messages.not_found')
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
                'code' => 'required|string|max:10|unique:course_levels,code,' . $id,
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:500',
                'order_index' => 'nullable|integer|min:0',
                'language_id' => 'nullable|exists:languages,id',
                'status' => 'required|in:active,inactive',
                'is_active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => __('settings.course_levels.messages.validation_failed'),
                    'errors' => $validator->errors()
                ], 422);
            }

            $courseLevel = CourseLevel::findOrFail($id);
            $courseLevel->update([
                'code' => $request->code,
                'name' => $request->name,
                'description' => $request->description,
                'order_index' => $request->order_index ?? 0,
                'language_id' => $request->language_id,
                'status' => $request->status,
                'is_active' => $request->boolean('is_active', true)
            ]);

            _log_info('Course level updated successfully', [
                'level_id' => $courseLevel->id,
                'code' => $courseLevel->code,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('settings.course_levels.messages.updated'),
                'data' => $courseLevel
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to update course level', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('settings.course_levels.messages.update_failed')
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $courseLevel = CourseLevel::findOrFail($id);
            $courseLevel->delete();
            
            _log_info('Course level deleted successfully', [
                'level_id' => $courseLevel->id,
                'code' => $courseLevel->code,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('settings.course_levels.messages.deleted')
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to delete course level', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('settings.course_levels.messages.delete_failed')
            ], 500);
        }
    }

    /**
     * Restore the specified resource.
     */
    public function restore($id)
    {
        try {
            $courseLevel = CourseLevel::withTrashed()->findOrFail($id);
            $courseLevel->restore();
            
            _log_info('Course level restored successfully', [
                'level_id' => $courseLevel->id,
                'code' => $courseLevel->code,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('settings.course_levels.messages.restored')
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to restore course level', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('settings.course_levels.messages.restore_failed')
            ], 500);
        }
    }

    /**
     * Get course levels by language
     */
    public function getByLanguage($languageId)
    {
        try {
            $courseLevels = CourseLevel::where('language_id', $languageId)
                ->where('is_active', true)
                ->orderBy('order_index')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $courseLevels
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('settings.course_levels.messages.fetch_failed')
            ], 500);
        }
    }

    /**
     * Export course levels to Excel
     */
    public function exportExcel()
    {
        // Implementation for Excel export
        return response()->json(['message' => 'Excel export functionality']);
    }

    /**
     * Export course levels to PDF
     */
    public function exportPdf()
    {
        // Implementation for PDF export
        return response()->json(['message' => 'PDF export functionality']);
    }

    /**
     * Search course levels
     */
    public function search(Request $request)
    {
        // Implementation for search functionality
        return response()->json(['message' => 'Search functionality']);
    }
}