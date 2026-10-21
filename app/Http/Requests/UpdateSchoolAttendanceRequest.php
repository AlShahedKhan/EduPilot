<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class UpdateSchoolAttendanceRequest extends FormRequest { public function authorize(): bool { return $this->user()?->can('update', \App\Models\DailyAttendances::class) ?? false; } public function rules(): array { return ['class_id'=>['required','integer'],'section_id'=>['required','integer'],'session_id'=>['required','integer'],'date'=>['required','date','before_or_equal:today'],'attendance'=>['required','array'],'attendance.*'=>['boolean']]; } }
