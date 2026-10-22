<?php

namespace App\Http\Controllers\Fees;

use App\Domain\Fees\RecordPayment;
use App\Domain\Fees\ReversePayment;
use App\Http\Controllers\Controller;
use App\Http\Requests\Fees\PaymentRequest;
use App\Http\Requests\Fees\ReversePaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Support\Money;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Payment::class);

        return view('fees.payments.index', [
            'payments' => Payment::query()->with(['invoice.student', 'receipt', 'reversal'])->orderByDesc('id')->limit(200)->get(),
            'paymentKey' => (string) Str::uuid(),
        ]);
    }

    /**
     * The form of a payment is a pop-up of the list, as in the template, so this address only
     * opens the list with the pop-up ready.
     */
    public function create(): RedirectResponse
    {
        Gate::authorize('create', Payment::class);

        return redirect()->route('fees.payments.index', ['add' => 'payment-modal']);
    }

    /** Record a payment for the invoice that owns the typed control number. */
    public function store(PaymentRequest $request, RecordPayment $record): RedirectResponse
    {
        $number = trim((string) $request->validated('control_number'));
        $invoice = $number === '' ? null : Invoice::query()->where('control_number', $number)->first();

        if ($invoice === null) {
            throw ValidationException::withMessages(['control_number' => __('fees.payments.errors.unknown_control')]);
        }

        return $this->record($request, $invoice, $record);
    }

    public function storeForInvoice(PaymentRequest $request, Invoice $invoice, RecordPayment $record): RedirectResponse
    {
        return $this->record($request, $invoice, $record);
    }

    public function reverse(ReversePaymentRequest $request, Payment $payment, ReversePayment $reverse): RedirectResponse
    {
        $reverse->handle($payment, (string) $request->validated('reason'));

        return redirect()->route('fees.invoices.show', $payment->invoice_id)->with('status', __('fees.payments.reversed'));
    }

    private function record(PaymentRequest $request, Invoice $invoice, RecordPayment $record): RedirectResponse
    {
        $amount = Money::parse((string) $request->validated('amount'), $invoice->currency);
        if ($amount === null) {
            throw ValidationException::withMessages(['amount' => __('fees.payments.errors.amount')]);
        }

        $result = $record->handle($invoice, [
            'amount' => $amount,
            'method' => (string) $request->validated('method'),
            'paid_on' => (string) $request->validated('paid_on'),
            'reference' => $request->validated('reference'),
            'idempotency_key' => (string) $request->validated('idempotency_key'),
        ]);

        return redirect()->route('fees.receipts.show', $result['payment']->receipt()->firstOrFail())
            ->with('status', __($result['created'] ? 'fees.payments.recorded' : 'fees.payments.already'));
    }
}
