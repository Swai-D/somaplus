<?php

namespace Database\Factories;

use App\Models\LibraryMember;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LibraryMember>
 */
class LibraryMemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'card_number' => strtoupper(fake()->unique()->bothify('LIB-####')),
            'joined_on' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
        ];
    }
}
