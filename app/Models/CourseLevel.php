<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseLevel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'order_index',
        'is_active',
        'language_id',
        'status',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order_index' => 'integer',
    ];

    /**
     * Get the language that owns the course level.
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * Get the courses for this level.
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Get the curriculum topics for this level.
     */
    public function curriculumTopics()
    {
        return $this->hasMany(CurriculumTopic::class);
    }

    /**
     * Scope a query to only include active levels.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by index.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order_index');
    }
}
