<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasUpt;
use App\Models\MasRiverBasin;
use App\Models\MasCity;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MasUptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = MasUpt::with(['riverBasin', 'city']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('upts_name', 'like', "%{$search}%")
                  ->orWhere('upts_code', 'like', "%{$search}%")
                  ->orWhereHas('riverBasin', function ($q) use ($search) {
                      $q->where('river_basin_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('city', function ($q) use ($search) {
                      $q->where('cities_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by river basin
        if ($request->filled('river_basin_code')) {
            $query->where('river_basin_code', $request->get('river_basin_code'));
        }

        // Filter by city
        if ($request->filled('cities_code')) {
            $query->where('cities_code', $request->get('cities_code'));
        }

        $upts = $query->orderBy('upts_name')
                     ->paginate(15)
                     ->withQueryString();

        // Get filter options
        $riverBasins = MasRiverBasin::orderBy('river_basin_name')->get();
        $cities = MasCity::orderBy('cities_name')->get();

        return view('admin.mas_upts.index', compact('upts', 'riverBasins', 'cities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $riverBasins = MasRiverBasin::orderBy('river_basin_name')->get();
        $cities = MasCity::orderBy('cities_name')->get();
        
        return view('admin.mas_upts.create', compact('riverBasins', 'cities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'upts_code' => 'required|string|max:255|unique:mas_upts,upts_code',
            'upts_name' => 'required|string|max:255',
            'river_basin_code' => 'required|string|exists:mas_river_basins,river_basins_code',
            'cities_code' => 'required|string|exists:mas_cities,cities_code',
        ]);

        MasUpt::create($validated);

        return redirect()->route('admin.mas-upts.index')
                        ->with('success', 'UPT created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MasUpt $masUpt): View
    {
        $masUpt->load(['riverBasin', 'city', 'uptds']);
        
        return view('admin.mas_upts.show', compact('masUpt'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasUpt $masUpt): View
    {
        $riverBasins = MasRiverBasin::orderBy('river_basin_name')->get();
        $cities = MasCity::orderBy('cities_name')->get();
        
        return view('admin.mas_upts.edit', compact('masUpt', 'riverBasins', 'cities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasUpt $masUpt): RedirectResponse
    {
        $validated = $request->validate([
            'upts_code' => 'required|string|max:255|unique:mas_upts,upts_code,' . $masUpt->id,
            'upts_name' => 'required|string|max:255',
            'river_basin_code' => 'required|string|exists:mas_river_basins,river_basins_code',
            'cities_code' => 'required|string|exists:mas_cities,cities_code',
        ]);

        $masUpt->update($validated);

        return redirect()->route('admin.mas-upts.index')
                        ->with('success', 'UPT updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasUpt $masUpt): RedirectResponse
    {
        // Check if UPT has related UPTDs
        if ($masUpt->uptds()->count() > 0) {
            return redirect()->route('admin.mas-upts.index')
                           ->with('error', 'Cannot delete UPT because it has related UPTDs.');
        }

        $masUpt->delete();

        return redirect()->route('admin.mas-upts.index')
                        ->with('success', 'UPT deleted successfully.');
    }
}