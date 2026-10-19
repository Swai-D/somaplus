<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'admission_number' => fake()->unique()->bothify('ADM-#####'),
            'first_name' => fake()->firstName(),
            'middle_name' => null,
            'last_name' => fake()->lastName(),
            'gender' => fake()->randomElement(['male', 'female']),
            'date_of_birth' => '2012-03-04',
            'admitted_on' => '2024-01-10',
        ];
    }

    public function left(): static
    {
        return $this->state(fn () => ['status' => Student::LEFT, 'left_on' => '2025-06-01', 'left_reason' => 'Moved away']);
    }
}
