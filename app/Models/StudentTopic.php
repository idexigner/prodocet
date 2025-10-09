<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'topic_id',
        'enrollment_request_id',
        'status',
        'order',
        'started_at',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the student that owns the topic.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the course for the topic.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the curriculum topic.
     */
    public function topic()
    {
        return $this->belongsTo(CurriculumTopic::class, 'topic_id');
    }

    /**
     * Get the enrollment request.
     */
    public function enrollmentRequest()
    {
        return $this->belongsTo(EnrollmentRequest::class);
    }
}
