<?php

namespace App\Http\Requests\Academics;

use App\Models\AcademicYear;
use App\Models\Term;
use Illuminate\Validation\Rule;

class TermRequest extends AcademicsRequest
{
    public function authorize(): bool
    {
        $term = $this->route('term');

        return $term instanceof Term
            ? (bool) $this->user()?->can('update', $term)
            : (bool) $this->user()?->can('create', Term::class);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $term = $this->route('term');
        $year = $this->route('year');
        $yearId = $term instanceof Term ? $term->academic_year_id : ($year instanceof AcademicYear ? $year->id : 0);

        $name = Rule::unique('terms', 'name')->where('academic_year_id', $yearId);

        return [
            'name' => ['required', 'string', 'max:100', $term instanceof Term ? $name->ignore($term->id) : $name],
            'starts_on' => ['required', 'date'],
            'ends_on' => ['required', 'date', 'after_or_equal:starts_on'],
        ];
    }
}
