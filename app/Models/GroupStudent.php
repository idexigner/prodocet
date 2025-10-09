<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupStudent extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'student_id',
        'academic_hours_purchased',
        'academic_hours_used',
        'enrollment_date',
        'status',
        'final_grade',
        'notes',
    ];

    protected $casts = [
        'academic_hours_purchased' => 'integer',
        'academic_hours_used' => 'decimal:2',
        'enrollment_date' => 'date',
        'final_grade' => 'decimal:2',
    ];

    /**
     * Get the group that owns the enrollment.
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the student that owns the enrollment.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by group.
     */
    public function scopeByGroup($query, $groupId)
    {
        return $query->where('group_id', $groupId);
    }

    /**
     * Scope a query to filter by student.
     */
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Get remaining academic hours.
     */
    public function getRemainingHoursAttribute()
    {
        return $this->academic_hours_purchased - $this->academic_hours_used;
    }

    /**
     * Get hours usage percentage.
     */
    public function getUsagePercentageAttribute()
    {
        if ($this->academic_hours_purchased == 0) {
            return 0;
        }
        return ($this->academic_hours_used / $this->academic_hours_purchased) * 100;
    }

    /**
     * Check if student has completed the course.
     */
    public function getIsCompletedAttribute()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if student is actively enrolled.
     */
    public function getIsActiveAttribute()
    {
        return $this->status === 'enrolled';
    }
}
