<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'language_id',
        'level_id',
        'rate_scheme_id',
        'teaching_hours',
        'total_hours',
        'mode',
        'description',
        'status',
        'is_curriculum_required',
        'max_students_per_group',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'teaching_hours' => 'integer',
        'is_curriculum_required' => 'boolean',
        'max_students_per_group' => 'integer',
        'is_active' => 'boolean',
        'created_by' => 'integer',
    ];

    /**
     * Get the language that owns the course.
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * Get the level that owns the course.
     */
    public function level()
    {
        return $this->belongsTo(CourseLevel::class);
    }

    /**
     * Get the rate scheme that owns the course.
     */
    public function rateScheme()
    {
        return $this->belongsTo(RateScheme::class);
    }

    /**
     * Get the creator of the course.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the groups for the course.
     */
    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    /**
     * Get the curriculum topics for the course.
     */
    public function curriculumTopics()
    {
        return $this->hasMany(CurriculumTopic::class, 'level_id', 'level_id')
                    ->where('language_id', $this->language_id);
    }

    /**
     * Get the teachers assigned to this course.
     */
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'teacher_course', 'course_id', 'teacher_id')
                    ->withPivot(['is_active', 'assigned_date', 'notes'])
                    ->withTimestamps();
    }

    /**
     * Get the curriculum topics assigned to this course.
     */
    public function courseCurriculum()
    {
        return $this->belongsToMany(CurriculumTopic::class, 'course_curriculum', 'course_id', 'curriculum_topic_id')
                    ->withPivot(['order_index', 'is_required', 'estimated_hours'])
                    ->withTimestamps()
                    ->orderBy('course_curriculum.order_index');
    }

    /**
     * Get the student academic hours for the course.
     */
    public function studentAcademicHours()
    {
        return $this->hasMany(StudentAcademicHour::class);
    }

    /**
     * Scope a query to only include active courses.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by language.
     */
    public function scopeByLanguage($query, $languageId)
    {
        return $query->where('language_id', $languageId);
    }

    /**
     * Scope a query to filter by level.
     */
    public function scopeByLevel($query, $levelId)
    {
        return $query->where('level_id', $levelId);
    }

    /**
     * Get the full course name with language and level.
     */
    public function getFullNameAttribute()
    {
        return $this->name . ' (' . $this->language->code . ' ' . $this->level->code . ')';
    }

    /**
     * Get the total teaching hours in regular hours.
     */
    public function getRegularHoursAttribute()
    {
        return $this->teaching_hours * 0.75; // Convert HC to regular hours
    }

    /**
     * Get the total number of classes (assuming 90-minute classes).
     */
    public function getTotalClassesAttribute()
    {
        return ceil($this->teaching_hours / 2); // 2 HC per 90-minute class
    }
}
