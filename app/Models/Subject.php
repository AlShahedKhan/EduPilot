<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'class_id', 'school_id', 'session_id'
    ];

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'session_id');
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'subject_id');
    }

    public function routines(): HasMany
    {
        return $this->hasMany(Routine::class, 'subject_id');
    }

    public function syllabuses(): HasMany
    {
        return $this->hasMany(Syllabus::class, 'subject_id');
    }
}
