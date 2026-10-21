<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateSchoolAccountantPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        $accountant = $this->route('accountant');

        return $accountant instanceof User && ($this->user()?->can('updateAccountantPassword', $accountant) ?? false);
    }

    public function rules(): array
    {
        return ['password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()]];
    }
}
