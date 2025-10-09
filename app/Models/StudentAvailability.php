<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'slot_id',
        'day_of_week',
        'is_available',
        'priority',
        'effective_from',
        'effective_until',
        'notes',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'priority' => 'integer',
        'effective_from' => 'date',
        'effective_until' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function slot()
    {
        return $this->belongsTo(Slot::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeUnavailable($query)
    {
        return $query->where('is_available', false);
    }
}
