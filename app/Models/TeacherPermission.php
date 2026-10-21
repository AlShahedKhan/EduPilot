<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherPermission extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = ['class_id', 'section_id', 'school_id', 'teacher_id', 'marks', 'attendance'];

    protected $casts = [
        'marks' => 'boolean',
        'attendance' => 'boolean',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

}
