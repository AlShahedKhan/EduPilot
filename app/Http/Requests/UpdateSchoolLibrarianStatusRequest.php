<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSchoolLibrarianStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $librarian = $this->route('librarian');

        return $librarian instanceof User && ($this->user()?->can('updateLibrarianStatus', $librarian) ?? false);
    }

    public function rules(): array
    {
        return ['status' => ['required', Rule::in(['enable', 'disable'])]];
    }
}
