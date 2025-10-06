<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasSensor;
use App\Models\MasDevice;
use App\Models\MasModel;
use App\Services\ForecastingService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class MasSensorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = MasSensor::with(['device', 'masModel']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sensor_code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('device', function ($deviceQuery) use ($search) {
                        $deviceQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by parameter
        if ($request->filled('parameter')) {
            $query->where('parameter', $request->parameter);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by forecasting status
        if ($request->filled('forecasting_status')) {
            $query->where('forecasting_status', $request->forecasting_status);
        }

        $perPage = $request->get('per_page', 10);
        $sensors = $query->orderBy('created_at', 'desc')->paginate($perPage)->appends(request()->query());

        // Prepare table headers
        $tableHeaders = [
            ['key' => 'sensor_code', 'label' => 'Kode Sensor', 'sortable' => true],
            ['key' => 'formatted_device_name', 'label' => 'Device'],
            ['key' => 'formatted_parameter', 'label' => 'Parameter'],
            ['key' => 'unit', 'label' => 'Unit'],
            ['key' => 'formatted_thresholds', 'label' => 'Threshold'],
            ['key' => 'status', 'label' => 'Status', 'format' => 'status'],
            ['key' => 'forecasting_status', 'label' => 'Forecasting', 'format' => 'forecasting_status'],
            ['key' => 'last_seen', 'label' => 'Last Seen', 'format' => 'date'],
            ['key' => 'actions', 'label' => 'Aksi', 'format' => 'actions']
        ];

        // Transform paginator items to include formatted data
        $sensors->getCollection()->transform(function ($sensor) {
            $sensor->formatted_parameter = MasSensor::getParameterOptions()[$sensor->parameter] ?? $sensor->parameter;
            $sensor->formatted_thresholds = $this->formatThresholds($sensor);
            $sensor->formatted_device_name = $sensor->device->name ?? '-';
            
            // Build actions array with forecasting controls
            $actions = [
                [
                    'type' => 'view',
                    'label' => 'Detail',
                    'url' => route('admin.sensors.show', $sensor),
                    'icon' => 'eye',
                    'color' => 'blue'
                ],
                [
                    'type' => 'edit',
                    'label' => 'Edit',
                    'url' => route('admin.sensors.edit', $sensor),
                    'icon' => 'edit',
                    'color' => 'indigo'
                ]
            ];

            // Add forecasting control buttons based on current status
            if ($sensor->forecasting_status === 'stopped') {
                $actions[] = [
                    'type' => 'form',
                    'label' => 'Start Forecasting',
                    'title' => 'Mulai Forecasting',
                    'url' => route('admin.sensors.start-forecasting', $sensor),
                    'icon' => 'play',
                    'color' => 'green',
                    'method' => 'POST',
                    'confirm' => 'Apakah Anda yakin ingin memulai forecasting untuk sensor ini?'
                ];
            } elseif ($sensor->forecasting_status === 'running') {
                $actions[] = [
                    'type' => 'form',
                    'label' => 'Pause Forecasting',
                    'title' => 'Jeda Forecasting',
                    'url' => route('admin.sensors.pause-forecasting', $sensor),
                    'icon' => 'pause',
                    'color' => 'yellow',
                    'method' => 'POST',
                    'confirm' => 'Apakah Anda yakin ingin menjeda forecasting untuk sensor ini?'
                ];
                $actions[] = [
                    'type' => 'form',
                    'label' => 'Stop Forecasting',
                    'title' => 'Hentikan Forecasting',
                    'url' => route('admin.sensors.stop-forecasting', $sensor),
                    'icon' => 'stop',
                    'color' => 'red',
                    'method' => 'POST',
                    'confirm' => 'Apakah Anda yakin ingin menghentikan forecasting untuk sensor ini?'
                ];
            } elseif ($sensor->forecasting_status === 'paused') {
                $actions[] = [
                    'type' => 'form',
                    'label' => 'Resume Forecasting',
                    'title' => 'Lanjutkan Forecasting',
                    'url' => route('admin.sensors.start-forecasting', $sensor),
                    'icon' => 'play',
                    'color' => 'green',
                    'method' => 'POST',
                    'confirm' => 'Apakah Anda yakin ingin melanjutkan forecasting untuk sensor ini?'
                ];
                $actions[] = [
                    'type' => 'form',
                    'label' => 'Stop Forecasting',
                    'title' => 'Hentikan Forecasting',
                    'url' => route('admin.sensors.stop-forecasting', $sensor),
                    'icon' => 'stop',
                    'color' => 'red',
                    'method' => 'POST',
                    'confirm' => 'Apakah Anda yakin ingin menghentikan forecasting untuk sensor ini?'
                ];
            }

            // Add delete action at the end
            $actions[] = [
                'type' => 'delete',
                'label' => 'Hapus',
                'url' => route('admin.sensors.destroy', $sensor),
                'icon' => 'trash',
                'color' => 'red',
                'method' => 'DELETE',
                'confirm' => 'Apakah Anda yakin ingin menghapus sensor ini?'
            ];

            $sensor->actions = $actions;
            return $sensor;
        });


        $parameterOptions = MasSensor::getParameterOptions();
        $statusOptions = MasSensor::getStatusOptions();
        $forecastingStatusOptions = MasSensor::getForecastingStatusOptions();

        return view('admin.mas_sensors.index', compact('sensors', 'tableHeaders', 'parameterOptions', 'statusOptions', 'forecastingStatusOptions'));
    }

    private function formatThresholds($sensor)
    {
        $thresholds = [];
        if ($sensor->threshold_safe)
            $thresholds[] = "Safe: {$sensor->threshold_safe}";
        if ($sensor->threshold_warning)
            $thresholds[] = "Warning: {$sensor->threshold_warning}";
        if ($sensor->threshold_danger)
            $thresholds[] = "Danger: {$sensor->threshold_danger}";

        return empty($thresholds) ? '-' : implode(', ', $thresholds);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $devices = MasDevice::where('status', 'active')->get();
        $models = MasModel::all();
        $parameterOptions = MasSensor::getParameterOptions();
        $statusOptions = MasSensor::getStatusOptions();

        return view('admin.mas_sensors.create', compact('devices', 'models', 'parameterOptions', 'statusOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'device_id' => 'required|exists:mas_devices,id',
            'sensor_code' => 'required|string|max:255|unique:mas_sensors,sensor_code',
            'parameter' => 'required|in:water_level,rainfall',
            'unit' => 'required|string|max:50',
            'description' => 'nullable|string|max:500',
            'mas_model_id' => 'nullable|exists:mas_models,id',
            'threshold_safe' => 'nullable|numeric|min:0',
            'threshold_warning' => 'nullable|numeric|min:0',
            'threshold_danger' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'last_seen' => 'nullable|date',
        ]);

        MasSensor::create($validated);

        return redirect()->route('admin.sensors.index')
            ->with('success', 'Sensor berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MasSensor $sensor): View
    {
        $sensor->load(['device', 'masModel']);
        $parameterOptions = MasSensor::getParameterOptions();
        $statusOptions = MasSensor::getStatusOptions();

        return view('admin.mas_sensors.show', compact('sensor', 'parameterOptions', 'statusOptions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasSensor $sensor): View
    {
        $devices = MasDevice::where('status', 'active')->get();
        $models = MasModel::all();
        $parameterOptions = MasSensor::getParameterOptions();
        $statusOptions = MasSensor::getStatusOptions();

        return view('admin.mas_sensors.edit', compact('sensor', 'devices', 'models', 'parameterOptions', 'statusOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasSensor $sensor): RedirectResponse
    {
        $validated = $request->validate([
            'device_id' => 'required|exists:mas_devices,id',
            'sensor_code' => 'required|string|max:255|unique:mas_sensors,sensor_code,' . $sensor->id,
            'parameter' => 'required|in:water_level,rainfall',
            'unit' => 'required|string|max:50',
            'description' => 'nullable|string|max:500',
            'mas_model_id' => 'nullable|exists:mas_models,id',
            'threshold_safe' => 'nullable|numeric|min:0',
            'threshold_warning' => 'nullable|numeric|min:0',
            'threshold_danger' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'last_seen' => 'nullable|date',
        ]);

        $sensor->update($validated);

        return redirect()->route('admin.sensors.index')
            ->with('success', 'Sensor berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasSensor $sensor): RedirectResponse
    {
        $sensor->delete();

        return redirect()->route('admin.sensors.index')
            ->with('success', 'Sensor berhasil dihapus.');
    }

    /**
     * Start forecasting for a sensor.
     */
    public function startForecasting(MasSensor $sensor, ForecastingService $forecastingService): RedirectResponse
    {
        try {
            // Call Python forecasting API to start forecasting
            $result = $forecastingService->startForecasting($sensor);
            
            if ($result['success']) {
                // Update sensor forecasting status only if API call succeeds
                $sensor->update(['forecasting_status' => 'running']);
                
                return redirect()->route('admin.sensors.index')
                    ->with('success', "Forecasting untuk sensor {$sensor->sensor_code} telah dimulai.");
            } else {
                return redirect()->route('admin.sensors.index')
                    ->with('error', "Gagal memulai forecasting: {$result['message']}");
            }
                
        } catch (\Exception $e) {
            return redirect()->route('admin.sensors.index')
                ->with('error', 'Gagal memulai forecasting. Silakan coba lagi.');
        }
    }

    /**
     * Pause forecasting for a sensor.
     */
    public function pauseForecasting(MasSensor $sensor, ForecastingService $forecastingService): RedirectResponse
    {
        try {
            // Call Python forecasting API to pause forecasting
            $result = $forecastingService->pauseForecasting($sensor);
            
            if ($result['success']) {
                // Update sensor forecasting status only if API call succeeds
                $sensor->update(['forecasting_status' => 'paused']);
                
                return redirect()->route('admin.sensors.index')
                    ->with('success', "Forecasting untuk sensor {$sensor->sensor_code} telah dijeda.");
            } else {
                return redirect()->route('admin.sensors.index')
                    ->with('error', "Gagal menjeda forecasting: {$result['message']}");
            }
                
        } catch (\Exception $e) {
            return redirect()->route('admin.sensors.index')
                ->with('error', 'Gagal menjeda forecasting. Silakan coba lagi.');
        }
    }

    /**
     * Stop forecasting for a sensor.
     */
    public function stopForecasting(MasSensor $sensor, ForecastingService $forecastingService): RedirectResponse
    {
        try {
            // Call Python forecasting API to stop forecasting
            $result = $forecastingService->stopForecasting($sensor);
            
            if ($result['success']) {
                // Update sensor forecasting status only if API call succeeds
                $sensor->update(['forecasting_status' => 'stopped']);
                
                return redirect()->route('admin.sensors.index')
                    ->with('success', "Forecasting untuk sensor {$sensor->sensor_code} telah dihentikan.");
            } else {
                return redirect()->route('admin.sensors.index')
                    ->with('error', "Gagal menghentikan forecasting: {$result['message']}");
            }
                
        } catch (\Exception $e) {
            return redirect()->route('admin.sensors.index')
                ->with('error', 'Gagal menghentikan forecasting. Silakan coba lagi.');
        }
    }
}
