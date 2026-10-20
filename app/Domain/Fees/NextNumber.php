<?php

namespace App\Domain\Fees;

use App\Models\DocumentSequence;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

/**
 * Gives out the next number of a kind of document (for example invoices) in the current school, without gaps.
 * It must be called inside the transaction that saves the document: the sequence row is locked until that
 * transaction ends, and if the transaction rolls back the number goes back too.
 */
final class NextNumber
{
    public function handle(string $type): int
    {
        return DB::transaction(function () use ($type): int {
            $sequence = DocumentSequence::query()->where('type', $type)->lockForUpdate()->first();

            if ($sequence === null) {
                try {
                    // Savepoint: a concurrent first use makes this insert fail without breaking the outer transaction.
                    DB::transaction(fn () => DocumentSequence::query()->create(['type' => $type, 'last_number' => 0]));
                } catch (UniqueConstraintViolationException) {
                    // Another request created it first; use that row.
                }
                $sequence = DocumentSequence::query()->where('type', $type)->lockForUpdate()->firstOrFail();
            }

            $next = $sequence->last_number + 1;
            $sequence->last_number = $next;
            $sequence->save();

            return $next;
        });
    }
}
