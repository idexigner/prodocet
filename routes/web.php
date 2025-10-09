<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LmsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Dashboard Routes - Redirect based on user role
Route::get('/dashboard', function () {
    if (Auth::check()) {
        $user = Auth::user();
        
        // Debug logging
        \Log::info('Dashboard access attempt', [
            'user_id' => $user->id,
            'email' => $user->email,
            'roles' => $user->roles->pluck('name')->toArray(),
            'has_student_role' => $user->hasRole('student'),
            'is_active' => $user->is_active
        ]);
        
        if ($user->hasRole('student')) {
            \Log::info('Redirecting student to student dashboard');
            try {
                return redirect()->route('student.dashboard');
            } catch (\Exception $e) {
                \Log::error('Error redirecting to student dashboard', [
                    'error' => $e->getMessage(),
                    'route' => 'student.dashboard'
                ]);
                return redirect()->route('login')->with('error', 'Error accessing student dashboard');
            }
        } else {
            \Log::info('Redirecting non-student to main dashboard');
            return app(LmsController::class)->dashboard();
        }
    }
    \Log::info('User not authenticated, redirecting to login');
    return redirect()->route('login');
})->name('dashboard')->middleware('auth');
Route::get('/calendar', [LmsController::class, 'calendar'])->name('calendar')->middleware('auth');

// Authentication Routes (without throttling)
Route::get('/', function () {
    if (Auth::check()) {
        // Always redirect to main dashboard route, let it handle role-based routing
        return redirect()->route('dashboard');
    }
    return app(LmsController::class)->index();
})->name('login');
Route::get('/login', [LmsController::class, 'index']);

Route::post('/login', [LmsController::class, 'login'])->name('login')->withoutMiddleware('throttle');
Route::post('/register', [LmsController::class, 'register'])->name('register')->withoutMiddleware('throttle');
Route::post('/logout', [LmsController::class, 'logout'])->name('logout');

// Dashboard and Main Application Routes (Protected)
Route::middleware(['auth'])->group(function () {
    // Dashboard route is handled above with role-based redirection
});

// Main Application Routes (Protected)
Route::middleware(['auth'])->group(function () {
    // Group & Course Management
    Route::get('/groups', [LmsController::class, 'groups'])->name('groups');

    // Calendar & Scheduling
    Route::get('/calendar', [LmsController::class, 'calendar'])->name('calendar');

    // Attendance & Performance
    Route::get('/attendance', [LmsController::class, 'attendance'])->name('attendance');

    // HR Client Panel
    Route::get('/hr-panel', [LmsController::class, 'hrPanel'])->name('hr-panel');

    // User Management
    Route::get('/users', [LmsController::class, 'users'])->name('users');

    // Reports & Billing
    Route::get('/reports', [LmsController::class, 'reports'])->name('reports');

    // Class Data Upload
    Route::get('/upload', [LmsController::class, 'upload'])->name('upload');

    // Settings
    Route::get('/settings', [LmsController::class, 'settings'])->name('settings');

    // Analytics
    Route::get('/analytics', [LmsController::class, 'analytics'])->name('analytics');

    // Mermaid Flowchart
    Route::get('/mermaid', [LmsController::class, 'mermaid'])->name('mermaid');
});

// Language routes
Route::post('/language/{locale}', [App\Http\Controllers\LanguageController::class, 'changeLanguage'])->name('language.change');
Route::get('/language/current', [App\Http\Controllers\LanguageController::class, 'getCurrentLanguage'])->name('language.current');

// Test route for debugging authentication
Route::get('/test-auth', function () {
    if (auth()->check()) {
        $user = auth()->user();
        return response()->json([
            'authenticated' => true,
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'roles' => $user->roles->pluck('name')->toArray(),
            'has_student_role' => $user->hasRole('student'),
            'is_active' => $user->is_active
        ]);
    } else {
        return response()->json(['authenticated' => false]);
    }
})->name('test-auth');

