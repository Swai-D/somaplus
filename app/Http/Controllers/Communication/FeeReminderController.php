<?php

namespace App\Http\Controllers\Communication;

use App\Domain\Communication\PhoneNumber;
use App\Domain\Communication\QueueFeeReminders;
use App\Domain\Communication\Sms\SchoolSms;
use App\Domain\Fees\InvoiceBalance;
use App\Http\Controllers\Controller;
use App\Http\Requests\Communication\FeeReminderRequest;
use App\Http\Requests\Fees\InvoiceFilterRequest;
use App\Models\Invoice;
use App\Models\SchoolClass;
use App\Models\SmsMessage;
use App\Models\Term;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class FeeReminderController extends Controller
{
    private const LIST_LIMIT = 200;

    public function index(InvoiceFilterRequest $request, SchoolSms $sms): View
    {
        Gate::authorize('send', SmsMessage::class);

        $termId = $request->filled('term_id') ? $request->integer('term_id') : null;
        $classId = $request->filled('school_class_id') ? $request->integer('school_class_id') : null;

        $rows = Invoice::query()->with(['student.guardians', 'term', 'schoolClass'])
            ->withSum(['payments as paid_sum' => fn ($q) => $q->whereDoesntHave('reversal')], 'amount')
            ->when($termId !== null, fn ($q) => $q->where('term_id', $termId))
            ->when($classId !== null, fn ($q) => $q->where('school_class_id', $classId))
            ->orderBy('number')->get()
            ->filter(fn (Invoice $invoice): bool => InvoiceBalance::of($invoice)->balance() > 0);

        $shown = $rows->take(self::LIST_LIMIT)->values();
        $last = SmsMessage::query()->whereIn('invoice_id', $shown->pluck('id'))->orderBy('id')->get()->groupBy('invoice_id')->map->last();

        return view('communication.reminders', [
            'rows' => $shown->map(fn (Invoice $invoice): array => [
                'invoice' => $invoice,
                'balance' => InvoiceBalance::of($invoice)->balanceMoney(),
                'reachable' => $invoice->student->guardians->filter(fn ($guardian): bool => PhoneNumber::normalize($guardian->phone) !== null)->count(),
                'last' => $last->get($invoice->id),
            ]),
            'truncated' => $rows->count() > self::LIST_LIMIT,
            'smsActive' => $sms->isActive(),
            'termId' => $termId,
            'classId' => $classId,
            'terms' => Term::query()->with('academicYear')->orderByDesc('starts_on')->get(),
            'classes' => SchoolClass::query()->orderBy('sort_order')->orderBy('name')->get(),
            'history' => SmsMessage::query()->with('invoice')->latest('id')->limit(20)->get(),
        ]);
    }

    public function store(FeeReminderRequest $request, QueueFeeReminders $queue): RedirectResponse
    {
        /** @var list<int> $ids */
        $ids = array_map('intval', (array) $request->validated('invoices'));
        $result = $queue->handle($ids, (string) $request->validated('locale'), $request->user());

        return redirect()->route('communication.reminders.index')->with('status', __('communication.queued', $result));
    }
}
