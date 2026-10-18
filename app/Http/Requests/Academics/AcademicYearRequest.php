<?php

namespace App\Http\Requests\Academics;

use App\Models\AcademicYear;

class AcademicYearRequest extends AcademicsRequest
{
    public function authorize(): bool
    {
        $year = $this->route('year');

        return $year instanceof AcademicYear
            ? (bool) $this->user()?->can('update', $year)
            : (bool) $this->user()?->can('create', AcademicYear::class);
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_current' => $this->boolean('is_current')]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $year = $this->route('year');

        return [
            'name' => ['required', 'string', 'max:100', $this->unique('academic_years', 'name', $year instanceof AcademicYear ? $year->id : null)],
            'starts_on' => ['required', 'date'],
            'ends_on' => ['required', 'date', 'after_or_equal:starts_on'],
            'is_current' => ['boolean'],
        ];
    }
}
