<?php

namespace Database\Factories;

use App\Models\MasSensor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DataActual>
 */
class DataActualFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sensor = MasSensor::inRandomOrder()->first();
        $value = $this->faker->randomFloat(2, 0, 20);
        $status = $this->faker->randomElement(['normal', 'watch', 'warning', 'danger', 'unknown']);

        return [
            'mas_sensor_code' => $sensor ? $sensor->sensor_code : $this->faker->regexify('[A-Z]{3}[0-9]{3}'),
            'value' => $value,
            'received_at' => $this->faker->dateTimeThisMonth(),
            'threshold_status' => $status,
        ];
    }
}
