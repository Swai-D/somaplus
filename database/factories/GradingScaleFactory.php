<?php

namespace Database\Factories;

use App\Models\GradingBand;
use App\Models\GradingScale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GradingScale>
 */
class GradingScaleFactory extends Factory
{
    public function definition(): array
    {
        return ['name' => fake()->unique()->bothify('Scale ####')];
    }

    /** A, B, C, D, F at 80, 70, 60, 50 and 0 percent (F is the band that starts at 0). */
    public function standard(): static
    {
        return $this->afterCreating(function (GradingScale $scale): void {
            foreach ([['80', 'A', 1], ['70', 'B', 2], ['60', 'C', 3], ['50', 'D', 4], ['0', 'F', 5]] as [$min, $grade, $points]) {
                $band = new GradingBand(['min_mark' => $min, 'grade' => $grade, 'points' => $points]);
                $band->grading_scale_id = $scale->id;
                $band->save();
            }
        });
    }
}
