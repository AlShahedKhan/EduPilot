<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'         => 'required|string',
            'email'         => 'required|email',
            'phone'         => 'required',
            'address'       => 'required|string',
            'school_info'   => 'required',
            'admin_name'    => 'required|string',
            'admin_email'   => 'required|email',
            'admin_address' => 'required|string',
            'admin_phone'   => 'required',
            'school_logo'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'btcl_email'    => 'nullable|email',
            'btcl_phone'    => 'nullable|string',
            'btcl_password' => 'nullable|string',
        ];
    }
}
