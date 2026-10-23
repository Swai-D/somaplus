<?php

namespace App\Domain\Communication;

use App\Domain\Settings\RecordAudit;
use App\Models\Notice;
use Illuminate\Support\Facades\DB;

/** Takes a notice off the board. */
final class RemoveNotice
{
    public function __construct(private readonly RecordAudit $audit) {}

    public function handle(Notice $notice): void
    {
        DB::transaction(function () use ($notice): void {
            $this->audit->handle('notice.deleted', $notice);
            $notice->delete();
        });
    }
}
