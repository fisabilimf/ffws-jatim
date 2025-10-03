<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTraits;
use App\Models\SensorValue;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SensorValueController extends Controller
{
    use ApiResponseTraits;

    /**
     * Display a listing of sensor values
     */
    public function index(Request $request)
    {
        try {
            $query = SensorValue::with(['device', 'sensor'])
                ->select(['id', 'device_code', 'sensor_code', 'value', 'unit', 'timestamp', 'created_at']);

            // Apply filters
            if ($request->has('device_code')) {
                $query->where('device_code', $request->device_code);
            }

            if ($request->has('sensor_code')) {
                $query->where('sensor_code', $request->sensor_code);
            }

            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('timestamp', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay()
                ]);
            }

            $sensorValues = $query->orderBy('timestamp', 'desc')
                ->limit($request->get('limit', 100))
                ->get();

            return $this->successResponse($sensorValues, 'Data sensor value berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data sensor value');
        }
    }

    /**
     * Store a newly created sensor value
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'device_code' => 'required|string|max:255|exists:mas_devices,device_code',
                'sensor_code' => 'required|string|max:255|exists:mas_sensors,sensor_code',
                'value' => 'required|numeric',
                'unit' => 'nullable|string|max:50',
                'timestamp' => 'required|date',
            ]);

            $sensorValue = SensorValue::create($validated);

            return $this->successResponse($sensorValue->load(['device', 'sensor']), 'Sensor value berhasil dibuat', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal membuat sensor value');
        }
    }

    /**
     * Display the specified sensor value
     */
    public function show($id)
    {
        try {
            $sensorValue = SensorValue::with(['device', 'sensor'])
                ->findOrFail($id);

            return $this->successResponse($sensorValue, 'Data sensor value berhasil diambil');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Sensor value tidak ditemukan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data sensor value');
        }
    }

    /**
     * Update the specified sensor value
     */
    public function update(Request $request, $id)
    {
        try {
            $sensorValue = SensorValue::findOrFail($id);

            $validated = $request->validate([
                'device_code' => 'required|string|max:255|exists:mas_devices,device_code',
                'sensor_code' => 'required|string|max:255|exists:mas_sensors,sensor_code',
                'value' => 'required|numeric',
                'unit' => 'nullable|string|max:50',
                'timestamp' => 'required|date',
            ]);

            $sensorValue->update($validated);

            return $this->successResponse($sensorValue->load(['device', 'sensor']), 'Sensor value berhasil diupdate');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Sensor value tidak ditemukan');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengupdate sensor value');
        }
    }

    /**
     * Remove the specified sensor value
     */
    public function destroy($id)
    {
        try {
            $sensorValue = SensorValue::findOrFail($id);
            $sensorValue->delete();

            return $this->successResponse(null, 'Sensor value berhasil dihapus');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Sensor value tidak ditemukan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal menghapus sensor value');
        }
    }

    /**
     * Get sensor values by device code
     */
    public function getByDevice($deviceCode)
    {
        try {
            $sensorValues = SensorValue::with(['device', 'sensor'])
                ->where('device_code', $deviceCode)
                ->orderBy('timestamp', 'desc')
                ->limit(100)
                ->get();

            return $this->successResponse($sensorValues, 'Data sensor value berdasarkan device berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data sensor value berdasarkan device');
        }
    }

    /**
     * Get sensor values by sensor code
     */
    public function getBySensor($sensorCode)
    {
        try {
            $sensorValues = SensorValue::with(['device', 'sensor'])
                ->where('sensor_code', $sensorCode)
                ->orderBy('timestamp', 'desc')
                ->limit(100)
                ->get();

            return $this->successResponse($sensorValues, 'Data sensor value berdasarkan sensor berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data sensor value berdasarkan sensor');
        }
    }

    /**
     * Get sensor values by device and sensor combination
     */
    public function getByDeviceAndSensor($deviceCode, $sensorCode)
    {
        try {
            $sensorValues = SensorValue::with(['device', 'sensor'])
                ->where('device_code', $deviceCode)
                ->where('sensor_code', $sensorCode)
                ->orderBy('timestamp', 'desc')
                ->limit(100)
                ->get();

            return $this->successResponse($sensorValues, 'Data sensor value berdasarkan device dan sensor berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data sensor value berdasarkan device dan sensor');
        }
    }

    /**
     * Get sensor values by date range
     */
    public function getByDateRange(Request $request)
    {
        try {
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'device_code' => 'nullable|string|exists:mas_devices,device_code',
                'sensor_code' => 'nullable|string|exists:mas_sensors,sensor_code',
                'limit' => 'nullable|integer|min:1|max:1000'
            ]);

            $query = SensorValue::with(['device', 'sensor'])
                ->whereBetween('timestamp', [
                    Carbon::parse($validated['start_date'])->startOfDay(),
                    Carbon::parse($validated['end_date'])->endOfDay()
                ]);

            if (!empty($validated['device_code'])) {
                $query->where('device_code', $validated['device_code']);
            }

            if (!empty($validated['sensor_code'])) {
                $query->where('sensor_code', $validated['sensor_code']);
            }

            $sensorValues = $query->orderBy('timestamp', 'desc')
                ->limit($validated['limit'] ?? 100)
                ->get();

            return $this->successResponse($sensorValues, 'Data sensor value berdasarkan rentang tanggal berhasil diambil');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data sensor value berdasarkan rentang tanggal');
        }
    }

    /**
     * Get latest sensor values for each device-sensor combination
     */
    public function getLatest()
    {
        try {
            $sensorValues = SensorValue::with(['device', 'sensor'])
                ->whereIn('id', function($query) {
                    $query->selectRaw('MAX(id)')
                        ->from('sensor_values')
                        ->groupBy(['device_code', 'sensor_code']);
                })
                ->orderBy('timestamp', 'desc')
                ->get();

            return $this->successResponse($sensorValues, 'Data sensor value terbaru berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data sensor value terbaru');
        }
    }

    /**
     * Get statistics for sensor values
     */
    public function getStatistics(Request $request)
    {
        try {
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'device_code' => 'nullable|string|exists:mas_devices,device_code',
                'sensor_code' => 'nullable|string|exists:mas_sensors,sensor_code',
            ]);

            $query = SensorValue::whereBetween('timestamp', [
                Carbon::parse($validated['start_date'])->startOfDay(),
                Carbon::parse($validated['end_date'])->endOfDay()
            ]);

            if (!empty($validated['device_code'])) {
                $query->where('device_code', $validated['device_code']);
            }

            if (!empty($validated['sensor_code'])) {
                $query->where('sensor_code', $validated['sensor_code']);
            }

            $statistics = [
                'total_records' => $query->count(),
                'value_stats' => [
                    'min' => $query->min('value'),
                    'max' => $query->max('value'),
                    'avg' => round($query->avg('value'), 4),
                ],
                'date_range' => [
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],
                    'days' => Carbon::parse($validated['start_date'])->diffInDays(Carbon::parse($validated['end_date'])) + 1
                ]
            ];

            return $this->successResponse($statistics, 'Statistik sensor value berhasil diambil');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil statistik sensor value');
        }
    }

    /**
     * Check sensor thresholds for values
     */
    public function checkThresholds(Request $request)
    {
        try {
            $validated = $request->validate([
                'device_code' => 'nullable|string|exists:mas_devices,device_code',
                'sensor_code' => 'nullable|string|exists:mas_sensors,sensor_code',
                'limit' => 'nullable|integer|min:1|max:100'
            ]);

            $query = SensorValue::with(['device', 'sensor', 'sensor.threshold'])
                ->whereHas('sensor.threshold', function($q) {
                    $q->where('is_active', true);
                });

            if (!empty($validated['device_code'])) {
                $query->where('device_code', $validated['device_code']);
            }

            if (!empty($validated['sensor_code'])) {
                $query->where('sensor_code', $validated['sensor_code']);
            }

            $sensorValues = $query->orderBy('timestamp', 'desc')
                ->limit($validated['limit'] ?? 50)
                ->get();

            $results = [];

            foreach ($sensorValues as $value) {
                $threshold = $value->sensor->threshold;
                $status = 'unknown';

                if ($threshold) {
                    // Check critical range first
                    if (($threshold->critical_min !== null && $value->value <= $threshold->critical_min) ||
                        ($threshold->critical_max !== null && $value->value >= $threshold->critical_max)) {
                        $status = 'critical';
                    }
                    // Check warning range
                    elseif (($threshold->warning_min !== null && $value->value <= $threshold->warning_min) ||
                            ($threshold->warning_max !== null && $value->value >= $threshold->warning_max)) {
                        $status = 'warning';
                    }
                    // Check normal range
                    elseif (($threshold->normal_min === null || $value->value >= $threshold->normal_min) &&
                            ($threshold->normal_max === null || $value->value <= $threshold->normal_max)) {
                        $status = 'normal';
                    }
                }

                $results[] = [
                    'id' => $value->id,
                    'device_code' => $value->device_code,
                    'sensor_code' => $value->sensor_code,
                    'value' => $value->value,
                    'unit' => $value->unit,
                    'timestamp' => $value->timestamp,
                    'threshold_status' => $status,
                    'threshold' => $threshold
                ];
            }

            return $this->successResponse($results, 'Pemeriksaan threshold sensor value berhasil dilakukan');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal memeriksa threshold sensor value');
        }
    }

    /**
     * Bulk import sensor values
     */
    public function bulkImport(Request $request)
    {
        try {
            $validated = $request->validate([
                'values' => 'required|array|min:1',
                'values.*.device_code' => 'required|string|max:255|exists:mas_devices,device_code',
                'values.*.sensor_code' => 'required|string|max:255|exists:mas_sensors,sensor_code',
                'values.*.value' => 'required|numeric',
                'values.*.unit' => 'nullable|string|max:50',
                'values.*.timestamp' => 'required|date',
            ]);

            $createdValues = [];
            $errors = [];

            foreach ($validated['values'] as $index => $valueData) {
                try {
                    $sensorValue = SensorValue::create($valueData);
                    $createdValues[] = $sensorValue;
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 1) . ": " . $e->getMessage();
                }
            }

            $result = [
                'created_count' => count($createdValues),
                'error_count' => count($errors),
                'created_values' => $createdValues,
                'errors' => $errors
            ];

            if (count($createdValues) > 0) {
                return $this->successResponse($result, count($createdValues) . ' sensor value berhasil diimport');
            } else {
                return $this->badRequestResponse('Tidak ada sensor value yang berhasil diimport', $result);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal melakukan bulk import sensor value');
        }
    }
}