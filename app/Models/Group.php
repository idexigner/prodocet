<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'code',
        'course_id',
        'teacher_id',
        'start_date',
        'end_date',
        'classroom',
        'virtual_link',
        'max_students',
        'current_students',
        'status',
        'can_cancel_classes',
        'cancellation_hours_advance',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'max_students' => 'integer',
        'current_students' => 'integer',
        'can_cancel_classes' => 'boolean',
        'cancellation_hours_advance' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the course that owns the group.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the teacher that owns the group.
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the students for the group.
     */
    public function students()
    {
        return $this->belongsToMany(User::class, 'group_students', 'group_id', 'student_id')
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

    /**
     * Get the group students pivot records.
     */
    public function groupStudents()
    {
        return $this->hasMany(GroupStudent::class);
    }

    /**
     * Get the sessions for the group.
     */
    public function sessions()
    {
        return $this->hasMany(GroupSession::class);
    }

    /**
     * Scope a query to only include active groups.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by teacher.
     */
    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    /**
     * Scope a query to filter by course.
     */
    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * Get the full group name with course info.
     */
    public function getFullNameAttribute()
    {
        return $this->name . ' - ' . $this->course->full_name;
    }

    /**
     * Get the duration in days.
     */
    public function getDurationDaysAttribute()
    {
        return $this->start_date->diffInDays($this->end_date);
    }

    /**
     * Check if group is full.
     */
    public function getIsFullAttribute()
    {
        return $this->current_students >= $this->max_students;
    }

    /**
     * Get available spots.
     */
    public function getAvailableSpotsAttribute()
    {
        return $this->max_students - $this->current_students;
    }
}
