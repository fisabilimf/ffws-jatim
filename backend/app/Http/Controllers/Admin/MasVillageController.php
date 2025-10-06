<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasVillage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MasVillageController extends Controller
{
    public function index(Request $request)
    {
        $query = MasVillage::with('city');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('villages_name', 'like', "%{$search}%")
                  ->orWhere('villages_code', 'like', "%{$search}%")
                  ->orWhereHas('city', function ($cQ) use ($search) {
                      $cQ->where('cities_name', 'like', "%{$search}%");
                  });
            });
        }

        $villages = $query->orderBy('villages_name')->paginate(10)->withQueryString();
        
        // Prepare data for table component
        $tableHeaders = [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true],
            ['key' => 'city_name', 'label' => 'Kecamatan', 'sortable' => false],
            ['key' => 'villages_name', 'label' => 'Nama Desa/Kelurahan', 'sortable' => true],
            ['key' => 'villages_code', 'label' => 'Kode', 'sortable' => true],
            ['key' => 'actions', 'label' => 'Aksi', 'format' => 'actions', 'sortable' => false]
        ];
        
        // Transform villages data for table
        $villages->getCollection()->transform(function ($village) {
            $detailData = [
                'id' => $village->id,
                'villages_name' => addslashes($village->villages_name),
                'villages_code' => addslashes($village->villages_code),
                'cities_code' => $village->cities_code
            ];
            $detailJson = json_encode($detailData);

            $village->city_name = $village->city->cities_name ?? '-';
            $village->actions = [
                [
                    'label' => 'Edit',
                    'title' => 'Edit Desa/Kelurahan',
                    'url' => '#',
                    'onclick' => "window.dispatchEvent(new CustomEvent('open-edit-village', { detail: {$detailJson} }))",
                    'icon' => 'pen',
                    'color' => 'blue'
                ],
                [
                    'label' => 'Hapus',
                    'title' => 'Hapus Desa/Kelurahan',
                    'url' => route('admin.mas-villages.destroy', $village->id),
                    'color' => 'red',
                    'method' => 'DELETE',
                    'icon' => 'trash',
                    'confirm' => 'Apakah Anda yakin ingin menghapus desa/kelurahan ini?'
                ]
            ];
            
            return $village;
        });

        // Get cities for form select options
        $cities = \App\Models\MasCity::all()->map(function ($city) {
            return [
                'value' => $city->cities_code,
                'label' => $city->cities_name
            ];
        });

        return view('admin.mas_villages.index', compact('villages', 'tableHeaders', 'cities'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'villages_name' => 'required|string|max:255|unique:mas_villages,villages_name',
                'villages_code' => 'required|string|max:50|unique:mas_villages,villages_code',
                'cities_code' => 'required|exists:mas_cities,cities_code'
            ]);

            $village = MasVillage::create($validated);

            return redirect()->route('admin.mas-villages.index')
                ->with('success', "Desa/kelurahan '{$village->villages_name}' berhasil ditambahkan.");
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Data yang diinput tidak valid. Silakan periksa kembali.');
                
        } catch (\Exception $e) {
            Log::error('Unexpected error when creating desa/kelurahan: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan tak terduga. Silakan coba lagi.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $item = MasVillage::findOrFail($id);
            
            $validated = $request->validate([
                'villages_name' => 'required|string|max:255|unique:mas_villages,villages_name',
            'villages_code' => 'required|string|max:50|unique:mas_villages,villages_code',
            'cities_code' => 'required|string|max:50|unique:mas_villages,cities_code'
            ]);

            $oldName = $item->villages_name;
            $item->update($validated);

            return redirect()->route('admin.mas-village.index')
                ->with('success', "Desa/kelurahan '{$oldName}' berhasil diperbarui.");
                
        } catch (\Exception $e) {
            Log::error('Unexpected error when updating desa/kelurahan: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan tak terduga. Silakan coba lagi.');
        }
    }

    public function destroy($id)
    {
        try {
            $item = MasVillage::findOrFail($id);
            $itemName = $item->villages_name;
            $item->delete();

            return redirect()->route('admin.mas-village.index')
                ->with('success', "Desa/kelurahan '{$itemName}' berhasil dihapus.");
                
        } catch (\Exception $e) {
            Log::error('Unexpected error when deleting desa/kelurahan: ' . $e->getMessage());
            
            return redirect()->route('admin.mas-village.index')
                ->with('error', 'Terjadi kesalahan tak terduga. Silakan coba lagi.');
        }
    }
}