<?php

namespace Database\Factories;

use App\Models\FeeStructure;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Tests pass academic_year_id, term_id and school_class_id.
 *
 * @extends Factory<FeeStructure>
 */
class FeeStructureFactory extends Factory
{
    public function definition(): array
    {
        return ['name' => fake()->unique()->bothify('Fees ####')];
    }
}
