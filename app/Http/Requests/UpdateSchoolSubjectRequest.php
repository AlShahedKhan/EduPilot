<?php

namespace App\Http\Requests;

use App\Models\Classes;
use App\Models\Session;
use App\Models\School;
use App\Models\Subject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateSchoolSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        $subject = $this->route('subject');

        return $subject instanceof Subject && ($this->user()?->can('update', $subject) ?? false);
    }

    protected function prepareForValidation(): void
    {
        $subject = $this->route('subject');

        $this->merge([
            'name' => Str::squish((string) $this->input('name')),
            'class_id' => filled($this->input('class_id')) ? (int) $this->input('class_id') : null,
            'session_id' => filled($this->input('session_id'))
                ? (int) $this->input('session_id')
                : (int) ($subject instanceof Subject
                    ? $subject->session_id
                    : School::query()->whereKey($this->user()->school_id)->value('running_session')),
        ]);
    }

    public function rules(): array
    {
        $schoolId = (int) $this->user()->school_id;
        $subject = $this->route('subject');

        return [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique((new Subject)->getTable(), 'name')
                    ->where('school_id', $schoolId)
                    ->where('class_id', $this->input('class_id'))
                    ->where('session_id', $this->input('session_id'))
                    ->ignore($subject),
            ],
            'class_id' => ['required', 'integer', Rule::exists((new Classes)->getTable(), 'id')->where('school_id', $schoolId)],
            'session_id' => ['required', 'integer', Rule::exists((new Session)->getTable(), 'id')->where('school_id', $schoolId)],
        ];
    }
}
