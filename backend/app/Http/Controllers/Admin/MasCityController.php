<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasCity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MasCityController extends Controller
{
    public function index(Request $request)
    {
        $query = MasCity::with('regency');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('cities_name', 'like', "%{$search}%")
                  ->orWhere('cities_code', 'like', "%{$search}%")
                  ->orWhereHas('regency', function ($rQ) use ($search) {
                      $rQ->where('regencies_name', 'like', "%{$search}%");
                  });
            });
        }

        $cities = $query->orderBy('cities_name')->paginate(10)->withQueryString();
        
        // Prepare data for table component
        $tableHeaders = [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true],
            ['key' => 'regency_name', 'label' => 'Kabupaten/Kota', 'sortable' => false],
            ['key' => 'cities_name', 'label' => 'Nama Kecamatan', 'sortable' => true],
            ['key' => 'cities_code', 'label' => 'Kode', 'sortable' => true],
            ['key' => 'actions', 'label' => 'Aksi', 'format' => 'actions', 'sortable' => false]
        ];
        
        // Transform cities data for table
        $cities->getCollection()->transform(function ($city) {
            $detailData = [
                'id' => $city->id,
                'cities_name' => addslashes($city->cities_name),
                'cities_code' => addslashes($city->cities_code),
                'regencies_code' => $city->regencies_code
            ];
            $detailJson = json_encode($detailData);

            $city->regency_name = $city->regency->regencies_name ?? '-';
            $city->actions = [
                [
                    'label' => 'Edit',
                    'title' => 'Edit Kecamatan',
                    'url' => '#',
                    'onclick' => "window.dispatchEvent(new CustomEvent('open-edit-city', { detail: {$detailJson} }))",
                    'icon' => 'pen',
                    'color' => 'blue'
                ],
                [
                    'label' => 'Hapus',
                    'title' => 'Hapus Kecamatan',
                    'url' => route('admin.mas-cities.destroy', $city->id),
                    'color' => 'red',
                    'method' => 'DELETE',
                    'icon' => 'trash',
                    'confirm' => 'Apakah Anda yakin ingin menghapus kecamatan ini?'
                ]
            ];
            
            return $city;
        });

        // Get regencies for form select options
        $regencies = \App\Models\MasRegency::all()->map(function ($regency) {
            return [
                'value' => $regency->regencies_code,
                'label' => $regency->regencies_name
            ];
        });

        return view('admin.mas_cities.index', compact('cities', 'tableHeaders', 'regencies'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'cities_name' => 'required|string|max:255|unique:mas_cities,cities_name',
                'cities_code' => 'required|string|max:50|unique:mas_cities,cities_code',
                'regencies_code' => 'required|exists:mas_regencies,regencies_code'
            ]);

            $city = MasCity::create($validated);

            return redirect()->route('admin.mas-cities.index')
                ->with('success', "Kecamatan '{$city->cities_name}' berhasil ditambahkan.");
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Data yang diinput tidak valid. Silakan periksa kembali.');
                
        } catch (\Exception $e) {
            Log::error('Unexpected error when creating kecamatan: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan tak terduga. Silakan coba lagi.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $item = MasCity::findOrFail($id);
            
            $validated = $request->validate([
                'cities_name' => 'required|string|max:255|unique:mas_cities,cities_name',
            'cities_code' => 'required|string|max:50|unique:mas_cities,cities_code',
            'regencies_code' => 'required|string|max:50|unique:mas_cities,regencies_code'
            ]);

            $oldName = $item->cities_name;
            $item->update($validated);

            return redirect()->route('admin.mas-cities.index')
                ->with('success', "Kecamatan '{$oldName}' berhasil diperbarui menjadi '{$city->cities_name}'.");
                
        } catch (\Exception $e) {
            Log::error('Unexpected error when updating kecamatan: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan tak terduga. Silakan coba lagi.');
        }
    }

    public function destroy($id)
    {
        try {
            $item = MasCity::findOrFail($id);
            $itemName = $item->cities_name;
            $item->delete();

            return redirect()->route('admin.mas-city.index')
                ->with('success', "Kecamatan '{$itemName}' berhasil dihapus.");
                
        } catch (\Exception $e) {
            Log::error('Unexpected error when deleting kecamatan: ' . $e->getMessage());
            
            return redirect()->route('admin.mas-city.index')
                ->with('error', 'Terjadi kesalahan tak terduga. Silakan coba lagi.');
        }
    }
}