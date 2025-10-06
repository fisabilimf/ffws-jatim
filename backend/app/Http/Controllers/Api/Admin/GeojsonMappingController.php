<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeojsonMapping;
use App\Models\GeojsonFile;
use App\Models\CalculatedDischarge;
use App\Models\PredictedCalculatedDischarge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GeojsonMappingController extends Controller
{
    /**
     * Get all geojson mappings with relationships
     */
    public function index()
    {
        return GeojsonMapping::with([
            'device',
            'riverBasin', 
            'watershed',
            'city',
            'regency',
            'village',
            'upt',
            'uptd',
            'deviceParameter'
        ])->orderBy('id')->get();
    }

    /**
     * Get geojson file based on sensor discharge value
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function getGeojsonByDischarge(Request $request)
    {
        $request->validate([
            'sensor_code' => 'required|string',
            'discharge_value' => 'required|numeric',
        ]);

        $inputCode = $request->sensor_code;
        $dischargeValue = $request->discharge_value;

        // Try to find as device code first, then as sensor code
        $deviceCode = $inputCode;
        
        // Check if it's a sensor code and get the device code
        $sensor = \App\Models\MasSensor::where('sensor_code', $inputCode)->first();
        if ($sensor) {
            $deviceCode = $sensor->mas_device_code;
        }

        // Find geojson mapping that matches the discharge value range
        $mapping = GeojsonMapping::findByDeviceAndDischarge($deviceCode, $dischargeValue);

        if (!$mapping) {
            return response()->json([
                'error' => 'No geojson mapping found for the given discharge value',
                'input_code' => $inputCode,
                'device_code' => $deviceCode,
                'discharge_value' => $dischargeValue
            ], 404);
        }

        // If file_path is stored directly in mapping, return file content
        if ($mapping->file_path) {
            return $this->returnGeojsonFile($mapping->file_path, $mapping->description);
        }

        // If geojson_code references GeojsonFile table
        if ($mapping->geojson_code && $mapping->geojsonFile) {
            return $this->returnGeojsonFileContent($mapping->geojsonFile);
        }

        return response()->json([
            'error' => 'Geojson file not found',
            'mapping_id' => $mapping->id
        ], 404);
    }

    /**
     * Get geojson file based on latest calculated discharge for a sensor
     * 
     * @param string $sensorCode
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function getGeojsonByLatestDischarge($sensorCode)
    {
        // Get the latest calculated discharge for this sensor
        $latestDischarge = CalculatedDischarge::where('mas_sensor_code', $sensorCode)
            ->orderBy('calculated_at', 'desc')
            ->first();

        if (!$latestDischarge) {
            return response()->json([
                'error' => 'No calculated discharge found for sensor',
                'sensor_code' => $sensorCode
            ], 404);
        }

        // Use the discharge value to find appropriate geojson
        $request = new Request([
            'sensor_code' => $sensorCode,
            'discharge_value' => $latestDischarge->sensor_discharge
        ]);

        return $this->getGeojsonByDischarge($request);
    }

    /**
     * Get geojson file based on latest predicted discharge for a sensor
     * 
     * @param string $sensorCode
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function getGeojsonByLatestPredictedDischarge($sensorCode)
    {
        // Get the latest predicted discharge for this sensor
        $latestPredictedDischarge = PredictedCalculatedDischarge::where('mas_sensor_code', $sensorCode)
            ->orderBy('predicted_at', 'desc')
            ->first();

        if (!$latestPredictedDischarge) {
            return response()->json([
                'error' => 'No predicted discharge found for sensor',
                'sensor_code' => $sensorCode
            ], 404);
        }

        // Use the predicted discharge value to find appropriate geojson
        $request = new Request([
            'sensor_code' => $sensorCode,
            'discharge_value' => $latestPredictedDischarge->predicted_discharge
        ]);

        return $this->getGeojsonByDischarge($request);
    }

    /**
     * Get all available geojson mappings for a specific sensor
     * 
     * @param string $sensorCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGeojsonMappingsForSensor($sensorCode)
    {
        $mappings = GeojsonMapping::where('mas_device_code', $sensorCode)
            ->orderBy('value_min')
            ->get();

        return response()->json([
            'sensor_code' => $sensorCode,
            'mappings' => $mappings->map(function ($mapping) {
                return [
                    'id' => $mapping->id,
                    'value_min' => $mapping->value_min,
                    'value_max' => $mapping->value_max,
                    'description' => $mapping->description,
                    'geojson_code' => $mapping->geojson_code,
                    'file_path' => $mapping->file_path,
                    'version' => $mapping->version,
                ];
            })
        ]);
    }

    /**
     * Helper method to return geojson file content from GeojsonFile model
     */
    private function returnGeojsonFileContent(GeojsonFile $geojsonFile)
    {
        if (!Storage::disk($geojsonFile->disk)->exists($geojsonFile->stored_path)) {
            return response()->json(['error' => 'File not found on disk'], 404);
        }

        $content = Storage::disk($geojsonFile->disk)->get($geojsonFile->stored_path);
        $mime = $geojsonFile->mime_type ?: 'application/geo+json';

        if (str_contains($mime, 'json')) {
            $mime = 'application/json';
        }

        return response($content, 200)
            ->header('Content-Type', $mime)
            ->header('Cache-Control', 'public, max-age=60')
            ->header('X-Geojson-Source', 'geojson_files_table')
            ->header('X-Geojson-Label', $geojsonFile->label);
    }

    /**
     * Helper method to return geojson file content from direct file path
     */
    private function returnGeojsonFile($filePath, $description = null)
    {
        // Handle absolute paths
        if (file_exists($filePath)) {
            $content = file_get_contents($filePath);
        } else if (Storage::exists($filePath)) {
            // Fallback to storage for relative paths
            $content = Storage::get($filePath);
        } else {
            return response()->json(['error' => 'File not found'], 404);
        }

        return response($content, 200)
            ->header('Content-Type', 'application/json')
            ->header('Cache-Control', 'public, max-age=60')
            ->header('X-Geojson-Source', 'direct_path')
            ->header('X-Geojson-Description', $description ?: 'Geojson file');
    }
}