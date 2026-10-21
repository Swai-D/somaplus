<?php

namespace App\Http\Requests\Fees;

use App\Models\Payment;
use Illuminate\Validation\Rule;

/** A mobile-money prompt for an invoice: who pays (phone and network), how much, and a key against double sending. */
class MobileMoneyRequest extends FeesRequest
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
            'phone' => ['required', 'string', 'max:20'],
            'provider' => ['required', 'string', Rule::in((array) config('azampay.providers'))],
            'amount' => ['required', 'string', 'max:14'],
            'idempotency_key' => ['required', 'string', 'max:100'],
        ];
    }
}
