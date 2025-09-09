<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ModelDataSeeder extends Seeder
{
    public function run()
    {
        $modelPath = base_path('../forecasting/models'); // Memperbaiki path
        $modelFiles = File::files($modelPath);

        foreach ($modelFiles as $file) {
            if ($file->getExtension() === 'h5') {
                $name = $file->getFilename();
                $modelType = $this->getModelType($name);
                $modelCode = $this->generateModelCode($name);

                DB::table('mas_models')->insert([
                    'name' => $name,
                    'model_type' => $modelType,
                    'model_code' => $modelCode,
                    'version' => '1.0', // Default version
                    'description' => 'Model for ' . $modelType,
                    'file_path' => $file->getRealPath(),
                    'n_steps_in' => 10, // Default value
                    'n_steps_out' => 1, // Default value
                    'is_active' => 1, // Default active
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function getModelType($fileName)
    {
        if (stripos($fileName, 'gru') !== false) {
            return 'GRU';
        } elseif (stripos($fileName, 'tcn') !== false) {
            return 'TCN';
        } elseif (stripos($fileName, 'lstm') !== false) {
            return 'LSTM';
        }

        return 'Unknown';
    }

    private function generateModelCode($fileName)
    {
        // Extract location and model type from filename
        // Example: dhompo_gru.h5 -> DHOMPO_GRU
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);
        $parts = explode('_', $baseName);
        
        if (count($parts) >= 2) {
            $location = strtoupper($parts[0]);
            $modelType = strtoupper($parts[1]);
            return $location . '_' . $modelType;
        }
        
        return strtoupper($baseName);
    }
}