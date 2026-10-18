<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Term;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Term>
 */
class TermFactory extends Factory
{
    public function definition(): array
    {
        return [
            'academic_year_id' => AcademicYear::factory(),
            'name' => 'Term '.fake()->unique()->numberBetween(1, 1000),
            'starts_on' => '2030-01-01',
            'ends_on' => '2030-03-31',
        ];
    }
}
