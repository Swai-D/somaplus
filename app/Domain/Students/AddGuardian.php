<?php

namespace App\Domain\Students;

use App\Domain\Settings\RecordAudit;
use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

/**
 * Links a guardian to a student. A school has one guardian record per phone
 * number, so a second child of the same parent reuses the record (and its
 * existing name and email).
 */
final class AddGuardian
{
    public function __construct(private readonly RecordAudit $audit) {}

    /**
     * @param  array{name: string, phone: string, email?: string|null, relationship: string, is_primary?: bool}  $data
     */
    public function handle(Student $student, array $data): Guardian
    {
        return DB::transaction(function () use ($student, $data): Guardian {
            $phone = self::normalizePhone($data['phone']);
            $email = trim((string) ($data['email'] ?? ''));

            $guardian = Guardian::query()->firstOrCreate(
                ['phone' => $phone],
                ['name' => $data['name'], 'email' => $email === '' ? null : $email],
            );

            $primary = (bool) ($data['is_primary'] ?? false);
            if ($primary) {
                $student->guardians()->newPivotStatement()
                    ->where('student_id', $student->id)
                    ->update(['is_primary' => false]);
            }

            $student->guardians()->syncWithoutDetaching([
                $guardian->id => ['relationship' => $data['relationship'], 'is_primary' => $primary],
            ]);

            $this->audit->handle('guardian.linked', $student);

            return $guardian;
        });
    }

    public static function normalizePhone(string $phone): string
    {
        return (string) preg_replace('/[^0-9+]/', '', $phone);
    }
}