// Simple test route for student dashboard
Route::get('/test-student-dashboard', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    
    $user = auth()->user();
    \Log::info('Test student dashboard accessed', [
        'user_id' => $user->id,
        'email' => $user->email,
        'roles' => $user->roles->pluck('name')->toArray()
    ]);
    
    return view('student.dashboard', [
        'student' => $user,
        'enrolledGroups' => collect(),
        'upcomingSessions' => collect(),
        'totalHoursPurchased' => 0,
        'totalHoursUsed' => 0,
        'remainingHours' => 0,
        'activeGroups' => 0
    ]);
})->middleware('auth')->name('test-student-dashboard');

// Test route for permission checking
Route::get('/test-permissions', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    
    $user = auth()->user();
    $studentPermissions = [
        'student.dashboard.view',
        'student.courses.view',
        'student.schedule.view',
        'student.profile.view'
    ];
    
    $permissionResults = [];
    foreach ($studentPermissions as $permission) {
        $permissionResults[$permission] = [
            'hasPermissionTo' => $user->hasPermissionTo($permission),
            'PermissionHelper' => \App\Helpers\PermissionHelper::hasPermission($user, $permission)
        ];
    }
    
    return response()->json([
        'user_id' => $user->id,
        'email' => $user->email,
        'roles' => $user->roles->pluck('name')->toArray(),
        'all_permissions' => $user->permissions->pluck('name')->toArray(),
        'permission_tests' => $permissionResults,
        'has_permission' => _has_permission('student.dashboard.view')
    ]);
})->middleware('auth')->name('test-permissions');

