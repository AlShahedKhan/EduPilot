<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolAccountantDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $accountant = $this->route('accountant');

        return $accountant instanceof User && ($this->user()?->can('manageAccountantDocuments', $accountant) ?? false);
    }

    public function rules(): array
    {
        return [
            'file_name' => ['required', 'string', 'max:100'],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,csv,txt', 'max:4096'],
        ];
    }
}
