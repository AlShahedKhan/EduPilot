<?php

namespace App\Http\Requests;

use App\Models\Classes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreSchoolClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Classes::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $sections = collect($this->input('sections', []))
            ->map(fn ($section): array => ['name' => Str::squish((string) ($section['name'] ?? ''))])
            ->all();

        $this->merge([
            'name' => Str::squish((string) $this->input('name')),
            'sections' => $sections,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique((new Classes)->getTable(), 'name')
                    ->where('school_id', $this->user()->school_id),
            ],
            'sections' => ['required', 'array', 'min:1', 'max:50'],
            'sections.*.name' => ['required', 'string', 'max:255'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $names = collect($this->input('sections', []))
                    ->pluck('name')
                    ->map(fn (string $name): string => Str::lower($name));

                if ($names->duplicates()->isNotEmpty()) {
                    $validator->errors()->add('sections', 'Section names must be unique within the class.');
                }
            },
        ];
    }
}
