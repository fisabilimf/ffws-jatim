<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasDeviceParameter;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MasDeviceParameterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = MasDeviceParameter::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $deviceParameters = $query->orderBy('name')
                                 ->paginate(15)
                                 ->withQueryString();

        return view('admin.mas_device_parameters.index', compact('deviceParameters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.mas_device_parameters.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:mas_device_parameters,code',
            'name' => 'required|string|max:255',
        ]);

        MasDeviceParameter::create($validated);

        return redirect()->route('admin.mas-device-parameters.index')
                        ->with('success', 'Device Parameter created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MasDeviceParameter $masDeviceParameter): View
    {
        return view('admin.mas_device_parameters.show', compact('masDeviceParameter'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasDeviceParameter $masDeviceParameter): View
    {
        return view('admin.mas_device_parameters.edit', compact('masDeviceParameter'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasDeviceParameter $masDeviceParameter): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:mas_device_parameters,code,' . $masDeviceParameter->id,
            'name' => 'required|string|max:255',
        ]);

        $masDeviceParameter->update($validated);

        return redirect()->route('admin.mas-device-parameters.index')
                        ->with('success', 'Device Parameter updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasDeviceParameter $masDeviceParameter): RedirectResponse
    {
        // Check if device parameter has related device values
        if ($masDeviceParameter->deviceValues()->count() > 0) {
            return redirect()->route('admin.mas-device-parameters.index')
                           ->with('error', 'Cannot delete Device Parameter because it has related device values.');
        }

        $masDeviceParameter->delete();

        return redirect()->route('admin.mas-device-parameters.index')
                        ->with('success', 'Device Parameter deleted successfully.');
    }
}