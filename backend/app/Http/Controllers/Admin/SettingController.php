<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    /**
     * Tampilkan halaman pengaturan
     */
    public function index()
    {
        // Ambil pengaturan dari config atau database
        $settings = [
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
            'app_description' => config('app.description', ''),
            'mail_driver' => config('mail.default'),
            'mail_host' => config('mail.mailers.smtp.host'),
            'mail_port' => config('mail.mailers.smtp.port'),
            'mail_username' => config('mail.mailers.smtp.username'),
            'mail_encryption' => config('mail.mailers.smtp.encryption'),
            'mail_from_address' => config('mail.from.address'),
            'mail_from_name' => config('mail.from.name'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update pengaturan umum
     */
    public function updateGeneral(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'app_name' => 'required|string|max:255',
            'app_url' => 'required|url',
            'timezone' => 'required|string',
            'locale' => 'required|string|size:2',
            'app_description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Update pengaturan (implementasi sesuai kebutuhan)
        // Bisa disimpan ke database atau file config
        
        return back()->with('success', 'Pengaturan umum berhasil diperbarui!');
    }

    /**
     * Update pengaturan email
     */
    public function updateEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mail_driver' => 'required|string',
            'mail_host' => 'required|string',
            'mail_port' => 'required|numeric',
            'mail_username' => 'required|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'required|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Update pengaturan email (implementasi sesuai kebutuhan)
        // Bisa disimpan ke database atau file config
        
        return back()->with('success', 'Pengaturan email berhasil diperbarui!');
    }

    /**
     * Clear cache
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('config:clear');
            
            return back()->with('success', 'Cache berhasil dibersihkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membersihkan cache: ' . $e->getMessage());
        }
    }

    /**
     * Clear config
     */
    public function clearConfig()
    {
        try {
            Artisan::call('config:clear');
            Artisan::call('config:cache');
            
            return back()->with('success', 'Config berhasil dibersihkan dan di-cache ulang!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membersihkan config: ' . $e->getMessage());
        }
    }
}


