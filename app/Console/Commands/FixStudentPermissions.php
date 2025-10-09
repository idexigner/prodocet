<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;

class FixStudentPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:student-permissions {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix student permissions by ensuring the student role has the correct permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'student1@yopmail.com';
        
        $this->info("Fixing permissions for student: {$email}");
        
        // Get the student user
        $student = User::where('email', $email)->first();
        
        if (!$student) {
            $this->error("Student with email {$email} not found.");
            return 1;
        }
        
        $this->info("Student found: {$student->name} ({$student->email})");
        
        // Check if user has student role
        if (!$student->hasRole('student')) {
            $this->error("User does not have student role. Assigning it now...");
            $student->assignRole('student');
        }
        
        // Get the student role
        $studentRole = Role::where('name', 'student')->first();
        if (!$studentRole) {
            $this->error("Student role not found in database!");
            return 1;
        }
        
        $this->info("Student role found: {$studentRole->name}");
        
        // Define student permissions
        $studentPermissions = [
            'dashboard.view',
            'calendar.view',
            'attendance.view',
            'student.dashboard.view',
            'student.courses.view',
            'student.schedule.view',
            'student.profile.view',
            'student.profile.edit',
            'student.attendance.view',
        ];
        
        $this->info("Assigning student permissions to role...");
        
        // Update role permissions
        $studentRole->permissions = $studentPermissions;
        $studentRole->save();
        
        $this->info("Assigned permissions to student role: " . implode(', ', $studentPermissions));
        
        // Refresh user permissions
        $student->refresh();
        
        // Test specific permissions
        $testPermissions = [
            'student.dashboard.view',
            'student.courses.view',
            'student.schedule.view',
            'student.profile.view'
        ];
        
        $this->info("\nTesting permissions:");
        foreach ($testPermissions as $permission) {
            $hasPermission = \App\Helpers\PermissionHelper::hasPermission($student, $permission);
            $status = $hasPermission ? '✅' : '❌';
            $this->line("  {$status} {$permission}: " . ($hasPermission ? 'YES' : 'NO'));
        }
        
        $this->info("\nStudent permissions fixed successfully!");
        
        return 0;
    }
}