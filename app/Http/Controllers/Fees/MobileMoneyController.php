<?php

namespace App\Http\Controllers\Fees;

use App\Domain\Integrations\Payments\AzamPay\RequestMobileMoneyPayment;
use App\Http\Controllers\Controller;
use App\Http\Requests\Fees\MobileMoneyRequest;
use App\Models\Invoice;
use App\Support\Money;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class MobileMoneyController extends Controller
{
    public function store(MobileMoneyRequest $request, Invoice $invoice, RequestMobileMoneyPayment $send): RedirectResponse
    {
        $amount = Money::parse((string) $request->validated('amount'), 'TZS');
        if ($amount === null) {
            throw ValidationException::withMessages(['amount' => __('fees.payments.errors.amount')]);
        }

        $result = $send->handle($invoice, [
            'idempotency_key' => (string) $request->validated('idempotency_key'),
            'provider' => (string) $request->validated('provider'),
            'phone' => (string) $request->validated('phone'),
            'amount' => $amount,
        ], $request->user());

        $back = redirect()->route('fees.invoices.show', $invoice);

        return $result->status === 'failed'
            ? $back->withErrors(['provider' => __('integrations.azampay.not_sent')])->withInput()
            : $back->with('status', __('integrations.azampay.sent'));
    }
}
