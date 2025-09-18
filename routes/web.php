<?php

use Illuminate\Support\Facades\Route;
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

// Authentication Routes (without throttling)
Route::get('/', [LmsController::class, 'index'])->name('login');
Route::post('/login', [LmsController::class, 'login'])->name('login')->withoutMiddleware('throttle');
Route::post('/register', [LmsController::class, 'register'])->name('register')->withoutMiddleware('throttle');
Route::post('/logout', [LmsController::class, 'logout'])->name('logout');

// Dashboard and Main Application Routes (Protected)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [LmsController::class, 'dashboard'])->name('dashboard');
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
        return 'User is authenticated: ' . auth()->user()->email;
    } else {
        return 'User is not authenticated';
    }
})->name('test-auth');

// Role Management Routes
Route::middleware(['auth'])->group(function () {
    // Roles CRUD
    Route::get('/roles', [App\Http\Controllers\RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [App\Http\Controllers\RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles', [App\Http\Controllers\RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{id}', [App\Http\Controllers\RoleController::class, 'show'])->name('roles.show');
    Route::get('/roles/{id}/edit', [App\Http\Controllers\RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{id}', [App\Http\Controllers\RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{id}', [App\Http\Controllers\RoleController::class, 'destroy'])->name('roles.destroy');
    Route::post('/roles/{id}/restore', [App\Http\Controllers\RoleController::class, 'restore'])->name('roles.restore');
    
    // Teachers CRUD
    Route::get('/teachers', [App\Http\Controllers\TeacherController::class, 'index'])->name('teachers.index');
    Route::get('/teachers/create', [App\Http\Controllers\TeacherController::class, 'create'])->name('teachers.create');
    Route::post('/teachers', [App\Http\Controllers\TeacherController::class, 'store'])->name('teachers.store');
    Route::get('/teachers/{id}', [App\Http\Controllers\TeacherController::class, 'show'])->name('teachers.show');
    Route::get('/teachers/{id}/edit', [App\Http\Controllers\TeacherController::class, 'edit'])->name('teachers.edit');
    Route::put('/teachers/{id}', [App\Http\Controllers\TeacherController::class, 'update'])->name('teachers.update');
    Route::delete('/teachers/{id}', [App\Http\Controllers\TeacherController::class, 'destroy'])->name('teachers.destroy');
    Route::post('/teachers/{id}/restore', [App\Http\Controllers\TeacherController::class, 'restore'])->name('teachers.restore');
    
    // Students CRUD
    Route::get('/students', [App\Http\Controllers\StudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [App\Http\Controllers\StudentController::class, 'create'])->name('students.create');
    Route::post('/students', [App\Http\Controllers\StudentController::class, 'store'])->name('students.store');
    Route::get('/students/{id}', [App\Http\Controllers\StudentController::class, 'show'])->name('students.show');
    Route::get('/students/{id}/edit', [App\Http\Controllers\StudentController::class, 'edit'])->name('students.edit');
    Route::put('/students/{id}', [App\Http\Controllers\StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{id}', [App\Http\Controllers\StudentController::class, 'destroy'])->name('students.destroy');
    Route::post('/students/{id}/restore', [App\Http\Controllers\StudentController::class, 'restore'])->name('students.restore');
});