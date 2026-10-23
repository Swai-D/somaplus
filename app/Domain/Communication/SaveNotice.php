<?php

namespace App\Domain\Communication;

use App\Domain\Settings\RecordAudit;
use App\Models\Notice;
use Illuminate\Support\Facades\DB;

/** Adds a notice to the board or changes one that is already on it. */
final class SaveNotice
{
    public function __construct(private readonly RecordAudit $audit) {}

    /**
     * @param  array{day: string, title: string, description?: string|null}  $data
     */
    public function handle(?Notice $notice, array $data): Notice
    {
        return DB::transaction(function () use ($notice, $data): Notice {
            $created = $notice === null;

            $notice ??= new Notice;
            $notice->fill([
                'day' => $data['day'],
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
            ]);
            $notice->save();

            $this->audit->handle($created ? 'notice.created' : 'notice.updated', $notice);

            return $notice;
        });
    }
}
