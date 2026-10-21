<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSchoolParentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $parent = $this->route('parent');

        return $parent instanceof User && ($this->user()?->can('updateParent', $parent) ?? false);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => $this->string('email')->trim()->lower()->toString(),
            'name' => $this->string('name')->trim()->toString(),
        ]);
    }

    public function rules(): array
    {
        /** @var User $parent */
        $parent = $this->route('parent');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255', Rule::unique('users', 'email')->ignore($parent)],
            'birthday' => ['required', 'date_format:m/d/Y', 'before_or_equal:today'],
            'gender' => ['required', Rule::in(['Male', 'Female', 'Others'])],
            'phone' => ['required', 'string', 'max:30'],
            'blood_group' => ['nullable', Rule::in(['a+', 'a-', 'b+', 'b-', 'ab+', 'ab-', 'o+', 'o-'])],
            'address' => ['required', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'cropped_photo' => ['nullable', 'string', 'max:255'],
            'child_ids' => ['nullable', 'array'],
            'child_ids.*' => [
                'integer',
                'distinct',
                Rule::exists('users', 'id')->where(fn ($query) => $query
                    ->where('school_id', $this->user()?->school_id)
                    ->where('role_id', 7)),
            ],
        ];
    }
}
