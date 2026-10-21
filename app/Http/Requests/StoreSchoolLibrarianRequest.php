<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreSchoolLibrarianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('createLibrarian', User::class) ?? false;
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
        return array_merge($this->profileRules(), [
            'email' => ['required', 'email:rfc', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);
    }

    /** @return array<string, mixed> */
    private function profileRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'birthday' => ['required', 'date_format:m/d/Y', 'before_or_equal:today'],
            'gender' => ['required', Rule::in(['Male', 'Female', 'Others'])],
            'phone' => ['required', 'string', 'max:30'],
            'blood_group' => ['nullable', Rule::in(['a+', 'a-', 'b+', 'b-', 'ab+', 'ab-', 'o+', 'o-'])],
            'address' => ['required', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'cropped_photo' => ['nullable', 'string', 'max:255'],
        ];
    }
}
