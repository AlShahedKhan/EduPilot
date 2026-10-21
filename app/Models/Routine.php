<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Routine extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'class_id', 'section_id', 'subject_id', 'starting_hour', 'ending_hour', 'starting_minute', 'ending_minute','day', 'teacher_id', 'room_id','school_id', 'session_id'
    ];

    public function schoolClass(): BelongsTo { return $this->belongsTo(Classes::class, 'class_id'); }
    public function section(): BelongsTo { return $this->belongsTo(Section::class); }
    public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }
    public function teacher(): BelongsTo { return $this->belongsTo(User::class, 'teacher_id'); }
    public function room(): BelongsTo { return $this->belongsTo(ClassRoom::class, 'room_id'); }
    public function session(): BelongsTo { return $this->belongsTo(Session::class); }
}
