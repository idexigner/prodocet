<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnrollmentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'total_hours',
        'total_cost',
        'status',
        'availability',
        'requested_at',
        'approved_at',
        'rejected_at',
        'rejection_reason',
        'approved_by',
    ];

    protected $casts = [
        'availability' => 'array',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'total_cost' => 'decimal:2',
    ];

    /**
     * Get the student that owns the enrollment request.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the course for the enrollment request.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the admin who approved the request.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the student topics for this enrollment.
     */
    public function studentTopics()
    {
        return $this->hasMany(StudentTopic::class);
    }
}
