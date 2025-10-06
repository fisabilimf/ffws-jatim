<?php

namespace App\Services;

use App\Models\MasSensor;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Exception;

class ForecastingService
{
    protected $baseUrl;
    protected $timeout = 30;

    public function __construct()
    {
        // Get the forecasting service URL from config or use default
        $this->baseUrl = Config::get('services.forecasting.url', 'http://127.0.0.1:5001');
    }

    /**
     * Start forecasting for a sensor
     */
    public function startForecasting(MasSensor $sensor): array
    {
        try {
            Log::info("Starting forecasting for sensor: {$sensor->sensor_code}");

            // Get sensor data for the forecasting system
            $sensorData = $this->prepareSensorData($sensor);

            // Call the Python forecasting API
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/api/enhanced/forecasting/start", [
                    'sensor_code' => $sensor->sensor_code,
                    'sensor_id' => $sensor->id,
                    'parameter' => $sensor->parameter,
                    'model_id' => $sensor->mas_model_id,
                    'sensor_data' => $sensorData
                ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info("Forecasting started successfully for sensor: {$sensor->sensor_code}");
                
                return [
                    'success' => true,
                    'message' => 'Forecasting started successfully',
                    'data' => $data
                ];
            } else {
                Log::error("Failed to start forecasting for sensor: {$sensor->sensor_code}. Status: {$response->status()}");
                
                return [
                    'success' => false,
                    'message' => 'Failed to start forecasting',
                    'error' => $response->body()
                ];
            }

        } catch (Exception $e) {
            Log::error("Exception while starting forecasting for sensor: {$sensor->sensor_code}. Error: " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Exception occurred while starting forecasting',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Pause forecasting for a sensor
     */
    public function pauseForecasting(MasSensor $sensor): array
    {
        try {
            Log::info("Pausing forecasting for sensor: {$sensor->sensor_code}");

            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/api/enhanced/forecasting/pause", [
                    'sensor_code' => $sensor->sensor_code,
                    'sensor_id' => $sensor->id
                ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info("Forecasting paused successfully for sensor: {$sensor->sensor_code}");
                
                return [
                    'success' => true,
                    'message' => 'Forecasting paused successfully',
                    'data' => $data
                ];
            } else {
                Log::error("Failed to pause forecasting for sensor: {$sensor->sensor_code}. Status: {$response->status()}");
                
                return [
                    'success' => false,
                    'message' => 'Failed to pause forecasting',
                    'error' => $response->body()
                ];
            }

        } catch (Exception $e) {
            Log::error("Exception while pausing forecasting for sensor: {$sensor->sensor_code}. Error: " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Exception occurred while pausing forecasting',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Stop forecasting for a sensor
     */
    public function stopForecasting(MasSensor $sensor): array
    {
        try {
            Log::info("Stopping forecasting for sensor: {$sensor->sensor_code}");

            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/api/enhanced/forecasting/stop", [
                    'sensor_code' => $sensor->sensor_code,
                    'sensor_id' => $sensor->id
                ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info("Forecasting stopped successfully for sensor: {$sensor->sensor_code}");
                
                return [
                    'success' => true,
                    'message' => 'Forecasting stopped successfully',
                    'data' => $data
                ];
            } else {
                Log::error("Failed to stop forecasting for sensor: {$sensor->sensor_code}. Status: {$response->status()}");
                
                return [
                    'success' => false,
                    'message' => 'Failed to stop forecasting',
                    'error' => $response->body()
                ];
            }

        } catch (Exception $e) {
            Log::error("Exception while stopping forecasting for sensor: {$sensor->sensor_code}. Error: " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Exception occurred while stopping forecasting',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get forecasting status for a sensor
     */
    public function getForecastingStatus(MasSensor $sensor): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->baseUrl}/api/enhanced/forecasting/status/{$sensor->sensor_code}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->body()
                ];
            }

        } catch (Exception $e) {
            Log::error("Exception while getting forecasting status for sensor: {$sensor->sensor_code}. Error: " . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Prepare sensor data for the forecasting system
     */
    private function prepareSensorData(MasSensor $sensor): array
    {
        return [
            'id' => $sensor->id,
            'sensor_code' => $sensor->sensor_code,
            'parameter' => $sensor->parameter,
            'unit' => $sensor->unit,
            'description' => $sensor->description,
            'device' => [
                'id' => $sensor->device->id ?? null,
                'name' => $sensor->device->name ?? null,
                'code' => $sensor->device->code ?? null,
            ],
            'thresholds' => [
                'safe' => $sensor->threshold_safe,
                'warning' => $sensor->threshold_warning,
                'danger' => $sensor->threshold_danger,
            ],
            'model_id' => $sensor->mas_model_id,
        ];
    }
}