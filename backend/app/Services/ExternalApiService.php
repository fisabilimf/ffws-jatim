<?php

namespace App\Services;

use App\Models\ExternalApiData;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Exception;

class ExternalApiService
{
    protected $timeout = 30;
    protected $retryAttempts = 3;
    protected $retryDelay = 1000; // milliseconds

    /**
     * Fetch data from all external APIs
     */
    public function fetchAllData()
    {
        $results = [
            'arr_pusda' => $this->fetchDataFromSource(ExternalApiData::SOURCE_ARR_PUSDA),
            'meteorologi_juanda' => $this->fetchDataFromSource(ExternalApiData::SOURCE_METEOROLOGI_JUANDA),
            'awlr_pusda' => $this->fetchDataFromSource(ExternalApiData::SOURCE_AWLR_PUSDA),
        ];

        $summary = [
            'total_sources' => 3,
            'successful_sources' => 0,
            'total_records' => 0,
            'new_records' => 0,
            'updated_records' => 0,
            'failed_sources' => [],
            'sync_time' => now(),
        ];

        foreach ($results as $source => $result) {
            if ($result['success']) {
                $summary['successful_sources']++;
                $summary['total_records'] += $result['total_records'];
                $summary['new_records'] += $result['new_records'];
                $summary['updated_records'] += $result['updated_records'];
            } else {
                $summary['failed_sources'][] = [
                    'source' => $source,
                    'error' => $result['error']
                ];
            }
        }

        Log::info('External API sync completed', $summary);

        return [
            'success' => $summary['successful_sources'] > 0,
            'summary' => $summary,
            'details' => $results
        ];
    }

    /**
     * Fetch data from a specific source
     */
    public function fetchDataFromSource($source)
    {
        try {
            Log::info("Starting data fetch from {$source}");

            $endpoint = ExternalApiData::getApiEndpoint($source);
            if (!$endpoint) {
                throw new Exception("Unknown API source: {$source}");
            }

            $response = $this->makeApiRequest($endpoint);
            
            if (!$response['success']) {
                throw new Exception($response['error']);
            }

            $apiData = $response['data'];
            $processed = $this->processApiResponse($apiData, $source);

            Log::info("Successfully processed data from {$source}", [
                'total_records' => $processed['total_records'],
                'new_records' => $processed['new_records'],
                'updated_records' => $processed['updated_records']
            ]);

            return [
                'success' => true,
                'source' => $source,
                'endpoint' => $endpoint,
                'total_records' => $processed['total_records'],
                'new_records' => $processed['new_records'],
                'updated_records' => $processed['updated_records'],
                'latest_record_time' => $processed['latest_record_time'],
                'sync_time' => now(),
            ];

        } catch (Exception $e) {
            Log::error("Failed to fetch data from {$source}: " . $e->getMessage());

            return [
                'success' => false,
                'source' => $source,
                'error' => $e->getMessage(),
                'sync_time' => now(),
            ];
        }
    }

