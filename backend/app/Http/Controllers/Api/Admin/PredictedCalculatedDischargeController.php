<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTraits;
use App\Models\PredictedCalculatedDischarge;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PredictedCalculatedDischargeController extends Controller
{
    use ApiResponseTraits;

    /**
     * Display a listing of predicted calculated discharges
     */
    public function index(Request $request)
    {
        try {
            $query = PredictedCalculatedDischarge::with(['device', 'sensor', 'model'])
                ->select(['id', 'device_code', 'sensor_code', 'model_code', 'water_level', 'discharge', 'prediction_timestamp', 'created_at']);

            // Apply filters
            if ($request->has('device_code')) {
                $query->where('device_code', $request->device_code);
            }

            if ($request->has('sensor_code')) {
                $query->where('sensor_code', $request->sensor_code);
            }

            if ($request->has('model_code')) {
                $query->where('model_code', $request->model_code);
            }

            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('prediction_timestamp', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay()
                ]);
            }

            $predictedDischarges = $query->orderBy('prediction_timestamp', 'desc')
                ->limit($request->get('limit', 100))
                ->get();

            return $this->successResponse($predictedDischarges, 'Data predicted calculated discharge berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data predicted calculated discharge');
        }
    }

    /**
     * Store a newly created predicted calculated discharge
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'device_code' => 'required|string|max:255|exists:mas_devices,device_code',
                'sensor_code' => 'required|string|max:255|exists:mas_sensors,sensor_code',
                'model_code' => 'required|string|max:255|exists:mas_models,model_code',
                'water_level' => 'required|numeric',
                'discharge' => 'required|numeric',
                'prediction_timestamp' => 'required|date',
            ]);

            $predictedDischarge = PredictedCalculatedDischarge::create($validated);

            return $this->successResponse($predictedDischarge->load(['device', 'sensor', 'model']), 'Predicted calculated discharge berhasil dibuat', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal membuat predicted calculated discharge');
        }
    }

    /**
     * Display the specified predicted calculated discharge
     */
    public function show($id)
    {
        try {
            $predictedDischarge = PredictedCalculatedDischarge::with(['device', 'sensor', 'model'])
                ->findOrFail($id);

            return $this->successResponse($predictedDischarge, 'Data predicted calculated discharge berhasil diambil');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Predicted calculated discharge tidak ditemukan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data predicted calculated discharge');
        }
    }

    /**
     * Update the specified predicted calculated discharge
     */
    public function update(Request $request, $id)
    {
        try {
            $predictedDischarge = PredictedCalculatedDischarge::findOrFail($id);

            $validated = $request->validate([
                'device_code' => 'required|string|max:255|exists:mas_devices,device_code',
                'sensor_code' => 'required|string|max:255|exists:mas_sensors,sensor_code',
                'model_code' => 'required|string|max:255|exists:mas_models,model_code',
                'water_level' => 'required|numeric',
                'discharge' => 'required|numeric',
                'prediction_timestamp' => 'required|date',
            ]);

            $predictedDischarge->update($validated);

            return $this->successResponse($predictedDischarge->load(['device', 'sensor', 'model']), 'Predicted calculated discharge berhasil diupdate');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Predicted calculated discharge tidak ditemukan');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengupdate predicted calculated discharge');
        }
    }

    /**
     * Remove the specified predicted calculated discharge
     */
    public function destroy($id)
    {
        try {
            $predictedDischarge = PredictedCalculatedDischarge::findOrFail($id);
            $predictedDischarge->delete();

            return $this->successResponse(null, 'Predicted calculated discharge berhasil dihapus');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Predicted calculated discharge tidak ditemukan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal menghapus predicted calculated discharge');
        }
    }

    /**
     * Get predicted discharges by device code
     */
    public function getByDevice($deviceCode)
    {
        try {
            $predictedDischarges = PredictedCalculatedDischarge::with(['device', 'sensor', 'model'])
                ->where('device_code', $deviceCode)
                ->orderBy('prediction_timestamp', 'desc')
                ->limit(100)
                ->get();

            return $this->successResponse($predictedDischarges, 'Data predicted calculated discharge berdasarkan device berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data predicted calculated discharge berdasarkan device');
        }
    }

    /**
     * Get predicted discharges by sensor code
     */
    public function getBySensor($sensorCode)
    {
        try {
            $predictedDischarges = PredictedCalculatedDischarge::with(['device', 'sensor', 'model'])
                ->where('sensor_code', $sensorCode)
                ->orderBy('prediction_timestamp', 'desc')
                ->limit(100)
                ->get();

            return $this->successResponse($predictedDischarges, 'Data predicted calculated discharge berdasarkan sensor berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data predicted calculated discharge berdasarkan sensor');
        }
    }

    /**
     * Get predicted discharges by model code
     */
    public function getByModel($modelCode)
    {
        try {
            $predictedDischarges = PredictedCalculatedDischarge::with(['device', 'sensor', 'model'])
                ->where('model_code', $modelCode)
                ->orderBy('prediction_timestamp', 'desc')
                ->limit(100)
                ->get();

            return $this->successResponse($predictedDischarges, 'Data predicted calculated discharge berdasarkan model berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data predicted calculated discharge berdasarkan model');
        }
    }

    /**
     * Get predicted discharges by date range
     */
    public function getByDateRange(Request $request)
    {
        try {
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'device_code' => 'nullable|string|exists:mas_devices,device_code',
                'sensor_code' => 'nullable|string|exists:mas_sensors,sensor_code',
                'model_code' => 'nullable|string|exists:mas_models,model_code',
                'limit' => 'nullable|integer|min:1|max:1000'
            ]);

            $query = PredictedCalculatedDischarge::with(['device', 'sensor', 'model'])
                ->whereBetween('prediction_timestamp', [
                    Carbon::parse($validated['start_date'])->startOfDay(),
                    Carbon::parse($validated['end_date'])->endOfDay()
                ]);

            if (!empty($validated['device_code'])) {
                $query->where('device_code', $validated['device_code']);
            }

            if (!empty($validated['sensor_code'])) {
                $query->where('sensor_code', $validated['sensor_code']);
            }

            if (!empty($validated['model_code'])) {
                $query->where('model_code', $validated['model_code']);
            }

            $predictedDischarges = $query->orderBy('prediction_timestamp', 'desc')
                ->limit($validated['limit'] ?? 100)
                ->get();

            return $this->successResponse($predictedDischarges, 'Data predicted calculated discharge berdasarkan rentang tanggal berhasil diambil');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data predicted calculated discharge berdasarkan rentang tanggal');
        }
    }

    /**
     * Get latest predicted discharges for each device-sensor-model combination
     */
    public function getLatest()
    {
        try {
            $predictedDischarges = PredictedCalculatedDischarge::with(['device', 'sensor', 'model'])
                ->whereIn('id', function($query) {
                    $query->selectRaw('MAX(id)')
                        ->from('predicted_calculated_discharges')
                        ->groupBy(['device_code', 'sensor_code', 'model_code']);
                })
                ->orderBy('prediction_timestamp', 'desc')
                ->get();

            return $this->successResponse($predictedDischarges, 'Data predicted calculated discharge terbaru berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data predicted calculated discharge terbaru');
        }
    }

    /**
     * Get future predictions (predictions with timestamp in the future)
     */
    public function getFuturePredictions()
    {
        try {
            $predictedDischarges = PredictedCalculatedDischarge::with(['device', 'sensor', 'model'])
                ->where('prediction_timestamp', '>', now())
                ->orderBy('prediction_timestamp', 'asc')
                ->limit(100)
                ->get();

            return $this->successResponse($predictedDischarges, 'Data prediksi discharge masa depan berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data prediksi discharge masa depan');
        }
    }

    /**
     * Get statistics for predicted discharges
     */
    public function getStatistics(Request $request)
    {
        try {
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'device_code' => 'nullable|string|exists:mas_devices,device_code',
                'sensor_code' => 'nullable|string|exists:mas_sensors,sensor_code',
                'model_code' => 'nullable|string|exists:mas_models,model_code',
            ]);

            $query = PredictedCalculatedDischarge::whereBetween('prediction_timestamp', [
                Carbon::parse($validated['start_date'])->startOfDay(),
                Carbon::parse($validated['end_date'])->endOfDay()
            ]);

            if (!empty($validated['device_code'])) {
                $query->where('device_code', $validated['device_code']);
            }

            if (!empty($validated['sensor_code'])) {
                $query->where('sensor_code', $validated['sensor_code']);
            }

            if (!empty($validated['model_code'])) {
                $query->where('model_code', $validated['model_code']);
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

            return $this->successResponse($statistics, 'Statistik predicted calculated discharge berhasil diambil');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil statistik predicted calculated discharge');
        }
    }

    /**
     * Bulk import predicted calculated discharges
     */
    public function bulkImport(Request $request)
    {
        try {
            $validated = $request->validate([
                'predictions' => 'required|array|min:1',
                'predictions.*.device_code' => 'required|string|max:255|exists:mas_devices,device_code',
                'predictions.*.sensor_code' => 'required|string|max:255|exists:mas_sensors,sensor_code',
                'predictions.*.model_code' => 'required|string|max:255|exists:mas_models,model_code',
                'predictions.*.water_level' => 'required|numeric',
                'predictions.*.discharge' => 'required|numeric',
                'predictions.*.prediction_timestamp' => 'required|date',
            ]);

            $createdPredictions = [];
            $errors = [];

            foreach ($validated['predictions'] as $index => $predictionData) {
                try {
                    $prediction = PredictedCalculatedDischarge::create($predictionData);
                    $createdPredictions[] = $prediction;
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 1) . ": " . $e->getMessage();
                }
            }

            $result = [
                'created_count' => count($createdPredictions),
                'error_count' => count($errors),
                'created_predictions' => $createdPredictions,
                'errors' => $errors
            ];

            if (count($createdPredictions) > 0) {
                return $this->successResponse($result, count($createdPredictions) . ' predicted calculated discharge berhasil diimport');
            } else {
                return $this->badRequestResponse('Tidak ada predicted calculated discharge yang berhasil diimport', $result);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal melakukan bulk import predicted calculated discharge');
        }
    }
}