<?php

namespace App\Services;

use App\Models\MasDevice;
use App\Models\MasSensor;
use App\Models\MasRiverBasin;
use App\Models\MasModel;
use App\Models\ExternalApiData;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class DeviceSensorImportService
{
    protected $externalApiService;
    protected $timeout = 30;
    protected $retryAttempts = 3;

    public function __construct(ExternalApiService $externalApiService)
    {
        $this->externalApiService = $externalApiService;
    }

    /**
     * Import devices and sensors from all external APIs
     */
    public function importAllDevicesAndSensors()
    {
        $results = [
            'arr_pusda' => $this->importFromSource('cuaca-arr-pusda'),
            'awlr_pusda' => $this->importFromSource('cuaca-awlr-pusda'),
            'meteorologi_juanda' => $this->importFromSource('meteorologi-juanda'),
        ];

        return [
            'total_sources' => 3,
            'results' => $results,
            'summary' => $this->generateSummary($results),
            'sync_time' => now(),
        ];
    }

    /**
     * Import devices and sensors from a specific API source
     */
    protected function importFromSource($source)
    {
        try {
            $data = $this->fetchDataFromApi($source);
            
            if (!$data) {
                return [
                    'success' => false,
                    'error' => 'No data received from API',
                    'devices_created' => 0,
                    'sensors_created' => 0,
                    'devices_updated' => 0,
                    'sensors_updated' => 0,
                ];
            }

            return $this->processApiData($data, $source);

        } catch (Exception $e) {
            Log::error("Error importing from source {$source}: " . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'devices_created' => 0,
                'sensors_created' => 0,
                'devices_updated' => 0,
                'sensors_updated' => 0,
            ];
        }
    }

    /**
     * Fetch data from external API
     */
    protected function fetchDataFromApi($source)
    {
        $urls = [
            'cuaca-arr-pusda' => 'https://sih3.dpuair.jatimprov.go.id/api/cuaca-arr-pusda',
            'cuaca-awlr-pusda' => 'https://sih3.dpuair.jatimprov.go.id/api/cuaca-awlr-pusda',
            'meteorologi-juanda' => 'https://sih3.dpuair.jatimprov.go.id/api/meteorologi-juanda',
        ];

        if (!isset($urls[$source])) {
            throw new Exception("Unknown source: {$source}");
        }

        $response = Http::timeout($this->timeout)->get($urls[$source]);

        if (!$response->successful()) {
            throw new Exception("API request failed with status: " . $response->status());
        }

        $data = $response->json();
        
        // Extract the actual data based on the API response structure
        if ($source === 'cuaca-arr-pusda' && isset($data['Pos Duga Air Jam-jam an PU SDA'])) {
            return $data['Pos Duga Air Jam-jam an PU SDA'];
        } elseif ($source === 'cuaca-awlr-pusda' && isset($data['Hujan Jam-Jam an PU SDA'])) {
            return $data['Hujan Jam-Jam an PU SDA'];
        } elseif ($source === 'meteorologi-juanda' && isset($data['Data Meteorologi Juanda'])) {
            return $data['Data Meteorologi Juanda'];
        }

        return $data;
    }

    /**
     * Process API data and create/update devices and sensors
     */
    protected function processApiData($data, $source)
    {
        $devicesCreated = 0;
        $sensorsCreated = 0;
        $devicesUpdated = 0;
        $sensorsUpdated = 0;

        DB::beginTransaction();

        try {
            // Get or create a default river basin for imported devices
            $riverBasin = $this->getOrCreateDefaultRiverBasin();
            
            foreach ($data as $item) {
                // Skip items without required data
                if (!isset($item['kode']) || !isset($item['judul']) || !isset($item['long']) || !isset($item['lat'])) {
                    continue;
                }

                // Create or update device
                $deviceResult = $this->createOrUpdateDevice($item, $riverBasin, $source);
                if ($deviceResult['created']) {
                    $devicesCreated++;
                } elseif ($deviceResult['updated']) {
                    $devicesUpdated++;
                }

                // Create or update sensor
                $sensorResult = $this->createOrUpdateSensor($item, $deviceResult['device'], $source);
                if ($sensorResult['created']) {
                    $sensorsCreated++;
                } elseif ($sensorResult['updated']) {
                    $sensorsUpdated++;
                }
            }

            DB::commit();

            return [
                'success' => true,
                'devices_created' => $devicesCreated,
                'sensors_created' => $sensorsCreated,
                'devices_updated' => $devicesUpdated,
                'sensors_updated' => $sensorsUpdated,
                'total_processed' => count($data),
            ];

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Create or update a device from API data
     */
    protected function createOrUpdateDevice($item, $riverBasin, $source)
    {
        $deviceCode = $item['kode'];
        $name = $item['judul'];
        $latitude = (float) $item['lat'];
        $longitude = (float) $item['long'];
        $address = $item['alamat'] ?? '';

        $deviceData = [
            'mas_river_basin_id' => $riverBasin->id,
            'name' => $name,
            'code' => $deviceCode,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'elevation_m' => 0, // Not provided in API
            'status' => 'active',
        ];

        $device = MasDevice::where('code', $deviceCode)->first();

        if ($device) {
            $device->update($deviceData);
            return ['device' => $device, 'created' => false, 'updated' => true];
        } else {
            $device = MasDevice::create($deviceData);
            return ['device' => $device, 'created' => true, 'updated' => false];
        }
    }

    /**
     * Create or update a sensor from API data
     */
    protected function createOrUpdateSensor($item, $device, $source)
    {
        $sensorCode = $device->code . '_SENSOR';
        $parameter = $this->determineParameter($source, $item);
        $unit = $this->determineUnit($source);
        $description = $this->generateSensorDescription($source, $item);

        // Get or create a default model
        $model = $this->getOrCreateDefaultModel($source);

        $sensorData = [
            'device_id' => $device->id,
            'mas_device_code' => $device->code,
            'sensor_code' => $sensorCode,
            'parameter' => $parameter,
            'unit' => $unit,
            'description' => $description,
            'mas_model_id' => $model->id,
            'threshold_safe' => $this->getDefaultThreshold($parameter, 'safe'),
            'threshold_warning' => $this->getDefaultThreshold($parameter, 'warning'),
            'threshold_danger' => $this->getDefaultThreshold($parameter, 'danger'),
            'status' => 'active',
            'forecasting_status' => 'stopped',
            'is_active' => true,
            'last_seen' => isset($item['updated_at']) ? Carbon::parse($item['updated_at']) : now(),
        ];

        $sensor = MasSensor::where('sensor_code', $sensorCode)->first();

        if ($sensor) {
            $sensor->update($sensorData);
            return ['sensor' => $sensor, 'created' => false, 'updated' => true];
        } else {
            $sensor = MasSensor::create($sensorData);
            return ['sensor' => $sensor, 'created' => true, 'updated' => false];
        }
    }

    /**
     * Determine parameter type based on source and data
     */
    protected function determineParameter($source, $item)
    {
        switch ($source) {
            case 'cuaca-arr-pusda':
                return 'water_level'; // AWLR data - water level
            case 'cuaca-awlr-pusda':
                return 'rainfall'; // ARR data - rainfall
            case 'meteorologi-juanda':
                return 'rainfall'; // Meteorological data - rainfall
            default:
                return 'unknown';
        }
    }

    /**
     * Determine unit based on source
     */
    protected function determineUnit($source)
    {
        switch ($source) {
            case 'cuaca-arr-pusda':
                return 'm'; // Water level in meters
            case 'cuaca-awlr-pusda':
            case 'meteorologi-juanda':
                return 'mm'; // Rainfall in millimeters
            default:
                return '';
        }
    }

    /**
     * Generate sensor description
     */
    protected function generateSensorDescription($source, $item)
    {
        $sourceNames = [
            'cuaca-arr-pusda' => 'AWLR PU SDA',
            'cuaca-awlr-pusda' => 'ARR PU SDA',
            'meteorologi-juanda' => 'Meteorologi Juanda',
        ];

        $sourceName = $sourceNames[$source] ?? 'External API';
        $location = $item['alamat'] ?? 'Unknown location';

        return "Sensor from {$sourceName} at {$location}";
    }

    /**
     * Get default thresholds based on parameter type
     */
    protected function getDefaultThreshold($parameter, $level)
    {
        $thresholds = [
            'water_level' => [
                'safe' => 1.0,
                'warning' => 2.0,
                'danger' => 3.0,
            ],
            'rainfall' => [
                'safe' => 10.0,
                'warning' => 50.0,
                'danger' => 100.0,
            ],
        ];

        return $thresholds[$parameter][$level] ?? 0.0;
    }

    /**
     * Get or create default river basin for imported devices
     */
    protected function getOrCreateDefaultRiverBasin()
    {
        $riverBasin = MasRiverBasin::where('river_basins_name', 'Imported from External APIs')->first();

        if (!$riverBasin) {
            // Get the first available city code
            $firstCity = \App\Models\MasCity::first();
            $cityCode = $firstCity ? $firstCity->cities_code : 'MLG001';
            
            $riverBasin = MasRiverBasin::create([
                'river_basins_name' => 'Imported from External APIs',
                'river_basins_code' => 'EXT_API',
                'cities_code' => $cityCode,
            ]);
        }

        return $riverBasin;
    }

    /**
     * Get or create default model for sensors
     */
    protected function getOrCreateDefaultModel($source)
    {
        $modelConfigs = [
            'cuaca-arr-pusda' => ['name' => 'AWLR External Model', 'code' => 'AWLR_EXT'],
            'cuaca-awlr-pusda' => ['name' => 'ARR External Model', 'code' => 'ARR_EXT'],
            'meteorologi-juanda' => ['name' => 'Meteorologi External Model', 'code' => 'METEO_EXT'],
        ];

        $config = $modelConfigs[$source] ?? ['name' => 'External API Model', 'code' => 'EXT_API'];
        
        $model = MasModel::where('name', $config['name'])->first();

        if (!$model) {
            $model = MasModel::create([
                'name' => $config['name'],
                'model_code' => $config['code'],
                'model_type' => 'external', // Type for externally imported models
                'description' => "Default model for {$source} sensors",
                'file_path' => '', // No actual model file
                'is_active' => true,
            ]);
        }

        return $model;
    }

    /**
     * Generate summary of import results
     */
    protected function generateSummary($results)
    {
        $totalDevicesCreated = 0;
        $totalSensorsCreated = 0;
        $totalDevicesUpdated = 0;
        $totalSensorsUpdated = 0;
        $successfulSources = 0;
        $failedSources = [];

        foreach ($results as $source => $result) {
            if ($result['success']) {
                $successfulSources++;
                $totalDevicesCreated += $result['devices_created'];
                $totalSensorsCreated += $result['sensors_created'];
                $totalDevicesUpdated += $result['devices_updated'];
                $totalSensorsUpdated += $result['sensors_updated'];
            } else {
                $failedSources[] = [
                    'source' => $source,
                    'error' => $result['error']
                ];
            }
        }

        return [
            'successful_sources' => $successfulSources,
            'total_devices_created' => $totalDevicesCreated,
            'total_sensors_created' => $totalSensorsCreated,
            'total_devices_updated' => $totalDevicesUpdated,
            'total_sensors_updated' => $totalSensorsUpdated,
            'failed_sources' => $failedSources,
        ];
    }
}