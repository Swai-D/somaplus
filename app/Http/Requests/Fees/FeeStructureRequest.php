<?php

namespace App\Http\Requests\Fees;

use App\Models\FeeStructure;

class FeeStructureRequest extends FeesRequest
{
    public function authorize(): bool
    {
        $structure = $this->route('structure');

        return $structure instanceof FeeStructure
            ? (bool) $this->user()?->can('update', $structure)
            : (bool) $this->user()?->can('create', FeeStructure::class);
    }

    /** Rows left completely empty (the spare lines of the form) are ignored. */
    protected function prepareForValidation(): void
    {
        $items = array_filter((array) $this->input('items', []), function ($item): bool {
            return is_array($item) && array_filter($item, fn ($value): bool => trim((string) $value) !== '') !== [];
        });

        $this->merge(['items' => array_values($items)]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $structure = $this->route('structure');

        return [
            'name' => ['required', 'string', 'max:100', $this->unique('fee_structures', 'name', $structure instanceof FeeStructure ? $structure->id : null)
                ->where('term_id', $this->input('term_id'))->where('school_class_id', $this->input('school_class_id'))],
            'term_id' => ['required', 'integer', $this->inSchool('terms')],
            'school_class_id' => ['required', 'integer', $this->inSchool('school_classes')],
            'items' => ['required', 'array', 'min:1', 'max:20'],
            'items.*.name' => ['required', 'string', 'max:100'],
            'items.*.amount' => ['required', 'string', 'max:14'],
        ];
    }
}
