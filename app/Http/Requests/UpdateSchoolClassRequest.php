<?php

namespace App\Http\Requests;

use App\Models\Classes;
use App\Models\Section;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateSchoolClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        $schoolClass = $this->route('class');

        return $schoolClass instanceof Classes && ($this->user()?->can('update', $schoolClass) ?? false);
    }

    protected function prepareForValidation(): void
    {
        $sections = collect($this->input('sections', []))
            ->map(fn ($section): array => [
                'id' => filled($section['id'] ?? null) ? (int) $section['id'] : null,
                'name' => Str::squish((string) ($section['name'] ?? '')),
            ])
            ->all();

        $this->merge([
            'name' => Str::squish((string) $this->input('name')),
            'sections' => $sections,
            'removed_section_ids' => collect($this->input('removed_section_ids', []))
                ->filter(fn ($id): bool => filled($id))
                ->map(fn ($id): int => (int) $id)
                ->values()
                ->all(),
        ]);
    }

    public function rules(): array
    {
        /** @var Classes $schoolClass */
        $schoolClass = $this->route('class');
        $schoolId = (int) $this->user()->school_id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique((new Classes)->getTable(), 'name')
                    ->where('school_id', $schoolId)
                    ->ignore($schoolClass),
            ],
            'sections' => ['required', 'array', 'min:1', 'max:50'],
            'sections.*.id' => [
                'nullable',
                'integer',
                'distinct',
                Rule::exists((new Section)->getTable(), 'id')
                    ->where('school_id', $schoolId)
                    ->where('class_id', $schoolClass->id),
            ],
            'sections.*.name' => ['required', 'string', 'max:255'],
            'removed_section_ids' => ['array'],
            'removed_section_ids.*' => [
                'integer',
                'distinct',
                Rule::exists((new Section)->getTable(), 'id')
                    ->where('school_id', $schoolId)
                    ->where('class_id', $schoolClass->id),
            ],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                /** @var Classes $schoolClass */
                $schoolClass = $this->route('class');
                $submittedSections = collect($this->input('sections', []));
                $submittedIds = $submittedSections->pluck('id')->filter()->map(fn ($id): int => (int) $id);
                $removedIds = collect($this->input('removed_section_ids', []))->map(fn ($id): int => (int) $id);

                if ($submittedIds->intersect($removedIds)->isNotEmpty()) {
                    $validator->errors()->add('sections', 'A section cannot be retained and removed in the same update.');
                    return;
                }

                $untouchedNames = Section::query()
                    ->where('school_id', $this->user()->school_id)
                    ->where('class_id', $schoolClass->id)
                    ->whereNotIn('id', $submittedIds->merge($removedIds)->all())
                    ->pluck('name');

                $finalNames = $submittedSections->pluck('name')
                    ->merge($untouchedNames)
                    ->map(fn (string $name): string => Str::lower(Str::squish($name)));

                if ($finalNames->isEmpty()) {
                    $validator->errors()->add('sections', 'Every class must retain at least one section.');
                } elseif ($finalNames->duplicates()->isNotEmpty()) {
                    $validator->errors()->add('sections', 'Section names must be unique within the class.');
                }
            },
        ];
    }
}
