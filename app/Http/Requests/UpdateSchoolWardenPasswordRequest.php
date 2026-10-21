<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateSchoolWardenPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        $warden = $this->route('warden');

        return $warden instanceof User && ($this->user()?->can('updateWardenPassword', $warden) ?? false);
    }

    public function rules(): array
    {
        return ['password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()]];
    }
}
