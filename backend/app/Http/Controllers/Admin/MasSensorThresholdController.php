<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasSensorThreshold;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MasSensorThresholdController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = MasSensorThreshold::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('sensor_thresholds_name', 'like', "%{$search}%")
                  ->orWhere('sensor_thresholds_code', 'like', "%{$search}%");
            });
        }

        $sensorThresholds = $query->orderBy('sensor_thresholds_name')
                                 ->paginate(15)
                                 ->withQueryString();

        return view('admin.mas_sensor_thresholds.index', compact('sensorThresholds'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.mas_sensor_thresholds.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sensor_thresholds_code' => 'required|string|max:255|unique:mas_sensor_thresholds,sensor_thresholds_code',
            'sensor_thresholds_name' => 'required|string|max:255',
            'sensor_thresholds_value_1' => 'nullable|numeric',
            'sensor_thresholds_value_1_color' => 'nullable|string|max:7',
            'sensor_thresholds_value_2' => 'nullable|numeric',
            'sensor_thresholds_value_2_color' => 'nullable|string|max:7',
            'sensor_thresholds_value_3' => 'nullable|numeric',
            'sensor_thresholds_value_3_color' => 'nullable|string|max:7',
            'sensor_thresholds_value_4' => 'nullable|numeric',
            'sensor_thresholds_value_4_color' => 'nullable|string|max:7',
        ]);

        MasSensorThreshold::create($validated);

        return redirect()->route('admin.mas-sensor-thresholds.index')
                        ->with('success', 'Sensor Threshold created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MasSensorThreshold $masSensorThreshold): View
    {
        return view('admin.mas_sensor_thresholds.show', compact('masSensorThreshold'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasSensorThreshold $masSensorThreshold): View
    {
        return view('admin.mas_sensor_thresholds.edit', compact('masSensorThreshold'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasSensorThreshold $masSensorThreshold): RedirectResponse
    {
        $validated = $request->validate([
            'sensor_thresholds_code' => 'required|string|max:255|unique:mas_sensor_thresholds,sensor_thresholds_code,' . $masSensorThreshold->id,
            'sensor_thresholds_name' => 'required|string|max:255',
            'sensor_thresholds_value_1' => 'nullable|numeric',
            'sensor_thresholds_value_1_color' => 'nullable|string|max:7',
            'sensor_thresholds_value_2' => 'nullable|numeric',
            'sensor_thresholds_value_2_color' => 'nullable|string|max:7',
            'sensor_thresholds_value_3' => 'nullable|numeric',
            'sensor_thresholds_value_3_color' => 'nullable|string|max:7',
            'sensor_thresholds_value_4' => 'nullable|numeric',
            'sensor_thresholds_value_4_color' => 'nullable|string|max:7',
        ]);

        $masSensorThreshold->update($validated);

        return redirect()->route('admin.mas-sensor-thresholds.index')
                        ->with('success', 'Sensor Threshold updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasSensorThreshold $masSensorThreshold): RedirectResponse
    {
        // Check if sensor threshold has related sensor values
        if ($masSensorThreshold->sensorValues()->count() > 0) {
            return redirect()->route('admin.mas-sensor-thresholds.index')
                           ->with('error', 'Cannot delete Sensor Threshold because it has related sensor values.');
        }

        $masSensorThreshold->delete();

        return redirect()->route('admin.mas-sensor-thresholds.index')
                        ->with('success', 'Sensor Threshold deleted successfully.');
    }
}