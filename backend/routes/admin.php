<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;

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

    // Master Data
    Route::prefix('master')->name('master.')->group(function () {
        // Halaman data wilayah
        Route::view('/kabupaten', 'admin.master.kabupaten')->name('kabupaten');
        Route::view('/kecamatan', 'admin.master.kecamatan')->name('kecamatan');
        Route::view('/desa', 'admin.master.desa')->name('desa');
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
