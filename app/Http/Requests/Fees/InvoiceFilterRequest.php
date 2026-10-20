<?php

namespace App\Http\Requests\Fees;

/**
 * The term and class chosen on the invoice list (query string; no personal data).
 */
class InvoiceFilterRequest extends FeesRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('fees.view');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'term_id' => ['nullable', 'integer', $this->inSchool('terms')],
            'school_class_id' => ['nullable', 'integer', $this->inSchool('school_classes')],
        ];
    }
}
