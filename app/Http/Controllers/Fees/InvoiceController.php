<?php

namespace App\Http\Controllers\Fees;

use App\Domain\Fees\InvoiceBalance;
use App\Domain\Fees\SetControlNumber;
use App\Domain\Integrations\Drivers\Payments\AzamPayDriver;
use App\Domain\Integrations\Services\IntegrationManager;
use App\Domain\Settings\RecordAudit;
use App\Http\Controllers\Controller;
use App\Http\Requests\Fees\ControlNumberRequest;
use App\Http\Requests\Fees\InvoiceFilterRequest;
use App\Models\Invoice;
use App\Models\PaymentRequest;
use App\Models\SchoolClass;
use App\Models\Term;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(InvoiceFilterRequest $request): View
    {
        $termId = $request->filled('term_id') ? $request->integer('term_id') : null;
        $classId = $request->filled('school_class_id') ? $request->integer('school_class_id') : null;

        return view('fees.invoices.index', [
            'invoices' => Invoice::query()->with(['student', 'term', 'schoolClass'])
                ->withSum(['payments as paid_sum' => fn ($q) => $q->whereDoesntHave('reversal')], 'amount')
                ->when($termId !== null, fn ($q) => $q->where('term_id', $termId))
                ->when($classId !== null, fn ($q) => $q->where('school_class_id', $classId))
                ->orderByDesc('number')->limit(200)->get(),
            'termId' => $termId,
            'classId' => $classId,
            'terms' => Term::query()->with('academicYear')->orderByDesc('starts_on')->get(),
            'classes' => SchoolClass::query()->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function show(Invoice $invoice, RecordAudit $audit): View
    {
        Gate::authorize('view', $invoice);

        $audit->handle('invoice.viewed', $invoice);

        return view('fees.invoices.show', [
            'invoice' => $invoice->load(['student', 'term.academicYear', 'schoolClass', 'lines', 'payments.receipt', 'payments.reversal']),
            'balance' => InvoiceBalance::of($invoice),
            'key' => (string) Str::uuid(),
            'mobileMoney' => app(IntegrationManager::class)->isActive('payments', AzamPayDriver::KEY),
            'requests' => PaymentRequest::query()->where('invoice_id', $invoice->id)->latest('id')->limit(10)->get(),
        ]);
    }

    public function controlNumber(ControlNumberRequest $request, Invoice $invoice, SetControlNumber $set): RedirectResponse
    {
        $set->handle($invoice, $request->validated('control_number'));

        return redirect()->route('fees.invoices.show', $invoice)->with('status', __('fees.saved'));
    }
}
