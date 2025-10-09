<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'slot_id',
        'day_of_week',
        'is_available',
        'effective_from',
        'effective_until',
        'notes',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'effective_from' => 'date',
        'effective_until' => 'date',
    ];

    /**
     * Get the teacher that owns the availability.
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the slot for this availability.
     */
    public function slot()
    {
        return $this->belongsTo(Slot::class);
    }

    /**
     * Scope to get only available slots.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope to get only unavailable slots.
     */
    public function scopeUnavailable($query)
    {
        return $query->where('is_available', false);
    }
}