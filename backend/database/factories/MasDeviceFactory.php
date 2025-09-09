<?php

namespace Database\Factories;

use App\Models\MasRiverBasin;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MasDevice>
 */
class MasDeviceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mas_river_basin_code' => MasRiverBasin::inRandomOrder()->first()->code,
            'name' => $this->faker->word,
            'device_code' => $this->faker->unique()->bothify('DEV-####'),
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'elevation_m' => $this->faker->randomFloat(2, 5, 1000),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
