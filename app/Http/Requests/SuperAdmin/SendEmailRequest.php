<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class SendEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'to'      => 'required|email',
            'subject' => 'required|string|max:255',
            'body'    => 'required|string',
        ];
    }
}
