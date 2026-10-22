<?php

namespace App\Domain\Library;

use App\Domain\Settings\RecordAudit;
use App\Models\LibraryMember;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Takes a member out of the library. A member the library already lent a book to cannot
 * simply vanish, because every issue is a record about this member and it must keep them,
 * so only a member the school never lent anything to goes away with their card.
 */
final class RemoveLibraryMember
{
    public function __construct(private readonly RecordAudit $audit) {}

    public function handle(LibraryMember $member): void
    {
        if ($member->issues()->exists()) {
            throw ValidationException::withMessages([
                'delete' => __('library.members.still_has_issues'),
            ]);
        }

        DB::transaction(function () use ($member): void {
            $this->audit->handle('member.deleted', $member);

            $member->delete();
        });
    }
}
