<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Language extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'native_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the courses for this language.
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Get the curriculum topics for this language.
     */
    public function curriculumTopics()
    {
        return $this->hasMany(CurriculumTopic::class);
    }

    /**
     * Scope a query to only include active languages.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get display name (native name if available, otherwise name).
     */
    public function getDisplayNameAttribute()
    {
        return $this->native_name ?: $this->name;
    }
}
