<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\ExternalApiService;
use App\Models\ExternalApiData;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ExternalApiController extends Controller
{
    protected $externalApiService;

    public function __construct(ExternalApiService $externalApiService)
    {
        $this->externalApiService = $externalApiService;
    }

    /**
     * Sync data from all external APIs
     */
    public function syncAll(Request $request): JsonResponse
    {
        try {
            Log::info('Manual sync all external APIs requested', [
                'user_id' => $request->user()?->id,
                'ip' => $request->ip()
            ]);

            $result = $this->externalApiService->fetchAllData();

            return response()->json([
                'success' => $result['success'],
                'message' => $result['success'] ? 
                    'Data synchronization completed successfully' : 
                    'Data synchronization completed with errors',
                'data' => $result['summary'],
                'details' => $result['details'],
            ], $result['success'] ? 200 : 207); // 207 = Multi-Status

        } catch (\Exception $e) {
            Log::error('Failed to sync all external APIs: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to synchronize data from external APIs',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Sync data from specific source
     */
    public function syncSource(Request $request, string $source): JsonResponse
    {
        $validator = Validator::make(['source' => $source], [
            'source' => 'required|in:cuaca-arr-pusda,meteorologi-juanda,cuaca-awlr-pusda'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API source',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            Log::info("Manual sync requested for source: {$source}", [
                'user_id' => $request->user()?->id,
                'ip' => $request->ip()
            ]);

            $result = $this->externalApiService->fetchDataFromSource($source);

            return response()->json([
                'success' => $result['success'],
                'message' => $result['success'] ? 
                    "Data synchronization from {$source} completed successfully" : 
                    "Failed to synchronize data from {$source}",
                'data' => $result,
            ], $result['success'] ? 200 : 500);

        } catch (\Exception $e) {
            Log::error("Failed to sync from {$source}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => "Failed to synchronize data from {$source}",
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Get sync status for all sources
     */
    public function status(): JsonResponse
    {
        try {
            $status = $this->externalApiService->getSyncStatus();

            return response()->json([
                'success' => true,
                'message' => 'Sync status retrieved successfully',
                'data' => $status,
                'summary' => [
                    'total_sources' => count($status),
                    'healthy_sources' => collect($status)->where('is_recent', true)->count(),
                    'last_checked' => now(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get sync status: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sync status',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Test API connectivity
     */
    public function testConnectivity(): JsonResponse
    {
        try {
            $results = $this->externalApiService->testConnectivity();

            $allHealthy = collect($results)->every('success');

            return response()->json([
                'success' => true,
                'message' => 'Connectivity test completed',
                'all_healthy' => $allHealthy,
                'data' => $results,
                'summary' => [
                    'total_sources' => count($results),
                    'healthy_sources' => collect($results)->where('success', true)->count(),
                    'average_response_time' => collect($results)->avg('response_time_ms'),
                    'tested_at' => now(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to test connectivity: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to test API connectivity',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Get latest data from all sources
     */
    public function latestData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'integer|min:1|max:100',
            'source' => 'string|in:cuaca-arr-pusda,meteorologi-juanda,cuaca-awlr-pusda',
            'parameter' => 'string|in:rainfall,water-level',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            $limit = $request->get('limit', 10);
            $source = $request->get('source');
            $parameter = $request->get('parameter');

            if ($source) {
                // Get data from specific source
                $query = ExternalApiData::bySource($source);
            } else {
                // Get data from all sources
                $query = ExternalApiData::query();
            }

            if ($parameter) {
                $query->byParameter($parameter);
            }

            $data = $query->latest()
                ->take($limit)
                ->get()
                ->map(function ($record) {
                    return [
                        'id' => $record->id,
                        'external_id' => $record->external_id,
                        'judul' => $record->judul,
                        'kode' => $record->kode,
                        'alamat' => $record->alamat,
                        'location' => $record->location,
                        'datetime' => $record->datetime,
                        'value' => $record->value,
                        'label' => $record->label,
                        'status' => $record->status,
                        'api_source' => $record->api_source,
                        'parameter_type' => $record->parameter_type,
                        'is_recent' => $record->is_recent,
                        'last_sync_at' => $record->last_sync_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Latest data retrieved successfully',
                'data' => $data,
                'meta' => [
                    'count' => $data->count(),
                    'limit' => $limit,
                    'source' => $source,
                    'parameter' => $parameter,
                    'retrieved_at' => now(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get latest data: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve latest data',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Get data by sensor code
     */
    public function getBySensorCode(Request $request, string $sensorCode): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'days' => 'integer|min:1|max:30',
            'parameter' => 'string|in:rainfall,water-level',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,  
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            $days = $request->get('days', 7);
            $parameter = $request->get('parameter');
            $startDate = now()->subDays($days)->toDateString();

            $query = ExternalApiData::where('kode', $sensorCode)
                ->dateRange($startDate, now()->toDateString());

            if ($parameter) {
                $query->byParameter($parameter);
            }

            $data = $query->orderBy('tanggal', 'desc')
                ->orderBy('jam', 'desc')
                ->get()
                ->map(function ($record) {
                    return [
                        'id' => $record->id,
                        'datetime' => $record->datetime,
                        'value' => $record->value,
                        'label' => $record->label,
                        'status' => $record->status,
                        'api_source' => $record->api_source,
                        'parameter_type' => $record->parameter_type,
                        'location' => $record->location,
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => "Data for sensor {$sensorCode} retrieved successfully",
                'data' => $data,
                'meta' => [
                    'sensor_code' => $sensorCode,
                    'count' => $data->count(),
                    'days' => $days,
                    'parameter' => $parameter,
                    'date_range' => [
                        'from' => $startDate,
                        'to' => now()->toDateString(),
                    ],
                    'retrieved_at' => now(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to get data for sensor {$sensorCode}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => "Failed to retrieve data for sensor {$sensorCode}",
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Get stations/sensors list
     */
    public function getStations(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'source' => 'string|in:cuaca-arr-pusda,meteorologi-juanda,cuaca-awlr-pusda',
            'parameter' => 'string|in:rainfall,water-level',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            $source = $request->get('source');
            $parameter = $request->get('parameter');

            $query = ExternalApiData::select([
                    'kode', 'judul', 'alamat', 'longitude', 'latitude', 
                    'api_source', 'parameter_type'
                ])
                ->distinct();

            if ($source) {
                $query->bySource($source);
            }

            if ($parameter) {
                $query->byParameter($parameter);
            }

            $stations = $query->get()
                ->map(function ($record) {
                    return [
                        'kode' => $record->kode,
                        'judul' => $record->judul,
                        'alamat' => $record->alamat,
                        'location' => $record->location,
                        'api_source' => $record->api_source,
                        'parameter_type' => $record->parameter_type,
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Stations list retrieved successfully',
                'data' => $stations,
                'meta' => [
                    'count' => $stations->count(),
                    'source' => $source,
                    'parameter' => $parameter,
                    'retrieved_at' => now(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get stations list: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve stations list',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Clean up old records
     */
    public function cleanup(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'days' => 'integer|min:7|max:365',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            $days = $request->get('days', 30);
            
            Log::info("Manual cleanup requested for records older than {$days} days", [
                'user_id' => $request->user()?->id,
                'ip' => $request->ip()
            ]);

            $deletedCount = $this->externalApiService->cleanupOldRecords($days);

            return response()->json([
                'success' => true,
                'message' => "Successfully cleaned up {$deletedCount} old records",
                'data' => [
                    'deleted_records' => $deletedCount,
                    'days_threshold' => $days,
                    'cleanup_date' => now(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to cleanup old records: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to cleanup old records',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Get API information and endpoints
     */
    public function info(): JsonResponse
    {
        try {
            $sources = [
                ExternalApiData::SOURCE_ARR_PUSDA,
                ExternalApiData::SOURCE_METEOROLOGI_JUANDA,
                ExternalApiData::SOURCE_AWLR_PUSDA,
            ];

            $info = [];

            foreach ($sources as $source) {
                $info[$source] = [
                    'name' => $this->getSourceName($source),
                    'endpoint' => ExternalApiData::getApiEndpoint($source),
                    'parameter_type' => ExternalApiData::getParameterType($source),
                    'description' => $this->getSourceDescription($source),
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'External API information retrieved successfully',
                'data' => [
                    'sources' => $info,
                    'total_sources' => count($sources),
                    'supported_parameters' => ['rainfall', 'water-level'],
                    'version' => '1.0',
                    'last_updated' => '2025-10-03',
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get API info: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve API information',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Get human-readable source name
     */
    private function getSourceName($source)
    {
        $names = [
            ExternalApiData::SOURCE_ARR_PUSDA => 'ARR Pusda (Rainfall)',
            ExternalApiData::SOURCE_METEOROLOGI_JUANDA => 'Meteorologi Juanda (Rainfall)',
            ExternalApiData::SOURCE_AWLR_PUSDA => 'AWLR Pusda (Rainfall)', // Note: This actually provides rainfall data
        ];

        return $names[$source] ?? $source;
    }

    /**
     * Get source description
     */
    private function getSourceDescription($source)
    {
        $descriptions = [
            ExternalApiData::SOURCE_ARR_PUSDA => 'Automatic Rain Recorder data from PU SDA stations across East Java',
            ExternalApiData::SOURCE_METEOROLOGI_JUANDA => 'Meteorological rainfall data from Juanda area grid stations',
            ExternalApiData::SOURCE_AWLR_PUSDA => 'Rainfall data from AWLR-equipped stations by PU SDA',
        ];

        return $descriptions[$source] ?? 'External API data source';
    }
}