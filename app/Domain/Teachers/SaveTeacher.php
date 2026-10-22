<?php

namespace App\Domain\Teachers;

use App\Domain\Settings\RecordAudit;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Adds a teacher to the school or changes what the school knows about them. A teacher is
 * a person who signs in, so the account is written here too: the name, the address and,
 * for a new teacher, the password. The account always holds the teacher role.
 *
 * The subjects and the classes of the teacher are the ones the school chose, so they are
 * replaced with what was sent. Only the role id of the change is written to the audit
 * log, never the password.
 */
final class SaveTeacher
{
    public function __construct(private readonly RecordAudit $audit) {}

    /**
     * @param  array{name: string, email: string, password?: string|null, phone: string, staff_number?: string|null, gender?: string|null, date_of_birth?: string|null, address?: string|null, qualification?: string|null, contract_type?: string|null, shift?: string|null, joined_on: string, notes?: string|null, subjects?: array<int>, classes?: array<int>}  $data
     */
    public function handle(?Teacher $teacher, array $data): Teacher
    {
        return DB::transaction(function () use ($teacher, $data): Teacher {
            $created = $teacher === null;

            $user = $teacher !== null ? $teacher->user : new User;
            $user->fill(['name' => $data['name'], 'email' => $data['email']]);

            if (($data['password'] ?? '') !== '') {
                $user->password = $data['password'];
            }

            if ($created) {
                $user->forceFill(['email_verified_at' => now()]);
            }

            $user->save();
            $user->syncRoles(['teacher']);

            $teacher ??= new Teacher;
            $teacher->fill([
                'user_id' => $user->getKey(),
                'staff_number' => $data['staff_number'] ?? null,
                'phone' => $data['phone'],
                'gender' => $data['gender'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'address' => $data['address'] ?? null,
                'qualification' => $data['qualification'] ?? null,
                'contract_type' => $data['contract_type'] ?? null,
                'shift' => $data['shift'] ?? null,
                'joined_on' => $data['joined_on'],
                'notes' => $data['notes'] ?? null,
            ]);

            if ($created) {
                $teacher->status = Teacher::ACTIVE;
            }

            $teacher->save();

            $teacher->subjects()->sync($data['subjects'] ?? []);
            $teacher->classes()->sync($data['classes'] ?? []);

            $this->audit->handle($created ? 'teacher.created' : 'teacher.updated', $teacher);

            return $teacher;
        });
    }
}
