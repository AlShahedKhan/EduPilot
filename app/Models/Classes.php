<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classes extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'school_id',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'class_id');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'class_id');
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'class_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'class_id');
    }

    public function dailyAttendances(): HasMany
    {
        return $this->hasMany(DailyAttendances::class, 'class_id');
    }

    public function routines(): HasMany
    {
        return $this->hasMany(Routine::class, 'class_id');
    }

    public function syllabuses(): HasMany
    {
        return $this->hasMany(Syllabus::class, 'class_id');
    }

    public function studentFeeManagers(): HasMany
    {
        return $this->hasMany(StudentFeeManager::class, 'class_id');
    }

    public function bookIssues(): HasMany
    {
        return $this->hasMany(BookIssue::class, 'class_id');
    }

    public function teacherPermissions(): HasMany
    {
        return $this->hasMany(TeacherPermission::class, 'class_id');
    }

    public function gradebooks(): HasMany
    {
        return $this->hasMany(Gradebook::class, 'class_id');
    }

    public function appraisals(): HasMany
    {
        return $this->hasMany(Appraisal::class, 'class_id');
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class, 'class_id');
    }
}
