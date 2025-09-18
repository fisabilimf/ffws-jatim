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
use App\Http\Controllers\Api\Admin\DataPredictionController;

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
