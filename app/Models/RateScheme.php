<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RateScheme extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'letter_code',
        'hourly_rate',
        'description',
        'is_active',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the courses for this rate scheme.
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Scope a query to only include active rate schemes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get formatted hourly rate.
     */
    public function getFormattedRateAttribute()
    {
        return '$' . number_format($this->hourly_rate, 2);
    }
}
