<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreGradebookResultRequest extends FormRequest { public function authorize(): bool { return $this->user()?->can('create', \App\Models\Gradebook::class) ?? false; } public function rules(): array { return ['class_id'=>['required','integer'],'section_id'=>['required','integer'],'session_id'=>['required','integer'],'exam_category_id'=>['required','integer'],'student_id'=>['required','integer'],'subject_id'=>['required','integer'],'mark'=>['required','numeric','min:0'],'comment'=>['nullable','string','max:1000']]; } }
