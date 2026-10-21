<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateSchoolTeacherPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        $teacher = $this->route('teacher');

        return $teacher instanceof User && ($this->user()?->can('updateTeacherPassword', $teacher) ?? false);
    }

    public function rules(): array
    {
        return ['password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()]];
    }
}
