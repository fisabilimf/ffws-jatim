<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasSensorParameter;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MasSensorParameterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = MasSensorParameter::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $sensorParameters = $query->orderBy('name')
                                 ->paginate(15)
                                 ->withQueryString();

        return view('admin.mas_sensor_parameters.index', compact('sensorParameters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.mas_sensor_parameters.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:mas_sensor_parameters,code',
            'name' => 'required|string|max:255',
        ]);

        MasSensorParameter::create($validated);

        return redirect()->route('admin.mas-sensor-parameters.index')
                        ->with('success', 'Sensor Parameter created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MasSensorParameter $masSensorParameter): View
    {
        return view('admin.mas_sensor_parameters.show', compact('masSensorParameter'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasSensorParameter $masSensorParameter): View
    {
        return view('admin.mas_sensor_parameters.edit', compact('masSensorParameter'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasSensorParameter $masSensorParameter): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:mas_sensor_parameters,code,' . $masSensorParameter->id,
            'name' => 'required|string|max:255',
        ]);

        $masSensorParameter->update($validated);

        return redirect()->route('admin.mas-sensor-parameters.index')
                        ->with('success', 'Sensor Parameter updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasSensorParameter $masSensorParameter): RedirectResponse
    {
        // Check if sensor parameter has related sensor values
        if ($masSensorParameter->sensorValues()->count() > 0) {
            return redirect()->route('admin.mas-sensor-parameters.index')
                           ->with('error', 'Cannot delete Sensor Parameter because it has related sensor values.');
        }

        $masSensorParameter->delete();

        return redirect()->route('admin.mas-sensor-parameters.index')
                        ->with('success', 'Sensor Parameter deleted successfully.');
    }
}