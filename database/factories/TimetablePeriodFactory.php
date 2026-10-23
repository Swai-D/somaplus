<?php

namespace Database\Factories;

use App\Models\TimetablePeriod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TimetablePeriod>
 */
class TimetablePeriodFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Period '.fake()->unique()->numberBetween(1, 9999),
            'starts_at' => '08:00:00',
            'ends_at' => '08:40:00',
            'is_break' => false,
        ];
    }

    public function between(string $start, string $end): static
    {
        return $this->state(fn () => ['starts_at' => $start.':00', 'ends_at' => $end.':00']);
    }

    public function break(): static
    {
        return $this->state(fn () => ['is_break' => true]);
    }
}
