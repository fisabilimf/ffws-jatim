<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\PredictedCalculatedDischarge;
use App\Models\MasSensor;
use App\Models\RatingCurve;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ForecastingController extends Controller
{
    private string $forecastingApiUrl;

    public function __construct()
    {
        $this->forecastingApiUrl = config('services.forecasting.url', 'http://localhost:5000');
    }

    /**
     * Get health status of the forecasting service
     */
    public function health(): JsonResponse
    {
        try {
            $response = Http::timeout(10)->get("{$this->forecastingApiUrl}/health");
            
            if ($response->successful()) {
                return response()->json([
                    'status' => 'healthy',
                    'forecasting_service' => $response->json(),
                    'integration' => 'active'
                ]);
            }
            
            return response()->json([
                'status' => 'unhealthy',
                'error' => 'Forecasting service not responding',
                'integration' => 'inactive'
            ], 503);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'unhealthy',
                'error' => 'Cannot connect to forecasting service',
                'detail' => $e->getMessage(),
                'integration' => 'inactive'
            ], 503);
        }
    }

    /**
     * Run forecast for a specific sensor and save to database
     */
    public function runForecast(Request $request): JsonResponse
    {
        $request->validate([
            'sensor_code' => 'required|string',
            'model_code' => 'nullable|string',
            'prediction_hours' => 'nullable|integer|min:1|max:24',
            'step_hours' => 'nullable|numeric|min:0.1|max:6.0',
            'save_to_database' => 'nullable|boolean'
        ]);

        $sensorCode = $request->sensor_code;
        $modelCode = $request->model_code;
        $predictionHours = $request->prediction_hours ?? 24;
        $stepHours = $request->step_hours ?? 1.0;
        $saveToDatabase = $request->save_to_database ?? true;

        // Check if sensor exists
        $sensor = MasSensor::where('sensor_code', $sensorCode)->first();
        if (!$sensor) {
            return response()->json(['error' => 'Sensor not found'], 404);
        }

        try {
            // Call forecasting service
            $response = Http::timeout(30)->post("{$this->forecastingApiUrl}/api/forecast/run", [
                'sensor_code' => $sensorCode,
                'model_code' => $modelCode,
                'prediction_hours' => $predictionHours,
                'step_hours' => $stepHours
            ]);

            if (!$response->successful()) {
                $error = $response->json()['error'] ?? 'Forecasting service error';
                return response()->json(['error' => $error], $response->status());
            }

            $forecastData = $response->json();

            // Save predictions to database if requested
            if ($saveToDatabase && isset($forecastData['predictions'])) {
                $this->savePredictionsToDatabase($sensorCode, $forecastData);
            }

            // Cache the results for quick access
            $cacheKey = "forecast_{$sensorCode}_" . md5(json_encode($request->only(['model_code', 'prediction_hours', 'step_hours'])));
            Cache::put($cacheKey, $forecastData, 300); // Cache for 5 minutes

            return response()->json([
                'success' => true,
                'sensor_code' => $sensorCode,
                'model_used' => $forecastData['model_code'] ?? 'fallback',
                'prediction_count' => count($forecastData['predictions'] ?? []),
                'forecast_data' => $forecastData,
                'saved_to_database' => $saveToDatabase
            ]);

        } catch (\Exception $e) {
            Log::error('Forecasting error', [
                'sensor_code' => $sensorCode,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Forecasting failed',
                'detail' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Run forecast for an entire river basin
     */
    public function runBasinForecast(Request $request): JsonResponse
    {
        $request->validate([
            'river_basin_code' => 'required|string',
            'only_active' => 'nullable|boolean'
        ]);

        $riverBasinCode = $request->river_basin_code;
        $onlyActive = $request->only_active ?? true;

        try {
            $response = Http::timeout(60)->post("{$this->forecastingApiUrl}/api/forecast/run-basin", [
                'river_basin_code' => $riverBasinCode,
                'only_active' => $onlyActive
            ]);

            if (!$response->successful()) {
                $error = $response->json()['error'] ?? 'Basin forecasting service error';
                return response()->json(['error' => $error], $response->status());
            }

            $basinForecastData = $response->json();

            // Save all predictions to database
            foreach ($basinForecastData['forecasts'] ?? [] as $forecast) {
                if (isset($forecast['sensor_code']) && isset($forecast['predictions'])) {
                    $this->savePredictionsToDatabase($forecast['sensor_code'], $forecast);
                }
            }

            return response()->json([
                'success' => true,
                'river_basin_code' => $riverBasinCode,
                'forecasts_generated' => count($basinForecastData['forecasts'] ?? []),
                'basin_forecast_data' => $basinForecastData
            ]);

        } catch (\Exception $e) {
            Log::error('Basin forecasting error', [
                'river_basin_code' => $riverBasinCode,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Basin forecasting failed',
                'detail' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get latest predictions for a sensor from database
     */
    public function getLatestPredictions(string $sensorCode): JsonResponse
    {
        $predictions = PredictedCalculatedDischarge::where('mas_sensor_code', $sensorCode)
            ->where('predicted_at', '>', Carbon::now())
            ->orderBy('predicted_at')
            ->get();

        if ($predictions->isEmpty()) {
            return response()->json([
                'sensor_code' => $sensorCode,
                'predictions' => [],
                'message' => 'No future predictions available'
            ]);
        }

        return response()->json([
            'sensor_code' => $sensorCode,
            'predictions' => $predictions->map(function ($prediction) {
                return [
                    'id' => $prediction->id,
                    'predicted_at' => $prediction->predicted_at,
                    'predicted_discharge' => $prediction->predicted_discharge,
                    'confidence_score' => $prediction->confidence_score,
                    'model_used' => $prediction->model_used,
                    'threshold_status' => $prediction->threshold_status,
                    'created_at' => $prediction->created_at
                ];
            }),
            'total_predictions' => $predictions->count(),
            'forecast_range' => [
                'from' => $predictions->first()->predicted_at,
                'to' => $predictions->last()->predicted_at
            ]
        ]);
    }

    /**
     * Get predictions with GeoJSON integration
     */
    public function getPredictionsWithGeojson(string $sensorCode): JsonResponse
    {
        $predictions = PredictedCalculatedDischarge::where('mas_sensor_code', $sensorCode)
            ->where('predicted_at', '>', Carbon::now())
            ->orderBy('predicted_at')
            ->get();

        if ($predictions->isEmpty()) {
            return response()->json([
                'sensor_code' => $sensorCode,
                'predictions' => [],
                'geojson_mappings' => [],
                'message' => 'No predictions available'
            ]);
        }

        // Get geojson mappings for different prediction levels
        $geojsonMappings = [];
        foreach ($predictions as $prediction) {
            $geojsonController = new GeojsonMappingController();
            $request = new Request([
                'sensor_code' => $sensorCode,
                'discharge_value' => $prediction->predicted_discharge
            ]);
            
            try {
                $geojsonResponse = $geojsonController->getGeojsonByDischarge($request);
                if ($geojsonResponse->getStatusCode() === 200) {
                    $geojsonMappings[] = [
                        'prediction_time' => $prediction->predicted_at,
                        'discharge_value' => $prediction->predicted_discharge,
                        'threshold_status' => $prediction->threshold_status,
                        'has_geojson' => true
                    ];
                }
            } catch (\Exception $e) {
                $geojsonMappings[] = [
                    'prediction_time' => $prediction->predicted_at,
                    'discharge_value' => $prediction->predicted_discharge,
                    'threshold_status' => $prediction->threshold_status,
                    'has_geojson' => false
                ];
            }
        }

        return response()->json([
            'sensor_code' => $sensorCode,
            'predictions' => $predictions,
            'geojson_mappings' => $geojsonMappings,
            'prediction_summary' => [
                'total_predictions' => $predictions->count(),
                'max_discharge' => $predictions->max('predicted_discharge'),
                'min_discharge' => $predictions->min('predicted_discharge'),
                'forecast_range' => [
                    'from' => $predictions->first()->predicted_at,
                    'to' => $predictions->last()->predicted_at
                ]
            ]
        ]);
    }

    /**
     * Run fallback forecast for sensors without ML models
     */
    public function runFallbackForecast(Request $request): JsonResponse
    {
        $request->validate([
            'sensor_code' => 'required|string',
            'hours_ahead' => 'nullable|integer|min:1|max:24'
        ]);

        $sensorCode = $request->sensor_code;
        $hoursAhead = $request->hours_ahead ?? 6;

        try {
            $response = Http::timeout(30)->post("{$this->forecastingApiUrl}/api/forecast/fallback", [
                'sensor_code' => $sensorCode,
                'hours_ahead' => $hoursAhead
            ]);

            if (!$response->successful()) {
                $error = $response->json()['error'] ?? 'Fallback forecasting error';
                return response()->json(['error' => $error], $response->status());
            }

            $fallbackData = $response->json();

            // Save fallback predictions to database
            if (isset($fallbackData['predictions'])) {
                $this->savePredictionsToDatabase($sensorCode, $fallbackData, 'fallback');
            }

            return response()->json([
                'success' => true,
                'sensor_code' => $sensorCode,
                'forecast_type' => 'fallback',
                'prediction_count' => count($fallbackData['predictions'] ?? []),
                'fallback_data' => $fallbackData
            ]);

        } catch (\Exception $e) {
            Log::error('Fallback forecasting error', [
                'sensor_code' => $sensorCode,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Fallback forecasting failed',
                'detail' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save predictions to the database
     */
    private function savePredictionsToDatabase(string $sensorCode, array $forecastData, string $modelType = 'ml'): void
    {
        // Clear existing future predictions for this sensor
        PredictedCalculatedDischarge::where('mas_sensor_code', $sensorCode)
            ->where('predicted_at', '>', Carbon::now())
            ->delete();

        // Get rating curve for this sensor
        $ratingCurve = RatingCurve::where('mas_sensor_code', $sensorCode)
            ->where('effective_date', '<=', Carbon::now())
            ->orderBy('effective_date', 'desc')
            ->first();

        foreach ($forecastData['predictions'] ?? [] as $index => $prediction) {
            $predictedAt = isset($prediction['timestamp']) 
                ? Carbon::parse($prediction['timestamp'])
                : Carbon::now()->addHours($index + 1);

            $discharge = $prediction['value'] ?? $prediction['discharge'] ?? null;
            $confidence = $prediction['confidence'] ?? null;
            $threshold = $prediction['threshold'] ?? 'unknown';

            if ($discharge !== null) {
                PredictedCalculatedDischarge::create([
                    'mas_sensor_code' => $sensorCode,
                    'predicted_discharge' => (float) $discharge,
                    'confidence_score' => $confidence ? (float) $confidence : null,
                    'model_used' => $forecastData['model_code'] ?? $modelType,
                    'threshold_status' => $threshold,
                    'rating_curve_id' => $ratingCurve?->id,
                    'predicted_at' => $predictedAt
                ]);
            }
        }

        Log::info('Predictions saved to database', [
            'sensor_code' => $sensorCode,
            'predictions_count' => count($forecastData['predictions'] ?? []),
            'model_type' => $modelType
        ]);
    }

    /**
     * List available ML models
     */
    public function listModels(): JsonResponse
    {
        try {
            $response = Http::timeout(10)->get("{$this->forecastingApiUrl}/api/models");
            
            if ($response->successful()) {
                return response()->json($response->json());
            }
            
            return response()->json(['error' => 'Cannot fetch models'], 503);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Models service unavailable'], 503);
        }
    }

    /**
     * List sensors available for forecasting
     */
    public function listForecastingSensors(): JsonResponse
    {
        try {
            $response = Http::timeout(10)->get("{$this->forecastingApiUrl}/api/sensors");
            
            if ($response->successful()) {
                return response()->json($response->json());
            }
            
            return response()->json(['error' => 'Cannot fetch sensors'], 503);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Sensors service unavailable'], 503);
        }
    }
}