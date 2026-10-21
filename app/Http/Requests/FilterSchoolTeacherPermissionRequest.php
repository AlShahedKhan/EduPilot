<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class FilterSchoolTeacherPermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewTeacherPermissions', User::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['search' => $this->string('search')->trim()->toString()]);
    }

    public function rules(): array
    {
        $schoolId = $this->user()?->school_id;

        return [
            'class_id' => ['nullable', 'integer', Rule::exists('classes', 'id')->where('school_id', $schoolId)],
            'section_id' => ['nullable', 'integer', Rule::exists('sections', 'id')->where('school_id', $schoolId)],
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['active', 'disabled'])],
            'permission' => ['nullable', Rule::in(['class_access', 'marks', 'attendance', 'none'])],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($this->filled('section_id') && !$this->filled('class_id')) {
                $validator->errors()->add('class_id', 'Select a class before selecting a section.');
                return;
            }

            if ($this->filled('section_id') && !\App\Models\Section::query()
                ->whereKey($this->integer('section_id'))
                ->where('class_id', $this->integer('class_id'))
                ->where('school_id', $this->user()?->school_id)
                ->exists()) {
                $validator->errors()->add('section_id', 'The selected section does not belong to this class.');
            }
        }];
    }
}
