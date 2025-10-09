<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CurriculumTopic extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'language_id',
        'level_id',
        'title',
        'description',
        'content',
        'documents',
        'order_index',
        'is_active',
    ];

    protected $casts = [
        'order_index' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the documents attribute as an array.
     */
    public function getDocumentsAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * Set the documents attribute from an array.
     */
    public function setDocumentsAttribute($value)
    {
        $this->attributes['documents'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * Get the document URLs for public access.
     */
    public function getDocumentUrlsAttribute()
    {
        $documents = $this->documents;
        return array_map(function($document) {
            return [
                'name' => $document,
                'url' => asset('storage/curriculum_documents/' . $document)
            ];
        }, $documents);
    }

    /**
     * Get the language that owns the topic.
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * Get the level that owns the topic.
     */
    public function level()
    {
        return $this->belongsTo(CourseLevel::class);
    }

    /**
     * Get the group sessions for this topic.
     */
    public function groupSessions()
    {
        return $this->hasMany(GroupSession::class, 'topic_id');
    }

    /**
     * Scope a query to only include active topics.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by language and level.
     */
    public function scopeByLanguageLevel($query, $languageId, $levelId)
    {
        return $query->where('language_id', $languageId)
                    ->where('level_id', $levelId);
    }

    /**
     * Scope a query to order by index.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order_index');
    }

    /**
     * Get the full topic name with language and level.
     */
    public function getFullNameAttribute()
    {
        return $this->title . ' (' . $this->language->code . ' ' . $this->level->code . ')';
    }
}
