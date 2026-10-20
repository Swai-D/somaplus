<?php

namespace App\Http\Requests\Fees;

use App\Models\Payment;
use Illuminate\Validation\Rule;

/**
 * A payment typed in by a person, for an invoice chosen in the address or found by its control number.
 */
class PaymentRequest extends FeesRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('create', Payment::class);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'control_number' => [$this->route('invoice') === null ? 'required' : 'nullable', 'string', 'max:30'],
            'amount' => ['required', 'string', 'max:14'],
            'method' => ['required', 'string', Rule::in(Payment::METHODS)],
            'paid_on' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'reference' => ['nullable', 'string', 'max:100'],
            'idempotency_key' => ['required', 'string', 'max:100'],
        ];
    }
}
