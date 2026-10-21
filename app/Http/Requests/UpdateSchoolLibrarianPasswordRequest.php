<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateSchoolLibrarianPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        $librarian = $this->route('librarian');

        return $librarian instanceof User && ($this->user()?->can('updateLibrarianPassword', $librarian) ?? false);
    }

    public function rules(): array
    {
        return ['password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()]];
    }
}
