<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Language;
use App\Models\CourseLevel;
use App\Models\RateScheme;
use App\Models\CurriculumTopic;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\CoursesExport;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $courses = Course::withTrashed()
                    ->with(['language', 'level', 'rateScheme'])
                    ->select(['id', 'name', 'description', 'language_id', 'level_id', 'rate_scheme_id', 'teaching_hours', 'total_hours', 'mode', 'is_active', 'created_at', 'deleted_at']);

                return DataTables::of($courses)
                    ->addColumn('language_name', function ($course) {
                        return $course->language ? $course->language->name : 'N/A';
                    })
                    ->addColumn('course_level_name', function ($course) {
                        return $course->level ? $course->level->name : 'N/A';
                    })
                    ->addColumn('rate_scheme_code', function ($course) {
                        return $course->rateScheme ? $course->rateScheme->letter_code : 'N/A';
                    })
                    ->addColumn('status', function ($course) {
                        if ($course->deleted_at) {
                            return '<span class="badge bg-danger">Deleted</span>';
                        }
                        return $course->is_active 
                            ? '<span class="badge bg-success">Active</span>'
                            : '<span class="badge bg-warning">Inactive</span>';
                    })
                    ->addColumn('actions', function ($course) {
                        $actions = '';
                        
                        if (_has_permission('courses.edit')) {
                            $actions .= '<button class="btn btn-sm btn-primary edit-btn" data-id="' . $course->id . '" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button> ';
                        }
                        
                        if (_has_permission('courses.delete')) {
                            if ($course->deleted_at) {
                                $actions .= '<button class="btn btn-sm btn-success restore-btn" data-id="' . $course->id . '" title="Restore">
                                    <i class="fas fa-undo"></i>
                                </button>';
                            } else {
                                $actions .= '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $course->id . '" title="Delete">
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
            $rateSchemes = \App\Models\RateScheme::all();
            
            return view('courses.index', compact('languages', 'levels', 'rateSchemes'));

        } catch (\Exception $e) {
            _log_error('Failed to fetch courses', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('courses.messages.fetch_failed')
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
            'message' => __('courses.messages.create_form_loaded')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:50',
                'description' => 'nullable|string|max:500',
                'language_id' => 'required|exists:languages,id',
                'level_id' => 'required|exists:course_levels,id',
                'rate_scheme_id' => 'required|exists:rate_schemes,id',
                'total_hours' => 'required|integer|min:1',
                'teaching_hours' => 'required|integer|min:1',
                'mode' => 'required|in:in_person,virtual,hybrid',
                'status' => 'required|in:pending,active,inactive,completed',
                'is_active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => __('courses.messages.validation_failed'),
                    'errors' => $validator->errors()
                ], 422);
            }

            $course = Course::create([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'language_id' => $request->language_id,
                'level_id' => $request->level_id,
                'rate_scheme_id' => $request->rate_scheme_id,
                'total_hours' => $request->total_hours,
                'teaching_hours' => $request->teaching_hours,
                'mode' => $request->mode,
                'status' => $request->status,
                'is_active' => $request->boolean('is_active', true),
                'created_by' => auth()->id()
            ]);

            _log_info('Course created successfully', [
                'course_id' => $course->id,
                'name' => $course->name,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('courses.messages.created'),
                'data' => $course
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to create course', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('courses.messages.create_failed')
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $course = Course::withTrashed()->with(['language', 'level', 'rateScheme'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $course
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('courses.messages.not_found')
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $course = Course::withTrashed()->with(['language', 'level', 'rateScheme'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $course
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('courses.messages.not_found')
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
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:50',
                'description' => 'nullable|string|max:500',
                'language_id' => 'required|exists:languages,id',
                'level_id' => 'required|exists:course_levels,id',
                'rate_scheme_id' => 'required|exists:rate_schemes,id',
                'total_hours' => 'required|integer|min:1',
                'teaching_hours' => 'required|integer|min:1',
                'mode' => 'required|in:in_person,virtual,hybrid',
                'status' => 'required|in:pending,active,inactive,completed',
                'is_active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => __('courses.messages.validation_failed'),
                    'errors' => $validator->errors()
                ], 422);
            }

            $course = Course::findOrFail($id);
            $course->update([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'language_id' => $request->language_id,
                'level_id' => $request->level_id,
                'rate_scheme_id' => $request->rate_scheme_id,
                'total_hours' => $request->total_hours,
                'teaching_hours' => $request->teaching_hours,
                'mode' => $request->mode,
                'status' => $request->status,
                'is_active' => $request->boolean('is_active', true)
            ]);

            _log_info('Course updated successfully', [
                'course_id' => $course->id,
                'name' => $course->name,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('courses.messages.updated'),
                'data' => $course
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to update course', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('courses.messages.update_failed')
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $course = Course::findOrFail($id);
            $course->delete();
            
            _log_info('Course deleted successfully', [
                'course_id' => $course->id,
                'name' => $course->name,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('courses.messages.deleted')
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to delete course', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('courses.messages.delete_failed')
            ], 500);
        }
    }

    /**
     * Restore the specified resource.
     */
    public function restore($id)
    {
        try {
            $course = Course::withTrashed()->findOrFail($id);
            $course->restore();
            
            _log_info('Course restored successfully', [
                'course_id' => $course->id,
                'name' => $course->name,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('courses.messages.restored')
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to restore course', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('courses.messages.restore_failed')
            ], 500);
        }
    }

    /**
     * Export courses to Excel
     */
    public function exportExcel()
    {
        try {
            $courses = Course::withTrashed()
                ->with(['language', 'level', 'rateScheme'])
                ->get();

            $filename = 'courses_' . date('Y-m-d_H-i-s') . '.xlsx';
            
            return Excel::download(new CoursesExport($courses), $filename);
        } catch (\Exception $e) {
            _log_error('Failed to export courses to Excel', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return redirect()->back()->with('error', __('courses.messages.export_error'));
        }
    }

    /**
     * Export courses to PDF
     */
    public function exportPdf()
    {
        try {
            $courses = Course::withTrashed()
                ->with(['language', 'level', 'rateScheme'])
                ->get();

            $pdf = PDF::loadView('exports.courses-pdf', compact('courses'));
            $filename = 'courses_' . date('Y-m-d_H-i-s') . '.pdf';
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            _log_error('Failed to export courses to PDF', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return redirect()->back()->with('error', __('courses.messages.export_error'));
        }
    }

    /**
     * Search courses
     */
    public function search(Request $request)
    {
        // Implementation for search functionality
        return response()->json(['message' => 'Search functionality']);
    }

    /**
     * Get available curriculum topics for a course based on language and level
     */
    public function getAvailableTopics(Request $request, $courseId)
    {
        try {
            $course = Course::findOrFail($courseId);
            
            // Get curriculum topics for the course's language and level
            $availableTopics = CurriculumTopic::where('language_id', $course->language_id)
                ->where('level_id', $course->level_id)
                ->where('is_active', true)
                ->orderBy('order_index')
                ->get();

            // Get already assigned topics
            $assignedTopicIds = $course->courseCurriculum()->pluck('curriculum_topic_id')->toArray();

            return response()->json([
                'success' => true,
                'data' => [
                    'available_topics' => $availableTopics,
                    'assigned_topic_ids' => $assignedTopicIds
                ]
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to fetch available topics for course', [
                'error' => $e->getMessage(),
                'course_id' => $courseId,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch available topics'
            ], 500);
        }
    }

    /**
     * Save course curriculum topics
     */
    public function saveCourseTopics(Request $request, $courseId)
    {
        try {
            $course = Course::findOrFail($courseId);

            $validator = Validator::make($request->all(), [
                'topic_ids' => 'required|array',
                'topic_ids.*' => 'integer|exists:curriculum_topics,id',
                'topic_data' => 'array',
                'topic_data.*.order_index' => 'integer|min:0',
                'topic_data.*.is_required' => 'boolean',
                'topic_data.*.estimated_hours' => 'integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::transaction(function () use ($course, $request) {
                // Clear existing curriculum assignments
                $course->courseCurriculum()->detach();
                
                // Insert new curriculum assignments
                $curriculumData = [];
                foreach ($request->topic_ids as $index => $topicId) {
                    $topicData = $request->topic_data[$topicId] ?? [];
                    $curriculumData[$topicId] = [
                        'order_index' => $topicData['order_index'] ?? $index + 1,
                        'is_required' => $topicData['is_required'] ?? true,
                        'estimated_hours' => $topicData['estimated_hours'] ?? 1,
                    ];
                }
                
                if (!empty($curriculumData)) {
                    $course->courseCurriculum()->sync($curriculumData);
                }
            });

            _log_info('Course curriculum topics saved', [
                'course_id' => $course->id,
                'topics_count' => count($request->topic_ids),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Course curriculum topics saved successfully'
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to save course curriculum topics', [
                'error' => $e->getMessage(),
                'course_id' => $courseId,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save course curriculum topics'
            ], 500);
        }
    }

    /**
     * Get available teachers for a course
     */
    public function getAvailableTeachers(Request $request, $courseId)
    {
        try {
            $course = Course::findOrFail($courseId);
            
            // Get all teachers
            $availableTeachers = User::where('role', 'teacher')
                ->select(['id', 'first_name', 'last_name', 'email'])
                ->get();

            // dd($availableTeachers);

            // Get already assigned teachers
            $assignedTeacherIds = $course->teachers()->pluck('users.id')->toArray();

            return response()->json([
                'success' => true,
                'data' => [
                    'available_teachers' => $availableTeachers,
                    'assigned_teacher_ids' => $assignedTeacherIds
                ]
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to fetch available teachers for course', [
                'error' => $e->getMessage(),
                'course_id' => $courseId,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch available teachers'
            ], 500);
        }
    }

    /**
     * Save course teachers
     */
    public function saveCourseTeachers(Request $request, $courseId)
    {
        try {
            $course = Course::findOrFail($courseId);

            $validator = Validator::make($request->all(), [
                'teacher_ids' => 'required|array',
                'teacher_ids.*' => 'integer|exists:users,id',
                'teacher_data' => 'array',
                'teacher_data.*.notes' => 'string|nullable',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::transaction(function () use ($course, $request) {
                // Clear existing teacher assignments
                $course->teachers()->detach();
                
                // Insert new teacher assignments
                $teacherData = [];
                foreach ($request->teacher_ids as $teacherId) {
                    $data = $request->teacher_data[$teacherId] ?? [];
                    $teacherData[$teacherId] = [
                        'is_active' => true,
                        'assigned_date' => now()->toDateString(),
                        'notes' => $data['notes'] ?? null,
                    ];
                }
                
                if (!empty($teacherData)) {
                    $course->teachers()->sync($teacherData);
                }
            });

            _log_info('Course teachers saved', [
                'course_id' => $course->id,
                'teachers_count' => count($request->teacher_ids),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Course teachers saved successfully'
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to save course teachers', [
                'error' => $e->getMessage(),
                'course_id' => $courseId,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save course teachers'
            ], 500);
        }
    }
}