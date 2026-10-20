<?php

namespace App\Domain\Fees;

use App\Domain\Settings\RecordAudit;
use App\Models\FeeStructure;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Deletes a fee structure that no invoice was made from. Invoices are never silently removed.
 */
final class DeleteFeeStructure
{
    public function __construct(private readonly RecordAudit $audit) {}

    public function handle(FeeStructure $structure): void
    {
        DB::transaction(function () use ($structure): void {
            $fresh = FeeStructure::query()->whereKey($structure->id)->lockForUpdate()->firstOrFail();

            if ($fresh->isUsed()) {
                throw ValidationException::withMessages(['delete' => __('fees.structures.cannot_delete')]);
            }

            $this->audit->handle('fee_structure.deleted', $fresh);
            $fresh->items()->delete();
            $fresh->delete();
        });
    }
}
