<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAcademicHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'hours_purchased',
        'hours_used',
        'purchase_date',
        'expiry_date',
        'status',
    ];

    protected $casts = [
        'hours_purchased' => 'integer',
        'hours_used' => 'decimal:2',
        'purchase_date' => 'date',
        'expiry_date' => 'date',
    ];

    /**
     * Get the student that owns the academic hours.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the course that owns the academic hours.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by student.
     */
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope a query to filter by course.
     */
    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * Scope a query to get active hours.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to get expired hours.
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
                    ->orWhere(function ($q) {
                        $q->where('status', 'active')
                          ->where('expiry_date', '<', now()->toDateString());
                    });
    }

    /**
     * Get remaining hours.
     */
    public function getRemainingHoursAttribute()
    {
        return $this->hours_purchased - $this->hours_used;
    }

    /**
     * Get usage percentage.
     */
    public function getUsagePercentageAttribute()
    {
        if ($this->hours_purchased == 0) {
            return 0;
        }
        return ($this->hours_used / $this->hours_purchased) * 100;
    }

    /**
     * Check if hours are expired.
     */
    public function getIsExpiredAttribute()
    {
        return $this->expiry_date && $this->expiry_date < now()->toDateString();
    }

    /**
     * Check if hours are completed.
     */
    public function getIsCompletedAttribute()
    {
        return $this->status === 'completed' || $this->remaining_hours <= 0;
    }

    /**
     * Check if hours are active.
     */
    public function getIsActiveAttribute()
    {
        return $this->status === 'active' && !$this->is_expired && $this->remaining_hours > 0;
    }
}
