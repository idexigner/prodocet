<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Group;
use App\Models\GroupStudent;
use App\Models\Course;
use App\Models\CurriculumTopic;

class StudentDashboardController extends Controller
{
    /**
     * Display the student dashboard.
     */
    public function index()
    {
        try {
            $student = Auth::user();
            
            // Check permission using custom system
            if (!_has_permission('student.dashboard.view')) {
                return redirect()->route('login')->with('error', 'No tienes permisos para acceder al dashboard de estudiante.');
            }
            
            // Debug logging
            \Log::info('StudentDashboardController::index called', [
                'user_id' => $student->id,
                'email' => $student->email,
                'roles' => $student->roles->pluck('name')->toArray()
            ]);
        
        // Get student's enrolled groups with course and teacher information
        $enrolledGroups = GroupStudent::where('student_id', $student->id)
            ->where('status', 'enrolled')
            ->with([
                'group.course.language',
                'group.course.level', 
                'group.teacher',
                'group.sessions' => function($query) {
                    $query->where('date', '>=', now()->toDateString())
                          ->orderBy('date', 'asc')
                          ->limit(5);
                }
            ])
            ->get();

        // Get upcoming sessions
        $upcomingSessions = collect();
        foreach ($enrolledGroups as $enrollment) {
            $upcomingSessions = $upcomingSessions->merge($enrollment->group->sessions);
        }
        $upcomingSessions = $upcomingSessions->sortBy('date')->take(5);

        // Get student statistics
        $totalHoursPurchased = $enrolledGroups->sum('academic_hours_purchased');
        $totalHoursUsed = $enrolledGroups->sum('academic_hours_used');
        $remainingHours = $totalHoursPurchased - $totalHoursUsed;
        $activeGroups = $enrolledGroups->count();

            return view('student.dashboard', compact(
                'student',
                'enrolledGroups', 
                'upcomingSessions',
                'totalHoursPurchased',
                'totalHoursUsed', 
                'remainingHours',
                'activeGroups'
            ));
        } catch (\Exception $e) {
            \Log::error('Error in StudentDashboardController::index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('login')->with('error', 'Error loading student dashboard: ' . $e->getMessage());
        }
    }

    /**
     * Display student's courses page.
     */
    public function courses()
    {
        $student = Auth::user();
        
        // Check permission using custom system
        if (!_has_permission('student.courses.view')) {
            return redirect()->route('student.dashboard')->with('error', 'No tienes permisos para ver los cursos.');
        }
        
        // Get student's enrolled groups with detailed course information
        $enrolledGroups = GroupStudent::where('student_id', $student->id)
            ->where('status', 'enrolled')
            ->with([
                'group.course.language',
                'group.course.level',
                'group.course.courseCurriculum.curriculumTopic',
                'group.teacher',
                'group.sessions' => function($query) {
                    $query->orderBy('date', 'asc');
                }
            ])
            ->get();

        // Get student's pending enrollment requests
        $enrollmentRequests = \App\Models\EnrollmentRequest::where('student_id', $student->id)
            ->with([
                'course.language',
                'course.level',
                'course.rateScheme'
            ])
            ->orderBy('requested_at', 'desc')
            ->get();

        return view('student.courses', compact('student', 'enrolledGroups', 'enrollmentRequests'));
    }

    /**
     * Display detailed course information for a specific group.
     */
    public function courseDetails($groupId)
    {
        $student = Auth::user();
        
        // Check permission using custom system
        if (!_has_permission('student.courses.view')) {
            return redirect()->route('student.dashboard')->with('error', 'No tienes permisos para ver los detalles del curso.');
        }
        
        // Verify student is enrolled in this group
        $enrollment = GroupStudent::where('student_id', $student->id)
            ->where('group_id', $groupId)
            ->where('status', 'enrolled')
            ->with([
                'group.course.language',
                'group.course.level',
                'group.course.courseCurriculum.curriculumTopic',
                'group.teacher',
                'group.sessions' => function($query) {
                    $query->orderBy('date', 'asc');
                }
            ])
            ->firstOrFail();

        $group = $enrollment->group;
        $course = $group->course;
        $teacher = $group->teacher;
        $topics = $course->courseCurriculum;
        $sessions = $group->sessions;

        return view('student.course-details', compact(
            'student',
            'enrollment',
            'group', 
            'course',
            'teacher',
            'topics',
            'sessions'
        ));
    }

    /**
     * Display student's schedule/calendar.
     */
    public function schedule()
    {
        $student = Auth::user();
        
        // Check permission using custom system
        if (!_has_permission('student.schedule.view')) {
            return redirect()->route('login')->with('error', 'No tienes permisos para ver el horario.');
        }
        
        // Get all sessions for student's enrolled groups
        $enrolledGroups = GroupStudent::where('student_id', $student->id)
            ->where('status', 'enrolled')
            ->with([
                'group.course.language',
                'group.course.level',
                'group.teacher',
                'group.sessions' => function($query) {
                    $query->where('date', '>=', now()->subDays(7))
                          ->orderBy('date', 'asc');
                }
            ])
            ->get();

        $sessions = collect();
        foreach ($enrolledGroups as $enrollment) {
            $sessions = $sessions->merge($enrollment->group->sessions);
        }
        $sessions = $sessions->sortBy('date');

        return view('student.schedule', compact('student', 'sessions', 'enrolledGroups'));
    }

    /**
     * Display student's profile.
     */
    public function profile()
    {
        $student = Auth::user();
        
        // Check permission using custom system
        if (!_has_permission('student.profile.view')) {
            return redirect()->route('login')->with('error', 'No tienes permisos para ver el perfil.');
        }
        
        return view('student.profile', compact('student'));
    }

    /**
     * Display available courses for enrollment.
     */
    public function availableCourses()
    {
        $student = Auth::user();
        
        // Check permission using custom system
        if (!_has_permission('student.courses.view')) {
            return redirect()->route('student.dashboard')->with('error', 'No tienes permisos para ver los cursos disponibles.');
        }
        
        // Get all active courses with their details
        $courses = Course::where('is_active', true)
            ->with([
                'level',
                'language',
                'rateScheme',
                'groups' => function($query) {
                    $query->where('is_active', true)
                          ->with('teacher');
                }
            ])
            ->get();

        
        // Calculate cost and fetch topics for each course
        $courses->each(function($course) {
            // Fetch topics separately for this course
            $topics = \App\Models\CurriculumTopic::where('level_id', $course->level_id)
                                                ->where('language_id', $course->language_id)
                                                ->orderBy('order_index')
                                                ->get();
            
            $course->topics = $topics;
            
            // Use course's total_hours since curriculum topics don't have teaching_hours
            $totalHours = $course->total_hours;
            $ratePerHour = $course->rateScheme ? $course->rateScheme->hourly_rate : 0;
            
            $course->total_hours = $totalHours;
            $course->total_cost = $totalHours * $ratePerHour;
            
            Log::info("Course: {$course->name}, Topics: {$topics->count()}, Hours: {$totalHours}, Rate: {$ratePerHour}, Cost: " . ($totalHours * $ratePerHour));
        });
        
        return view('student.available-courses', compact('courses', 'student'));
    }

    /**
     * Show enrollment form for a specific course.
     */
    public function showEnrollmentForm($courseId)
    {
        $student = Auth::user();
        
        // Check permission using custom system
        if (!_has_permission('student.courses.view')) {
            return redirect()->route('student.dashboard')->with('error', 'No tienes permisos para inscribirte en cursos.');
        }
        
        $course = Course::with([
            'level',
            'language',
            'rateScheme'
        ])->findOrFail($courseId);
        
        // Fetch topics separately for this course
        $topics = \App\Models\CurriculumTopic::where('level_id', $course->level_id)
                                            ->where('language_id', $course->language_id)
                                            ->orderBy('order_index')
                                            ->get();
        
        $course->topics = $topics;
        
        // Calculate total cost based on teaching hours and rate scheme
        $totalHours = $course->total_hours;
        $ratePerHour = $course->rateScheme ? $course->rateScheme->hourly_rate : 0;
        $totalCost = $totalHours * $ratePerHour;
        
        return view('student.enrollment-form', compact('course', 'student', 'totalHours', 'totalCost'));
    }

    /**
     * Process enrollment request.
     */
    public function requestEnrollment(Request $request, $courseId)
    {
        \Log::info('Enrollment request received', [
            'course_id' => $courseId,
            'request_data' => $request->all(),
            'user_id' => Auth::id()
        ]);
        

        $student = Auth::user();
        
        // Check permission using custom system
        if (!_has_permission('student.courses.view')) {
            return redirect()->route('student.dashboard')->with('error', 'No tienes permisos para inscribirte en cursos.');
        }
        
        // Custom validation for availability
        $availability = $request->input('availability', []);
        
        // Filter out null/empty values and validate only checked days
        $validAvailability = [];
        foreach ($availability as $day => $schedule) {
            if (!empty($schedule['start_time'])) {
                $validAvailability[$day] = $schedule;
            }
        }
        
        if (empty($validAvailability)) {
            return redirect()->back()
                ->with('error', 'Por favor selecciona al menos un día de disponibilidad.')
                ->withInput();
        }
        
        // Validate time format for selected days
        foreach ($validAvailability as $day => $schedule) {
            if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $schedule['start_time'])) {
                return redirect()->back()
                    ->with('error', "Formato de hora inválido para {$day}.")
                    ->withInput();
            }
        }
    
        


        
        $course = Course::with(['rateScheme'])->findOrFail($courseId);
        
