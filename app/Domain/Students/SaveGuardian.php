<?php

namespace App\Domain\Students;

use App\Domain\Settings\RecordAudit;
use App\Models\Guardian;
use Illuminate\Support\Facades\DB;

/**
 * Changes what a school knows about a guardian: the name, the phone number and the optional
 * email address. The pupils of the guardian are not touched; only the details of the person.
 */
final class SaveGuardian
{
    public function __construct(private readonly RecordAudit $audit) {}

    /**
     * @param  array{name: string, phone: string, email?: string|null}  $data
     */
    public function handle(Guardian $guardian, array $data): Guardian
    {
        return DB::transaction(function () use ($guardian, $data): Guardian {
            $email = trim((string) ($data['email'] ?? ''));

            $guardian->fill([
                'name' => $data['name'],
                'phone' => AddGuardian::normalizePhone($data['phone']),
                'email' => $email === '' ? null : $email,
            ])->save();

            $this->audit->handle('guardian.updated', $guardian);

            return $guardian;
        });
    }
}
