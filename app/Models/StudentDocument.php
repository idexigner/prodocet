<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'document_type',
        'document_number',
        'document_name',
        'is_primary',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the student that owns the document.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Scope to get only active documents.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get primary documents.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Get the full document type name.
     */
    public function getDocumentTypeNameAttribute()
    {
        $types = [
            'C.C.' => 'Cédula de Ciudadanía',
            'C.E.' => 'Cédula de Extranjería',
            'T.I.' => 'Tarjeta de Identidad',
            'Pa.' => 'Pasaporte',
            'Nit' => 'NIT',
            'other' => 'Other'
        ];

        return $types[$this->document_type] ?? $this->document_type;
    }

    /**
     * Get the display name for the document.
     */
    public function getDisplayNameAttribute()
    {
        if ($this->document_type === 'other' && $this->document_name) {
            return $this->document_name;
        }

        return $this->document_type_name;
    }
}