<?php

namespace App\Domain\Fees;

use App\Domain\Attendance\ClassRoll;
use App\Domain\Settings\RecordAudit;
use App\Models\Enrollment;
use App\Models\FeeStructure;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\Stream;
use App\Support\Money;
use App\Tenancy\TenantContext;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

/**
 * Makes the invoices of a fee structure for the students who are in its class now (optionally one stream).
 * Safe to run again and again: a student who already has an invoice of this structure is skipped, so a
 * resend, a double click or a second run never makes a second invoice. Lines and total are copied from
 * the structure at that moment. Numbers are given in the same transaction, so there are no gaps.
 */
final class GenerateInvoices
{
    public function __construct(
        private readonly ClassRoll $roll,
        private readonly NextNumber $numbers,
        private readonly RecordAudit $audit,
        private readonly TenantContext $tenant,
    ) {}

    /**
     * @return array{created: int, existing: int}
     */
    public function handle(FeeStructure $structure, ?Stream $stream = null): array
    {
        return DB::transaction(function () use ($structure, $stream): array {
            // One generation of a structure at a time, so two runs cannot both number the same student.
            $structure = FeeStructure::query()->whereKey($structure->id)->lockForUpdate()->firstOrFail();
            $items = $structure->items()->get();
            $total = (int) $items->sum('amount');
            $currency = $this->tenant->current()->currency ?? Money::DEFAULT_CURRENCY;

            $year = $structure->academicYear()->firstOrFail();
            $class = $structure->schoolClass()->firstOrFail();
            $students = $this->roll->students($class, $stream, $year);
            $have = Invoice::query()->where('fee_structure_id', $structure->id)->pluck('student_id')->all();

            $created = 0;
            $existing = 0;
            foreach ($students as $student) {
                if (in_array($student->id, $have, true)) {
                    $existing++;

                    continue;
                }

                $placement = $this->placement($student->id, $year->id);

                $invoice = new Invoice;
                $invoice->forceFill([
                    'number' => $this->numbers->handle('invoice'),
                    'student_id' => $student->id,
                    'academic_year_id' => $structure->academic_year_id,
                    'term_id' => $structure->term_id,
                    'fee_structure_id' => $structure->id,
                    'school_class_id' => $structure->school_class_id,
                    'stream_id' => $placement,
                    'currency' => $currency,
                    'total' => $total,
                    'issued_on' => CarbonImmutable::today(),
                ])->save();

                foreach ($items as $item) {
                    $line = new InvoiceLine;
                    $line->forceFill(['invoice_id' => $invoice->id, 'name' => $item->name, 'amount' => $item->amount])->save();
                }
                $created++;
            }

            if ($created > 0) {
                $this->audit->handle('invoices.generated', $structure);
            }

            return ['created' => $created, 'existing' => $existing];
        });
    }

    /** The stream the student is in now for the year (their newest placement). */
    private function placement(int $studentId, int $yearId): ?int
    {
        $enrollment = Enrollment::query()->where('student_id', $studentId)->where('academic_year_id', $yearId)->orderByDesc('id')->first();

        return $enrollment?->stream_id;
    }
}
