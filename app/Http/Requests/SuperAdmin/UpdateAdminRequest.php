<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => 'required|string',
            'email'        => 'required|email',
            'password'     => 'nullable|min:8',
            'phone'        => 'nullable|string',
            'address'      => 'nullable|string',
            'gender'       => 'nullable|string',
            'blood_group'  => 'nullable|string',
            'birthday'     => 'nullable|date',
            'photo'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
