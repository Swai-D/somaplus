<?php

namespace App\Http\Requests\Fees;

use App\Models\Payment;

class ReversePaymentRequest extends FeesRequest
{
    public function authorize(): bool
    {
        $payment = $this->route('payment');

        return $payment instanceof Payment && $this->user()?->can('reverse', $payment);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return ['reason' => ['required', 'string', 'min:3', 'max:250']];
    }
}
