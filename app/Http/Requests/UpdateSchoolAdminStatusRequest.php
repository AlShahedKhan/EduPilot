<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSchoolAdminStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $admin = $this->route('admin');

        return $admin instanceof User && ($this->user()?->can('updateStatus', $admin) ?? false);
    }

    public function rules(): array
    {
        return ['status' => ['required', Rule::in(['enable', 'disable'])]];
    }
}
