<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTraits;
use App\Models\MasUpt;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasUptController extends Controller
{
    use ApiResponseTraits;

    /**
     * Display a listing of UPTs
     */
    public function index()
    {
        try {
            $upts = MasUpt::with(['riverBasin', 'city'])
                ->select(['id', 'river_basin_code', 'cities_code', 'upts_name', 'upts_code', 'created_at'])
                ->orderBy('upts_name')
                ->get();

            return $this->successResponse($upts, 'Data UPTs berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data UPTs');
        }
    }

    /**
     * Store a newly created UPT
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'river_basin_code' => 'required|string|max:100|exists:mas_river_basins,river_basins_code',
                'cities_code' => 'required|string|max:100|exists:mas_cities,cities_code',
                'upts_name' => 'required|string|max:255',
                'upts_code' => 'required|string|max:100|unique:mas_upts,upts_code',
            ]);

            $upt = MasUpt::create($validated);

            return $this->successResponse($upt->load(['riverBasin', 'city']), 'UPT berhasil dibuat', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal membuat UPT');
        }
    }

    /**
     * Display the specified UPT
     */
    public function show($id)
    {
        try {
            $upt = MasUpt::with(['riverBasin', 'city', 'uptds', 'deviceValues', 'userByRoles'])
                ->findOrFail($id);

            return $this->successResponse($upt, 'Data UPT berhasil diambil');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('UPT tidak ditemukan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data UPT');
        }
    }

    /**
     * Update the specified UPT
     */
    public function update(Request $request, $id)
    {
        try {
            $upt = MasUpt::findOrFail($id);

            $validated = $request->validate([
                'river_basin_code' => 'required|string|max:100|exists:mas_river_basins,river_basins_code',
                'cities_code' => 'required|string|max:100|exists:mas_cities,cities_code',
                'upts_name' => 'required|string|max:255',
                'upts_code' => [
                    'required',
                    'string',
                    'max:100',
                    Rule::unique('mas_upts', 'upts_code')->ignore($upt->id)
                ],
            ]);

            $upt->update($validated);

            return $this->successResponse($upt->load(['riverBasin', 'city']), 'UPT berhasil diupdate');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('UPT tidak ditemukan');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengupdate UPT');
        }
    }

    /**
     * Remove the specified UPT
     */
    public function destroy($id)
    {
        try {
            $upt = MasUpt::findOrFail($id);
            $upt->delete();

            return $this->successResponse(null, 'UPT berhasil dihapus');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('UPT tidak ditemukan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal menghapus UPT');
        }
    }

    /**
     * Get UPTs by river basin
     */
    public function getByRiverBasin($riverBasinCode)
    {
        try {
            $upts = MasUpt::with(['riverBasin', 'city'])
                ->where('river_basin_code', $riverBasinCode)
                ->orderBy('upts_name')
                ->get();

            return $this->successResponse($upts, 'Data UPTs berdasarkan river basin berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data UPTs berdasarkan river basin');
        }
    }

    /**
     * Get UPTs by city
     */
    public function getByCity($citiesCode)
    {
        try {
            $upts = MasUpt::with(['riverBasin', 'city'])
                ->where('cities_code', $citiesCode)
                ->orderBy('upts_name')
                ->get();

            return $this->successResponse($upts, 'Data UPTs berdasarkan city berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data UPTs berdasarkan city');
        }
    }
}