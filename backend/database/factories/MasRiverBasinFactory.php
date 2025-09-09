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
        static $counter = 1;
        
        return [
            'name' => $this->faker->city . ' River Basin',
            'code' => 'RB' . str_pad($counter++, 3, '0', STR_PAD_LEFT), // RB001, RB002, etc.
        ];
    }
}
