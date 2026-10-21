<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSchoolTeacherStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $teacher = $this->route('teacher');

        return $teacher instanceof User && ($this->user()?->can('updateTeacherStatus', $teacher) ?? false);
    }

    public function rules(): array
    {
        return ['status' => ['required', Rule::in(['enable', 'disable'])]];
    }
}
