<?php

namespace Database\Factories;

use App\Models\MasDevice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MasSensor>
 */
class MasSensorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $device = MasDevice::inRandomOrder()->first();

        return [
            'mas_device_code' => $device ? $device->device_code : $this->faker->regexify('[A-Z]{3}[0-9]{4}'),
            'sensor_code' => $this->faker->unique()->regexify('[A-Z]{3}[0-9]{3}'),
        ];
    }
}
