<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_time',
        'end_time',
        'duration',
        'day_of_week',
        'is_active',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
        'is_active' => 'boolean',
    ];

    /**
     * Get the teacher availabilities for this slot.
     */
    public function teacherAvailabilities()
    {
        return $this->hasMany(TeacherAvailability::class, 'slot_id');
    }

    /**
     * Get the student availabilities for this slot.
     */
    public function studentAvailabilities()
    {
        return $this->hasMany(StudentAvailability::class, 'slot_id');
    }

    /**
     * Scope to get active slots only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get slots for a specific day.
     */
    public function scopeForDay($query, $day)
    {
        return $query->where('day_of_week', $day);
    }

    /**
     * Get formatted time range.
     */
    public function getTimeRangeAttribute()
    {
        return $this->start_time . ' - ' . $this->end_time;
    }
}