    /**
     * Make HTTP request to external API with retry logic
     */
    protected function makeApiRequest($url)
    {
        $attempt = 0;
        
        while ($attempt < $this->retryAttempts) {
            try {
                $response = Http::timeout($this->timeout)
                    ->withHeaders([
                        'Accept' => 'application/json',
                        'User-Agent' => 'FFWS-JATIM/1.0'
                    ])
                    ->get($url);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    // Validate response structure
                    if (!is_array($data) || empty($data)) {
                        throw new Exception('Invalid or empty API response');
                    }

                    return [
                        'success' => true,
                        'data' => $data,
                        'response_time' => $response->handlerStats()['total_time'] ?? null,
                    ];
                } else {
                    throw new Exception("HTTP {$response->status()}: {$response->body()}");
                }

            } catch (Exception $e) {
                $attempt++;
                Log::warning("API request attempt {$attempt} failed: " . $e->getMessage());
                
                if ($attempt >= $this->retryAttempts) {
                    return [
                        'success' => false,
                        'error' => $e->getMessage(),
                        'attempts' => $attempt,
                    ];
                }
                
                // Wait before retry
                usleep($this->retryDelay * 1000 * $attempt);
            }
        }
    }

    /**
     * Process API response and store in database
     */
    protected function processApiResponse($apiData, $source)
    {
        $newRecords = 0;
        $updatedRecords = 0;
        $totalRecords = 0;
        $latestRecordTime = null;

        // Get the first key which contains the actual data array
        $dataKey = array_keys($apiData)[0];
        $records = $apiData[$dataKey];

        if (!is_array($records)) {
            throw new Exception('Invalid data structure in API response');
        }

        foreach ($records as $record) {
            try {
                // Check if record already exists
                $existingRecord = ExternalApiData::where('external_id', $record['id'])
                    ->where('api_source', $source)
                    ->first();

                $isNew = !$existingRecord;
                
                // Create or update record
                $savedRecord = ExternalApiData::createFromApiResponse($record, $source);

                if ($isNew) {
                    $newRecords++;
                } else {
                    $updatedRecords++;
                }

                $totalRecords++;

                // Track latest record time
                $recordTime = $savedRecord->datetime;
                if ($recordTime && (!$latestRecordTime || $recordTime->gt($latestRecordTime))) {
                    $latestRecordTime = $recordTime;
                }

            } catch (Exception $e) {
                Log::warning("Failed to process record {$record['id']} from {$source}: " . $e->getMessage());
                continue;
            }
        }

        return [
            'total_records' => $totalRecords,
            'new_records' => $newRecords,
            'updated_records' => $updatedRecords,
            'latest_record_time' => $latestRecordTime,
        ];
    }

    /**
     * Get sync status for all sources
     */
    public function getSyncStatus()
    {
        $sources = [
            ExternalApiData::SOURCE_ARR_PUSDA,
            ExternalApiData::SOURCE_METEOROLOGI_JUANDA,
            ExternalApiData::SOURCE_AWLR_PUSDA,
        ];

        $status = [];

        foreach ($sources as $source) {
            $latest = ExternalApiData::bySource($source)
                ->orderBy('last_sync_at', 'desc')
                ->first();

            $total = ExternalApiData::bySource($source)->count();
            $recent = ExternalApiData::bySource($source)
                ->where('last_sync_at', '>=', now()->subHours(24))
                ->count();

            $status[$source] = [
                'total_records' => $total,
                'recent_records' => $recent,
                'last_sync' => $latest ? $latest->last_sync_at : null,
                'last_sync_human' => $latest && $latest->last_sync_at ? 
                    $latest->last_sync_at->diffForHumans() : 'Never',
                'is_recent' => $latest && $latest->last_sync_at && 
                    $latest->last_sync_at->diffInHours(now()) <= 2,
                'endpoint' => ExternalApiData::getApiEndpoint($source),
                'parameter_type' => ExternalApiData::getParameterType($source),
            ];
        }

        return $status;
    }

    /**
     * Get latest data for each source
     */
    public function getLatestData($limit = 10)
    {
        $sources = [
            ExternalApiData::SOURCE_ARR_PUSDA,
            ExternalApiData::SOURCE_METEOROLOGI_JUANDA,
            ExternalApiData::SOURCE_AWLR_PUSDA,
        ];

        $data = [];

        foreach ($sources as $source) {
            $records = ExternalApiData::bySource($source)
                ->latest()
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
                        'parameter_type' => $record->parameter_type,
                        'is_recent' => $record->is_recent,
                        'last_sync_at' => $record->last_sync_at,
                    ];
                });

            $data[$source] = $records;
        }

        return $data;
    }

    /**
     * Test API connectivity
     */
    public function testConnectivity()
    {
        $sources = [
            ExternalApiData::SOURCE_ARR_PUSDA,
            ExternalApiData::SOURCE_METEOROLOGI_JUANDA,
            ExternalApiData::SOURCE_AWLR_PUSDA,
        ];

        $results = [];

        foreach ($sources as $source) {
            $endpoint = ExternalApiData::getApiEndpoint($source);
            $startTime = microtime(true);
            
            try {
                $response = Http::timeout(10)->get($endpoint);
                $responseTime = round((microtime(true) - $startTime) * 1000, 2);
                
                $results[$source] = [
                    'success' => $response->successful(),
                    'status_code' => $response->status(),
                    'response_time_ms' => $responseTime,
                    'endpoint' => $endpoint,
                    'data_available' => $response->successful() && !empty($response->json()),
                    'error' => $response->successful() ? null : $response->body(),
                ];
            } catch (Exception $e) {
                $responseTime = round((microtime(true) - $startTime) * 1000, 2);
                
                $results[$source] = [
                    'success' => false,
                    'status_code' => null,
                    'response_time_ms' => $responseTime,
                    'endpoint' => $endpoint,
                    'data_available' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Clear old records (older than specified days)
     */
    public function cleanupOldRecords($daysToKeep = 30)
    {
        $cutoffDate = now()->subDays($daysToKeep);
        
        $deleted = ExternalApiData::where('tanggal', '<', $cutoffDate->toDateString())->delete();
        
        Log::info("Cleaned up {$deleted} old external API records older than {$daysToKeep} days");
        
        return $deleted;
    }
}