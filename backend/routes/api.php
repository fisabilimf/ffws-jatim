<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\Admin\MasDeviceController;
use App\Http\Controllers\Api\Admin\MasSensorController;
use App\Http\Controllers\Api\Admin\RiverBasinController;

// Geographic Controllers
use App\Http\Controllers\Api\Admin\MasProvinceController;
use App\Http\Controllers\Api\Admin\MasCityController;
use App\Http\Controllers\Api\Admin\MasRegencyController;
use App\Http\Controllers\Api\Admin\MasVillageController;
use App\Http\Controllers\Api\Admin\MasWatershedController;

// Administrative Controllers
use App\Http\Controllers\Api\Admin\MasUptController;
use App\Http\Controllers\Api\Admin\MasUptdController;
use App\Http\Controllers\Api\Admin\UserByRoleController;

// Parameter Controllers
use App\Http\Controllers\Api\Admin\MasDeviceParameterController;
use App\Http\Controllers\Api\Admin\MasSensorParameterController;
use App\Http\Controllers\Api\Admin\MasSensorThresholdController;

// Complex Data Controllers
use App\Http\Controllers\Api\Admin\RatingCurveController;
use App\Http\Controllers\Api\Admin\CalculatedDischargeController;
use App\Http\Controllers\Api\Admin\PredictedCalculatedDischargeController;
use App\Http\Controllers\Api\Admin\DeviceValueController;
use App\Http\Controllers\Api\Admin\SensorValueController;

// Forecasting Controller
use App\Http\Controllers\Api\Admin\ForecastingController;

// External API Controller
use App\Http\Controllers\Api\Admin\ExternalApiController;
use App\Http\Controllers\Api\Admin\DeviceSensorImportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes (no authentication required)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Public data routes for frontend testing (no authentication required)
Route::prefix('public')->group(function () {
    Route::get('/provinces', [MasProvinceController::class, 'index']);
    Route::get('/cities', [MasCityController::class, 'index']);
    Route::get('/regencies', [MasRegencyController::class, 'index']);
    Route::get('/villages', [MasVillageController::class, 'index']);
    Route::get('/river-basins', [RiverBasinController::class, 'index']);
    Route::get('/devices', [MasDeviceController::class, 'index']);
    Route::get('/sensors', [MasSensorController::class, 'index']);
    
    // Public geojson mapping endpoint for testing
    Route::post('/geojson-mapping/by-discharge', [\App\Http\Controllers\Api\Admin\GeojsonMappingController::class, 'getGeojsonByDischarge']);
});