// Role Management Routes
Route::middleware(['auth', 'permission:roles.view'])->group(function () {
    // Roles CRUD
    Route::get('/roles', [App\Http\Controllers\RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [App\Http\Controllers\RoleController::class, 'create'])->middleware(['permission:roles.create'])->name('roles.create');
    Route::post('/roles', [App\Http\Controllers\RoleController::class, 'store'])->middleware(['permission:roles.create'])->name('roles.store');
    Route::get('/roles/{id}', [App\Http\Controllers\RoleController::class, 'show'])->name('roles.show');
    Route::get('/roles/{id}/edit', [App\Http\Controllers\RoleController::class, 'edit'])->middleware(['permission:roles.edit'])->name('roles.edit');
    Route::put('/roles/{id}', [App\Http\Controllers\RoleController::class, 'update'])->middleware(['permission:roles.edit'])->name('roles.update');
    Route::delete('/roles/{id}', [App\Http\Controllers\RoleController::class, 'destroy'])->middleware(['permission:roles.delete'])->name('roles.destroy');
    Route::post('/roles/{id}/restore', [App\Http\Controllers\RoleController::class, 'restore'])->middleware(['permission:roles.edit'])->name('roles.restore');
    
    // Student Permissions Management
Route::get('/student-permissions', [App\Http\Controllers\StudentPermissionController::class, 'index'])->name('student-permissions.index');
Route::post('/student-permissions/update', [App\Http\Controllers\StudentPermissionController::class, 'updatePermissions'])->name('student-permissions.update');
Route::get('/student-permissions/{studentId}', [App\Http\Controllers\StudentPermissionController::class, 'getStudentPermissions'])->name('student-permissions.get');
Route::post('/student-permissions/bulk-update', [App\Http\Controllers\StudentPermissionController::class, 'bulkUpdatePermissions'])->name('student-permissions.bulk-update');

// Enrollment Request Management
Route::get('/enrollment-requests', [App\Http\Controllers\EnrollmentRequestController::class, 'index'])->name('enrollment-requests.index');
Route::get('/enrollment-requests/{id}', [App\Http\Controllers\EnrollmentRequestController::class, 'show'])->name('enrollment-requests.show');
Route::post('/enrollment-requests/{id}/approve', [App\Http\Controllers\EnrollmentRequestController::class, 'approve'])->name('enrollment-requests.approve');
Route::post('/enrollment-requests/{id}/reject', [App\Http\Controllers\EnrollmentRequestController::class, 'reject'])->name('enrollment-requests.reject');
Route::get('/enrollment-requests/{id}/availability-matching', [App\Http\Controllers\EnrollmentRequestController::class, 'getAvailabilityMatching'])->name('enrollment-requests.availability-matching');
Route::post('/enrollment-requests/{id}/auto-assign-slots', [App\Http\Controllers\EnrollmentRequestController::class, 'autoAssignSlots'])->name('enrollment-requests.auto-assign-slots');
    
    // Teachers CRUD
    Route::get('/teachers', [App\Http\Controllers\TeacherController::class, 'index'])->name('teachers.index');
    Route::get('/teachers/create', [App\Http\Controllers\TeacherController::class, 'create'])->name('teachers.create');
    Route::post('/teachers', [App\Http\Controllers\TeacherController::class, 'store'])->name('teachers.store');
    Route::get('/teachers/{id}', [App\Http\Controllers\TeacherController::class, 'show'])->name('teachers.show');
    Route::get('/teachers/{id}/edit', [App\Http\Controllers\TeacherController::class, 'edit'])->name('teachers.edit');
    Route::put('/teachers/{id}', [App\Http\Controllers\TeacherController::class, 'update'])->name('teachers.update');
    Route::delete('/teachers/{id}', [App\Http\Controllers\TeacherController::class, 'destroy'])->name('teachers.destroy');
    Route::post('/teachers/{id}/restore', [App\Http\Controllers\TeacherController::class, 'restore'])->name('teachers.restore');
    Route::delete('/teachers/{id}/documents', [App\Http\Controllers\TeacherController::class, 'deleteDocument'])->name('teachers.delete-document');
    
// Teacher Course Management
Route::get('/teachers/{id}/courses', [App\Http\Controllers\TeacherController::class, 'getCourses'])->name('teachers.courses');
Route::post('/teachers/{id}/courses', [App\Http\Controllers\TeacherController::class, 'saveCourses'])->name('teachers.courses.save');

// Teacher Availability Management
Route::get('/teachers/{id}/availability', [App\Http\Controllers\TeacherController::class, 'getAvailability'])->name('teachers.availability');
Route::post('/teachers/{id}/availability', [App\Http\Controllers\TeacherController::class, 'saveAvailability'])->name('teachers.availability.save');
Route::delete('/teachers/{id}/availability', [App\Http\Controllers\TeacherController::class, 'clearAvailability'])->name('teachers.availability.clear');
    
    // Students CRUD
    Route::get('/students', [App\Http\Controllers\StudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [App\Http\Controllers\StudentController::class, 'create'])->name('students.create');
    Route::post('/students', [App\Http\Controllers\StudentController::class, 'store'])->name('students.store');
    Route::get('/students/{id}', [App\Http\Controllers\StudentController::class, 'show'])->name('students.show');
    Route::get('/students/{id}/edit', [App\Http\Controllers\StudentController::class, 'edit'])->name('students.edit');
    Route::put('/students/{id}', [App\Http\Controllers\StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{id}', [App\Http\Controllers\StudentController::class, 'destroy'])->name('students.destroy');
    Route::post('/students/{id}/restore', [App\Http\Controllers\StudentController::class, 'restore'])->name('students.restore');
    Route::delete('/students/{id}/documents', [App\Http\Controllers\StudentController::class, 'deleteDocument'])->name('students.delete-document');
    
    // Student Documents Management
    Route::get('/students/{id}/documents', [App\Http\Controllers\StudentController::class, 'getDocuments'])->name('students.documents');
    Route::post('/students/{id}/documents', [App\Http\Controllers\StudentController::class, 'storeDocument'])->name('students.documents.store');
    Route::put('/students/{id}/documents/{documentId}', [App\Http\Controllers\StudentController::class, 'updateDocument'])->name('students.documents.update');
    Route::delete('/students/{id}/documents/{documentId}', [App\Http\Controllers\StudentController::class, 'deleteStudentDocument'])->name('students.documents.delete');
    Route::post('/students/{id}/documents/{documentId}/set-primary', [App\Http\Controllers\StudentController::class, 'setPrimaryDocument'])->name('students.documents.set-primary');
    
    // Student Availability Management
    Route::get('/students/{id}/availability', [App\Http\Controllers\StudentController::class, 'getAvailability'])->name('students.availability');
    Route::post('/students/{id}/availability', [App\Http\Controllers\StudentController::class, 'saveAvailability'])->name('students.availability.save');
    Route::delete('/students/{id}/availability', [App\Http\Controllers\StudentController::class, 'clearAvailability'])->name('students.availability.clear');
    
    // Courses CRUD
    Route::get('/courses', [App\Http\Controllers\CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [App\Http\Controllers\CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [App\Http\Controllers\CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{id}', [App\Http\Controllers\CourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{id}/edit', [App\Http\Controllers\CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{id}', [App\Http\Controllers\CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{id}', [App\Http\Controllers\CourseController::class, 'destroy'])->name('courses.destroy');
    Route::post('/courses/{id}/restore', [App\Http\Controllers\CourseController::class, 'restore'])->name('courses.restore');
    Route::post('/courses/{id}/toggle-status', [App\Http\Controllers\CourseController::class, 'toggleStatus'])->name('courses.toggle-status');
    Route::post('/courses/{id}/duplicate', [App\Http\Controllers\CourseController::class, 'duplicate'])->name('courses.duplicate');
    
    // Course Management Routes
    Route::get('/courses/{id}/topics', [App\Http\Controllers\CourseController::class, 'getAvailableTopics'])->name('courses.topics');
    Route::post('/courses/{id}/topics', [App\Http\Controllers\CourseController::class, 'saveCourseTopics'])->name('courses.topics.save');
    Route::get('/courses/{id}/teachers', [App\Http\Controllers\CourseController::class, 'getAvailableTeachers'])->name('courses.teachers');
    Route::post('/courses/{id}/teachers', [App\Http\Controllers\CourseController::class, 'saveCourseTeachers'])->name('courses.teachers.save');
    
    Route::get('/courses/by-language/{languageId}', [App\Http\Controllers\CourseController::class, 'getByLanguage'])->name('courses.by-language');
    Route::get('/courses/by-level/{levelId}', [App\Http\Controllers\CourseController::class, 'getByLevel'])->name('courses.by-level');
    Route::get('/courses/search', [App\Http\Controllers\CourseController::class, 'search'])->name('courses.search');
    Route::get('/courses/export/excel', [App\Http\Controllers\CourseController::class, 'exportExcel'])->name('courses.export.excel');
    Route::get('/courses/export/pdf', [App\Http\Controllers\CourseController::class, 'exportPdf'])->name('courses.export.pdf');
    
    // Groups CRUD
    Route::get('/groups', [App\Http\Controllers\GroupController::class, 'index'])->name('groups.index');
    Route::get('/groups/create', [App\Http\Controllers\GroupController::class, 'create'])->name('groups.create');
    Route::post('/groups', [App\Http\Controllers\GroupController::class, 'store'])->name('groups.store');
    Route::get('/groups/{id}', [App\Http\Controllers\GroupController::class, 'show'])->name('groups.show');
    Route::get('/groups/{id}/edit', [App\Http\Controllers\GroupController::class, 'edit'])->name('groups.edit');
    Route::put('/groups/{id}', [App\Http\Controllers\GroupController::class, 'update'])->name('groups.update');
    Route::delete('/groups/{id}', [App\Http\Controllers\GroupController::class, 'destroy'])->name('groups.destroy');
    Route::post('/groups/{id}/restore', [App\Http\Controllers\GroupController::class, 'restore'])->name('groups.restore');
    
    // Group Student Management
    Route::post('/groups/enroll-student', [App\Http\Controllers\GroupController::class, 'enrollStudent'])->name('groups.enroll-student');
    Route::post('/groups/remove-student', [App\Http\Controllers\GroupController::class, 'removeStudent'])->name('groups.remove-student');
    Route::post('/groups/transfer-student', [App\Http\Controllers\GroupController::class, 'transferStudent'])->name('groups.transfer-student');
    Route::get('/groups/{groupId}/students', [App\Http\Controllers\GroupController::class, 'getGroupStudents'])->name('groups.students');
    Route::post('/groups/update-student-status', [App\Http\Controllers\GroupController::class, 'updateStudentStatus'])->name('groups.update-student-status');
    
    // Group Session Management
    Route::post('/groups/create-sessions', [App\Http\Controllers\GroupController::class, 'createSessions'])->name('groups.create-sessions');
    Route::post('/groups/sessions/{sessionId}', [App\Http\Controllers\GroupController::class, 'updateSession'])->name('groups.update-session');
    Route::post('/groups/sessions/{sessionId}/cancel', [App\Http\Controllers\GroupController::class, 'cancelSession'])->name('groups.cancel-session');
    Route::get('/groups/{groupId}/sessions', [App\Http\Controllers\GroupController::class, 'getGroupSessions'])->name('groups.sessions');
    Route::get('/groups/upcoming-sessions', [App\Http\Controllers\GroupController::class, 'getUpcomingSessions'])->name('groups.upcoming-sessions');
    
    // Group Utilities
    Route::post('/groups/find-compatible', [App\Http\Controllers\GroupController::class, 'findCompatibleGroups'])->name('groups.find-compatible');
    Route::get('/groups/search', [App\Http\Controllers\GroupController::class, 'search'])->name('groups.search');
    Route::get('/groups/export/excel', [App\Http\Controllers\GroupController::class, 'exportExcel'])->name('groups.export.excel');
    Route::get('/groups/export/pdf', [App\Http\Controllers\GroupController::class, 'exportPdf'])->name('groups.export.pdf');
    
    // Curriculum CRUD
    Route::get('/curriculum', [App\Http\Controllers\CurriculumController::class, 'index'])->name('curriculum.index');
    Route::get('/curriculum/create', [App\Http\Controllers\CurriculumController::class, 'create'])->name('curriculum.create');
    Route::post('/curriculum', [App\Http\Controllers\CurriculumController::class, 'store'])->name('curriculum.store');
    
    // Curriculum Utilities (must be before parameterized routes)
    Route::get('/curriculum/grouped', [App\Http\Controllers\CurriculumController::class, 'getGroupedCurriculum'])->name('curriculum.grouped');
    Route::get('/curriculum/by-language-level', [App\Http\Controllers\CurriculumController::class, 'getByLanguageLevel'])->name('curriculum.by-language-level');
    Route::get('/curriculum/export/excel', [App\Http\Controllers\CurriculumController::class, 'exportExcel'])->name('curriculum.export.excel');
    Route::get('/curriculum/export/pdf', [App\Http\Controllers\CurriculumController::class, 'exportPdf'])->name('curriculum.export.pdf');
    Route::get('/curriculum/search', [App\Http\Controllers\CurriculumController::class, 'search'])->name('curriculum.search');
    Route::post('/curriculum/reorder', [App\Http\Controllers\CurriculumController::class, 'reorder'])->name('curriculum.reorder');
    
    // Parameterized routes (must be after specific routes)
    Route::get('/curriculum/{id}', [App\Http\Controllers\CurriculumController::class, 'show'])->name('curriculum.show');
    Route::get('/curriculum/{id}/edit', [App\Http\Controllers\CurriculumController::class, 'edit'])->name('curriculum.edit');
    Route::put('/curriculum/{id}', [App\Http\Controllers\CurriculumController::class, 'update'])->name('curriculum.update');
    Route::delete('/curriculum/{id}', [App\Http\Controllers\CurriculumController::class, 'destroy'])->name('curriculum.destroy');
    Route::post('/curriculum/{id}/restore', [App\Http\Controllers\CurriculumController::class, 'restore'])->name('curriculum.restore');
    Route::get('/curriculum/by-language/{languageId}', [App\Http\Controllers\CurriculumController::class, 'getByLanguage'])->name('curriculum.by-language');
    Route::get('/curriculum/by-level/{levelId}', [App\Http\Controllers\CurriculumController::class, 'getByLevel'])->name('curriculum.by-level');
    Route::get('/curriculum/groups/{groupId}/available', [App\Http\Controllers\CurriculumController::class, 'getAvailableTopics'])->name('curriculum.available-topics');
    Route::get('/curriculum/groups/{groupId}/used', [App\Http\Controllers\CurriculumController::class, 'getUsedTopics'])->name('curriculum.used-topics');

    // Settings Management
    Route::prefix('settings')->name('settings.')->middleware(['permission:settings.view'])->group(function () {
        // Settings Index
        Route::get('/', [App\Http\Controllers\SettingsController::class, 'index'])->name('index');
        
        // Rate Schemes CRUD
        Route::get('/rate-schemes', [App\Http\Controllers\RateSchemeController::class, 'index'])->name('rate-schemes.index');
        Route::get('/rate-schemes/create', [App\Http\Controllers\RateSchemeController::class, 'create'])->middleware(['permission:settings.rate_schemes.create'])->name('rate-schemes.create');
        Route::post('/rate-schemes', [App\Http\Controllers\RateSchemeController::class, 'store'])->middleware(['permission:settings.rate_schemes.create'])->name('rate-schemes.store');
        Route::get('/rate-schemes/{id}', [App\Http\Controllers\RateSchemeController::class, 'show'])->name('rate-schemes.show');
        Route::get('/rate-schemes/{id}/edit', [App\Http\Controllers\RateSchemeController::class, 'edit'])->middleware(['permission:settings.rate_schemes.edit'])->name('rate-schemes.edit');
        Route::put('/rate-schemes/{id}', [App\Http\Controllers\RateSchemeController::class, 'update'])->middleware(['permission:settings.rate_schemes.edit'])->name('rate-schemes.update');
        Route::delete('/rate-schemes/{id}', [App\Http\Controllers\RateSchemeController::class, 'destroy'])->middleware(['permission:settings.rate_schemes.delete'])->name('rate-schemes.destroy');
        Route::post('/rate-schemes/{id}/restore', [App\Http\Controllers\RateSchemeController::class, 'restore'])->middleware(['permission:settings.rate_schemes.edit'])->name('rate-schemes.restore');
        
        // Rate Schemes Utilities
        Route::get('/rate-schemes/export/excel', [App\Http\Controllers\RateSchemeController::class, 'exportExcel'])->name('rate-schemes.export.excel');
        Route::get('/rate-schemes/export/pdf', [App\Http\Controllers\RateSchemeController::class, 'exportPdf'])->name('rate-schemes.export.pdf');
        Route::get('/rate-schemes/search', [App\Http\Controllers\RateSchemeController::class, 'search'])->name('rate-schemes.search');

        // Languages CRUD
        Route::get('/languages', [App\Http\Controllers\LanguageController::class, 'index'])->name('languages.index');
        Route::get('/languages/create', [App\Http\Controllers\LanguageController::class, 'create'])->middleware(['permission:settings.languages.create'])->name('languages.create');
        Route::post('/languages', [App\Http\Controllers\LanguageController::class, 'store'])->middleware(['permission:settings.languages.create'])->name('languages.store');
        Route::get('/languages/{id}', [App\Http\Controllers\LanguageController::class, 'show'])->name('languages.show');
        Route::get('/languages/{id}/edit', [App\Http\Controllers\LanguageController::class, 'edit'])->middleware(['permission:settings.languages.edit'])->name('languages.edit');
        Route::put('/languages/{id}', [App\Http\Controllers\LanguageController::class, 'update'])->middleware(['permission:settings.languages.edit'])->name('languages.update');
        Route::delete('/languages/{id}', [App\Http\Controllers\LanguageController::class, 'destroy'])->middleware(['permission:settings.languages.delete'])->name('languages.destroy');
        Route::post('/languages/{id}/restore', [App\Http\Controllers\LanguageController::class, 'restore'])->middleware(['permission:settings.languages.edit'])->name('languages.restore');
        
        // Languages Utilities
        Route::get('/languages/export/excel', [App\Http\Controllers\LanguageController::class, 'exportExcel'])->name('languages.export.excel');
        Route::get('/languages/export/pdf', [App\Http\Controllers\LanguageController::class, 'exportPdf'])->name('languages.export.pdf');
        Route::get('/languages/search', [App\Http\Controllers\LanguageController::class, 'search'])->name('languages.search');

        // Course Levels CRUD
        Route::get('/course-levels', [App\Http\Controllers\CourseLevelController::class, 'index'])->name('course-levels.index');
        Route::get('/course-levels/create', [App\Http\Controllers\CourseLevelController::class, 'create'])->middleware(['permission:settings.course_levels.create'])->name('course-levels.create');
        Route::post('/course-levels', [App\Http\Controllers\CourseLevelController::class, 'store'])->middleware(['permission:settings.course_levels.create'])->name('course-levels.store');
        Route::get('/course-levels/{id}', [App\Http\Controllers\CourseLevelController::class, 'show'])->name('course-levels.show');
        Route::get('/course-levels/{id}/edit', [App\Http\Controllers\CourseLevelController::class, 'edit'])->middleware(['permission:settings.course_levels.edit'])->name('course-levels.edit');
        Route::put('/course-levels/{id}', [App\Http\Controllers\CourseLevelController::class, 'update'])->middleware(['permission:settings.course_levels.edit'])->name('course-levels.update');
        Route::delete('/course-levels/{id}', [App\Http\Controllers\CourseLevelController::class, 'destroy'])->middleware(['permission:settings.course_levels.delete'])->name('course-levels.destroy');
        Route::post('/course-levels/{id}/restore', [App\Http\Controllers\CourseLevelController::class, 'restore'])->middleware(['permission:settings.course_levels.edit'])->name('course-levels.restore');
        
        // Course Levels Utilities
        Route::get('/course-levels/export/excel', [App\Http\Controllers\CourseLevelController::class, 'exportExcel'])->name('course-levels.export.excel');
        Route::get('/course-levels/export/pdf', [App\Http\Controllers\CourseLevelController::class, 'exportPdf'])->name('course-levels.export.pdf');
        Route::get('/course-levels/search', [App\Http\Controllers\CourseLevelController::class, 'search'])->name('course-levels.search');
        Route::get('/course-levels/by-language/{languageId}', [App\Http\Controllers\CourseLevelController::class, 'getByLanguage'])->name('course-levels.by-language');
    });



});

// Student Routes (Outside of permission middleware)
Route::prefix('student')->name('student.')->middleware(['auth'])->group(function () {
    Route::post('/enroll', [App\Http\Controllers\StudentController::class, 'enroll'])->name('enroll');
    
    // Student Dashboard Routes with specific permissions
    Route::get('/dashboard', [App\Http\Controllers\StudentDashboardController::class, 'index'])
        // ->middleware(['permission:student.dashboard.view'])
        ->name('dashboard');
    
    Route::get('/courses', [App\Http\Controllers\StudentDashboardController::class, 'courses'])->name('courses');
    Route::get('/courses/{groupId}', [App\Http\Controllers\StudentDashboardController::class, 'courseDetails'])->name('course-details');
    Route::get('/available-courses', [App\Http\Controllers\StudentDashboardController::class, 'availableCourses'])->name('available-courses');
    Route::get('/enroll/{courseId}', [App\Http\Controllers\StudentDashboardController::class, 'showEnrollmentForm'])->name('enroll-form');
    Route::post('/enroll/{courseId}', [App\Http\Controllers\StudentDashboardController::class, 'requestEnrollment'])->name('request-enrollment');
    Route::get('/schedule', [App\Http\Controllers\StudentDashboardController::class, 'schedule'])->name('schedule');
    Route::get('/profile', [App\Http\Controllers\StudentDashboardController::class, 'profile'])->name('profile');
});     
 