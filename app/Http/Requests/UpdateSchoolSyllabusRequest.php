<?php

namespace App\Http\Requests;

class UpdateSchoolSyllabusRequest extends StoreSchoolSyllabusRequest
{
    public function authorize(): bool { return $this->user()?->can('update', $this->route('syllabus')) ?? false; }
    public function rules(): array
    {
        $rules=parent::rules();
        $rules['syllabus_file']=['nullable','file','mimes:pdf,doc,docx','mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document','max:10240'];
        return $rules;
    }
}
