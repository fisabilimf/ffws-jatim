<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTraits;
use App\Models\MasSensorThreshold;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasSensorThresholdController extends Controller
{
    use ApiResponseTraits;

    /**
     * Display a listing of sensor thresholds
     */
    public function index()
    {
        try {
            $sensorThresholds = MasSensorThreshold::with('sensor')
                ->select(['id', 'sensor_code', 'normal_min', 'normal_max', 'warning_min', 'warning_max', 'critical_min', 'critical_max', 'is_active', 'created_at'])
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->successResponse($sensorThresholds, 'Data threshold sensor berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data threshold sensor');
        }
    }

    /**
     * Store a newly created sensor threshold
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'sensor_code' => 'required|string|max:255|exists:mas_sensors,sensor_code|unique:mas_sensor_thresholds,sensor_code',
                'normal_min' => 'nullable|numeric',
                'normal_max' => 'nullable|numeric',
                'warning_min' => 'nullable|numeric',
                'warning_max' => 'nullable|numeric',
                'critical_min' => 'nullable|numeric',
                'critical_max' => 'nullable|numeric',
                'is_active' => 'required|boolean',
            ]);

            // Additional validation for logical threshold ranges
            if ($validated['normal_min'] && $validated['normal_max'] && $validated['normal_min'] >= $validated['normal_max']) {
                return $this->badRequestResponse('Normal min harus lebih kecil dari normal max');
            }

            if ($validated['warning_min'] && $validated['warning_max'] && $validated['warning_min'] >= $validated['warning_max']) {
                return $this->badRequestResponse('Warning min harus lebih kecil dari warning max');
            }

            if ($validated['critical_min'] && $validated['critical_max'] && $validated['critical_min'] >= $validated['critical_max']) {
                return $this->badRequestResponse('Critical min harus lebih kecil dari critical max');
            }

            $sensorThreshold = MasSensorThreshold::create($validated);

            return $this->successResponse($sensorThreshold->load('sensor'), 'Threshold sensor berhasil dibuat', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal membuat threshold sensor');
        }
    }

    /**
     * Display the specified sensor threshold
     */
    public function show($id)
    {
        try {
            $sensorThreshold = MasSensorThreshold::with('sensor')
                ->findOrFail($id);

            return $this->successResponse($sensorThreshold, 'Data threshold sensor berhasil diambil');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Threshold sensor tidak ditemukan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data threshold sensor');
        }
    }

    /**
     * Update the specified sensor threshold
     */
    public function update(Request $request, $id)
    {
        try {
            $sensorThreshold = MasSensorThreshold::findOrFail($id);

            $validated = $request->validate([
                'sensor_code' => [
                    'required',
                    'string',
                    'max:255',
                    'exists:mas_sensors,sensor_code',
                    Rule::unique('mas_sensor_thresholds', 'sensor_code')->ignore($sensorThreshold->id)
                ],
                'normal_min' => 'nullable|numeric',
                'normal_max' => 'nullable|numeric',
                'warning_min' => 'nullable|numeric',
                'warning_max' => 'nullable|numeric',
                'critical_min' => 'nullable|numeric',
                'critical_max' => 'nullable|numeric',
                'is_active' => 'required|boolean',
            ]);

            // Additional validation for logical threshold ranges
            if ($validated['normal_min'] && $validated['normal_max'] && $validated['normal_min'] >= $validated['normal_max']) {
                return $this->badRequestResponse('Normal min harus lebih kecil dari normal max');
            }

            if ($validated['warning_min'] && $validated['warning_max'] && $validated['warning_min'] >= $validated['warning_max']) {
                return $this->badRequestResponse('Warning min harus lebih kecil dari warning max');
            }

            if ($validated['critical_min'] && $validated['critical_max'] && $validated['critical_min'] >= $validated['critical_max']) {
                return $this->badRequestResponse('Critical min harus lebih kecil dari critical max');
            }

            $sensorThreshold->update($validated);

            return $this->successResponse($sensorThreshold->load('sensor'), 'Threshold sensor berhasil diupdate');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Threshold sensor tidak ditemukan');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengupdate threshold sensor');
        }
    }

    /**
     * Remove the specified sensor threshold
     */
    public function destroy($id)
    {
        try {
            $sensorThreshold = MasSensorThreshold::findOrFail($id);
            $sensorThreshold->delete();

            return $this->successResponse(null, 'Threshold sensor berhasil dihapus');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Threshold sensor tidak ditemukan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal menghapus threshold sensor');
        }
    }

    /**
     * Get sensor threshold by sensor code
     */
    public function getBySensor($sensorCode)
    {
        try {
            $sensorThreshold = MasSensorThreshold::with('sensor')
                ->where('sensor_code', $sensorCode)
                ->first();

            if (!$sensorThreshold) {
                return $this->notFoundResponse('Threshold untuk sensor tidak ditemukan');
            }

            return $this->successResponse($sensorThreshold, 'Data threshold sensor berdasarkan sensor berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data threshold sensor berdasarkan sensor');
        }
    }

    /**
     * Get active sensor thresholds
     */
    public function getActive()
    {
        try {
            $sensorThresholds = MasSensorThreshold::with('sensor')
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->successResponse($sensorThresholds, 'Data threshold sensor aktif berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data threshold sensor aktif');
        }
    }

    /**
     * Check threshold status for a specific value and sensor
     */
    public function checkThreshold(Request $request, $sensorCode)
    {
        try {
            $validated = $request->validate([
                'value' => 'required|numeric',
            ]);

            $threshold = MasSensorThreshold::where('sensor_code', $sensorCode)
                ->where('is_active', true)
                ->first();

            if (!$threshold) {
                return $this->notFoundResponse('Threshold untuk sensor tidak ditemukan');
            }

            $value = $validated['value'];
            $status = 'unknown';

            // Check critical range first
            if (($threshold->critical_min !== null && $value <= $threshold->critical_min) ||
                ($threshold->critical_max !== null && $value >= $threshold->critical_max)) {
                $status = 'critical';
            }
            // Check warning range
            elseif (($threshold->warning_min !== null && $value <= $threshold->warning_min) ||
                    ($threshold->warning_max !== null && $value >= $threshold->warning_max)) {
                $status = 'warning';
            }
            // Check normal range
            elseif (($threshold->normal_min === null || $value >= $threshold->normal_min) &&
                    ($threshold->normal_max === null || $value <= $threshold->normal_max)) {
                $status = 'normal';
            }

            $result = [
                'sensor_code' => $sensorCode,
                'value' => $value,
                'status' => $status,
                'threshold' => $threshold
            ];

            return $this->successResponse($result, 'Status threshold berhasil diperiksa');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal memeriksa status threshold');
        }
    }
}