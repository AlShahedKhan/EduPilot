<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSchoolWardenStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $warden = $this->route('warden');

        return $warden instanceof User && ($this->user()?->can('updateWardenStatus', $warden) ?? false);
    }

    public function rules(): array
    {
        return ['status' => ['required', Rule::in(['enable', 'disable'])]];
    }
}
