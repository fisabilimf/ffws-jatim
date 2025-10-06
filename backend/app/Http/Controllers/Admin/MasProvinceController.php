<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasProvince;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MasProvinceController extends Controller
{
    public function index(Request $request)
    {
        $query = MasProvince::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('provinces_name', 'like', "%{$search}%")
                  ->orWhere('provinces_code', 'like', "%{$search}%");
            });
        }

        $provinces = $query->orderBy('provinces_name')->paginate(10)->withQueryString();
        
        // Prepare data for table component
        $tableHeaders = [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true],
            ['key' => 'provinces_name', 'label' => 'Nama Provinsi', 'sortable' => true],
            ['key' => 'provinces_code', 'label' => 'Kode', 'sortable' => true],
            ['key' => 'actions', 'label' => 'Aksi', 'format' => 'actions', 'sortable' => false]
        ];
        
        // Transform provinces data for table
        $provinces->getCollection()->transform(function ($province) {
            $detailData = [
                'id' => $province->id,
                'provinces_name' => addslashes($province->provinces_name),
                'provinces_code' => addslashes($province->provinces_code)
            ];
            $detailJson = json_encode($detailData);

            $province->actions = [
                [
                    'label' => 'Edit',
                    'title' => 'Edit Provinsi',
                    'url' => '#',
                    'onclick' => "window.dispatchEvent(new CustomEvent('open-edit-province', { detail: {$detailJson} }))",
                    'icon' => 'pen',
                    'color' => 'blue'
                ],
                [
                    'label' => 'Hapus',
                    'title' => 'Hapus Provinsi',
                    'url' => route('admin.mas-provinces.destroy', $province->id),
                    'color' => 'red',
                    'method' => 'DELETE',
                    'icon' => 'trash',
                    'confirm' => 'Apakah Anda yakin ingin menghapus provinsi ini?'
                ]
            ];
            
            return $province;
        });

        return view('admin.mas_provinces.index', compact('provinces', 'tableHeaders'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'provinces_name' => 'required|string|max:255|unique:mas_provinces,provinces_name',
                'provinces_code' => 'required|string|max:10|unique:mas_provinces,provinces_code',
            ]);

            $province = MasProvince::create($validated);

            return redirect()->route('admin.mas-provinces.index')
                ->with('success', "Provinsi '{$province->provinces_name}' berhasil ditambahkan.");
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Data yang diinput tidak valid. Silakan periksa kembali.');
                
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error when creating province: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'error_code' => $e->getCode()
            ]);
            
            // Handle specific database constraint errors
            if ($e->getCode() == 23000) {
                if (strpos($e->getMessage(), 'provinces_name') !== false) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "Nama provinsi '{$request->provinces_name}' sudah digunakan. Silakan gunakan nama yang berbeda.");
                }
                if (strpos($e->getMessage(), 'provinces_code') !== false) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "Kode provinsi '{$request->provinces_code}' sudah digunakan. Silakan gunakan kode yang berbeda.");
                }
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan database saat menyimpan provinsi. Silakan coba lagi.');
                
        } catch (\Exception $e) {
            Log::error('Unexpected error when creating province: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan tak terduga. Silakan coba lagi atau hubungi administrator.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $province = MasProvince::findOrFail($id);
            
            $validated = $request->validate([
                'provinces_name' => 'required|string|max:255|unique:mas_provinces,provinces_name,' . $id,
                'provinces_code' => 'required|string|max:10|unique:mas_provinces,provinces_code,' . $id,
            ]);

            $oldName = $province->provinces_name;
            $province->update($validated);

            return redirect()->route('admin.mas-provinces.index')
                ->with('success', "Provinsi '{$oldName}' berhasil diperbarui menjadi '{$province->provinces_name}'.");
                
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.mas-provinces.index')
                ->with('error', 'Provinsi yang akan diperbarui tidak ditemukan.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Data yang diinput tidak valid. Silakan periksa kembali.');
                
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error when updating province: ' . $e->getMessage(), [
                'province_id' => $id,
                'request_data' => $request->all(),
                'error_code' => $e->getCode()
            ]);
            
            // Handle specific database constraint errors
            if ($e->getCode() == 23000) {
                if (strpos($e->getMessage(), 'provinces_name') !== false) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "Nama provinsi '{$request->provinces_name}' sudah digunakan. Silakan gunakan nama yang berbeda.");
                }
                if (strpos($e->getMessage(), 'provinces_code') !== false) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "Kode provinsi '{$request->provinces_code}' sudah digunakan. Silakan gunakan kode yang berbeda.");
                }
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan database saat memperbarui provinsi. Silakan coba lagi.');
                
        } catch (\Exception $e) {
            Log::error('Unexpected error when updating province: ' . $e->getMessage(), [
                'province_id' => $id,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan tak terduga. Silakan coba lagi atau hubungi administrator.');
        }
    }

    public function destroy($id)
    {
        try {
            $province = MasProvince::findOrFail($id);
            
            // Check if province has related regencies
            $regenciesCount = DB::table('mas_regencies')
                              ->where('provinces_code', $province->provinces_code)
                              ->count();
            
            if ($regenciesCount > 0) {
                return redirect()->route('admin.mas-provinces.index')
                    ->with('error', "Provinsi '{$province->provinces_name}' tidak dapat dihapus karena masih memiliki {$regenciesCount} kabupaten/kota yang terkait. Hapus terlebih dahulu kabupaten/kota terkait sebelum menghapus provinsi ini.");
            }
            
            $provinceName = $province->provinces_name;
            $province->delete();

            return redirect()->route('admin.mas-provinces.index')
                ->with('success', "Provinsi '{$provinceName}' berhasil dihapus.");
                
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.mas-provinces.index')
                ->with('error', 'Provinsi yang akan dihapus tidak ditemukan.');
                
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error when deleting province: ' . $e->getMessage(), [
                'province_id' => $id,
                'error_code' => $e->getCode(),
                'sql_state' => $e->errorInfo[0] ?? null
            ]);
            
            // Handle specific database constraint errors
            if ($e->getCode() == 23000 || strpos($e->getMessage(), 'foreign key constraint') !== false) {
                $errorMsg = "Provinsi tidak dapat dihapus karena masih digunakan oleh data lain dalam sistem. ";
                $errorMsg .= "Pastikan untuk menghapus semua kabupaten/kota dan data terkait terlebih dahulu.";
                
                return redirect()->route('admin.mas-provinces.index')
                    ->with('error', $errorMsg);
            }
            
            // Generic database error
            return redirect()->route('admin.mas-provinces.index')
                ->with('error', 'Terjadi kesalahan database saat menghapus provinsi. Silakan coba lagi atau hubungi administrator.');
                
        } catch (\Exception $e) {
            Log::error('Unexpected error when deleting province: ' . $e->getMessage(), [
                'province_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('admin.mas-provinces.index')
                ->with('error', 'Terjadi kesalahan tak terduga. Silakan coba lagi atau hubungi administrator.');
        }
    }
}