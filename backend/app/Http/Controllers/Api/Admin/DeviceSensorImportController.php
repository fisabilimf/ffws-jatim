<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\DeviceSensorImportService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DeviceSensorImportController extends Controller
{
    protected $importService;

    public function __construct(DeviceSensorImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Import devices and sensors from all external APIs
     */
    public function importAll(): JsonResponse
    {
        try {
            $results = $this->importService->importAllDevicesAndSensors();

            return response()->json([
                'success' => true,
                'message' => 'Device and sensor import completed',
                'data' => $results,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    /**
     * Preview what would be imported (dry run)
     */
    public function preview(): JsonResponse
    {
        try {
            $apis = [
                'cuaca-arr-pusda' => 'https://sih3.dpuair.jatimprov.go.id/api/cuaca-arr-pusda',
                'cuaca-awlr-pusda' => 'https://sih3.dpuair.jatimprov.go.id/api/cuaca-awlr-pusda',
                'meteorologi-juanda' => 'https://sih3.dpuair.jatimprov.go.id/api/meteorologi-juanda',
            ];

            $preview = [];

            foreach ($apis as $source => $url) {
                try {
                    $response = \Illuminate\Support\Facades\Http::timeout(30)->get($url);
                    
                    if ($response->successful()) {
                        $data = $response->json();
                        
                        // Extract the actual data count and sample data
                        $count = 0;
                        $sampleData = [];
                        
                        if ($source === 'cuaca-arr-pusda' && isset($data['Pos Duga Air Jam-jam an PU SDA'])) {
                            $items = $data['Pos Duga Air Jam-jam an PU SDA'];
                            $count = count($items);
                            $sampleData = array_slice($items, 0, 3); // First 3 items as sample
                        } elseif ($source === 'cuaca-awlr-pusda' && isset($data['Hujan Jam-Jam an PU SDA'])) {
                            $items = $data['Hujan Jam-Jam an PU SDA'];
                            $count = count($items);
                            $sampleData = array_slice($items, 0, 3);
                        } elseif ($source === 'meteorologi-juanda' && isset($data['Data Meteorologi Juanda'])) {
                            $items = $data['Data Meteorologi Juanda'];
                            $count = count($items);
                            $sampleData = array_slice($items, 0, 3);
                        }

                        $preview[$source] = [
                            'status' => 'available',
                            'count' => $count,
                            'sample_data' => $sampleData,
                        ];
                    } else {
                        $preview[$source] = [
                            'status' => 'error',
                            'error' => 'API returned status ' . $response->status(),
                            'count' => 0,
                        ];
                    }
                } catch (\Exception $e) {
                    $preview[$source] = [
                        'status' => 'error',
                        'error' => $e->getMessage(),
                        'count' => 0,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Preview data retrieved successfully',
                'data' => [
                    'apis' => $preview,
                    'total_available' => array_sum(array_column($preview, 'count')),
                    'checked_at' => now(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Preview failed: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    /**
     * Get import status and statistics
     */
    public function status(): JsonResponse
    {
        try {
            $stats = [
                'total_devices' => \App\Models\MasDevice::count(),
                'total_sensors' => \App\Models\MasSensor::count(),
                'devices_by_source' => [
                    'imported' => \App\Models\MasDevice::whereHas('riverBasin', function($q) {
                        $q->where('name', 'Imported from External APIs');
                    })->count(),
                    'manual' => \App\Models\MasDevice::whereDoesntHave('riverBasin', function($q) {
                        $q->where('name', 'Imported from External APIs');
                    })->count(),
                ],
                'sensors_by_parameter' => \App\Models\MasSensor::select('parameter')
                    ->selectRaw('count(*) as count')
                    ->groupBy('parameter')
                    ->pluck('count', 'parameter')
                    ->toArray(),
                'active_sensors' => \App\Models\MasSensor::where('is_active', true)->count(),
                'last_updated' => \App\Models\MasSensor::latest('updated_at')->value('updated_at'),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Status retrieved successfully',
                'data' => $stats,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get status: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }
}