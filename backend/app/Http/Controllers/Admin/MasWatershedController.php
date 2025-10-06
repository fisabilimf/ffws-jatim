<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasWatershed;
use App\Models\MasRiverBasin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MasWatershedController extends Controller
{
    public function index(Request $request)
    {
        $query = MasWatershed::query()->with('riverBasin');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('watersheds_name', 'like', "%{$search}%")
                  ->orWhere('watersheds_code', 'like', "%{$search}%");
            });
        }

        if ($riverBasinCode = $request->get('river_basin_code')) {
            $query->where('river_basin_code', $riverBasinCode);
        }

        $watersheds = $query->orderBy('watersheds_name')->paginate(10)->withQueryString();
        
        // Get river basins for dropdown
        $riverBasins = MasRiverBasin::orderBy('river_basins_name')->get()
            ->map(function ($riverBasin) {
                return [
                    'value' => $riverBasin->river_basins_code,
                    'label' => $riverBasin->river_basins_name
                ];
            });
        
        // Prepare data for table component
        $tableHeaders = [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true],
            ['key' => 'watersheds_name', 'label' => 'Nama DAS', 'sortable' => true],
            ['key' => 'watersheds_code', 'label' => 'Kode', 'sortable' => true],
            ['key' => 'river_basin_name', 'label' => 'Sub DAS', 'sortable' => false],
            ['key' => 'actions', 'label' => 'Aksi', 'format' => 'actions', 'sortable' => false]
        ];
        
        // Transform watersheds data for table
        $watersheds->getCollection()->transform(function ($watershed) {
            $detailData = [
                'id' => $watershed->id,
                'watersheds_name' => addslashes($watershed->watersheds_name),
                'watersheds_code' => addslashes($watershed->watersheds_code),
                'river_basin_code' => $watershed->river_basin_code
            ];
            $detailJson = json_encode($detailData);

            $watershed->river_basin_name = $watershed->riverBasin ? $watershed->riverBasin->river_basins_name : '-';
            $watershed->actions = [
                [
                    'label' => 'Edit',
                    'title' => 'Edit DAS',
                    'url' => '#',
                    'onclick' => "window.dispatchEvent(new CustomEvent('open-edit-watershed', { detail: {$detailJson} }))",
                    'icon' => 'pen',
                    'color' => 'blue'
                ],
                [
                    'label' => 'Hapus',
                    'title' => 'Hapus DAS',
                    'url' => route('admin.mas-watersheds.destroy', $watershed->id),
                    'color' => 'red',
                    'method' => 'DELETE',
                    'icon' => 'trash',
                    'confirm' => 'Apakah Anda yakin ingin menghapus DAS ini?'
                ]
            ];
            
            return $watershed;
        });

        return view('admin.mas_watersheds.index', compact('watersheds', 'riverBasins', 'tableHeaders'));
    }

    public function create()
    {
        $riverBasins = MasRiverBasin::orderBy('river_basins_name', 'asc')->get();
        return view('admin.mas_watersheds.create', compact('riverBasins'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'watersheds_name' => 'required|string|max:255',
                'watersheds_code' => 'required|string|max:10|unique:mas_watersheds,watersheds_code',
                'river_basin_code' => 'required|exists:mas_river_basins,river_basins_code',
            ]);

            $watershed = MasWatershed::create($validated);

            return redirect()->route('admin.mas-watersheds.index')
                ->with('success', "DAS '{$watershed->watersheds_name}' berhasil ditambahkan.");

        } catch (\Exception $e) {
            Log::error('Error creating watershed: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.mas-watersheds.index')
                ->with('error', 'Terjadi kesalahan saat menambahkan DAS. Silakan coba lagi.');
        }
    }

    public function show(MasWatershed $masWatershed)
    {
        $masWatershed->load('riverBasin');
        return view('admin.mas_watersheds.show', compact('masWatershed'));
    }

    public function edit(MasWatershed $masWatershed)
    {
        $riverBasins = MasRiverBasin::orderBy('river_basins_name', 'asc')->get();
        return view('admin.mas_watersheds.edit', compact('masWatershed', 'riverBasins'));
    }

    public function update(Request $request, MasWatershed $masWatershed)
    {
        try {
            $validated = $request->validate([
                'watersheds_name' => 'required|string|max:255',
                'watersheds_code' => 'required|string|max:10|unique:mas_watersheds,watersheds_code,' . $masWatershed->id,
                'river_basin_code' => 'required|exists:mas_river_basins,river_basins_code',
            ]);

            $masWatershed->update($validated);

            return redirect()->route('admin.mas-watersheds.index')
                ->with('success', "DAS '{$masWatershed->watersheds_name}' berhasil diperbarui.");

        } catch (\Exception $e) {
            Log::error('Error updating watershed: ' . $e->getMessage(), [
                'watershed_id' => $masWatershed->id,
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.mas-watersheds.index')
                ->with('error', 'Terjadi kesalahan saat memperbarui DAS. Silakan coba lagi.');
        }
    }

    public function destroy(MasWatershed $masWatershed)
    {
        try {
            DB::beginTransaction();

            $watershedName = $masWatershed->watersheds_name;
            $masWatershed->delete();

            DB::commit();

            return redirect()->route('admin.mas-watersheds.index')
                ->with('success', "DAS '{$watershedName}' berhasil dihapus.");

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Error deleting watershed: ' . $e->getMessage(), [
                'watershed_id' => $masWatershed->id,
                'watershed_name' => $masWatershed->watersheds_name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.mas-watersheds.index')
                ->with('error', 'DAS tidak dapat dihapus karena masih memiliki data terkait atau terjadi kesalahan sistem.');
        }
    }
}