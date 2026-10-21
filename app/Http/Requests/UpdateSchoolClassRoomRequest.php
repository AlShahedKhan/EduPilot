<?php

namespace App\Http\Requests;

use App\Models\ClassRoom;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateSchoolClassRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        $classRoom = $this->route('classRoom');

        return $classRoom instanceof ClassRoom && ($this->user()?->can('update', $classRoom) ?? false);
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['name' => Str::squish((string) $this->input('name'))]);
    }

    public function rules(): array
    {
        /** @var ClassRoom $classRoom */
        $classRoom = $this->route('classRoom');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique((new ClassRoom)->getTable(), 'name')
                    ->where('school_id', $this->user()->school_id)
                    ->ignore($classRoom),
            ],
        ];
    }
}
