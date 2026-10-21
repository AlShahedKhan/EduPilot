<?php

namespace App\Http\Requests;

use App\Models\Classes;
use App\Models\School;
use App\Models\Session;
use App\Models\Subject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreSchoolSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Subject::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => Str::squish((string) $this->input('name')),
            'class_id' => filled($this->input('class_id')) ? (int) $this->input('class_id') : null,
            'session_id' => filled($this->input('session_id'))
                ? (int) $this->input('session_id')
                : School::query()->whereKey($this->user()->school_id)->value('running_session'),
        ]);
    }

    public function rules(): array
    {
        $schoolId = (int) $this->user()->school_id;

        return [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique((new Subject)->getTable(), 'name')
                    ->where('school_id', $schoolId)
                    ->where('class_id', $this->input('class_id'))
                    ->where('session_id', $this->input('session_id')),
            ],
            'class_id' => ['required', 'integer', Rule::exists((new Classes)->getTable(), 'id')->where('school_id', $schoolId)],
            'session_id' => ['required', 'integer', Rule::exists((new Session)->getTable(), 'id')->where('school_id', $schoolId)],
        ];
    }
}
