<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class CreateSchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'school_name'    => 'required|string',
            'school_url'     => 'required|string|max:60',
            'school_email'   => 'required|email',
            'school_address' => 'required|string',
            'school_phone'   => 'required',
            'school_info'    => 'required',
            'admin_name'     => 'required|string',
            'admin_email'    => 'required|email',
            'admin_password' => 'required|min:8',
            'admin_address'  => 'required|string',
            'admin_phone'    => 'required',
            'btcl_email'     => 'nullable|email',
            'btcl_phone'     => 'nullable|string',
            'btcl_password'  => 'nullable|string',
        ];
    }
}
