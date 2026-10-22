<?php

namespace App\Domain\Library;

use App\Domain\Settings\RecordAudit;
use App\Models\BookIssue;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Takes a copy of a book back into the library, on the day the school says it came.
 * A copy that already came back cannot come back twice.
 */
final class ReturnBook
{
    public function __construct(private readonly RecordAudit $audit) {}

    public function handle(BookIssue $issue, string $returnedOn): BookIssue
    {
        return DB::transaction(function () use ($issue, $returnedOn): BookIssue {
            if ($issue->isReturned()) {
                throw ValidationException::withMessages([
                    'returned_on' => __('library.issues.already_returned'),
                ]);
            }

            $issue->returned_on = CarbonImmutable::parse($returnedOn);
            $issue->save();

            $this->audit->handle('issue.returned', $issue);

            return $issue;
        });
    }
}
