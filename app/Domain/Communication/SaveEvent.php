<?php

namespace App\Domain\Communication;

use App\Domain\Settings\RecordAudit;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

/** Adds an event to the school's calendar or changes one that is already on it. */
final class SaveEvent
{
    public function __construct(private readonly RecordAudit $audit) {}

    /**
     * @param  array{starts_at: string, ends_at?: string|null, title: string, description?: string|null}  $data
     */
    public function handle(?Event $event, array $data): Event
    {
        return DB::transaction(function () use ($event, $data): Event {
            $created = $event === null;

            $event ??= new Event;
            $event->fill([
                'starts_at' => $data['starts_at'],
                'ends_at' => $data['ends_at'] ?? null,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
            ]);
            $event->save();

            $this->audit->handle($created ? 'event.created' : 'event.updated', $event);

            return $event;
        });
    }
}
