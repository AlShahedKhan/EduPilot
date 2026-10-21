<?php

namespace App\Http\Requests;

use App\Models\Classes;
use App\Models\Section;
use App\Models\Session;
use App\Models\Subject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreSchoolSyllabusRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('create', \App\Models\Syllabus::class) ?? false; }
    protected function prepareForValidation(): void { $this->merge(['title'=>Str::squish((string)$this->input('title'))]); }
    public function rules(): array
    {
        $schoolId=(int)$this->user()->school_id; $classId=(int)$this->input('class_id');
        return [
            'title'=>['required','string','max:255'],
            'class_id'=>['required','integer',Rule::exists((new Classes)->getTable(),'id')->where('school_id',$schoolId)],
            'section_id'=>['required','integer',Rule::exists((new Section)->getTable(),'id')->where(fn($q)=>$q->where('school_id',$schoolId)->where('class_id',$classId))],
            'subject_id'=>['required','integer',Rule::exists((new Subject)->getTable(),'id')->where(fn($q)=>$q->where('school_id',$schoolId)->where('class_id',$classId))],
            'session_id'=>['required','integer',Rule::exists((new Session)->getTable(),'id')->where('school_id',$schoolId)],
            'syllabus_file'=>['required','file','mimes:pdf,doc,docx','mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document','max:10240'],
        ];
    }
}
