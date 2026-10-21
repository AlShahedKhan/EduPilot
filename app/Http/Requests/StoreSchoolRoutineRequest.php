<?php

namespace App\Http\Requests;

use App\Models\Classes;
use App\Models\ClassRoom;
use App\Models\Section;
use App\Models\Session;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSchoolRoutineRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('create', \App\Models\Routine::class) ?? false; }
    public function rules(): array
    {
        $schoolId=(int)$this->user()->school_id; $classId=(int)$this->input('class_id');
        return [
            'class_id'=>['required','integer',Rule::exists((new Classes)->getTable(),'id')->where('school_id',$schoolId)],
            'section_id'=>['required','integer',Rule::exists((new Section)->getTable(),'id')->where(fn($q)=>$q->where('school_id',$schoolId)->where('class_id',$classId))],
            'subject_id'=>['required','integer',Rule::exists((new Subject)->getTable(),'id')->where(fn($q)=>$q->where('school_id',$schoolId)->where('class_id',$classId))],
            'teacher_id'=>['required','integer',Rule::exists((new User)->getTable(),'id')->where(fn($q)=>$q->where('school_id',$schoolId)->where('role_id',3)->where('account_status','!=','disable'))],
            'room_id'=>['required','integer',Rule::exists((new ClassRoom)->getTable(),'id')->where('school_id',$schoolId)],
            'day'=>['required',Rule::in(['saturday','sunday','monday','tuesday','wednesday','thursday','friday'])],
            'starting_hour'=>['required','integer','between:0,23'],'starting_minute'=>['required','integer','between:0,59'],
            'ending_hour'=>['required','integer','between:0,23'],'ending_minute'=>['required','integer','between:0,59'],
            'session_id'=>['required','integer',Rule::exists((new Session)->getTable(),'id')->where('school_id',$schoolId)],
        ];
    }
}
