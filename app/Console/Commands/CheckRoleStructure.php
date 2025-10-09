<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CheckRoleStructure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:role-structure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the role and permission structure';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Checking role structure...");
        
        // Check Spatie Role
        $spatieRole = Role::where('name', 'student')->first();
        if ($spatieRole) {
            $this->info("Spatie Role found: {$spatieRole->name}");
            $this->info("Spatie Role permissions field type: " . gettype($spatieRole->permissions));
            $this->info("Spatie Role permissions field value: " . $spatieRole->permissions);
            
            // Check if it's JSON
            if (is_string($spatieRole->permissions)) {
                $decoded = json_decode($spatieRole->permissions, true);
                if ($decoded) {
                    $this->info("Spatie Role permissions (decoded): " . implode(', ', $decoded));
                }
            }
        } else {
            $this->error("Spatie Role not found");
        }
        
        // Check Custom Role
        $customRole = \App\Models\Role::where('name', 'student')->first();
        if ($customRole) {
            $this->info("Custom Role found: {$customRole->name}");
            $this->info("Custom Role permissions: " . implode(', ', $customRole->permissions ?? []));
        } else {
            $this->error("Custom Role not found");
        }
        
        // Check User
        $user = User::where('email', 'student1@yopmail.com')->first();
        if ($user) {
            $this->info("User found: {$user->name}");
            $this->info("User roles: " . $user->roles->pluck('name')->implode(', '));
            $this->info("User permissions: " . $user->permissions->pluck('name')->implode(', '));
        }
        
        // Check all permissions
        $allPermissions = Permission::all();
        $this->info("Total permissions in database: {$allPermissions->count()}");
        $studentPermissions = $allPermissions->where('name', 'like', 'student.%');
        $this->info("Student permissions: " . $studentPermissions->pluck('name')->implode(', '));
        
        return 0;
    }
}