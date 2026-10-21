<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSchoolParentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $parent = $this->route('parent');

        return $parent instanceof User && ($this->user()?->can('updateParentStatus', $parent) ?? false);
    }

    public function rules(): array
    {
        return ['status' => ['required', Rule::in(['enable', 'disable'])]];
    }
}
