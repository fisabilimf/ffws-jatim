<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTraits;
use App\Models\MasSensorParameter;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasSensorParameterController extends Controller
{
    use ApiResponseTraits;

    /**
     * Display a listing of sensor parameters
     */
    public function index()
    {
        try {
            $sensorParameters = MasSensorParameter::with('sensor')
                ->select(['id', 'sensor_code', 'parameter_name', 'unit', 'is_active', 'created_at'])
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->successResponse($sensorParameters, 'Data parameter sensor berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data parameter sensor');
        }
    }

    /**
     * Store a newly created sensor parameter
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'sensor_code' => 'required|string|max:255|exists:mas_sensors,sensor_code',
                'parameter_name' => 'required|string|max:255',
                'unit' => 'nullable|string|max:50',
                'is_active' => 'required|boolean',
            ]);

            // Check for unique combination
            $existingParameter = MasSensorParameter::where('sensor_code', $validated['sensor_code'])
                ->where('parameter_name', $validated['parameter_name'])
                ->first();

            if ($existingParameter) {
                return $this->badRequestResponse('Kombinasi sensor dan parameter name sudah ada');
            }

            $sensorParameter = MasSensorParameter::create($validated);

            return $this->successResponse($sensorParameter->load('sensor'), 'Parameter sensor berhasil dibuat', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal membuat parameter sensor');
        }
    }

    /**
     * Display the specified sensor parameter
     */
    public function show($id)
    {
        try {
            $sensorParameter = MasSensorParameter::with('sensor')
                ->findOrFail($id);

            return $this->successResponse($sensorParameter, 'Data parameter sensor berhasil diambil');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Parameter sensor tidak ditemukan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data parameter sensor');
        }
    }

    /**
     * Update the specified sensor parameter
     */
    public function update(Request $request, $id)
    {
        try {
            $sensorParameter = MasSensorParameter::findOrFail($id);

            $validated = $request->validate([
                'sensor_code' => 'required|string|max:255|exists:mas_sensors,sensor_code',
                'parameter_name' => 'required|string|max:255',
                'unit' => 'nullable|string|max:50',
                'is_active' => 'required|boolean',
            ]);

            // Check for unique combination (excluding current record)
            $existingParameter = MasSensorParameter::where('sensor_code', $validated['sensor_code'])
                ->where('parameter_name', $validated['parameter_name'])
                ->where('id', '!=', $id)
                ->first();

            if ($existingParameter) {
                return $this->badRequestResponse('Kombinasi sensor dan parameter name sudah ada');
            }

            $sensorParameter->update($validated);

            return $this->successResponse($sensorParameter->load('sensor'), 'Parameter sensor berhasil diupdate');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Parameter sensor tidak ditemukan');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengupdate parameter sensor');
        }
    }

    /**
     * Remove the specified sensor parameter
     */
    public function destroy($id)
    {
        try {
            $sensorParameter = MasSensorParameter::findOrFail($id);
            $sensorParameter->delete();

            return $this->successResponse(null, 'Parameter sensor berhasil dihapus');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Parameter sensor tidak ditemukan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal menghapus parameter sensor');
        }
    }

    /**
     * Get sensor parameters by sensor code
     */
    public function getBySensor($sensorCode)
    {
        try {
            $sensorParameters = MasSensorParameter::with('sensor')
                ->where('sensor_code', $sensorCode)
                ->orderBy('parameter_name')
                ->get();

            return $this->successResponse($sensorParameters, 'Data parameter sensor berdasarkan sensor berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data parameter sensor berdasarkan sensor');
        }
    }

    /**
     * Get sensor parameters by parameter name
     */
    public function getByParameterName($parameterName)
    {
        try {
            $sensorParameters = MasSensorParameter::with('sensor')
                ->where('parameter_name', 'like', '%' . $parameterName . '%')
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->successResponse($sensorParameters, 'Data parameter sensor berdasarkan nama parameter berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data parameter sensor berdasarkan nama parameter');
        }
    }

    /**
     * Get active sensor parameters
     */
    public function getActive()
    {
        try {
            $sensorParameters = MasSensorParameter::with('sensor')
                ->where('is_active', true)
                ->orderBy('parameter_name')
                ->get();

            return $this->successResponse($sensorParameters, 'Data parameter sensor aktif berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data parameter sensor aktif');
        }
    }

    /**
     * Get sensor parameters by unit
     */
    public function getByUnit($unit)
    {
        try {
            $sensorParameters = MasSensorParameter::with('sensor')
                ->where('unit', $unit)
                ->orderBy('parameter_name')
                ->get();

            return $this->successResponse($sensorParameters, 'Data parameter sensor berdasarkan unit berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data parameter sensor berdasarkan unit');
        }
    }
}