<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'class_id',
        'school_id',
    ];

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'section_id');
    }

    public function dailyAttendances(): HasMany
    {
        return $this->hasMany(DailyAttendances::class, 'section_id');
    }

    public function routines(): HasMany
    {
        return $this->hasMany(Routine::class, 'section_id');
    }

    public function syllabuses(): HasMany
    {
        return $this->hasMany(Syllabus::class, 'section_id');
    }

    public function teacherPermissions(): HasMany
    {
        return $this->hasMany(TeacherPermission::class, 'section_id');
    }

    public function gradebooks(): HasMany
    {
        return $this->hasMany(Gradebook::class, 'section_id');
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class, 'section_id');
    }
}
