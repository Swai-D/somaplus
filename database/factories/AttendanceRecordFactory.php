<?php

namespace Database\Factories;

use App\Models\AttendanceRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Tests pass student_id, academic_year_id and school_class_id.
 *
 * @extends Factory<AttendanceRecord>
 */
class AttendanceRecordFactory extends Factory
{
    public function definition(): array
    {
        return ['attended_on' => now()->toDateString(), 'status' => AttendanceRecord::PRESENT];
    }
}
