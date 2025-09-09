<?php

namespace Database\Factories;

use App\Models\MasSensor;
use App\Models\MasModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DataPrediction>
 */
class DataPredictionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sensor = MasSensor::inRandomOrder()->first();
        $model = MasModel::inRandomOrder()->first();
        $predictionRunAt = $this->faker->dateTimeThisMonth();
        $predictionForTs = $this->faker->dateTimeBetween($predictionRunAt, '+7 days');
        $predictedValue = $this->faker->randomFloat(2, 0, 20);
        
        // Calculate threshold status based on sensor thresholds
        $status = 'safe';
        if ($sensor) {
            if ($predictedValue >= $sensor->threshold_danger) {
                $status = 'danger';
            } elseif ($predictedValue >= $sensor->threshold_warning) {
                $status = 'warning';
            }
        }

        return [
            'mas_sensor_code' => $sensor->sensor_code,
            'mas_model_code' => $model->model_code,
            'prediction_run_at' => $predictionRunAt,
            'prediction_for_ts' => $predictionForTs,
            'predicted_value' => $predictedValue,
            'confidence_score' => $this->faker->randomFloat(2, 0.7, 1.0),
            'threshold_prediction_status' => $status,
        ];
    }
}
