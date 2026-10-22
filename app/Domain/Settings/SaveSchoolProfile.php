<?php

namespace App\Domain\Settings;

use App\Models\SchoolProfile;

/** Saves the school's contact details (empty fields are cleared) and writes the change to the audit log. */
final class SaveSchoolProfile
{
    public function __construct(private readonly RecordAudit $audit) {}

    /**
     * @param  array{phone?: string|null, email?: string|null, address?: string|null}  $data
     */
    public function handle(array $data): SchoolProfile
    {
        $profile = SchoolProfile::query()->first() ?? new SchoolProfile;

        $clean = fn (?string $value): ?string => ($value = trim((string) $value)) === '' ? null : $value;

        $profile->fill([
            'phone' => $clean($data['phone'] ?? null),
            'email' => $clean($data['email'] ?? null),
            'address' => $clean($data['address'] ?? null),
        ])->save();

        $this->audit->handle('settings.profile_updated', $profile);

        return $profile;
    }
}
