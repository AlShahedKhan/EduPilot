<?php

namespace App\Http\Requests;

use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateSchoolTeacherPermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manageTeacherPermissions', User::class) ?? false;
    }

    public function rules(): array
    {
        $schoolId = $this->user()?->school_id;

        return [
            'class_id' => ['required', 'integer', Rule::exists('classes', 'id')->where('school_id', $schoolId)],
            'section_id' => ['required', 'integer', Rule::exists('sections', 'id')->where('school_id', $schoolId)],
            'permissions' => ['required', 'array', 'max:50'],
            'permissions.*' => ['required', 'array:class_access,marks,attendance'],
            'permissions.*.class_access' => ['required', 'boolean'],
            'permissions.*.marks' => ['required', 'boolean'],
            'permissions.*.attendance' => ['required', 'boolean'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if (!Section::query()->whereKey($this->integer('section_id'))->where('class_id', $this->integer('class_id'))->where('school_id', $this->user()?->school_id)->exists()) {
                $validator->errors()->add('section_id', 'The selected section does not belong to this class.');
            }

            $teacherIds = collect(array_keys($this->input('permissions', [])))->map(fn ($id): int => (int) $id)->filter()->unique();
            $validTeachers = User::query()->whereKey($teacherIds)->where('role_id', 3)->where('school_id', $this->user()?->school_id)
                ->where(fn ($query) => $query->whereNull('account_status')->orWhere('account_status', '!=', 'disable'))
                ->count();

            if ($validTeachers !== $teacherIds->count()) {
                $validator->errors()->add('permissions', 'Submitted teachers must be active teachers from this school.');
            }

            foreach ($this->input('permissions', []) as $teacherId => $permission) {
                if (!filter_var($permission['class_access'] ?? false, FILTER_VALIDATE_BOOL)
                    && (filter_var($permission['marks'] ?? false, FILTER_VALIDATE_BOOL) || filter_var($permission['attendance'] ?? false, FILTER_VALIDATE_BOOL))) {
                    $validator->errors()->add("permissions.{$teacherId}.class_access", 'Marks and attendance require class access.');
                }
            }
        }];
    }
}
