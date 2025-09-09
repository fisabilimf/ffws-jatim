<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ScalerDataSeeder extends Seeder
{
    public function run()
    {
        $scalerPath = base_path('../forecasting/scalers');
        $scalerFiles = File::files($scalerPath);

        foreach ($scalerFiles as $file) {
            if ($file->getExtension() === 'pkl') {
                $name = $file->getFilename();
                $ioAxis = $this->getIoAxis($name);
                $technique = $this->getTechnique($name);
                $fileHash = hash_file('sha256', $file->getRealPath());
                $masModelCode = $this->getMasModelCode($name); // Dapatkan code model berdasarkan nama file
                $masSensorCode = null; // Set to null since no sensors exist yet
                $scalerCode = $this->generateScalerCode($name);

                // Periksa apakah data sudah ada berdasarkan kombinasi unik dan file hash
                $existsQuery = DB::table('mas_scalers')
                    ->where('mas_model_code', $masModelCode)
                    ->where('io_axis', $ioAxis)
                    ->where('file_hash_sha256', $fileHash)
                    ->where('is_active', 1);
                    
                if ($masSensorCode === null) {
                    $existsQuery->whereNull('mas_sensor_code');
                } else {
                    $existsQuery->where('mas_sensor_code', $masSensorCode);
                }
                
                $exists = $existsQuery->exists();

                if (!$exists && $masModelCode) {
                    DB::table('mas_scalers')->insert([
                        'mas_model_code' => $masModelCode,
                        'mas_sensor_code' => $masSensorCode,
                        'name' => $name,
                        'scaler_code' => $scalerCode,
                        'io_axis' => $ioAxis,
                        'technique' => $technique,
                        'version' => '1.0', // Default version
                        'file_path' => $file->getRealPath(),
                        'file_hash_sha256' => $fileHash,
                        'is_active' => 1, // Default active
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    private function getIoAxis($fileName)
    {
        if (stripos($fileName, '_x_') !== false) {
            return 'x';
        } elseif (stripos($fileName, '_y_') !== false) {
            return 'y';
        }

        // Default to a valid value if unknown
        return 'x';
    }

    private function getTechnique($fileName)
    {
        if (stripos($fileName, 'minmax') !== false) {
            return 'minmax';
        } elseif (stripos($fileName, 'standard') !== false) {
            return 'standard';
        } elseif (stripos($fileName, 'robust') !== false) {
            return 'robust';
        } elseif (stripos($fileName, 'custom') !== false) {
            return 'custom';
        }

        // Default to a valid value if unknown
        return 'custom';
    }

    private function getMasModelCode($fileName)
    {
        $modelType = null;

        if (stripos($fileName, 'gru') !== false) {
            $modelType = 'GRU';
        } elseif (stripos($fileName, 'tcn') !== false) {
            $modelType = 'TCN';
        } elseif (stripos($fileName, 'lstm') !== false) {
            $modelType = 'LSTM';
        }

        if ($modelType) {
            $model = DB::table('mas_models')->where('model_type', $modelType)->first();
            return $model ? $model->model_code : null;
        }

        return null;
    }

    private function generateScalerCode($fileName)
    {
        // Extract information from filename
        // Example: dhompo_gru_x_scaler.pkl -> DHOMPO_GRU_X_SCALER
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);
        return strtoupper(str_replace('_', '_', $baseName));
    }
}
