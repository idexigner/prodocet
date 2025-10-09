<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LmsController extends Controller
{
    /**
     * Display the login page.
     */
    public function index()
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        // Log the login attempt
        \Log::info('Login attempt', [
            'email' => $request->email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // Basic validation
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Try to authenticate the user
        if (\Illuminate\Support\Facades\Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Update last login time
            $user = \Illuminate\Support\Facades\Auth::user();
            $user->update(['last_login_at' => now()]);
            
            \Log::info('Login successful', [
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name
            ]);
            
            // if ($user->hasRole('student')) {
            //     \Log::info('user login auth Redirecting student to student dashboard');
            //     return redirect()->route('student.dashboard');
            // }else{
            //     \Log::info('to dashboard');
            //     return redirect()->route('dashboard');
            // }

            // Always redirect to main dashboard route, let it handle role-based routing
            \Log::info('Login successful, redirecting to dashboard');
            return redirect()->route('dashboard');
        }

        \Log::warning('Login failed', [
            'email' => $request->email,
            'ip' => $request->ip()
        ]);

        // If authentication fails, redirect back with error
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /** 
     * Handle registration request.
     */
    public function register(Request $request)
    {
        // Basic validation
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,teacher,student,hr',
            'terms' => 'required|accepted',
        ]);

        // Create the user
        $user = \App\Models\User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => $request->role,
            'language_preference' => 'es',
            'is_active' => true,
        ]);

        // Login the user
        \Illuminate\Support\Facades\Auth::login($user);

        return redirect()->route('dashboard');
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        \Illuminate\Support\Facades\Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }

    /**
     * Display the dashboard.
     */
    public function dashboard()
    {
        return view('dashboard.index');
    }

    /**
     * Display the groups page.
     */
    public function groups()
    {
        return view('groups.index');
    }

    /**
     * Display the calendar page.
     */
    public function calendar()
    {
        return view('calendar.index');
    }

    /**
     * Display the attendance page.
     */
    public function attendance()
    {
        return view('attendance.index');
    }

    /**
     * Display the teachers page.
     */
    public function teachers()
    {
        return view('teachers.index');
    }

    /**
     * Display the students page.
     */
    public function students()
    {
        return view('students.index');
    }

    /**
     * Display the HR panel page.
     */
    public function hrPanel()
    {
        return view('hr-panel.index');
    }

    /**
     * Display the users page.
     */
    public function users()
    {
        return view('users.index');
    }

    /**
     * Display the reports page.
     */
    public function reports()
    {
        return view('reports.index');
    }

    /**
     * Display the upload page.
     */
    public function upload()
    {
        return view('upload.index');
    }

    /**
     * Display the settings page.
     */
    public function settings()
    {
        return view('settings.index');
    }

    /**
     * Display the analytics page.
     */
    public function analytics()
    {
        return view('analytics.index');
    }

    /**
     * Display mermaid flowchart page.
     */
    public function mermaid()
    {
        return view('mermaid.index');
    }
}
