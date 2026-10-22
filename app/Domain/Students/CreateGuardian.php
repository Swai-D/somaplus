<?php

namespace App\Domain\Students;

use App\Domain\Settings\RecordAudit;
use App\Models\Guardian;
use Illuminate\Support\Facades\DB;

/**
 * Adds a guardian to the school on their own, without a pupil yet. A school has one guardian
 * record per phone number, so a phone that is already in the school gives back the guardian that
 * is already there (the caller tells the person about it) instead of a second record.
 *
 * The personal data of a guardian is only what the school needs: a name, a phone number and an
 * optional email address.
 */
final class CreateGuardian
{
    public function __construct(private readonly RecordAudit $audit) {}

    /**
     * @param  array{name: string, phone: string, email?: string|null}  $data
     */
    public function handle(array $data): Guardian
    {
        return DB::transaction(function () use ($data): Guardian {
            $phone = AddGuardian::normalizePhone($data['phone']);
            $email = trim((string) ($data['email'] ?? ''));

            $guardian = Guardian::query()->firstOrCreate(
                ['phone' => $phone],
                ['name' => $data['name'], 'email' => $email === '' ? null : $email],
            );

            if ($guardian->wasRecentlyCreated) {
                $this->audit->handle('guardian.created', $guardian);
            }

            return $guardian;
        });
    }
}
