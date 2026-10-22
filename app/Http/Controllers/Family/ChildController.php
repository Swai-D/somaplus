<?php

namespace App\Http\Controllers\Family;

use App\Domain\Exams\ReportCard;
use App\Domain\Family\FamilyChildren;
use App\Domain\Fees\InvoiceBalance;
use App\Domain\Settings\RecordAudit;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** A parent's view of their own children: fees (read only) and report cards of locked exams. */
class ChildController extends Controller
{
    public function index(Request $request, FamilyChildren $children): View
    {
        $rows = $children->for($request->user())->map(function (Student $student): array {
            $owed = Invoice::query()->where('student_id', $student->id)
                ->withSum(['payments as paid_sum' => fn ($q) => $q->whereDoesntHave('reversal')], 'amount')->get()
                ->map(fn (Invoice $invoice): InvoiceBalance => InvoiceBalance::fromLoaded($invoice))
                ->filter(fn (InvoiceBalance $balance): bool => $balance->balance() > 0)
                ->groupBy('currency')->map(fn ($group) => $group->sum(fn (InvoiceBalance $b): int => $b->balance()));

            return ['student' => $student, 'owed' => $owed];
        });

        return view('family.index', ['rows' => $rows]);
    }

    public function show(Request $request, int $student, FamilyChildren $children, RecordAudit $audit): View
    {
        $child = $children->find($request->user(), $student);
        abort_if($child === null, 404);

        $invoices = Invoice::query()->where('student_id', $child->id)->with('term.academicYear')
            ->withSum(['payments as paid_sum' => fn ($q) => $q->whereDoesntHave('reversal')], 'amount')
            ->orderByDesc('id')->get();
        $payments = Payment::query()->whereIn('invoice_id', $invoices->pluck('id'))->with(['receipt', 'reversal'])->orderByDesc('id')->limit(50)->get();

        $audit->handle('family.child_viewed', $child);

        return view('family.show', [
            'student' => $child,
            'invoices' => $invoices->map(fn (Invoice $invoice): array => ['invoice' => $invoice, 'balance' => InvoiceBalance::fromLoaded($invoice)]),
            'payments' => $payments,
            'terms' => Term::query()->with('academicYear')->orderByDesc('starts_on')->get(),
        ]);
    }

    public function report(Request $request, int $student, Term $term, FamilyChildren $children, ReportCard $card, RecordAudit $audit): View
    {
        $child = $children->find($request->user(), $student);
        abort_if($child === null, 404);

        $exams = $card->handle($child, $term);
        $audit->handle('report.viewed', $child);

        return view('family.report', ['student' => $child, 'term' => $term->load('academicYear'), 'exams' => $exams]);
    }
}
