<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasScaler;
use App\Models\MasModel;
use App\Models\MasSensor;
use App\Models\SensorValue;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class MasScalerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = MasScaler::with(['masModel', 'masSensor']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('scaler_code', 'like', "%{$search}%")
                  ->orWhereHas('masModel', function ($q) use ($search) {
                      $q->where('model_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by technique
        if ($request->filled('technique')) {
            $query->where('technique', $request->get('technique'));
        }

        // Filter by axis
        if ($request->filled('axis')) {
            $query->where('io_axis', $request->get('axis'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->get('status') === 'active');
        }

        $scalers = $query->orderBy('name')
                        ->paginate(15)
                        ->withQueryString();

        $techniques = MasScaler::getTechniqueOptions();
        $axes = MasScaler::getAxisOptions();

        return view('admin.mas_scalers.index', compact('scalers', 'techniques', 'axes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $models = MasModel::where('is_active', true)
                         ->orderBy('model_name')
                         ->get();
        
        $sensors = MasSensor::leftJoin('sensor_values', 'mas_sensors.sensor_code', '=', 'sensor_values.mas_sensor_code')
                           ->where('mas_sensors.is_active', true)
                           ->select('mas_sensors.id', 'mas_sensors.sensor_code', 'sensor_values.sensor_name')
                           ->orderBy('sensor_values.sensor_name')
                           ->get();

        $techniques = MasScaler::getTechniqueOptions();
        $axes = MasScaler::getAxisOptions();

        return view('admin.mas_scalers.create', compact('models', 'sensors', 'techniques', 'axes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mas_model_id' => 'required|exists:mas_models,id',
            'mas_sensor_id' => 'nullable|exists:mas_sensors,id',
            'name' => 'required|string|max:255',
            'scaler_code' => 'required|string|max:255|unique:mas_scalers,scaler_code',
            'io_axis' => 'required|in:x,y',
            'technique' => 'required|in:standard,minmax,robust,custom',
            'version' => 'nullable|string|max:64',
            'scaler_file' => 'required|file|mimes:pkl,joblib,json|max:10240', // 10MB max
            'is_active' => 'boolean',
        ]);

        // Handle file upload
        if ($request->hasFile('scaler_file')) {
            $file = $request->file('scaler_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('scalers', $filename, 'public');
            
            $validated['file_path'] = $path;
            $validated['file_hash_sha256'] = hash_file('sha256', $file->getPathname());
        }

        // Get model code
        $model = MasModel::find($validated['mas_model_id']);
        $validated['mas_model_code'] = $model->model_code;

        $validated['is_active'] = $request->has('is_active');

        MasScaler::create($validated);

        return redirect()->route('admin.mas-scalers.index')
                        ->with('success', 'Scaler created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MasScaler $masScaler): View
    {
        $masScaler->load(['masModel', 'masSensor']);
        
        return view('admin.mas_scalers.show', compact('masScaler'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasScaler $masScaler): View
    {
        $models = MasModel::where('is_active', true)
                         ->orderBy('model_name')
                         ->get();
        
        $sensors = MasSensor::leftJoin('sensor_values', 'mas_sensors.sensor_code', '=', 'sensor_values.mas_sensor_code')
                           ->where('mas_sensors.is_active', true)
                           ->select('mas_sensors.id', 'mas_sensors.sensor_code', 'sensor_values.sensor_name')
                           ->orderBy('sensor_values.sensor_name')
                           ->get();

        $techniques = MasScaler::getTechniqueOptions();
        $axes = MasScaler::getAxisOptions();

        return view('admin.mas_scalers.edit', compact('masScaler', 'models', 'sensors', 'techniques', 'axes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasScaler $masScaler): RedirectResponse
    {
        $validated = $request->validate([
            'mas_model_id' => 'required|exists:mas_models,id',
            'mas_sensor_id' => 'nullable|exists:mas_sensors,id',
            'name' => 'required|string|max:255',
            'scaler_code' => 'required|string|max:255|unique:mas_scalers,scaler_code,' . $masScaler->id,
            'io_axis' => 'required|in:x,y',
            'technique' => 'required|in:standard,minmax,robust,custom',
            'version' => 'nullable|string|max:64',
            'scaler_file' => 'nullable|file|mimes:pkl,joblib,json|max:10240', // 10MB max
            'is_active' => 'boolean',
        ]);

        // Handle file upload if new file is provided
        if ($request->hasFile('scaler_file')) {
            // Delete old file
            if ($masScaler->file_path && Storage::disk('public')->exists($masScaler->file_path)) {
                Storage::disk('public')->delete($masScaler->file_path);
            }

            $file = $request->file('scaler_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('scalers', $filename, 'public');
            
            $validated['file_path'] = $path;
            $validated['file_hash_sha256'] = hash_file('sha256', $file->getPathname());
        }

        // Get model code
        $model = MasModel::find($validated['mas_model_id']);
        $validated['mas_model_code'] = $model->model_code;

        $validated['is_active'] = $request->has('is_active');

        $masScaler->update($validated);

        return redirect()->route('admin.mas-scalers.index')
                        ->with('success', 'Scaler updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasScaler $masScaler): RedirectResponse
    {
        // Delete associated file
        if ($masScaler->file_path && Storage::disk('public')->exists($masScaler->file_path)) {
            Storage::disk('public')->delete($masScaler->file_path);
        }

        $masScaler->delete();

        return redirect()->route('admin.mas-scalers.index')
                        ->with('success', 'Scaler deleted successfully.');
    }

    /**
     * Download scaler file.
     */
    public function download(MasScaler $masScaler)
    {
        if (!$masScaler->file_path || !Storage::disk('public')->exists($masScaler->file_path)) {
            return redirect()->back()->with('error', 'Scaler file not found.');
        }

        return Storage::disk('public')->download($masScaler->file_path, $masScaler->name . '_scaler.' . pathinfo($masScaler->file_path, PATHINFO_EXTENSION));
    }

    /**
     * Toggle scaler status.
     */
    public function toggleStatus(MasScaler $masScaler): RedirectResponse
    {
        $masScaler->update(['is_active' => !$masScaler->is_active]);

        $status = $masScaler->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
                        ->with('success', "Scaler {$status} successfully.");
    }
}