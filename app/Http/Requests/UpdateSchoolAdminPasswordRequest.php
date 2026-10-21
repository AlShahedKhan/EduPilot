<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateSchoolAdminPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        $admin = $this->route('admin');

        return $admin instanceof User && ($this->user()?->can('updatePassword', $admin) ?? false);
    }

    public function rules(): array
    {
        return ['password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()]];
    }
}
