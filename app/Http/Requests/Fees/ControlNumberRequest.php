<?php

namespace App\Http\Requests\Fees;

use App\Models\Invoice;

class ControlNumberRequest extends FeesRequest
{
    public function authorize(): bool
    {
        $invoice = $this->route('invoice');

        return $invoice instanceof Invoice && $this->user()?->can('update', $invoice);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return ['control_number' => ['nullable', 'string', 'max:30']];
    }
}
