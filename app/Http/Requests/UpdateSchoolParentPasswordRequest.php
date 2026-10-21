<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateSchoolParentPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        $parent = $this->route('parent');

        return $parent instanceof User && ($this->user()?->can('updateParentPassword', $parent) ?? false);
    }

    public function rules(): array
    {
        return ['password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()]];
    }
}
