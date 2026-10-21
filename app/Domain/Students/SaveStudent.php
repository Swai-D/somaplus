<?php

namespace App\Domain\Students;

use App\Domain\Settings\RecordAudit;
use App\Models\Student;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

final class SaveStudent
{
    public function __construct(private readonly EnrollStudent $enroll, private readonly RecordAudit $audit, private readonly AdmissionNumbers $numbers) {}

    /**
     * @param  array<string, mixed>  $data  student fields, plus optional academic_year_id, school_class_id, stream_id. Without an admission_number a new student gets the next one in the school's format; a student being changed keeps the one it has.
     */
    public function handle(array $data, ?Student $student = null): Student
    {
        return DB::transaction(function () use ($data, $student): Student {
            $creating = $student === null;
            $student ??= new Student;

            $given = trim((string) ($data['admission_number'] ?? ''));
            $number = $given !== '' ? $given : ($creating ? $this->numbers->generate(CarbonImmutable::parse((string) $data['admitted_on'])) : $student->admission_number);

            $middle = trim((string) ($data['middle_name'] ?? ''));
            $student->fill([
                'admission_number' => $number,
                'first_name' => $data['first_name'],
                'middle_name' => $middle === '' ? null : $middle,
                'last_name' => $data['last_name'],
                'gender' => $data['gender'],
                'date_of_birth' => $data['date_of_birth'],
                'admitted_on' => $data['admitted_on'],
            ])->save();

            if ($creating && ! empty($data['school_class_id']) && ! empty($data['academic_year_id'])) {
                $this->enroll->handle($student, [
                    'academic_year_id' => $data['academic_year_id'],
                    'school_class_id' => $data['school_class_id'],
                    'stream_id' => $data['stream_id'] ?? null,
                    'enrolled_on' => $data['admitted_on'],
                ]);
            }

            $this->audit->handle($creating ? 'student.created' : 'student.updated', $student);

            return $student;
        });
    }
}
