<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTraits;
use App\Models\DeviceValue;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DeviceValueController extends Controller
{
    use ApiResponseTraits;

    /**
     * Display a listing of device values
     */
    public function index(Request $request)
    {
        try {
            $query = DeviceValue::with('device')
                ->select(['id', 'device_code', 'value', 'unit', 'timestamp', 'created_at']);

            // Apply filters
            if ($request->has('device_code')) {
                $query->where('device_code', $request->device_code);
            }

            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('timestamp', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay()
                ]);
            }

            $deviceValues = $query->orderBy('timestamp', 'desc')
                ->limit($request->get('limit', 100))
                ->get();

            return $this->successResponse($deviceValues, 'Data device value berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data device value');
        }
    }

    /**
     * Store a newly created device value
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'device_code' => 'required|string|max:255|exists:mas_devices,device_code',
                'value' => 'required|numeric',
                'unit' => 'nullable|string|max:50',
                'timestamp' => 'required|date',
            ]);

            $deviceValue = DeviceValue::create($validated);

            return $this->successResponse($deviceValue->load('device'), 'Device value berhasil dibuat', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal membuat device value');
        }
    }

    /**
     * Display the specified device value
     */
    public function show($id)
    {
        try {
            $deviceValue = DeviceValue::with('device')
                ->findOrFail($id);

            return $this->successResponse($deviceValue, 'Data device value berhasil diambil');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Device value tidak ditemukan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data device value');
        }
    }

    /**
     * Update the specified device value
     */
    public function update(Request $request, $id)
    {
        try {
            $deviceValue = DeviceValue::findOrFail($id);

            $validated = $request->validate([
                'device_code' => 'required|string|max:255|exists:mas_devices,device_code',
                'value' => 'required|numeric',
                'unit' => 'nullable|string|max:50',
                'timestamp' => 'required|date',
            ]);

            $deviceValue->update($validated);

            return $this->successResponse($deviceValue->load('device'), 'Device value berhasil diupdate');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Device value tidak ditemukan');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengupdate device value');
        }
    }

    /**
     * Remove the specified device value
     */
    public function destroy($id)
    {
        try {
            $deviceValue = DeviceValue::findOrFail($id);
            $deviceValue->delete();

            return $this->successResponse(null, 'Device value berhasil dihapus');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Device value tidak ditemukan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal menghapus device value');
        }
    }

    /**
     * Get device values by device code
     */
    public function getByDevice($deviceCode)
    {
        try {
            $deviceValues = DeviceValue::with('device')
                ->where('device_code', $deviceCode)
                ->orderBy('timestamp', 'desc')
                ->limit(100)
                ->get();

            return $this->successResponse($deviceValues, 'Data device value berdasarkan device berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data device value berdasarkan device');
        }
    }

    /**
     * Get device values by date range
     */
    public function getByDateRange(Request $request)
    {
        try {
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'device_code' => 'nullable|string|exists:mas_devices,device_code',
                'limit' => 'nullable|integer|min:1|max:1000'
            ]);

            $query = DeviceValue::with('device')
                ->whereBetween('timestamp', [
                    Carbon::parse($validated['start_date'])->startOfDay(),
                    Carbon::parse($validated['end_date'])->endOfDay()
                ]);

            if (!empty($validated['device_code'])) {
                $query->where('device_code', $validated['device_code']);
            }

            $deviceValues = $query->orderBy('timestamp', 'desc')
                ->limit($validated['limit'] ?? 100)
                ->get();

            return $this->successResponse($deviceValues, 'Data device value berdasarkan rentang tanggal berhasil diambil');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data device value berdasarkan rentang tanggal');
        }
    }

    /**
     * Get latest device values for each device
     */
    public function getLatest()
    {
        try {
            $deviceValues = DeviceValue::with('device')
                ->whereIn('id', function($query) {
                    $query->selectRaw('MAX(id)')
                        ->from('device_values')
                        ->groupBy('device_code');
                })
                ->orderBy('timestamp', 'desc')
                ->get();

            return $this->successResponse($deviceValues, 'Data device value terbaru berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data device value terbaru');
        }
    }

    /**
     * Get statistics for device values
     */
    public function getStatistics(Request $request)
    {
        try {
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'device_code' => 'nullable|string|exists:mas_devices,device_code',
            ]);

            $query = DeviceValue::whereBetween('timestamp', [
                Carbon::parse($validated['start_date'])->startOfDay(),
                Carbon::parse($validated['end_date'])->endOfDay()
            ]);

            if (!empty($validated['device_code'])) {
                $query->where('device_code', $validated['device_code']);
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

            return $this->successResponse($statistics, 'Statistik device value berhasil diambil');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil statistik device value');
        }
    }

    /**
     * Bulk import device values
     */
    public function bulkImport(Request $request)
    {
        try {
            $validated = $request->validate([
                'values' => 'required|array|min:1',
                'values.*.device_code' => 'required|string|max:255|exists:mas_devices,device_code',
                'values.*.value' => 'required|numeric',
                'values.*.unit' => 'nullable|string|max:50',
                'values.*.timestamp' => 'required|date',
            ]);

            $createdValues = [];
            $errors = [];

            foreach ($validated['values'] as $index => $valueData) {
                try {
                    $deviceValue = DeviceValue::create($valueData);
                    $createdValues[] = $deviceValue;
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
                return $this->successResponse($result, count($createdValues) . ' device value berhasil diimport');
            } else {
                return $this->badRequestResponse('Tidak ada device value yang berhasil diimport', $result);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal melakukan bulk import device value');
        }
    }
}