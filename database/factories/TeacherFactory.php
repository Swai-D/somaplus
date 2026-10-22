<?php

namespace Database\Factories;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Teacher>
 */
class TeacherFactory extends Factory
{
    public function definition(): array
    {
        return [
            'staff_number' => strtoupper(fake()->unique()->bothify('TCH-####')),
            'phone' => '+2557'.fake()->unique()->numerify('########'),
            'gender' => fake()->randomElement(['male', 'female']),
            'date_of_birth' => fake()->dateTimeBetween('-50 years', '-25 years')->format('Y-m-d'),
            'address' => fake()->address(),
            'qualification' => fake()->randomElement(['BSc', 'BA Education', 'BEd', 'MEd', 'PhD']),
            'contract_type' => fake()->randomElement(['permanent', 'contractual', 'part_time']),
            'shift' => fake()->randomElement(['day', 'evening', 'night']),
            'joined_on' => fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            'status' => Teacher::ACTIVE,
            'notes' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Teacher::INACTIVE,
        ]);
    }
}
