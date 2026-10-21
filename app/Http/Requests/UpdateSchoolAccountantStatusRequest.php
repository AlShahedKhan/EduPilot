<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSchoolAccountantStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $accountant = $this->route('accountant');

        return $accountant instanceof User && ($this->user()?->can('updateAccountantStatus', $accountant) ?? false);
    }

    public function rules(): array
    {
        return ['status' => ['required', Rule::in(['enable', 'disable'])]];
    }
}
