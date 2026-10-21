<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Syllabus extends Model
{
    use HasFactory;

    protected $table = 'syllabuses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'class_id', 'section_id', 'subject_id', 'file', 'school_id','session_id'
    ];

    public function schoolClass(): BelongsTo { return $this->belongsTo(Classes::class, 'class_id'); }
    public function section(): BelongsTo { return $this->belongsTo(Section::class); }
    public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }
    public function session(): BelongsTo { return $this->belongsTo(Session::class); }
}
