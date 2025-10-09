<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckStudentRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:student-role {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if a student user has the student role assigned';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'student1@yopmail.com';
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }
        
        $this->info("User found: {$user->name} ({$user->email})");
        $this->info("User ID: {$user->id}");
        $this->info("Is Active: " . ($user->is_active ? 'Yes' : 'No'));
        
        $roles = $user->roles->pluck('name')->toArray();
        $this->info("Roles: " . (empty($roles) ? 'None' : implode(', ', $roles)));
        
        $hasStudentRole = $user->hasRole('student');
        $this->info("Has Student Role: " . ($hasStudentRole ? 'Yes' : 'No'));
        
        if (!$hasStudentRole) {
            $this->warn("User does not have student role. Assigning it now...");
            $user->assignRole('student');
            $this->info("Student role assigned successfully!");
        }
        
        return 0;
    }
}