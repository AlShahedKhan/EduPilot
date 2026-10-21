<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolLibrarianDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $librarian = $this->route('librarian');

        return $librarian instanceof User && ($this->user()?->can('manageLibrarianDocuments', $librarian) ?? false);
    }

    public function rules(): array
    {
        return [
            'file_name' => ['required', 'string', 'max:100'],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,csv,txt', 'max:4096'],
        ];
    }
}
