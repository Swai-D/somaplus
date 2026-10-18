<?php

namespace Database\Factories;

use App\Models\SchoolClass;
use App\Models\Stream;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Stream>
 */
class StreamFactory extends Factory
{
    public function definition(): array
    {
        return [
            'school_class_id' => SchoolClass::factory(),
            'name' => fake()->unique()->bothify('Stream ##??'),
        ];
    }
}