        // Fetch topics separately for this course (same as showEnrollmentForm)
        $topics = \App\Models\CurriculumTopic::where('level_id', $course->level_id)
                                            ->where('language_id', $course->language_id)
                                            ->orderBy('order_index')
                                            ->get();
        
        $course->topics = $topics;
        
        \Log::info('Course topics loaded', [
            'course_id' => $courseId,
            'topics_count' => $topics->count(),
            'level_id' => $course->level_id,
            'language_id' => $course->language_id
        ]);
        
        // Calculate total cost
        $totalHours = $course->total_hours;
        $ratePerHour = $course->rateScheme ? $course->rateScheme->hourly_rate : 0;
        $totalCost = $totalHours * $ratePerHour;
        
        try {
            \Log::info('Attempting to create enrollment request', [
                'student_id' => $student->id,
                'course_id' => $courseId,
                'total_hours' => $totalHours,
                'total_cost' => $totalCost,
                'availability' => $validAvailability
            ]);
            
            // Create enrollment request
            $enrollmentRequest = \App\Models\EnrollmentRequest::create([
                'student_id' => $student->id,
                'course_id' => $courseId,
                'total_hours' => $totalHours,
                'total_cost' => $totalCost,
                'status' => 'pending',
                'availability' => $validAvailability,
                'requested_at' => now(),
            ]);
            
            \Log::info('Enrollment request created successfully', [
                'enrollment_request_id' => $enrollmentRequest->id
            ]);
            
            // Save student topics to cover
            foreach ($course->topics as $topic) {
                \App\Models\StudentTopic::create([
                    'student_id' => $student->id,
                    'course_id' => $courseId,
                    'topic_id' => $topic->id,
                    'enrollment_request_id' => $enrollmentRequest->id,
                    'status' => 'pending',
                    'order' => $topic->order_index,
                ]);
            }
            
            \Log::info('Enrollment request created', [
                'student_id' => $student->id,
                'course_id' => $courseId,
                'enrollment_request_id' => $enrollmentRequest->id,
                'total_cost' => $totalCost
            ]);
            
            return redirect()->route('student.courses')
                ->with('success', 'Solicitud de inscripción enviada exitosamente. Espera la aprobación del administrador.');
                
        } catch (\Exception $e) {
            \Log::error('Failed to create enrollment request', [
                'student_id' => $student->id,
                'course_id' => $courseId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Error al procesar la solicitud de inscripción: ' . $e->getMessage())
                ->withInput();
        }
    }
}
