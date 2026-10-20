<?php

namespace Database\Factories;

use App\Models\Exam;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Tests pass academic_year_id, term_id and grading_scale_id.
 *
 * @extends Factory<Exam>
 */
class ExamFactory extends Factory
{
    public function definition(): array
    {
        return ['name' => fake()->unique()->bothify('Exam ####'), 'max_mark' => 100];
    }
}
