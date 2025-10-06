<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\RiverBasinController;
use App\Http\Controllers\Admin\MasDeviceController;
use App\Http\Controllers\Admin\MasSensorController;
use App\Http\Controllers\Admin\MasModelController;
use App\Http\Controllers\Admin\DataActualController;
use App\Http\Controllers\Admin\DataPredictionController;
use App\Http\Controllers\Admin\GeojsonFileController;
use App\Http\Controllers\Admin\MasProvinceController;
use App\Http\Controllers\Admin\MasCityController;
use App\Http\Controllers\Admin\MasRegencyController;
use App\Http\Controllers\Admin\MasVillageController;
use App\Http\Controllers\Admin\MasWatershedController;
use App\Http\Controllers\Admin\MasUptController;
use App\Http\Controllers\Admin\MasUptdController;
use App\Http\Controllers\Admin\MasDeviceParameterController;
use App\Http\Controllers\Admin\MasSensorParameterController;
use App\Http\Controllers\Admin\MasSensorThresholdController;
use App\Http\Controllers\Admin\MasScalerController;
use App\Http\Controllers\Admin\MasWhatsappNumberController;
use App\Http\Controllers\Admin\RatingCurveController;
use App\Http\Controllers\Admin\CalculatedDischargeController;
use App\Http\Controllers\Admin\PredictedCalculatedDischargeController;

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
|
| Berikut adalah routes untuk admin panel yang dapat diakses
| oleh user yang sudah login dan memiliki role admin
|
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    
    // Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });
    
    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::post('/general', [SettingController::class, 'updateGeneral'])->name('general.update');
        Route::post('/email', [SettingController::class, 'updateEmail'])->name('email.update');
        Route::post('/clear-cache', [SettingController::class, 'clearCache'])->name('cache.clear');
        Route::post('/clear-config', [SettingController::class, 'clearConfig'])->name('config.clear');
    });

    // Data Master - Provinces
    Route::prefix('mas-provinces')->name('mas-provinces.')->group(function () {
        Route::get('/', [MasProvinceController::class, 'index'])->name('index');
        Route::get('/create', [MasProvinceController::class, 'create'])->name('create');
        Route::post('/', [MasProvinceController::class, 'store'])->name('store');
        Route::get('/{masProvince}', [MasProvinceController::class, 'show'])->name('show');
        Route::get('/{masProvince}/edit', [MasProvinceController::class, 'edit'])->name('edit');
        Route::put('/{masProvince}', [MasProvinceController::class, 'update'])->name('update');
        Route::delete('/{masProvince}', [MasProvinceController::class, 'destroy'])->name('destroy');
    });

    // Data Master - Cities
    Route::prefix('mas-cities')->name('mas-cities.')->group(function () {
        Route::get('/', [MasCityController::class, 'index'])->name('index');
        Route::get('/create', [MasCityController::class, 'create'])->name('create');
        Route::post('/', [MasCityController::class, 'store'])->name('store');
        Route::get('/{masCity}', [MasCityController::class, 'show'])->name('show');
        Route::get('/{masCity}/edit', [MasCityController::class, 'edit'])->name('edit');
        Route::put('/{masCity}', [MasCityController::class, 'update'])->name('update');
        Route::delete('/{masCity}', [MasCityController::class, 'destroy'])->name('destroy');
    });

    // Data Master - Regencies
    Route::prefix('mas-regencies')->name('mas-regencies.')->group(function () {
        Route::get('/', [MasRegencyController::class, 'index'])->name('index');
        Route::get('/create', [MasRegencyController::class, 'create'])->name('create');
        Route::post('/', [MasRegencyController::class, 'store'])->name('store');
        Route::get('/{masRegency}', [MasRegencyController::class, 'show'])->name('show');
        Route::get('/{masRegency}/edit', [MasRegencyController::class, 'edit'])->name('edit');
        Route::put('/{masRegency}', [MasRegencyController::class, 'update'])->name('update');
        Route::delete('/{masRegency}', [MasRegencyController::class, 'destroy'])->name('destroy');
    });

    // Data Master - Villages
    Route::prefix('mas-villages')->name('mas-villages.')->group(function () {
        Route::get('/', [MasVillageController::class, 'index'])->name('index');
        Route::get('/create', [MasVillageController::class, 'create'])->name('create');
        Route::post('/', [MasVillageController::class, 'store'])->name('store');
        Route::get('/{masVillage}', [MasVillageController::class, 'show'])->name('show');
        Route::get('/{masVillage}/edit', [MasVillageController::class, 'edit'])->name('edit');
        Route::put('/{masVillage}', [MasVillageController::class, 'update'])->name('update');
        Route::delete('/{masVillage}', [MasVillageController::class, 'destroy'])->name('destroy');
    });

    // Data Master - Watersheds
    Route::prefix('mas-watersheds')->name('mas-watersheds.')->group(function () {
        Route::get('/', [MasWatershedController::class, 'index'])->name('index');
        Route::get('/create', [MasWatershedController::class, 'create'])->name('create');
        Route::post('/', [MasWatershedController::class, 'store'])->name('store');
        Route::get('/{masWatershed}', [MasWatershedController::class, 'show'])->name('show');
        Route::get('/{masWatershed}/edit', [MasWatershedController::class, 'edit'])->name('edit');
        Route::put('/{masWatershed}', [MasWatershedController::class, 'update'])->name('update');
        Route::delete('/{masWatershed}', [MasWatershedController::class, 'destroy'])->name('destroy');
    });

    // Data Master - UPTs
    Route::prefix('mas-upts')->name('mas-upts.')->group(function () {
        Route::get('/', [MasUptController::class, 'index'])->name('index');
        Route::get('/create', [MasUptController::class, 'create'])->name('create');
        Route::post('/', [MasUptController::class, 'store'])->name('store');
        Route::get('/{masUpt}', [MasUptController::class, 'show'])->name('show');
        Route::get('/{masUpt}/edit', [MasUptController::class, 'edit'])->name('edit');
        Route::put('/{masUpt}', [MasUptController::class, 'update'])->name('update');
        Route::delete('/{masUpt}', [MasUptController::class, 'destroy'])->name('destroy');
    });

    // Data Master - UPTDs
    Route::prefix('mas-uptds')->name('mas-uptds.')->group(function () {
        Route::get('/', [MasUptdController::class, 'index'])->name('index');
        Route::get('/create', [MasUptdController::class, 'create'])->name('create');
        Route::post('/', [MasUptdController::class, 'store'])->name('store');
        Route::get('/{masUptd}', [MasUptdController::class, 'show'])->name('show');
        Route::get('/{masUptd}/edit', [MasUptdController::class, 'edit'])->name('edit');
        Route::put('/{masUptd}', [MasUptdController::class, 'update'])->name('update');
        Route::delete('/{masUptd}', [MasUptdController::class, 'destroy'])->name('destroy');
    });

    // Data Master - Device Parameters
    Route::prefix('mas-device-parameters')->name('mas-device-parameters.')->group(function () {
        Route::get('/', [MasDeviceParameterController::class, 'index'])->name('index');
        Route::get('/create', [MasDeviceParameterController::class, 'create'])->name('create');
        Route::post('/', [MasDeviceParameterController::class, 'store'])->name('store');
        Route::get('/{masDeviceParameter}', [MasDeviceParameterController::class, 'show'])->name('show');
        Route::get('/{masDeviceParameter}/edit', [MasDeviceParameterController::class, 'edit'])->name('edit');
        Route::put('/{masDeviceParameter}', [MasDeviceParameterController::class, 'update'])->name('update');
        Route::delete('/{masDeviceParameter}', [MasDeviceParameterController::class, 'destroy'])->name('destroy');
    });

    // Data Master - Sensor Parameters
    Route::prefix('mas-sensor-parameters')->name('mas-sensor-parameters.')->group(function () {
        Route::get('/', [MasSensorParameterController::class, 'index'])->name('index');
        Route::get('/create', [MasSensorParameterController::class, 'create'])->name('create');
        Route::post('/', [MasSensorParameterController::class, 'store'])->name('store');
        Route::get('/{masSensorParameter}', [MasSensorParameterController::class, 'show'])->name('show');
        Route::get('/{masSensorParameter}/edit', [MasSensorParameterController::class, 'edit'])->name('edit');
        Route::put('/{masSensorParameter}', [MasSensorParameterController::class, 'update'])->name('update');
        Route::delete('/{masSensorParameter}', [MasSensorParameterController::class, 'destroy'])->name('destroy');
    });

    // Data Master - Sensor Thresholds
    Route::prefix('mas-sensor-thresholds')->name('mas-sensor-thresholds.')->group(function () {
        Route::get('/', [MasSensorThresholdController::class, 'index'])->name('index');
        Route::get('/create', [MasSensorThresholdController::class, 'create'])->name('create');
        Route::post('/', [MasSensorThresholdController::class, 'store'])->name('store');
        Route::get('/{masSensorThreshold}', [MasSensorThresholdController::class, 'show'])->name('show');
        Route::get('/{masSensorThreshold}/edit', [MasSensorThresholdController::class, 'edit'])->name('edit');
        Route::put('/{masSensorThreshold}', [MasSensorThresholdController::class, 'update'])->name('update');
        Route::delete('/{masSensorThreshold}', [MasSensorThresholdController::class, 'destroy'])->name('destroy');
    });

    // Data Master - Scalers
    Route::prefix('mas-scalers')->name('mas-scalers.')->group(function () {
        Route::get('/', [MasScalerController::class, 'index'])->name('index');
        Route::get('/create', [MasScalerController::class, 'create'])->name('create');
        Route::post('/', [MasScalerController::class, 'store'])->name('store');
        Route::get('/{masScaler}', [MasScalerController::class, 'show'])->name('show');
        Route::get('/{masScaler}/edit', [MasScalerController::class, 'edit'])->name('edit');
        Route::put('/{masScaler}', [MasScalerController::class, 'update'])->name('update');
        Route::delete('/{masScaler}', [MasScalerController::class, 'destroy'])->name('destroy');
        Route::get('/{masScaler}/download', [MasScalerController::class, 'download'])->name('download');
        Route::post('/{masScaler}/toggle-status', [MasScalerController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Data Master - WhatsApp Numbers
    Route::prefix('mas-whatsapp-numbers')->name('mas-whatsapp-numbers.')->group(function () {
        Route::get('/', [MasWhatsappNumberController::class, 'index'])->name('index');
        Route::get('/create', [MasWhatsappNumberController::class, 'create'])->name('create');
        Route::post('/', [MasWhatsappNumberController::class, 'store'])->name('store');
        Route::get('/export', [MasWhatsappNumberController::class, 'export'])->name('export');
        Route::get('/{masWhatsappNumber}', [MasWhatsappNumberController::class, 'show'])->name('show');
        Route::get('/{masWhatsappNumber}/edit', [MasWhatsappNumberController::class, 'edit'])->name('edit');
        Route::put('/{masWhatsappNumber}', [MasWhatsappNumberController::class, 'update'])->name('update');
        Route::delete('/{masWhatsappNumber}', [MasWhatsappNumberController::class, 'destroy'])->name('destroy');
        Route::post('/{masWhatsappNumber}/test-message', [MasWhatsappNumberController::class, 'testMessage'])->name('test-message');
    });

    // Rating Curves
    Route::prefix('rating-curves')->name('rating-curves.')->group(function () {
        Route::get('/', [RatingCurveController::class, 'index'])->name('index');
        Route::get('/create', [RatingCurveController::class, 'create'])->name('create');
        Route::post('/', [RatingCurveController::class, 'store'])->name('store');
        Route::get('/{ratingCurve}', [RatingCurveController::class, 'show'])->name('show');
        Route::get('/{ratingCurve}/edit', [RatingCurveController::class, 'edit'])->name('edit');
        Route::put('/{ratingCurve}', [RatingCurveController::class, 'update'])->name('update');
        Route::delete('/{ratingCurve}', [RatingCurveController::class, 'destroy'])->name('destroy');
        Route::post('/{ratingCurve}/test-calculation', [RatingCurveController::class, 'testCalculation'])->name('test-calculation');
        Route::post('/{ratingCurve}/generate-sample', [RatingCurveController::class, 'generateSample'])->name('generate-sample');
    });

    // Calculated Discharges
    Route::prefix('calculated-discharges')->name('calculated-discharges.')->group(function () {
        Route::get('/', [CalculatedDischargeController::class, 'index'])->name('index');
        Route::get('/create', [CalculatedDischargeController::class, 'create'])->name('create');
        Route::post('/', [CalculatedDischargeController::class, 'store'])->name('store');
        Route::get('/export', [CalculatedDischargeController::class, 'export'])->name('export');
        Route::get('/{calculatedDischarge}', [CalculatedDischargeController::class, 'show'])->name('show');
        Route::get('/{calculatedDischarge}/edit', [CalculatedDischargeController::class, 'edit'])->name('edit');
        Route::put('/{calculatedDischarge}', [CalculatedDischargeController::class, 'update'])->name('update');
        Route::delete('/{calculatedDischarge}', [CalculatedDischargeController::class, 'destroy'])->name('destroy');
    });

    // Predicted Calculated Discharges
    Route::prefix('predicted-calculated-discharges')->name('predicted-calculated-discharges.')->group(function () {
        Route::get('/', [PredictedCalculatedDischargeController::class, 'index'])->name('index');
        Route::get('/create', [PredictedCalculatedDischargeController::class, 'create'])->name('create');
        Route::post('/', [PredictedCalculatedDischargeController::class, 'store'])->name('store');
        Route::get('/export', [PredictedCalculatedDischargeController::class, 'export'])->name('export');
        Route::get('/{predictedCalculatedDischarge}', [PredictedCalculatedDischargeController::class, 'show'])->name('show');
        Route::get('/{predictedCalculatedDischarge}/edit', [PredictedCalculatedDischargeController::class, 'edit'])->name('edit');
        Route::put('/{predictedCalculatedDischarge}', [PredictedCalculatedDischargeController::class, 'update'])->name('update');
        Route::delete('/{predictedCalculatedDischarge}', [PredictedCalculatedDischargeController::class, 'destroy'])->name('destroy');
    });

    // Data Region
    Route::prefix('region')->name('region.')->group(function () {
        // Halaman data region
        Route::view('/kabupaten', 'admin.region.kabupaten')->name('kabupaten');
        Route::view('/kecamatan', 'admin.region.kecamatan')->name('kecamatan');
        Route::view('/desa', 'admin.region.desa')->name('desa');

        // CRUD DAS (River Basins) - Menggunakan modal
        Route::prefix('river-basins')->name('river-basins.')->group(function () {
            Route::get('/', [RiverBasinController::class, 'index'])->name('index');
            Route::post('/', [RiverBasinController::class, 'store'])->name('store');
            Route::put('/{river_basin}', [RiverBasinController::class, 'update'])->name('update');
            Route::delete('/{river_basin}', [RiverBasinController::class, 'destroy'])->name('destroy');
        });
    });

    // Data Master (Devices)
    Route::prefix('devices')->name('devices.')->group(function () {
        Route::get('/', [MasDeviceController::class, 'index'])->name('index');
        Route::post('/', [MasDeviceController::class, 'store'])->name('store');
        Route::put('/{id}', [MasDeviceController::class, 'update'])->name('update');
        Route::delete('/{id}', [MasDeviceController::class, 'destroy'])->name('destroy');
    });

    // Data Master (Sensors)
    Route::prefix('sensors')->name('sensors.')->group(function () {
        Route::get('/', [MasSensorController::class, 'index'])->name('index');
        Route::get('/create', [MasSensorController::class, 'create'])->name('create');
        Route::post('/', [MasSensorController::class, 'store'])->name('store');
        Route::get('/{sensor}', [MasSensorController::class, 'show'])->name('show');
        Route::get('/{sensor}/edit', [MasSensorController::class, 'edit'])->name('edit');
        Route::put('/{sensor}', [MasSensorController::class, 'update'])->name('update');
        Route::delete('/{sensor}', [MasSensorController::class, 'destroy'])->name('destroy');
        
        // Forecasting control routes
        Route::post('/{sensor}/start-forecasting', [MasSensorController::class, 'startForecasting'])->name('start-forecasting');
        Route::post('/{sensor}/pause-forecasting', [MasSensorController::class, 'pauseForecasting'])->name('pause-forecasting');
        Route::post('/{sensor}/stop-forecasting', [MasSensorController::class, 'stopForecasting'])->name('stop-forecasting');
    });

    // Data Master (Models)
    Route::prefix('mas-models')->name('mas-models.')->group(function () {
        Route::get('/', [MasModelController::class, 'index'])->name('index');
        Route::get('/create', [MasModelController::class, 'create'])->name('create');
        Route::get('/form', [MasModelController::class, 'form'])->name('form');
        Route::get('/form/{masModel}', [MasModelController::class, 'form'])->name('form.edit');
        Route::post('/', [MasModelController::class, 'store'])->name('store');
        Route::get('/export', [MasModelController::class, 'export'])->name('export');
        Route::post('/import', [MasModelController::class, 'import'])->name('import');
        Route::get('/{masModel}/edit', [MasModelController::class, 'edit'])->name('edit');
        Route::put('/{masModel}', [MasModelController::class, 'update'])->name('update');
        Route::delete('/{masModel}', [MasModelController::class, 'destroy'])->name('destroy');
        Route::post('/{masModel}/toggle-status', [MasModelController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/{masModel}', [MasModelController::class, 'show'])->name('show');
    });

    // Data Actuals
    Route::prefix('data-actuals')->name('data-actuals.')->group(function () {
        Route::get('/', [DataActualController::class, 'index'])->name('index');
        Route::get('/create', [DataActualController::class, 'create'])->name('create');
        Route::post('/', [DataActualController::class, 'store'])->name('store');
        Route::get('/export/csv', [DataActualController::class, 'export'])->name('export');
        Route::get('/chart/data', [DataActualController::class, 'chartData'])->name('chart.data');
        Route::get('/{dataActual}', [DataActualController::class, 'show'])->name('show');
        Route::get('/{dataActual}/edit', [DataActualController::class, 'edit'])->name('edit');
        Route::put('/{dataActual}', [DataActualController::class, 'update'])->name('update');
        Route::delete('/{dataActual}', [DataActualController::class, 'destroy'])->name('destroy');
    });

    // Data Predictions
    Route::prefix('data_predictions')->name('data_predictions.')->group(function () {
        Route::get('/', [DataPredictionController::class, 'index'])->name('index');
        Route::get('/create', [DataPredictionController::class, 'create'])->name('create');
        Route::post('/', [DataPredictionController::class, 'store'])->name('store');
        Route::get('/{dataPrediction}', [DataPredictionController::class, 'show'])->name('show');
        Route::get('/{dataPrediction}/edit', [DataPredictionController::class, 'edit'])->name('edit');
        Route::put('/{dataPrediction}', [DataPredictionController::class, 'update'])->name('update');
        Route::delete('/{dataPrediction}', [DataPredictionController::class, 'destroy'])->name('destroy');
    });

    // GeoJSON Files Management
    Route::prefix('geojson-files')->name('geojson-files.')->group(function () {
        Route::get('/', [GeojsonFileController::class, 'index'])->name('index');
        Route::get('/create', [GeojsonFileController::class, 'create'])->name('create');
        Route::post('/', [GeojsonFileController::class, 'store'])->name('store');
        Route::get('/{geojsonFile}', [GeojsonFileController::class, 'show'])->name('show');
        Route::get('/{geojsonFile}/edit', [GeojsonFileController::class, 'edit'])->name('edit');
        Route::put('/{geojsonFile}', [GeojsonFileController::class, 'update'])->name('update');
        Route::delete('/{geojsonFile}', [GeojsonFileController::class, 'destroy'])->name('destroy');
        Route::get('/{geojsonFile}/download', [GeojsonFileController::class, 'download'])->name('download');
    });

    // Profile & Account
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [AdminController::class, 'profile'])->name('index');
        Route::get('/edit', [AdminController::class, 'profile'])->name('edit');
        Route::put('/', [AdminController::class, 'updateProfile'])->name('update');
        Route::put('/password', [AdminController::class, 'updatePassword'])->name('password.update');
    });
    
    // Logout
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
});

// Fallback route untuk admin yang tidak ditemukan
Route::fallback(function () {
    if (request()->is('admin/*')) {
        return redirect()->route('admin.dashboard');
    }
});

Route::get('/phpinfo', fn() => phpinfo());