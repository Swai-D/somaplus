<?php

namespace App\Http\Requests\Exams;

use App\Models\GradingScale;

class GradingScaleRequest extends ExamsRequest
{
    public function authorize(): bool
    {
        $scale = $this->route('scale');

        return $scale instanceof GradingScale
            ? (bool) $this->user()?->can('update', $scale)
            : (bool) $this->user()?->can('create', GradingScale::class);
    }

    /** Rows left completely empty (the spare lines of the form) are ignored. */
    protected function prepareForValidation(): void
    {
        $bands = array_filter((array) $this->input('bands', []), function ($band): bool {
            return is_array($band) && array_filter($band, fn ($value): bool => trim((string) $value) !== '') !== [];
        });

        $this->merge(['bands' => array_values($bands)]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $scale = $this->route('scale');

        return [
            'name' => ['required', 'string', 'max:100', $this->unique('grading_scales', 'name', $scale instanceof GradingScale ? $scale->id : null)],
            'bands' => ['required', 'array', 'min:1', 'max:15'],
            'bands.*.min_mark' => ['required', 'string', 'max:8'],
            'bands.*.grade' => ['required', 'string', 'max:5'],
            'bands.*.points' => ['nullable', 'integer', 'between:0,20'],
            'bands.*.remark' => ['nullable', 'string', 'max:100'],
        ];
    }
}
