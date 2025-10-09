<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\EnrollmentRequest;
use App\Models\User;
use App\Models\Course;
use App\Models\Group;
use App\Models\GroupStudent;
use App\Models\TeacherAvailability;
use App\Models\StudentAvailability;
use Yajra\DataTables\Facades\DataTables;

class EnrollmentRequestController extends Controller
{
    /**
     * Display a listing of enrollment requests.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $enrollmentRequests = EnrollmentRequest::with([
                    'student',
                    'course',
                    'approver'
                ])->select(['id', 'student_id', 'course_id', 'total_hours', 'total_cost', 'status', 'requested_at', 'approved_at', 'approved_by']);

                return DataTables::of($enrollmentRequests)
                    ->addColumn('student_name', function ($request) {
                        return $request->student ? $request->student->name : 'N/A';
                    })
                    ->addColumn('student_email', function ($request) {
                        return $request->student ? $request->student->email : 'N/A';
                    })
                    ->addColumn('course_name', function ($request) {
                        return $request->course ? $request->course->name : 'N/A';
                    })
                    ->addColumn('total_cost_formatted', function ($request) {
                        return '$' . number_format($request->total_cost, 2);
                    })
                    ->addColumn('status_badge', function ($request) {
                        $badgeClass = match($request->status) {
                            'pending' => 'bg-warning',
                            'approved' => 'bg-success',
                            'rejected' => 'bg-danger',
                            'cancelled' => 'bg-secondary',
                            default => 'bg-secondary'
                        };
                        return '<span class="badge ' . $badgeClass . '">' . ucfirst($request->status) . '</span>';
                    })
                    ->addColumn('requested_at_formatted', function ($request) {
                        return $request->requested_at ? $request->requested_at->format('M d, Y H:i') : 'N/A';
                    })
                    ->addColumn('action', function ($request) {
                        $actions = '';
                        
                        if (_has_permission('enrollment-requests.view')) {
                            $actions .= '<button class="btn btn-sm btn-info view-btn" data-id="' . $request->id . '" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button> ';
                        }
                        
                        if ($request->status === 'pending' && _has_permission('enrollment-requests.approve')) {
                            $actions .= '<button class="btn btn-sm btn-success approve-btn" data-id="' . $request->id . '" title="Approve">
                                <i class="fas fa-check"></i>
                            </button> ';
                            
                            $actions .= '<button class="btn btn-sm btn-danger reject-btn" data-id="' . $request->id . '" title="Reject">
                                <i class="fas fa-times"></i>
                            </button> ';
                        }
                        
                        return $actions;
                    })
                    ->rawColumns(['status_badge', 'action'])
                    ->make(true);
            }

            return view('admin.enrollment-requests');

        } catch (\Exception $e) {
            _log_error('Failed to fetch enrollment requests', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch enrollment requests'
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to fetch enrollment requests');
        }
    }

    /**
     * Show enrollment request details.
     */
    public function show($id)
    {
        try {
            $enrollmentRequest = EnrollmentRequest::with([
                'student',
                'course.level',
                'course.language',
                'course.curriculumTopics',
                'course.rateScheme',
                'studentTopics.topic',
                'approver'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $enrollmentRequest
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to fetch enrollment request details', [
                'enrollment_request_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch enrollment request details'
            ], 500);
        }
    }

    /**
     * Approve enrollment request.
     */
    public function approve(Request $request, $id)
    {
        try {
            $enrollmentRequest = EnrollmentRequest::with(['student', 'course'])->findOrFail($id);
            
            if ($enrollmentRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'This enrollment request cannot be approved'
                ], 422);
            }

