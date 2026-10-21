<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolParentDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $parent = $this->route('parent');

        return $parent instanceof User && ($this->user()?->can('manageParentDocuments', $parent) ?? false);
    }

    public function rules(): array
    {
        return [
            'file_name' => ['required', 'string', 'max:100'],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,csv,txt', 'max:4096'],
        ];
    }
}
