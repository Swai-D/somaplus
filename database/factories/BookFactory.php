<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(fake()->numberBetween(2, 4), true),
            'publisher' => fake()->company(),
            'author' => fake()->name(),
            'book_number' => strtoupper(fake()->unique()->bothify('BK-####')),
            'rack_number' => fake()->randomElement(['A1', 'A2', 'B1', 'B2', 'C3']),
            'quantity' => fake()->numberBetween(1, 10),
            'price' => fake()->numberBetween(3000, 25000),
        ];
    }
}