            // Update enrollment request status
            $enrollmentRequest->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => Auth::id()
            ]);

            // Create student availability record
            StudentAvailability::create([
                'student_id' => $enrollmentRequest->student_id,
                'availability' => $enrollmentRequest->availability,
                'is_active' => true
            ]);

            // Update student topics status
            $enrollmentRequest->studentTopics()->update(['status' => 'pending']);

            Log::info('Enrollment request approved', [
                'enrollment_request_id' => $id,
                'student_id' => $enrollmentRequest->student_id,
                'course_id' => $enrollmentRequest->course_id,
                'approved_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Enrollment request approved successfully'
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to approve enrollment request', [
                'enrollment_request_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to approve enrollment request'
            ], 500);
        }
    }

    /**
     * Reject enrollment request.
     */
    public function reject(Request $request, $id)
    {
        try {
            $request->validate([
                'rejection_reason' => 'required|string|max:500'
            ]);

            $enrollmentRequest = EnrollmentRequest::findOrFail($id);
            
            if ($enrollmentRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'This enrollment request cannot be rejected'
                ], 422);
            }

            // Update enrollment request status
            $enrollmentRequest->update([
                'status' => 'rejected',
                'rejected_at' => now(),
                'rejection_reason' => $request->rejection_reason,
                'approved_by' => Auth::id()
            ]);

            Log::info('Enrollment request rejected', [
                'enrollment_request_id' => $id,
                'student_id' => $enrollmentRequest->student_id,
                'course_id' => $enrollmentRequest->course_id,
                'rejection_reason' => $request->rejection_reason,
                'rejected_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Enrollment request rejected successfully'
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to reject enrollment request', [
                'enrollment_request_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reject enrollment request'
            ], 500);
        }
    }

    /**
     * Get availability matching data for auto-assignment.
     */
    public function getAvailabilityMatching($enrollmentRequestId)
    {
        try {
            $enrollmentRequest = EnrollmentRequest::with(['student', 'course.groups.teacher'])->findOrFail($enrollmentRequestId);
            
            if ($enrollmentRequest->status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Enrollment request must be approved first'
                ], 422);
            }

            $studentAvailability = $enrollmentRequest->availability;
            $course = $enrollmentRequest->course;
            
            // Get teachers for this course
            $teachers = $course->groups->pluck('teacher')->filter()->unique('id');
            
            $matchingData = [];
            
            foreach ($teachers as $teacher) {
                $teacherAvailability = TeacherAvailability::where('teacher_id', $teacher->id)
                    ->where('is_active', true)
                    ->first();
                
                if ($teacherAvailability) {
                    $matchingData[] = [
                        'teacher' => $teacher,
                        'availability' => $teacherAvailability->availability,
                        'matching_slots' => $this->findMatchingSlots($studentAvailability, $teacherAvailability->availability)
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'enrollment_request' => $enrollmentRequest,
                    'student_availability' => $studentAvailability,
                    'teacher_matching' => $matchingData
                ]
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to get availability matching', [
                'enrollment_request_id' => $enrollmentRequestId,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get availability matching data'
            ], 500);
        }
    }

    /**
     * Auto-assign slots for approved enrollment.
     */
    public function autoAssignSlots(Request $request, $enrollmentRequestId)
    {
        try {
            $request->validate([
                'teacher_id' => 'required|exists:users,id',
                'slots' => 'required|array',
                'slots.*.day' => 'required|string',
                'slots.*.start_time' => 'required|date_format:H:i',
                'slots.*.end_time' => 'required|date_format:H:i'
            ]);

            $enrollmentRequest = EnrollmentRequest::with(['student', 'course'])->findOrFail($enrollmentRequestId);
            
            if ($enrollmentRequest->status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Enrollment request must be approved first'
                ], 422);
            }

            // Create group for this student
            $group = Group::create([
                'name' => $enrollmentRequest->course->name . ' - ' . $enrollmentRequest->student->name,
                'course_id' => $enrollmentRequest->course_id,
                'teacher_id' => $request->teacher_id,
                'max_students' => 1,
                'current_students' => 1,
                'is_active' => true,
                'start_date' => now(),
                'end_date' => now()->addMonths(3) // Default 3 months
            ]);

            // Enroll student in the group
            GroupStudent::create([
                'group_id' => $group->id,
                'student_id' => $enrollmentRequest->student_id,
                'status' => 'enrolled',
                'enrolled_at' => now()
            ]);

            // Create slots for each assigned time
            foreach ($request->slots as $slot) {
                \App\Models\Slot::create([
                    'group_id' => $group->id,
                    'day_of_week' => $slot['day'],
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'],
                    'is_active' => true
                ]);
            }

            Log::info('Auto-assigned slots for enrollment', [
                'enrollment_request_id' => $enrollmentRequestId,
                'group_id' => $group->id,
                'teacher_id' => $request->teacher_id,
                'slots_count' => count($request->slots),
                'assigned_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Slots assigned successfully',
                'data' => [
                    'group_id' => $group->id,
                    'slots_assigned' => count($request->slots)
                ]
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to auto-assign slots', [
                'enrollment_request_id' => $enrollmentRequestId,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to auto-assign slots'
            ], 500);
        }
    }

    /**
     * Find matching time slots between student and teacher availability.
     */
    private function findMatchingSlots($studentAvailability, $teacherAvailability)
    {
        $matchingSlots = [];
        
        foreach ($studentAvailability as $day => $studentTimes) {
            if (isset($teacherAvailability[$day]) && $teacherAvailability[$day]) {
                $teacherTimes = $teacherAvailability[$day];
                
                // Find overlapping time ranges
                $studentStart = strtotime($studentTimes['start_time']);
                $studentEnd = strtotime($studentTimes['end_time']);
                $teacherStart = strtotime($teacherTimes['start_time']);
                $teacherEnd = strtotime($teacherTimes['end_time']);
                
                // Check for overlap
                if ($studentStart < $teacherEnd && $studentEnd > $teacherStart) {
                    $overlapStart = max($studentStart, $teacherStart);
                    $overlapEnd = min($studentEnd, $teacherEnd);
                    
                    if ($overlapEnd - $overlapStart >= 3600) { // At least 1 hour
                        $matchingSlots[] = [
                            'day' => $day,
                            'start_time' => date('H:i', $overlapStart),
                            'end_time' => date('H:i', $overlapEnd)
                        ];
                    }
                }
            }
        }
        
        return $matchingSlots;
    }
}