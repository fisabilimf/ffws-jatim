<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTraits;
use App\Models\CalculatedDischarge;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalculatedDischargeController extends Controller
{
    use ApiResponseTraits;

    /**
     * Display a listing of calculated discharges
     */
    public function index(Request $request)
    {
        try {
            $query = CalculatedDischarge::with(['device', 'sensor'])
                ->select(['id', 'device_code', 'sensor_code', 'water_level', 'discharge', 'timestamp', 'created_at']);

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

            $calculatedDischarges = $query->orderBy('timestamp', 'desc')
                ->limit($request->get('limit', 100))
                ->get();

            return $this->successResponse($calculatedDischarges, 'Data calculated discharge berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data calculated discharge');
        }
    }

    /**
     * Store a newly created calculated discharge
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'device_code' => 'required|string|max:255|exists:mas_devices,device_code',
                'sensor_code' => 'required|string|max:255|exists:mas_sensors,sensor_code',
                'water_level' => 'required|numeric',
                'discharge' => 'required|numeric',
                'timestamp' => 'required|date',
            ]);

            $calculatedDischarge = CalculatedDischarge::create($validated);

            return $this->successResponse($calculatedDischarge->load(['device', 'sensor']), 'Calculated discharge berhasil dibuat', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal membuat calculated discharge');
        }
    }

    /**
     * Display the specified calculated discharge
     */
    public function show($id)
    {
        try {
            $calculatedDischarge = CalculatedDischarge::with(['device', 'sensor'])
                ->findOrFail($id);

            return $this->successResponse($calculatedDischarge, 'Data calculated discharge berhasil diambil');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Calculated discharge tidak ditemukan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data calculated discharge');
        }
    }

    /**
     * Update the specified calculated discharge
     */
    public function update(Request $request, $id)
    {
        try {
            $calculatedDischarge = CalculatedDischarge::findOrFail($id);

            $validated = $request->validate([
                'device_code' => 'required|string|max:255|exists:mas_devices,device_code',
                'sensor_code' => 'required|string|max:255|exists:mas_sensors,sensor_code',
                'water_level' => 'required|numeric',
                'discharge' => 'required|numeric',
                'timestamp' => 'required|date',
            ]);

            $calculatedDischarge->update($validated);

            return $this->successResponse($calculatedDischarge->load(['device', 'sensor']), 'Calculated discharge berhasil diupdate');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Calculated discharge tidak ditemukan');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengupdate calculated discharge');
        }
    }

    /**
     * Remove the specified calculated discharge
     */
    public function destroy($id)
    {
        try {
            $calculatedDischarge = CalculatedDischarge::findOrFail($id);
            $calculatedDischarge->delete();

            return $this->successResponse(null, 'Calculated discharge berhasil dihapus');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Calculated discharge tidak ditemukan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal menghapus calculated discharge');
        }
    }

    /**
     * Get calculated discharges by device code
     */
    public function getByDevice($deviceCode)
    {
        try {
            $calculatedDischarges = CalculatedDischarge::with(['device', 'sensor'])
                ->where('device_code', $deviceCode)
                ->orderBy('timestamp', 'desc')
                ->limit(100)
                ->get();

            return $this->successResponse($calculatedDischarges, 'Data calculated discharge berdasarkan device berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data calculated discharge berdasarkan device');
        }
    }

    /**
     * Get calculated discharges by sensor code
     */
    public function getBySensor($sensorCode)
    {
        try {
            $calculatedDischarges = CalculatedDischarge::with(['device', 'sensor'])
                ->where('sensor_code', $sensorCode)
                ->orderBy('timestamp', 'desc')
                ->limit(100)
                ->get();

            return $this->successResponse($calculatedDischarges, 'Data calculated discharge berdasarkan sensor berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data calculated discharge berdasarkan sensor');
        }
    }

    /**
     * Get calculated discharges by date range
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

            $query = CalculatedDischarge::with(['device', 'sensor'])
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

            $calculatedDischarges = $query->orderBy('timestamp', 'desc')
                ->limit($validated['limit'] ?? 100)
                ->get();

            return $this->successResponse($calculatedDischarges, 'Data calculated discharge berdasarkan rentang tanggal berhasil diambil');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data calculated discharge berdasarkan rentang tanggal');
        }
    }

    /**
     * Get latest calculated discharges for each device-sensor combination
     */
    public function getLatest()
    {
        try {
            $calculatedDischarges = CalculatedDischarge::with(['device', 'sensor'])
                ->whereIn('id', function($query) {
                    $query->selectRaw('MAX(id)')
                        ->from('calculated_discharges')
                        ->groupBy(['device_code', 'sensor_code']);
                })
                ->orderBy('timestamp', 'desc')
                ->get();

            return $this->successResponse($calculatedDischarges, 'Data calculated discharge terbaru berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data calculated discharge terbaru');
        }
    }

    /**
     * Get statistics for calculated discharges
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

            $query = CalculatedDischarge::whereBetween('timestamp', [
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
                'discharge_stats' => [
                    'min' => $query->min('discharge'),
                    'max' => $query->max('discharge'),
                    'avg' => round($query->avg('discharge'), 4),
                ],
                'water_level_stats' => [
                    'min' => $query->min('water_level'),
                    'max' => $query->max('water_level'),
                    'avg' => round($query->avg('water_level'), 4),
                ],
                'date_range' => [
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],
                    'days' => Carbon::parse($validated['start_date'])->diffInDays(Carbon::parse($validated['end_date'])) + 1
                ]
            ];

            return $this->successResponse($statistics, 'Statistik calculated discharge berhasil diambil');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil statistik calculated discharge');
        }
    }

    /**
     * Bulk import calculated discharges
     */
    public function bulkImport(Request $request)
    {
        try {
            $validated = $request->validate([
                'discharges' => 'required|array|min:1',
                'discharges.*.device_code' => 'required|string|max:255|exists:mas_devices,device_code',
                'discharges.*.sensor_code' => 'required|string|max:255|exists:mas_sensors,sensor_code',
                'discharges.*.water_level' => 'required|numeric',
                'discharges.*.discharge' => 'required|numeric',
                'discharges.*.timestamp' => 'required|date',
            ]);

            $createdDischarges = [];
            $errors = [];

            foreach ($validated['discharges'] as $index => $dischargeData) {
                try {
                    $discharge = CalculatedDischarge::create($dischargeData);
                    $createdDischarges[] = $discharge;
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 1) . ": " . $e->getMessage();
                }
            }

            $result = [
                'created_count' => count($createdDischarges),
                'error_count' => count($errors),
                'created_discharges' => $createdDischarges,
                'errors' => $errors
            ];

            if (count($createdDischarges) > 0) {
                return $this->successResponse($result, count($createdDischarges) . ' calculated discharge berhasil diimport');
            } else {
                return $this->badRequestResponse('Tidak ada calculated discharge yang berhasil diimport', $result);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal melakukan bulk import calculated discharge');
        }
    }
}