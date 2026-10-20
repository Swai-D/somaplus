<?php

namespace App\Http\Requests\Fees;

use App\Models\FeeStructure;

class GenerateInvoicesRequest extends FeesRequest
{
    public function authorize(): bool
    {
        $structure = $this->route('structure');

        return $structure instanceof FeeStructure && $this->user()?->can('generate', $structure);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $structure = $this->route('structure');

        return [
            'stream_id' => ['nullable', 'integer', $this->inSchool('streams')->where('school_class_id', $structure instanceof FeeStructure ? $structure->school_class_id : 0)],
        ];
    }
}
