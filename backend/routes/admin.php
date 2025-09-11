<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\RiverBasinController;
use App\Http\Controllers\Admin\MasDeviceController;
use App\Http\Controllers\Admin\MasSensorController;

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
