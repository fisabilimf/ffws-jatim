<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataPredictionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sensors = DB::table('mas_sensors')->pluck('sensor_code')->toArray();
        $models = DB::table('mas_models')->pluck('model_code')->toArray();
        
        for ($i = 0; $i < 50; $i++) {
            $sensorCode = $sensors[array_rand($sensors)];
            $modelCode = $models[array_rand($models)];
            
            $sensor = DB::table('mas_sensors')->where('sensor_code', $sensorCode)->first();
            $predictionRunAt = now()->subDays(rand(0, 30));
            $predictionForTs = $predictionRunAt->copy()->addHours(rand(1, 168)); // 1 hour to 1 week ahead
            $predictedValue = rand(0, 2000) / 100; // 0.00 to 20.00
            
            // Calculate threshold status
            $status = 'safe';
            if ($sensor && $predictedValue >= $sensor->threshold_danger) {
                $status = 'danger';
            } elseif ($sensor && $predictedValue >= $sensor->threshold_warning) {
                $status = 'warning';
            }
            
            DB::table('data_predictions')->insert([
                'mas_sensor_code' => $sensorCode,
                'mas_model_code' => $modelCode,
                'prediction_run_at' => $predictionRunAt,
                'prediction_for_ts' => $predictionForTs,
                'predicted_value' => $predictedValue,
                'confidence_score' => rand(70, 100) / 100, // 0.70 to 1.00
                'threshold_prediction_status' => $status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
