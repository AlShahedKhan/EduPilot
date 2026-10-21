<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSchoolAdminPermissionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        $admin = $this->route('admin');

        return $admin instanceof User && ($this->user()?->can('managePermissions', $admin) ?? false);
    }

    public function rules(): array
    {
        return [
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in(array_keys(config('school_admin_permissions', [])))],
        ];
    }
}
