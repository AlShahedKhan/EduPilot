<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyAttendances extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'class_id', 'section_id', 'student_id', 'status', 'session_id', 'school_id', 'timestamp'
    ];

    protected function casts(): array { return ['timestamp' => 'integer', 'status' => 'boolean']; }
    public function student(): BelongsTo { return $this->belongsTo(User::class, 'student_id'); }
    public function schoolClass(): BelongsTo { return $this->belongsTo(Classes::class, 'class_id'); }
    public function section(): BelongsTo { return $this->belongsTo(Section::class); }
}
