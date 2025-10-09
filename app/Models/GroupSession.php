<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'session_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'teaching_hours',
        'topic_id',
        'custom_topic',
        'status',
        'attendance_taken',
        'notes',
    ];

    protected $casts = [
        'session_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'duration_minutes' => 'integer',
        'teaching_hours' => 'decimal:2',
        'attendance_taken' => 'boolean',
    ];

    /**
     * Get the group that owns the session.
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the topic for the session.
     */
    public function topic()
    {
        return $this->belongsTo(CurriculumTopic::class, 'topic_id');
    }

    /**
     * Get the attendance records for the session.
     */
    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'session_id');
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
     * Scope a query to get upcoming sessions.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('session_date', '>=', now()->toDateString())
                    ->where('status', 'scheduled')
                    ->orderBy('session_date')
                    ->orderBy('start_time');
    }

    /**
     * Scope a query to get past sessions.
     */
    public function scopePast($query)
    {
        return $query->where('session_date', '<', now()->toDateString())
                    ->orderBy('session_date', 'desc')
                    ->orderBy('start_time', 'desc');
    }

    /**
     * Scope a query to get sessions for a date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('session_date', [$startDate, $endDate]);
    }

    /**
     * Get the session title (topic or custom topic).
     */
    public function getSessionTitleAttribute()
    {
        return $this->custom_topic ?: $this->topic?->title;
    }

    /**
     * Get the session duration in hours.
     */
    public function getDurationHoursAttribute()
    {
        return $this->duration_minutes / 60;
    }

    /**
     * Check if session is upcoming.
     */
    public function getIsUpcomingAttribute()
    {
        return $this->session_date >= now()->toDateString() && $this->status === 'scheduled';
    }

    /**
     * Check if session is past.
     */
    public function getIsPastAttribute()
    {
        return $this->session_date < now()->toDateString();
    }

    /**
     * Check if session can be cancelled.
     */
    public function getCanBeCancelledAttribute()
    {
        if (!$this->group->can_cancel_classes) {
            return false;
        }

        $hoursUntilSession = now()->diffInHours($this->session_date . ' ' . $this->start_time, false);
        return $hoursUntilSession >= $this->group->cancellation_hours_advance;
    }
}