// Protected routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });

    // User routes
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });

    // Data routes (contoh untuk data actuals)
    Route::prefix('data')->group(function () {
        Route::get('/actuals', function () {
            return response()->json([
                'success' => true,
                'message' => 'Data actuals endpoint - implementasi selanjutnya'
            ]);
        });
    });

    // Device routes
    Route::prefix('devices')->group(function () {
        Route::get('/', [MasDeviceController::class, 'index']);
        Route::get('/{id}', [MasDeviceController::class, 'show']);
    });

    // Sensor routes
    Route::prefix('sensors')->group(function () {
        Route::get('/', [MasSensorController::class, 'index']);
        Route::get('/{id}', [MasSensorController::class, 'show']);
        Route::get('/device/{deviceId}', [MasSensorController::class, 'getByDevice']);
        Route::get('/parameter/{parameter}', [MasSensorController::class, 'getByParameter']);
        Route::get('/status/{status}', [MasSensorController::class, 'getByStatus']);
    });

    // River Basin routes
    Route::prefix('river-basins')->group(function () {
        Route::get('/{id}', [RiverBasinController::class, 'show']);
    });

    // ===========================================
    // GEOGRAPHIC ROUTES - Master Data
    // ===========================================
    
    // Province routes
    Route::prefix('provinces')->group(function () {
        Route::get('/', [MasProvinceController::class, 'index']);
        Route::post('/', [MasProvinceController::class, 'store']);
        Route::get('/{id}', [MasProvinceController::class, 'show']);
        Route::put('/{id}', [MasProvinceController::class, 'update']);
        Route::delete('/{id}', [MasProvinceController::class, 'destroy']);
        Route::get('/active/list', [MasProvinceController::class, 'getActive']);
    });

    // City routes
    Route::prefix('cities')->group(function () {
        Route::get('/', [MasCityController::class, 'index']);
        Route::post('/', [MasCityController::class, 'store']);
        Route::get('/{id}', [MasCityController::class, 'show']);
        Route::put('/{id}', [MasCityController::class, 'update']);
        Route::delete('/{id}', [MasCityController::class, 'destroy']);
        Route::get('/province/{provinceCode}', [MasCityController::class, 'getByProvince']);
        Route::get('/active/list', [MasCityController::class, 'getActive']);
    });

    // Regency routes
    Route::prefix('regencies')->group(function () {
        Route::get('/', [MasRegencyController::class, 'index']);
        Route::post('/', [MasRegencyController::class, 'store']);
        Route::get('/{id}', [MasRegencyController::class, 'show']);
        Route::put('/{id}', [MasRegencyController::class, 'update']);
        Route::delete('/{id}', [MasRegencyController::class, 'destroy']);
        Route::get('/city/{cityCode}', [MasRegencyController::class, 'getByCity']);
        Route::get('/active/list', [MasRegencyController::class, 'getActive']);
    });

    // Village routes
    Route::prefix('villages')->group(function () {
        Route::get('/', [MasVillageController::class, 'index']);
        Route::post('/', [MasVillageController::class, 'store']);
        Route::get('/{id}', [MasVillageController::class, 'show']);
        Route::put('/{id}', [MasVillageController::class, 'update']);
        Route::delete('/{id}', [MasVillageController::class, 'destroy']);
        Route::get('/regency/{regencyCode}', [MasVillageController::class, 'getByRegency']);
        Route::get('/active/list', [MasVillageController::class, 'getActive']);
    });

    // Watershed routes
    Route::prefix('watersheds')->group(function () {
        Route::get('/', [MasWatershedController::class, 'index']);
        Route::post('/', [MasWatershedController::class, 'store']);
        Route::get('/{id}', [MasWatershedController::class, 'show']);
        Route::put('/{id}', [MasWatershedController::class, 'update']);
        Route::delete('/{id}', [MasWatershedController::class, 'destroy']);
        Route::get('/village/{villageCode}', [MasWatershedController::class, 'getByVillage']);
        Route::get('/river-basin/{riverBasinCode}', [MasWatershedController::class, 'getByRiverBasin']);
        Route::get('/active/list', [MasWatershedController::class, 'getActive']);
    });

    // ===========================================
    // ADMINISTRATIVE ROUTES - Management
    // ===========================================
    
    // UPT routes
    Route::prefix('upts')->group(function () {
        Route::get('/', [MasUptController::class, 'index']);
        Route::post('/', [MasUptController::class, 'store']);
        Route::get('/{id}', [MasUptController::class, 'show']);
        Route::put('/{id}', [MasUptController::class, 'update']);
        Route::delete('/{id}', [MasUptController::class, 'destroy']);
        Route::get('/river-basin/{riverBasinCode}', [MasUptController::class, 'getByRiverBasin']);
        Route::get('/city/{cityCode}', [MasUptController::class, 'getByCity']);
        Route::get('/active/list', [MasUptController::class, 'getActive']);
    });

    // UPTD routes
    Route::prefix('uptds')->group(function () {
        Route::get('/', [MasUptdController::class, 'index']);
        Route::post('/', [MasUptdController::class, 'store']);
        Route::get('/{id}', [MasUptdController::class, 'show']);
        Route::put('/{id}', [MasUptdController::class, 'update']);
        Route::delete('/{id}', [MasUptdController::class, 'destroy']);
        Route::get('/upt/{uptCode}', [MasUptdController::class, 'getByUpt']);
        Route::get('/active/list', [MasUptdController::class, 'getActive']);
    });

    // User by Role routes
    Route::prefix('user-roles')->group(function () {
        Route::get('/', [UserByRoleController::class, 'index']);
        Route::post('/', [UserByRoleController::class, 'store']);
        Route::get('/{id}', [UserByRoleController::class, 'show']);
        Route::put('/{id}', [UserByRoleController::class, 'update']);
        Route::delete('/{id}', [UserByRoleController::class, 'destroy']);
        Route::get('/role/{role}', [UserByRoleController::class, 'getByRole']);
        Route::get('/status/{status}', [UserByRoleController::class, 'getByStatus']);
        Route::get('/upt/{uptCode}', [UserByRoleController::class, 'getByUpt']);
    });

    // ===========================================
    // PARAMETER ROUTES - Configuration
    // ===========================================
    
    // Device Parameter routes
    Route::prefix('device-parameters')->group(function () {
        Route::get('/', [MasDeviceParameterController::class, 'index']);
        Route::post('/', [MasDeviceParameterController::class, 'store']);
        Route::get('/{id}', [MasDeviceParameterController::class, 'show']);
        Route::put('/{id}', [MasDeviceParameterController::class, 'update']);
        Route::delete('/{id}', [MasDeviceParameterController::class, 'destroy']);
        Route::get('/device/{deviceCode}', [MasDeviceParameterController::class, 'getByDevice']);
        Route::get('/sensor/{sensorCode}', [MasDeviceParameterController::class, 'getBySensor']);
        Route::get('/primary/list', [MasDeviceParameterController::class, 'getPrimary']);
        Route::get('/active/list', [MasDeviceParameterController::class, 'getActive']);
    });

    // Sensor Parameter routes
    Route::prefix('sensor-parameters')->group(function () {
        Route::get('/', [MasSensorParameterController::class, 'index']);
        Route::post('/', [MasSensorParameterController::class, 'store']);
        Route::get('/{id}', [MasSensorParameterController::class, 'show']);
        Route::put('/{id}', [MasSensorParameterController::class, 'update']);
        Route::delete('/{id}', [MasSensorParameterController::class, 'destroy']);
        Route::get('/sensor/{sensorCode}', [MasSensorParameterController::class, 'getBySensor']);
        Route::get('/parameter/{parameterName}', [MasSensorParameterController::class, 'getByParameterName']);
        Route::get('/unit/{unit}', [MasSensorParameterController::class, 'getByUnit']);
        Route::get('/active/list', [MasSensorParameterController::class, 'getActive']);
    });

    // Sensor Threshold routes
    Route::prefix('sensor-thresholds')->group(function () {
        Route::get('/', [MasSensorThresholdController::class, 'index']);
        Route::post('/', [MasSensorThresholdController::class, 'store']);
        Route::get('/{id}', [MasSensorThresholdController::class, 'show']);
        Route::put('/{id}', [MasSensorThresholdController::class, 'update']);
        Route::delete('/{id}', [MasSensorThresholdController::class, 'destroy']);
        Route::get('/sensor/{sensorCode}', [MasSensorThresholdController::class, 'getBySensor']);
        Route::get('/active/list', [MasSensorThresholdController::class, 'getActive']);
        Route::post('/check/{sensorCode}', [MasSensorThresholdController::class, 'checkThreshold']);
    });

    // ===========================================
    // DATA ROUTES - Operational Data
    // ===========================================
    
    // Rating Curve routes
    Route::prefix('rating-curves')->group(function () {
        Route::get('/', [RatingCurveController::class, 'index']);
        Route::post('/', [RatingCurveController::class, 'store']);
        Route::get('/{id}', [RatingCurveController::class, 'show']);
        Route::put('/{id}', [RatingCurveController::class, 'update']);
        Route::delete('/{id}', [RatingCurveController::class, 'destroy']);
        Route::get('/station/{stationCode}', [RatingCurveController::class, 'getByStation']);
        Route::get('/active/list', [RatingCurveController::class, 'getActive']);
        Route::post('/calculate/{stationCode}', [RatingCurveController::class, 'calculateDischarge']);
        Route::post('/bulk-import', [RatingCurveController::class, 'bulkImport']);
    });

    // Calculated Discharge routes
    Route::prefix('calculated-discharges')->group(function () {
        Route::get('/', [CalculatedDischargeController::class, 'index']);
        Route::post('/', [CalculatedDischargeController::class, 'store']);
        Route::get('/{id}', [CalculatedDischargeController::class, 'show']);
        Route::put('/{id}', [CalculatedDischargeController::class, 'update']);
        Route::delete('/{id}', [CalculatedDischargeController::class, 'destroy']);
        Route::get('/device/{deviceCode}', [CalculatedDischargeController::class, 'getByDevice']);
        Route::get('/sensor/{sensorCode}', [CalculatedDischargeController::class, 'getBySensor']);
        Route::get('/date-range', [CalculatedDischargeController::class, 'getByDateRange']);
        Route::get('/latest/list', [CalculatedDischargeController::class, 'getLatest']);
        Route::get('/statistics', [CalculatedDischargeController::class, 'getStatistics']);
        Route::post('/bulk-import', [CalculatedDischargeController::class, 'bulkImport']);
    });

    // Predicted Calculated Discharge routes
    Route::prefix('predicted-discharges')->group(function () {
        Route::get('/', [PredictedCalculatedDischargeController::class, 'index']);
        Route::post('/', [PredictedCalculatedDischargeController::class, 'store']);
        Route::get('/{id}', [PredictedCalculatedDischargeController::class, 'show']);
        Route::put('/{id}', [PredictedCalculatedDischargeController::class, 'update']);
        Route::delete('/{id}', [PredictedCalculatedDischargeController::class, 'destroy']);
        Route::get('/device/{deviceCode}', [PredictedCalculatedDischargeController::class, 'getByDevice']);
        Route::get('/sensor/{sensorCode}', [PredictedCalculatedDischargeController::class, 'getBySensor']);
        Route::get('/model/{modelCode}', [PredictedCalculatedDischargeController::class, 'getByModel']);
        Route::get('/date-range', [PredictedCalculatedDischargeController::class, 'getByDateRange']);
        Route::get('/latest/list', [PredictedCalculatedDischargeController::class, 'getLatest']);
        Route::get('/future/list', [PredictedCalculatedDischargeController::class, 'getFuturePredictions']);
        Route::get('/statistics', [PredictedCalculatedDischargeController::class, 'getStatistics']);
        Route::post('/bulk-import', [PredictedCalculatedDischargeController::class, 'bulkImport']);
    });

    // Device Value routes
    Route::prefix('device-values')->group(function () {
        Route::get('/', [DeviceValueController::class, 'index']);
        Route::post('/', [DeviceValueController::class, 'store']);
        Route::get('/{id}', [DeviceValueController::class, 'show']);
        Route::put('/{id}', [DeviceValueController::class, 'update']);
        Route::delete('/{id}', [DeviceValueController::class, 'destroy']);
        Route::get('/device/{deviceCode}', [DeviceValueController::class, 'getByDevice']);
        Route::get('/date-range', [DeviceValueController::class, 'getByDateRange']);
        Route::get('/latest/list', [DeviceValueController::class, 'getLatest']);
        Route::get('/statistics', [DeviceValueController::class, 'getStatistics']);
        Route::post('/bulk-import', [DeviceValueController::class, 'bulkImport']);
    });

    // Sensor Value routes
    Route::prefix('sensor-values')->group(function () {
        Route::get('/', [SensorValueController::class, 'index']);
        Route::post('/', [SensorValueController::class, 'store']);
        Route::get('/{id}', [SensorValueController::class, 'show']);
        Route::put('/{id}', [SensorValueController::class, 'update']);
        Route::delete('/{id}', [SensorValueController::class, 'destroy']);
        Route::get('/device/{deviceCode}', [SensorValueController::class, 'getByDevice']);
        Route::get('/sensor/{sensorCode}', [SensorValueController::class, 'getBySensor']);
        Route::get('/device/{deviceCode}/sensor/{sensorCode}', [SensorValueController::class, 'getByDeviceAndSensor']);
        Route::get('/date-range', [SensorValueController::class, 'getByDateRange']);
        Route::get('/latest/list', [SensorValueController::class, 'getLatest']);
        Route::get('/statistics', [SensorValueController::class, 'getStatistics']);
        Route::get('/check-thresholds', [SensorValueController::class, 'checkThresholds']);
        Route::post('/bulk-import', [SensorValueController::class, 'bulkImport']);
    });
 
    // GeoJSON files - list and content
    Route::prefix('geojson-files')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\Admin\GeojsonFileController::class, 'index']);
        Route::get('/{id}/content', [\App\Http\Controllers\Api\Admin\GeojsonFileController::class, 'content']);
    });

    // GeoJSON mapping - dynamic geojson loading based on discharge values
    Route::prefix('geojson-mapping')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\Admin\GeojsonMappingController::class, 'index']);
        Route::post('/by-discharge', [\App\Http\Controllers\Api\Admin\GeojsonMappingController::class, 'getGeojsonByDischarge']);
        Route::get('/sensor/{sensorCode}/latest', [\App\Http\Controllers\Api\Admin\GeojsonMappingController::class, 'getGeojsonByLatestDischarge']);
        Route::get('/sensor/{sensorCode}/predicted', [\App\Http\Controllers\Api\Admin\GeojsonMappingController::class, 'getGeojsonByLatestPredictedDischarge']);
        Route::get('/sensor/{sensorCode}/mappings', [\App\Http\Controllers\Api\Admin\GeojsonMappingController::class, 'getGeojsonMappingsForSensor']);
    });

    // Forecasting API - ML predictions and flood forecasting
    Route::prefix('forecasting')->group(function () {
        Route::get('/health', [ForecastingController::class, 'health']);
        Route::get('/models', [ForecastingController::class, 'listModels']);
        Route::get('/sensors', [ForecastingController::class, 'listForecastingSensors']);
        Route::post('/run', [ForecastingController::class, 'runForecast']);
        Route::post('/run-basin', [ForecastingController::class, 'runBasinForecast']);
        Route::post('/fallback', [ForecastingController::class, 'runFallbackForecast']);
        Route::get('/predictions/{sensorCode}', [ForecastingController::class, 'getLatestPredictions']);
        Route::get('/predictions/{sensorCode}/with-geojson', [ForecastingController::class, 'getPredictionsWithGeojson']);
    });

    // External API Gateway - Sync data from external sources
    Route::prefix('external-api')->group(function () {
        Route::get('/info', [ExternalApiController::class, 'info']);
        Route::get('/status', [ExternalApiController::class, 'status']);
        Route::get('/test-connectivity', [ExternalApiController::class, 'testConnectivity']);
        Route::post('/sync-all', [ExternalApiController::class, 'syncAll']);
        Route::post('/sync/{source}', [ExternalApiController::class, 'syncSource']);
        Route::get('/latest-data', [ExternalApiController::class, 'latestData']);
        Route::get('/stations', [ExternalApiController::class, 'getStations']);
        Route::get('/sensor/{sensorCode}', [ExternalApiController::class, 'getBySensorCode']);
        Route::post('/cleanup', [ExternalApiController::class, 'cleanup']);
        
        // Device and Sensor Import routes
        Route::prefix('import')->group(function () {
            Route::get('/preview', [DeviceSensorImportController::class, 'preview']);
            Route::post('/devices-sensors', [DeviceSensorImportController::class, 'importAll']);
            Route::get('/status', [DeviceSensorImportController::class, 'status']);
        });
    });
});

// Test route untuk memastikan API berjalan
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API berjalan dengan baik'
    ]);
});
