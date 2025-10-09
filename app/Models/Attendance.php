<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'student_id',
        'status',
        'notes',
        'marked_by',
        'marked_at',
    ];

    protected $casts = [
        'marked_at' => 'datetime',
    ];

    /**
     * Get the session that owns the attendance.
     */
    public function session()
    {
        return $this->belongsTo(GroupSession::class, 'session_id');
    }

    /**
     * Get the student that owns the attendance.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the teacher who marked the attendance.
     */
    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by');
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
     * Scope a query to filter by session.
     */
    public function scopeBySession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope a query to get present attendance.
     */
    public function scopePresent($query)
    {
        return $query->whereIn('status', ['present', 'late']);
    }

    /**
     * Scope a query to get absent attendance.
     */
    public function scopeAbsent($query)
    {
        return $query->whereIn('status', ['absent', 'excused']);
    }

    /**
     * Check if student was present.
     */
    public function getIsPresentAttribute()
    {
        return in_array($this->status, ['present', 'late']);
    }

    /**
     * Check if student was absent.
     */
    public function getIsAbsentAttribute()
    {
        return in_array($this->status, ['absent', 'excused']);
    }

    /**
     * Check if absence is excused.
     */
    public function getIsExcusedAttribute()
    {
        return $this->status === 'excused';
    }

    /**
     * Check if student was late.
     */
    public function getIsLateAttribute()
    {
        return $this->status === 'late';
    }
}
