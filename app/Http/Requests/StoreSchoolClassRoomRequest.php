<?php

namespace App\Http\Requests;

use App\Models\ClassRoom;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreSchoolClassRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', ClassRoom::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['name' => Str::squish((string) $this->input('name'))]);
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique((new ClassRoom)->getTable(), 'name')
                    ->where('school_id', $this->user()->school_id),
            ],
        ];
    }
}
