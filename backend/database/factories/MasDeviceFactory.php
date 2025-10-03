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
            'device_code' => $this->faker->unique()->regexify('[A-Z]{3}[0-9]{4}'),
        ];
    }
}
