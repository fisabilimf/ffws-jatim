<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasRegency;
use App\Models\MasProvince;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MasRegencyController extends Controller
{
    public function index(Request $request)
    {
        $query = MasRegency::with('province');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('regencies_name', 'like', "%{$search}%")
                  ->orWhere('regencies_code', 'like', "%{$search}%")
                  ->orWhereHas('province', function ($pQ) use ($search) {
                      $pQ->where('provinces_name', 'like', "%{$search}%");
                  });
            });
        }

        $regencies = $query->orderBy('regencies_name')->paginate(10)->withQueryString();
        
        // Prepare data for table component
        $tableHeaders = [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true],
            ['key' => 'province_name', 'label' => 'Provinsi', 'sortable' => false],
            ['key' => 'regencies_name', 'label' => 'Nama Kabupaten/Kota', 'sortable' => true],
            ['key' => 'regencies_code', 'label' => 'Kode', 'sortable' => true],
            ['key' => 'actions', 'label' => 'Aksi', 'format' => 'actions', 'sortable' => false]
        ];
        
        // Transform regencies data for table
        $regencies->getCollection()->transform(function ($regency) {
            $detailData = [
                'id' => $regency->id,
                'regencies_name' => addslashes($regency->regencies_name),
                'regencies_code' => addslashes($regency->regencies_code),
                'provinces_code' => $regency->provinces_code
            ];
            $detailJson = json_encode($detailData);

            $regency->province_name = $regency->province->provinces_name ?? '-';
            $regency->actions = [
                [
                    'label' => 'Edit',
                    'title' => 'Edit Kabupaten/Kota',
                    'url' => '#',
                    'onclick' => "window.dispatchEvent(new CustomEvent('open-edit-regency', { detail: {$detailJson} }))",
                    'icon' => 'pen',
                    'color' => 'blue'
                ],
                [
                    'label' => 'Hapus',
                    'title' => 'Hapus Kabupaten/Kota',
                    'url' => route('admin.mas-regencies.destroy', $regency->id),
                    'color' => 'red',
                    'method' => 'DELETE',
                    'icon' => 'trash',
                    'confirm' => 'Apakah Anda yakin ingin menghapus kabupaten/kota ini?'
                ]
            ];
            
            return $regency;
        });

        // Get provinces for form select options
        $provinces = MasProvince::all()->map(function ($province) {
            return [
                'value' => $province->provinces_code,
                'label' => $province->provinces_name
            ];
        });

        return view('admin.mas_regencies.index', compact('regencies', 'tableHeaders', 'provinces'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'regencies_name' => 'required|string|max:255|unique:mas_regencies,regencies_name',
                'regencies_code' => 'required|string|max:10|unique:mas_regencies,regencies_code',
                'provinces_code' => 'required|exists:mas_provinces,provinces_code',
            ]);

            $regency = MasRegency::create($validated);

            return redirect()->route('admin.mas-regencies.index')
                ->with('success', "Kabupaten/Kota '{$regency->regencies_name}' berhasil ditambahkan.");
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Data yang diinput tidak valid. Silakan periksa kembali.');
                
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error when creating regency: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'error_code' => $e->getCode()
            ]);
            
            if ($e->getCode() == 23000) {
                if (strpos($e->getMessage(), 'regencies_name') !== false) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "Nama kabupaten/kota '{$request->regencies_name}' sudah digunakan.");
                }
                if (strpos($e->getMessage(), 'regencies_code') !== false) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "Kode kabupaten/kota '{$request->regencies_code}' sudah digunakan.");
                }
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan database saat menyimpan kabupaten/kota.');
                
        } catch (\Exception $e) {
            Log::error('Unexpected error when creating regency: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan tak terduga. Silakan coba lagi.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $regency = MasRegency::findOrFail($id);
            
            $validated = $request->validate([
                'regencies_name' => 'required|string|max:255|unique:mas_regencies,regencies_name,' . $id,
                'regencies_code' => 'required|string|max:10|unique:mas_regencies,regencies_code,' . $id,
                'provinces_code' => 'required|exists:mas_provinces,provinces_code',
            ]);

            $oldName = $regency->regencies_name;
            $regency->update($validated);

            return redirect()->route('admin.mas-regencies.index')
                ->with('success', "Kabupaten/Kota '{$oldName}' berhasil diperbarui menjadi '{$regency->regencies_name}'.");
                
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.mas-regencies.index')
                ->with('error', 'Kabupaten/Kota yang akan diperbarui tidak ditemukan.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Data yang diinput tidak valid. Silakan periksa kembali.');
                
        } catch (\Exception $e) {
            Log::error('Unexpected error when updating regency: ' . $e->getMessage(), [
                'regency_id' => $id,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan tak terduga. Silakan coba lagi.');
        }
    }

    public function destroy($id)
    {
        try {
            $regency = MasRegency::findOrFail($id);
            
            // Check if regency has related cities
            $citiesCount = DB::table('mas_cities')
                           ->where('regencies_code', $regency->regencies_code)
                           ->count();
            
            if ($citiesCount > 0) {
                return redirect()->route('admin.mas-regencies.index')
                    ->with('error', "Kabupaten/Kota '{$regency->regencies_name}' tidak dapat dihapus karena masih memiliki {$citiesCount} kecamatan yang terkait.");
            }
            
            $regencyName = $regency->regencies_name;
            $regency->delete();

            return redirect()->route('admin.mas-regencies.index')
                ->with('success', "Kabupaten/Kota '{$regencyName}' berhasil dihapus.");
                
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.mas-regencies.index')
                ->with('error', 'Kabupaten/Kota yang akan dihapus tidak ditemukan.');
                
        } catch (\Exception $e) {
            Log::error('Unexpected error when deleting regency: ' . $e->getMessage(), [
                'regency_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('admin.mas-regencies.index')
                ->with('error', 'Terjadi kesalahan tak terduga. Silakan coba lagi.');
        }
    }
}