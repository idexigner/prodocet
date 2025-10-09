<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\GroupsExport;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $groups = Group::withTrashed()
                    ->with(['course', 'teacher', 'students'])
                    ->select(['id', 'name', 'code', 'description', 'course_id', 'teacher_id', 'max_students', 'current_students', 'status', 'start_date', 'end_date', 'classroom', 'virtual_link', 'is_active', 'created_at', 'deleted_at']);

                return DataTables::of($groups)
                    ->addColumn('course_name', function ($group) {
                        return $group->course ? $group->course->name : 'N/A';
                    })
                    ->addColumn('teacher_name', function ($group) {
                        return $group->teacher ? $group->teacher->first_name . ' ' . $group->teacher->last_name : 'N/A';
                    })
                    ->addColumn('students_count', function ($group) {
                        return $group->students ? $group->students->count() : 0;
                    })
                    ->addColumn('status', function ($group) {
                        if ($group->deleted_at) {
                            return '<span class="badge bg-danger">Deleted</span>';
                        }
                        return match($group->status) {
                            'active' => '<span class="badge bg-success">Active</span>',
                            'inactive' => '<span class="badge bg-warning">Inactive</span>',
                            'completed' => '<span class="badge bg-info">Completed</span>',
                            'cancelled' => '<span class="badge bg-secondary">Cancelled</span>',
                            default => '<span class="badge bg-secondary">Unknown</span>'
                        };
                    })
                    ->addColumn('actions', function ($group) {
                        $actions = '';
                        
                        if (_has_permission('groups.edit')) {
                            $actions .= '<button class="btn btn-sm btn-primary edit-btn" data-id="' . $group->id . '" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button> ';
                        }
                        
                        if (_has_permission('groups.delete')) {
                            if ($group->deleted_at) {
                                $actions .= '<button class="btn btn-sm btn-success restore-btn" data-id="' . $group->id . '" title="Restore">
                                    <i class="fas fa-undo"></i>
                                </button>';
                            } else {
                                $actions .= '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $group->id . '" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>';
                            }
                        }
                        
                        return $actions;
                    })
                    ->rawColumns(['status', 'actions'])
                    ->make(true);
            }

            $courses = \App\Models\Course::where('is_active', true)->get();
            // $teachers = \App\Models\User::whereHas('roles', function($q) {
            //     $q->where('name', 'teacher');
            // })->get();
            $teachers = \App\Models\User::where('role', 'teacher')->get();
            
            return view('groups.index', compact('courses', 'teachers'));

        } catch (\Exception $e) {
            _log_error('Failed to fetch groups', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('groups.messages.fetch_failed')
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
            'message' => __('groups.messages.create_form_loaded')
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
                'code' => 'nullable|string|max:50|unique:groups,code',
                'description' => 'nullable|string|max:500',
                'course_id' => 'required|exists:courses,id',
                'teacher_id' => 'required|exists:users,id',
                'max_students' => 'required|integer|min:1|max:50',
                'current_students' => 'nullable|integer|min:0',
                'status' => 'required|in:active,inactive,completed,cancelled',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after:start_date',
                'classroom' => 'nullable|string|max:100',
                'virtual_link' => 'nullable|url|max:500',
                'is_active' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => __('groups.messages.validation_failed'),
                    'errors' => $validator->errors()
                ], 422);
            }

            $group = Group::create([
                'name' => $request->name,
                'code' => $request->code ?? 'GRP-' . strtoupper(uniqid()),
                'description' => $request->description,
                'course_id' => $request->course_id,
                'teacher_id' => $request->teacher_id,
                'max_students' => $request->max_students,
                'current_students' => $request->current_students ?? 0,
                'status' => $request->status,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'classroom' => $request->classroom,
                'virtual_link' => $request->virtual_link,
                'is_active' => $request->has('is_active')
            ]);

            _log_info('Group created successfully', [
                'group_id' => $group->id,
                'name' => $group->name,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('groups.messages.created'),
                'data' => $group
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to create group', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('groups.messages.create_failed')
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $group = Group::withTrashed()->with(['course', 'teacher', 'students'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $group
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('groups.messages.not_found')
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $group = Group::withTrashed()->with(['course', 'teacher', 'students'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $group
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('groups.messages.not_found')
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
                'code' => 'nullable|string|max:50|unique:groups,code,' . $id,
                'description' => 'nullable|string|max:500',
                'course_id' => 'required|exists:courses,id',
                'teacher_id' => 'required|exists:users,id',
                'max_students' => 'required|integer|min:1|max:50',
                'current_students' => 'nullable|integer|min:0',
                'status' => 'required|in:active,inactive,completed,cancelled',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after:start_date',
                'classroom' => 'nullable|string|max:100',
                'virtual_link' => 'nullable|url|max:500',
                'is_active' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => __('groups.messages.validation_failed'),
                    'errors' => $validator->errors()
                ], 422);
            }

            $group = Group::findOrFail($id);
            $group->update([
                'name' => $request->name,
                'code' => $request->code ?? $group->code,
                'description' => $request->description,
                'course_id' => $request->course_id,
                'teacher_id' => $request->teacher_id,
                'max_students' => $request->max_students,
                'current_students' => $request->current_students ?? 0,
                'status' => $request->status,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'classroom' => $request->classroom,
                'virtual_link' => $request->virtual_link,
                'is_active' => $request->has('is_active')
            ]);

            _log_info('Group updated successfully', [
                'group_id' => $group->id,
                'name' => $group->name,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('groups.messages.updated'),
                'data' => $group
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to update group', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('groups.messages.update_failed')
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $group = Group::findOrFail($id);
            $group->delete();
            
            _log_info('Group deleted successfully', [
                'group_id' => $group->id,
                'name' => $group->name,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('groups.messages.deleted')
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to delete group', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('groups.messages.delete_failed')
            ], 500);
        }
    }

    /**
     * Restore the specified resource.
     */
    public function restore($id)
    {
        try {
            $group = Group::withTrashed()->findOrFail($id);
            $group->restore();
            
            _log_info('Group restored successfully', [
                'group_id' => $group->id,
                'name' => $group->name,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('groups.messages.restored')
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to restore group', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('groups.messages.restore_failed')
            ], 500);
        }
    }

    /**
     * Export groups to Excel
     */
    public function exportExcel()
    {
        try {
            $groups = Group::withTrashed()
                ->with(['course', 'teacher'])
                ->get();

            $filename = 'groups_' . date('Y-m-d_H-i-s') . '.xlsx';
            
            return Excel::download(new GroupsExport($groups), $filename);
        } catch (\Exception $e) {
            _log_error('Failed to export groups to Excel', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return redirect()->back()->with('error', __('groups.messages.export_error'));
        }
    }

    /**
     * Export groups to PDF
     */
    public function exportPdf()
    {
        try {
            $groups = Group::withTrashed()
                ->with(['course', 'teacher'])
                ->get();

            $pdf = PDF::loadView('exports.groups-pdf', compact('groups'));
            $filename = 'groups_' . date('Y-m-d_H-i-s') . '.pdf';
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            _log_error('Failed to export groups to PDF', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return redirect()->back()->with('error', __('groups.messages.export_error'));
        }
    }

    /**
     * Search groups
     */
    public function search(Request $request)
    {
        // Implementation for search functionality
        return response()->json(['message' => 'Search functionality']);
    }
}