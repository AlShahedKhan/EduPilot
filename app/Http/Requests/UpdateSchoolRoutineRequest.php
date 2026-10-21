<?php

namespace App\Http\Requests;

class UpdateSchoolRoutineRequest extends StoreSchoolRoutineRequest
{
    public function authorize(): bool { return $this->user()?->can('update', $this->route('routine')) ?? false; }
}
