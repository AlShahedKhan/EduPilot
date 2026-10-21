<?php

namespace App\Http\Requests;

use App\Models\Classes;
use App\Models\Session;
use App\Models\Subject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterSchoolSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', Subject::class) ?? false;
    }

    public function rules(): array
    {
        $schoolId = (int) $this->user()->school_id;

        return [
            'search' => ['nullable', 'string', 'max:255'],
            'class_id' => ['nullable', 'integer', Rule::exists((new Classes)->getTable(), 'id')->where('school_id', $schoolId)],
            'session_id' => ['nullable', 'integer', Rule::exists((new Session)->getTable(), 'id')->where('school_id', $schoolId)],
            'usage' => ['nullable', Rule::in(['scheduled', 'unused'])],
        ];
    }
}
