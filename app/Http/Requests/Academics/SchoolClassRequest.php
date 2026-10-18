<?php

namespace App\Http\Requests\Academics;

use App\Models\SchoolClass;

class SchoolClassRequest extends AcademicsRequest
{
    public function authorize(): bool
    {
        $class = $this->route('schoolClass');

        return $class instanceof SchoolClass
            ? (bool) $this->user()?->can('update', $class)
            : (bool) $this->user()?->can('create', SchoolClass::class);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $class = $this->route('schoolClass');

        return [
            'name' => ['required', 'string', 'max:100', $this->unique('school_classes', 'name', $class instanceof SchoolClass ? $class->id : null)],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:1000'],
        ];
    }
}
