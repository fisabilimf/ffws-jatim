<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasWatershed;
use App\Models\MasRiverBasin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MasWatershedController extends Controller
{
    public function index(Request $request)
    {
        $query = MasWatershed::query()->with('riverBasin');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('watersheds_name', 'like', '%' . $search . '%')
                    ->orWhere('watersheds_code', 'like', '%' . $search . '%');
            });
        }

        // Filter by river basin
        if ($request->filled('river_basin_code')) {
            $query->where('river_basin_code', $request->get('river_basin_code'));
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $watersheds = $query->orderBy('watersheds_name', 'asc')->paginate($perPage);

        // Get river basins for filter dropdown
        $riverBasins = MasRiverBasin::orderBy('river_basins_name', 'asc')->get()
            ->map(function ($riverBasin) {
                return [
                    'value' => $riverBasin->river_basins_code,
                    'label' => $riverBasin->river_basins_name
                ];
            });

        return view('admin.mas_watersheds.index', compact('watersheds', 'riverBasins'));
    }

    public function create()
    {
        $riverBasins = MasRiverBasin::orderBy('river_basins_name', 'asc')->get();
        return view('admin.mas_watersheds.create', compact('riverBasins'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'watersheds_name' => 'required|string|max:255',
            'watersheds_code' => 'required|string|max:10|unique:mas_watersheds,watersheds_code',
            'river_basin_code' => 'required|exists:mas_river_basins,river_basins_code',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        MasWatershed::create($request->only(['watersheds_name', 'watersheds_code', 'river_basin_code']));

        return redirect()->route('admin.mas-watersheds.index')
            ->with('success', 'Watershed berhasil ditambahkan');
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
        $validator = Validator::make($request->all(), [
            'watersheds_name' => 'required|string|max:255',
            'watersheds_code' => 'required|string|max:10|unique:mas_watersheds,watersheds_code,' . $masWatershed->id,
            'river_basin_code' => 'required|exists:mas_river_basins,river_basins_code',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $masWatershed->update($request->only(['watersheds_name', 'watersheds_code', 'river_basin_code']));

        return redirect()->route('admin.mas-watersheds.index')
            ->with('success', 'Watershed berhasil diperbarui');
    }

    public function destroy(MasWatershed $masWatershed)
    {
        try {
            $masWatershed->delete();
            return redirect()->route('admin.mas-watersheds.index')
                ->with('success', 'Watershed berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('admin.mas-watersheds.index')
                ->with('error', 'Watershed tidak dapat dihapus karena masih memiliki data terkait');
        }
    }
}