<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\PermissionHelper;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
          protected $fillable = [
              'name',
              'first_name',
              'last_name',
              'email',
              'password',
              'phone',
              'document_id',
              'passport_number',
              'birth_date',
              'address',
              'emergency_contact',
              'emergency_phone',
              'language_preference',
              'role',
              'is_active',
              'last_login_at',
              'documents',
          ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'birth_date' => 'date',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user's name (for Laravel authentication compatibility).
     */
    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get the user's display name.
     */
    public function getDisplayNameAttribute()
    {
        return $this->full_name ?: $this->email;
    }

    /**
     * Scope for active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for users by role.
     */
    public function scopeByRole($query, $role)
    {
        return $query->whereHas('roles', function ($q) use ($role) {
            $q->where('name', $role);
        });
    }

    /**
     * Get user's primary role.
     */
    public function getPrimaryRoleAttribute()
    {
        return $this->roles->first();
    }

    /**
     * Check if user has specific permission using custom helper.
     */
    public function hasCustomPermission($permission)
    {
        // return _has_permission($this, $permission);
        return PermissionHelper::hasPermission($this, $permission);
    }

    /**
     * Get documents as array.
     */
    public function getDocumentsAttribute($value)
    {
        if (empty($value)) {
            return [];
        }
        return is_string($value) ? explode(',', $value) : $value;
    }

    /**
     * Set documents as comma-separated string.
     */
    public function setDocumentsAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['documents'] = implode(',', $value);
        } else {
            $this->attributes['documents'] = $value;
        }
    }

    /**
     * Get document URLs for display.
     */
    public function getDocumentUrlsAttribute()
    {
        $documents = $this->documents;
        if (empty($documents)) {
            return [];
        }
        
        return array_map(function($doc) {
            return asset('storage/documents/' . $doc);
        }, $documents);
    }

    /**
     * Get the student documents for this user.
     */
    public function studentDocuments()
    {
        return $this->hasMany(StudentDocument::class, 'student_id');
    }

    /**
     * Get the primary document for this student.
     */
    public function primaryDocument()
    {
        return $this->hasOne(StudentDocument::class, 'student_id')->where('is_primary', true);
    }

    /**
     * Get the teacher availabilities for this user.
     */
    public function teacherAvailabilities()
    {
        return $this->hasMany(TeacherAvailability::class, 'teacher_id');
    }

    /**
     * Get the group enrollments for this student.
     */
    public function groupEnrollments()
    {
        return $this->hasMany(GroupStudent::class, 'student_id');
    }

    /**
     * Get the groups this student is enrolled in.
     */
    public function enrolledGroups()
    {
        return $this->belongsToMany(Group::class, 'group_students', 'student_id', 'group_id')
                    ->withPivot([
                        'academic_hours_purchased',
                        'academic_hours_used',
                        'enrollment_date',
                        'status',
                        'final_grade',
                        'notes'
                    ])
                    ->withTimestamps();
    }
}
