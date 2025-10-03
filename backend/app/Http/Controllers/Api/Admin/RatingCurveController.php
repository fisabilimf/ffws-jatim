<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTraits;
use App\Models\RatingCurve;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RatingCurveController extends Controller
{
    use ApiResponseTraits;

    /**
     * Display a listing of rating curves
     */
    public function index()
    {
        try {
            $ratingCurves = RatingCurve::with(['station'])
                ->select(['id', 'station_code', 'water_level', 'discharge', 'is_active', 'created_at'])
                ->orderBy('station_code')
                ->orderBy('water_level')
                ->get();

            return $this->successResponse($ratingCurves, 'Data rating curve berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data rating curve');
        }
    }

    /**
     * Store a newly created rating curve
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'station_code' => 'required|string|max:255',
                'water_level' => 'required|numeric|min:0',
                'discharge' => 'required|numeric|min:0',
                'is_active' => 'required|boolean',
            ]);

            // Check for unique combination of station_code and water_level
            $existingCurve = RatingCurve::where('station_code', $validated['station_code'])
                ->where('water_level', $validated['water_level'])
                ->first();

            if ($existingCurve) {
                return $this->badRequestResponse('Rating curve untuk station dan water level ini sudah ada');
            }

            $ratingCurve = RatingCurve::create($validated);

            return $this->successResponse($ratingCurve->load('station'), 'Rating curve berhasil dibuat', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal membuat rating curve');
        }
    }

    /**
     * Display the specified rating curve
     */
    public function show($id)
    {
        try {
            $ratingCurve = RatingCurve::with(['station'])
                ->findOrFail($id);

            return $this->successResponse($ratingCurve, 'Data rating curve berhasil diambil');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Rating curve tidak ditemukan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data rating curve');
        }
    }

    /**
     * Update the specified rating curve
     */
    public function update(Request $request, $id)
    {
        try {
            $ratingCurve = RatingCurve::findOrFail($id);

            $validated = $request->validate([
                'station_code' => 'required|string|max:255',
                'water_level' => 'required|numeric|min:0',
                'discharge' => 'required|numeric|min:0',
                'is_active' => 'required|boolean',
            ]);

            // Check for unique combination (excluding current record)
            $existingCurve = RatingCurve::where('station_code', $validated['station_code'])
                ->where('water_level', $validated['water_level'])
                ->where('id', '!=', $id)
                ->first();

            if ($existingCurve) {
                return $this->badRequestResponse('Rating curve untuk station dan water level ini sudah ada');
            }

            $ratingCurve->update($validated);

            return $this->successResponse($ratingCurve->load('station'), 'Rating curve berhasil diupdate');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Rating curve tidak ditemukan');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengupdate rating curve');
        }
    }

    /**
     * Remove the specified rating curve
     */
    public function destroy($id)
    {
        try {
            $ratingCurve = RatingCurve::findOrFail($id);
            $ratingCurve->delete();

            return $this->successResponse(null, 'Rating curve berhasil dihapus');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Rating curve tidak ditemukan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal menghapus rating curve');
        }
    }

    /**
     * Get rating curves by station code
     */
    public function getByStation($stationCode)
    {
        try {
            $ratingCurves = RatingCurve::with(['station'])
                ->where('station_code', $stationCode)
                ->orderBy('water_level')
                ->get();

            return $this->successResponse($ratingCurves, 'Data rating curve berdasarkan station berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data rating curve berdasarkan station');
        }
    }

    /**
     * Get active rating curves
     */
    public function getActive()
    {
        try {
            $ratingCurves = RatingCurve::with(['station'])
                ->where('is_active', true)
                ->orderBy('station_code')
                ->orderBy('water_level')
                ->get();

            return $this->successResponse($ratingCurves, 'Data rating curve aktif berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data rating curve aktif');
        }
    }

    /**
     * Calculate discharge for given water level using rating curve
     */
    public function calculateDischarge(Request $request, $stationCode)
    {
        try {
            $validated = $request->validate([
                'water_level' => 'required|numeric|min:0',
            ]);

            $waterLevel = $validated['water_level'];

            // Get rating curves for the station, ordered by water level
            $curves = RatingCurve::where('station_code', $stationCode)
                ->where('is_active', true)
                ->orderBy('water_level')
                ->get();

            if ($curves->isEmpty()) {
                return $this->notFoundResponse('Rating curve untuk station tidak ditemukan');
            }

            $discharge = null;

            // Find exact match first
            $exactMatch = $curves->where('water_level', $waterLevel)->first();
            if ($exactMatch) {
                $discharge = $exactMatch->discharge;
            } else {
                // Linear interpolation between two points
                $lowerCurve = null;
                $upperCurve = null;

                foreach ($curves as $curve) {
                    if ($curve->water_level <= $waterLevel) {
                        $lowerCurve = $curve;
                    } elseif ($curve->water_level > $waterLevel && !$upperCurve) {
                        $upperCurve = $curve;
                        break;
                    }
                }

                if ($lowerCurve && $upperCurve) {
                    // Linear interpolation
                    $x1 = $lowerCurve->water_level;
                    $y1 = $lowerCurve->discharge;
                    $x2 = $upperCurve->water_level;
                    $y2 = $upperCurve->discharge;

                    $discharge = $y1 + (($waterLevel - $x1) * ($y2 - $y1)) / ($x2 - $x1);
                } elseif ($lowerCurve) {
                    // Use the highest available curve
                    $discharge = $lowerCurve->discharge;
                } elseif ($upperCurve) {
                    // Use the lowest available curve
                    $discharge = $upperCurve->discharge;
                }
            }

            $result = [
                'station_code' => $stationCode,
                'water_level' => $waterLevel,
                'calculated_discharge' => round($discharge, 4),
                'calculation_method' => $exactMatch ? 'exact_match' : 'interpolation'
            ];

            return $this->successResponse($result, 'Discharge berhasil dihitung');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal menghitung discharge');
        }
    }

    /**
     * Bulk import rating curves
     */
    public function bulkImport(Request $request)
    {
        try {
            $validated = $request->validate([
                'curves' => 'required|array|min:1',
                'curves.*.station_code' => 'required|string|max:255',
                'curves.*.water_level' => 'required|numeric|min:0',
                'curves.*.discharge' => 'required|numeric|min:0',
                'curves.*.is_active' => 'boolean',
            ]);

            $createdCurves = [];
            $errors = [];

            foreach ($validated['curves'] as $index => $curveData) {
                try {
                    // Set default active status
                    $curveData['is_active'] = $curveData['is_active'] ?? true;

                    // Check for duplicates
                    $existing = RatingCurve::where('station_code', $curveData['station_code'])
                        ->where('water_level', $curveData['water_level'])
                        ->first();

                    if ($existing) {
                        $errors[] = "Baris " . ($index + 1) . ": Rating curve untuk station {$curveData['station_code']} dan water level {$curveData['water_level']} sudah ada";
                        continue;
                    }

                    $curve = RatingCurve::create($curveData);
                    $createdCurves[] = $curve;
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 1) . ": " . $e->getMessage();
                }
            }

            $result = [
                'created_count' => count($createdCurves),
                'error_count' => count($errors),
                'created_curves' => $createdCurves,
                'errors' => $errors
            ];

            if (count($createdCurves) > 0) {
                return $this->successResponse($result, count($createdCurves) . ' rating curve berhasil diimport');
            } else {
                return $this->badRequestResponse('Tidak ada rating curve yang berhasil diimport', $result);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal melakukan bulk import rating curve');
        }
    }
}