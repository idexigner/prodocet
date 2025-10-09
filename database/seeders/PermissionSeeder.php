<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // Create permissions
            $permissions = [
                // Dashboard permissions
                'dashboard.view',
                
                // Groups permissions
                'groups.view',
                'groups.create',
                'groups.edit',
                'groups.delete',
                
                // Students permissions
                'students.view',
                'students.create',
                'students.edit',
                'students.delete',
                
                // Teachers permissions
                'teachers.view',
                'teachers.create',
                'teachers.edit',
                'teachers.delete',
                
                // Calendar permissions
                'calendar.view',
                'calendar.create',
                'calendar.edit',
                'calendar.delete',
                
                // Attendance permissions
                'attendance.view',
                'attendance.create',
                'attendance.edit',
                
                // Reports permissions
                'reports.view',
                'reports.generate',
                
                // Users permissions
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            
            // Enrollment Request Management
            'enrollment-requests.view',
            'enrollment-requests.approve',
            'enrollment-requests.reject',
            'enrollment-requests.auto-assign',
                
                // Settings permissions
                'settings.view',
                'settings.edit',
                
                // Analytics permissions
                'analytics.view',
                
                // Role and permission management
                'roles.view',
                'roles.create',
                'roles.edit',
                'roles.delete',
                'permissions.view',
                'permissions.assign',
                
                // Student-specific permissions
                'student.dashboard.view',
                'student.courses.view',
                'student.courses.enroll',
                'student.schedule.view',
                'student.profile.view',
                'student.profile.edit',
                'student.documents.view',
                'student.documents.upload',
                'student.attendance.view',
                'student.grades.view',
            'student.assignments.view',
            'student.assignments.submit',
            
            // Enrollment Request Management
            'enrollment-requests.view',
            'enrollment-requests.approve',
            'enrollment-requests.reject',
            'enrollment-requests.auto-assign',
            ];

            foreach ($permissions as $permission) {
                Permission::firstOrCreate(['name' => $permission]);
            }

            // Create roles
            $roles = [
                'super-admin' => [
                    'display_name' => 'Super Administrador',
                    'permissions' => Permission::all()->pluck('name')->toArray()
                ],
                'admin' => [
                    'display_name' => 'Administrador',
                    'permissions' => [
                        'dashboard.view',
                        'groups.view', 'groups.create', 'groups.edit', 'groups.delete',
                        'students.view', 'students.create', 'students.edit', 'students.delete',
                        'teachers.view', 'teachers.create', 'teachers.edit', 'teachers.delete',
                        'calendar.view', 'calendar.create', 'calendar.edit', 'calendar.delete',
                        'attendance.view', 'attendance.create', 'attendance.edit',
                        'reports.view', 'reports.generate',
                        'users.view', 'users.create', 'users.edit', 'users.delete',
                        'settings.view', 'settings.edit',
                        'analytics.view',
                    ]
                ],
                'teacher' => [
                    'display_name' => 'Profesor',
                    'permissions' => [
                        'dashboard.view',
                        'groups.view',
                        'students.view',
                        'calendar.view',
                        'attendance.view', 'attendance.create', 'attendance.edit',
                        'reports.view',
                    ]
                ],
                'student' => [
                    'display_name' => 'Estudiante',
                    'permissions' => [
                        'dashboard.view',
                        'calendar.view',
                        'attendance.view',
                        // Student-specific permissions
                        'student.dashboard.view',
                        'student.courses.view',
                        'student.schedule.view',
                        'student.profile.view',
                        'student.profile.edit',
                        'student.attendance.view',
                    ]
                ],
                'hr' => [
                    'display_name' => 'Recursos Humanos',
                    'permissions' => [
                        'dashboard.view',
                        'students.view',
                        'teachers.view',
                        'attendance.view',
                        'reports.view', 'reports.generate',
                        'analytics.view',
                    ]
                ],
            ];

            foreach ($roles as $roleName => $roleData) {
                $role = Role::firstOrCreate(
                    ['name' => $roleName],
                    [
                        'name' => $roleName,
                        'guard_name' => 'web',
                        'display_name' => $roleData['display_name'],
                        'description' => 'Default ' . $roleData['display_name'] . ' role',
                        'permissions' => $roleData['permissions'],
                        'is_active' => true,
                    ]
                );
            }

            // Create super admin user
            $superAdmin = User::firstOrCreate(
                ['email' => 'admin@prodocet.com'],
                [
                    'name' => 'Super Admin',
                    'first_name' => 'Super',
                    'last_name' => 'Admin',
                    'email' => 'admin@prodocet.com',
                    'password' => Hash::make('admin123'),
                    'phone' => '+56 9 1234 5678',
                    'document_id' => '12.345.678-9',
                    'language_preference' => 'es',
                    'is_active' => true,
                ]
            );

            $superAdmin->assignRole('super-admin');

            Log::info('Permissions and roles seeded successfully');

        } catch (\Exception $e) {
            Log::error('Permission seeder failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
