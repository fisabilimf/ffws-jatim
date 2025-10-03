<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MasRiverBasin>
 */
class MasRiverBasinFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'river_basins_name' => $this->faker->city . ' River Basin',
            'river_basins_code' => $this->faker->unique()->regexify('[A-Z]{2}[0-9]{3}'),
            'cities_code' => $this->faker->regexify('[A-Z]{2}[0-9]{3}'),
        ];
    }
}
