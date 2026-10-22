<?php

namespace Database\Factories;

use App\Models\BookIssue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BookIssue>
 */
class BookIssueFactory extends Factory
{
    public function definition(): array
    {
        return [
            'issued_on' => fake()->dateTimeBetween('-3 months', '-1 week')->format('Y-m-d'),
            'returned_on' => null,
        ];
    }

    /** A book that came back. */
    public function returned(): static
    {
        return $this->state(fn (array $attributes) => [
            'returned_on' => fake()->dateTimeBetween('-3 days', 'now')->format('Y-m-d'),
        ]);
    }
}
