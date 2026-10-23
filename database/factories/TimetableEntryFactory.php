<?php

namespace Database\Factories;

use App\Models\TimetableEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * The ids (term, class, subject, period) are always given by the test.
 *
 * @extends Factory<TimetableEntry>
 */
class TimetableEntryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'weekday' => 1,
            'stream_id' => null,
            'teacher_id' => null,
            'room' => null,
        ];
    }
}
