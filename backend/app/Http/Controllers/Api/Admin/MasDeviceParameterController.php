<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTraits;
use App\Models\MasDeviceParameter;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasDeviceParameterController extends Controller
{
    use ApiResponseTraits;

    /**
     * Display a listing of device parameters
     */
    public function index()
    {
        try {
            $deviceParameters = MasDeviceParameter::with(['device', 'sensor'])
                ->select(['id', 'device_code', 'sensor_code', 'is_primary', 'is_active', 'created_at'])
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->successResponse($deviceParameters, 'Data parameter device berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data parameter device');
        }
    }

    /**
     * Store a newly created device parameter
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'device_code' => 'required|string|max:255|exists:mas_devices,device_code',
                'sensor_code' => 'required|string|max:255|exists:mas_sensors,sensor_code',
                'is_primary' => 'required|boolean',
                'is_active' => 'required|boolean',
            ]);

            // Check for unique combination
            $existingParameter = MasDeviceParameter::where('device_code', $validated['device_code'])
                ->where('sensor_code', $validated['sensor_code'])
                ->first();

            if ($existingParameter) {
                return $this->badRequestResponse('Kombinasi device dan sensor sudah ada');
            }

            $deviceParameter = MasDeviceParameter::create($validated);

            return $this->successResponse($deviceParameter->load(['device', 'sensor']), 'Parameter device berhasil dibuat', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal membuat parameter device');
        }
    }

    /**
     * Display the specified device parameter
     */
    public function show($id)
    {
        try {
            $deviceParameter = MasDeviceParameter::with(['device', 'sensor'])
                ->findOrFail($id);

            return $this->successResponse($deviceParameter, 'Data parameter device berhasil diambil');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Parameter device tidak ditemukan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data parameter device');
        }
    }

    /**
     * Update the specified device parameter
     */
    public function update(Request $request, $id)
    {
        try {
            $deviceParameter = MasDeviceParameter::findOrFail($id);

            $validated = $request->validate([
                'device_code' => 'required|string|max:255|exists:mas_devices,device_code',
                'sensor_code' => 'required|string|max:255|exists:mas_sensors,sensor_code',
                'is_primary' => 'required|boolean',
                'is_active' => 'required|boolean',
            ]);

            // Check for unique combination (excluding current record)
            $existingParameter = MasDeviceParameter::where('device_code', $validated['device_code'])
                ->where('sensor_code', $validated['sensor_code'])
                ->where('id', '!=', $id)
                ->first();

            if ($existingParameter) {
                return $this->badRequestResponse('Kombinasi device dan sensor sudah ada');
            }

            $deviceParameter->update($validated);

            return $this->successResponse($deviceParameter->load(['device', 'sensor']), 'Parameter device berhasil diupdate');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Parameter device tidak ditemukan');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengupdate parameter device');
        }
    }

    /**
     * Remove the specified device parameter
     */
    public function destroy($id)
    {
        try {
            $deviceParameter = MasDeviceParameter::findOrFail($id);
            $deviceParameter->delete();

            return $this->successResponse(null, 'Parameter device berhasil dihapus');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Parameter device tidak ditemukan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal menghapus parameter device');
        }
    }

    /**
     * Get device parameters by device code
     */
    public function getByDevice($deviceCode)
    {
        try {
            $deviceParameters = MasDeviceParameter::with(['device', 'sensor'])
                ->where('device_code', $deviceCode)
                ->orderBy('is_primary', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->successResponse($deviceParameters, 'Data parameter device berdasarkan device berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data parameter device berdasarkan device');
        }
    }

    /**
     * Get device parameters by sensor code
     */
    public function getBySensor($sensorCode)
    {
        try {
            $deviceParameters = MasDeviceParameter::with(['device', 'sensor'])
                ->where('sensor_code', $sensorCode)
                ->orderBy('is_primary', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->successResponse($deviceParameters, 'Data parameter device berdasarkan sensor berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data parameter device berdasarkan sensor');
        }
    }

    /**
     * Get primary device parameters
     */
    public function getPrimary()
    {
        try {
            $deviceParameters = MasDeviceParameter::with(['device', 'sensor'])
                ->where('is_primary', true)
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->successResponse($deviceParameters, 'Data parameter device utama berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data parameter device utama');
        }
    }

    /**
     * Get active device parameters
     */
    public function getActive()
    {
        try {
            $deviceParameters = MasDeviceParameter::with(['device', 'sensor'])
                ->where('is_active', true)
                ->orderBy('is_primary', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->successResponse($deviceParameters, 'Data parameter device aktif berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data parameter device aktif');
        }
    }
}