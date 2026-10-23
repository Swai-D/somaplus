<?php

namespace App\Domain\Communication;

use App\Domain\Settings\RecordAudit;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

/** Takes an event off the school's calendar. */
final class RemoveEvent
{
    public function __construct(private readonly RecordAudit $audit) {}

    public function handle(Event $event): void
    {
        DB::transaction(function () use ($event): void {
            $this->audit->handle('event.deleted', $event);
            $event->delete();
        });
    }
}
