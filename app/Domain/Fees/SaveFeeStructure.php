<?php

namespace App\Domain\Fees;

use App\Domain\Settings\RecordAudit;
use App\Models\FeeItem;
use App\Models\FeeStructure;
use App\Models\SchoolClass;
use App\Models\Term;
use App\Support\Money;
use App\Tenancy\TenantContext;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Creates or replaces a fee structure (the fees of one class in one term) and its items.
 * Amounts are read into integer minor units of the school's currency. A structure that an invoice
 * was made from is never changed, so invoices always agree with the structure they came from.
 */
final class SaveFeeStructure
{
    public function __construct(private readonly RecordAudit $audit, private readonly TenantContext $tenant) {}

    /**
     * @param  array{name: string, term_id: int|string, school_class_id: int|string, items: array<int|string, array{name?: mixed, amount?: mixed}>}  $data
     */
    public function handle(array $data, ?FeeStructure $structure = null): FeeStructure
    {
        $items = $this->normalise($data['items']);

        return DB::transaction(function () use ($data, $structure, $items): FeeStructure {
            $term = Term::query()->findOrFail((int) $data['term_id']);
            $class = SchoolClass::query()->findOrFail((int) $data['school_class_id']);

            if ($structure !== null) {
                // Locks the structure while invoices might be generated from it at the same moment.
                $structure = FeeStructure::query()->whereKey($structure->id)->lockForUpdate()->firstOrFail();

                if ($structure->isUsed()) {
                    throw ValidationException::withMessages(['name' => __('fees.structures.used')]);
                }

                $structure->items()->delete();
            }

            $creating = $structure === null;
            $structure ??= new FeeStructure;
            $structure->fill(['name' => trim($data['name'])]);
            $structure->term_id = $term->id;
            $structure->academic_year_id = $term->academic_year_id;
            $structure->school_class_id = $class->id;
            $structure->save();

            foreach ($items as $row) {
                $item = new FeeItem(['name' => $row['name'], 'amount' => $row['amount']]);
                $item->fee_structure_id = $structure->id;
                $item->save();
            }

            $this->audit->handle($creating ? 'fee_structure.created' : 'fee_structure.updated', $structure);

            return $structure;
        });
    }

    /**
     * @param  array<int|string, array{name?: mixed, amount?: mixed}>  $items
     * @return list<array{name: string, amount: int}>
     */
    private function normalise(array $items): array
    {
        $currency = $this->tenant->current()->currency ?? Money::DEFAULT_CURRENCY;
        $rows = [];
        $names = [];
        $total = 0;

        foreach ($items as $item) {
            $name = trim((string) ($item['name'] ?? ''));
            $amount = Money::parse((string) ($item['amount'] ?? ''), $currency);

            if ($name === '' || mb_strlen($name) > 100) {
                $this->fail('fees.structures.errors.name');
            }
            if ($amount === null || $amount <= 0) {
                $this->fail('fees.structures.errors.amount');
            }
            if (in_array(mb_strtolower($name), $names, true)) {
                $this->fail('fees.structures.errors.duplicate');
            }

            $names[] = mb_strtolower($name);
            $total += $amount;
            $rows[] = ['name' => $name, 'amount' => $amount];
        }

        if ($rows === []) {
            $this->fail('fees.structures.errors.empty');
        }
        if ($total > 9_999_999_999_999) {
            $this->fail('fees.structures.errors.amount');
        }

        return $rows;
    }

    private function fail(string $key): never
    {
        throw ValidationException::withMessages(['items' => __($key)]);
    }
}
