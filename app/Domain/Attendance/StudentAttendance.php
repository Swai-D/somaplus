<?php

namespace App\Domain\Attendance;

use App\Models\AttendanceRecord;
use App\Models\Student;

/**
 * One student's counts of each attendance status over a period.
 */
final class StudentAttendance
{
    public int $present = 0;

    public int $absent = 0;

    public int $late = 0;

    public int $excused = 0;

    public function __construct(public readonly Student $student) {}

    public function add(string $status): void
    {
        match ($status) {
            AttendanceRecord::PRESENT => $this->present++,
            AttendanceRecord::ABSENT => $this->absent++,
            AttendanceRecord::LATE => $this->late++,
            default => $this->excused++,
        };
    }

    /**
     * Present and late days out of present, late and absent days, as a whole percent; excused days are left out.
     */
    public function rate(): ?int
    {
        $counted = $this->present + $this->late + $this->absent;

        return $counted === 0 ? null : (int) round(100 * ($this->present + $this->late) / $counted);
    }
}
