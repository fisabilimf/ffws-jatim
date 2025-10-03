<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTraits;
use App\Models\MasWatershed;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasWatershedController extends Controller
{
    use ApiResponseTraits;

    /**
     * Display a listing of watersheds
     */
    public function index()
    {
        try {
            $watersheds = MasWatershed::with(['village', 'riverBasin'])
                ->select(['id', 'watershed_code', 'watershed_name', 'village_code', 'river_basin_code', 'is_active', 'created_at'])
                ->orderBy('watershed_name')
                ->get();

            return $this->successResponse($watersheds, 'Data watershed berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data watershed');
        }
    }

    /**
     * Store a newly created watershed
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'watershed_code' => 'required|string|max:100|unique:mas_watersheds,watershed_code',
                'watershed_name' => 'required|string|max:255',
                'village_code' => 'nullable|string|max:100|exists:mas_villages,village_code',
                'river_basin_code' => 'nullable|string|max:100|exists:mas_river_basins,river_basin_code',
                'is_active' => 'required|boolean',
            ]);

            $watershed = MasWatershed::create($validated);

            return $this->successResponse($watershed->load(['village', 'riverBasin']), 'Watershed berhasil dibuat', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal membuat watershed');
        }
    }

    /**
     * Display the specified watershed
     */
    public function show($id)
    {
        try {
            $watershed = MasWatershed::with(['village', 'riverBasin'])
                ->findOrFail($id);

            return $this->successResponse($watershed, 'Data watershed berhasil diambil');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Watershed tidak ditemukan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data watershed');
        }
    }

    /**
     * Update the specified watershed
     */
    public function update(Request $request, $id)
    {
        try {
            $watershed = MasWatershed::findOrFail($id);

            $validated = $request->validate([
                'watershed_code' => [
                    'required',
                    'string',
                    'max:100',
                    Rule::unique('mas_watersheds', 'watershed_code')->ignore($watershed->id)
                ],
                'watershed_name' => 'required|string|max:255',
                'village_code' => 'nullable|string|max:100|exists:mas_villages,village_code',
                'river_basin_code' => 'nullable|string|max:100|exists:mas_river_basins,river_basin_code',
                'is_active' => 'required|boolean',
            ]);

            $watershed->update($validated);

            return $this->successResponse($watershed->load(['village', 'riverBasin']), 'Watershed berhasil diupdate');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Watershed tidak ditemukan');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengupdate watershed');
        }
    }

    /**
     * Remove the specified watershed
     */
    public function destroy($id)
    {
        try {
            $watershed = MasWatershed::findOrFail($id);
            $watershed->delete();

            return $this->successResponse(null, 'Watershed berhasil dihapus');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Watershed tidak ditemukan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal menghapus watershed');
        }
    }

    /**
     * Get watersheds by village code
     */
    public function getByVillage($villageCode)
    {
        try {
            $watersheds = MasWatershed::with(['village', 'riverBasin'])
                ->where('village_code', $villageCode)
                ->orderBy('watershed_name')
                ->get();

            return $this->successResponse($watersheds, 'Data watershed berdasarkan village berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data watershed berdasarkan village');
        }
    }

    /**
     * Get watersheds by river basin code
     */
    public function getByRiverBasin($riverBasinCode)
    {
        try {
            $watersheds = MasWatershed::with(['village', 'riverBasin'])
                ->where('river_basin_code', $riverBasinCode)
                ->orderBy('watershed_name')
                ->get();

            return $this->successResponse($watersheds, 'Data watershed berdasarkan river basin berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data watershed berdasarkan river basin');
        }
    }

    /**
     * Get active watersheds
     */
    public function getActive()
    {
        try {
            $watersheds = MasWatershed::with(['village', 'riverBasin'])
                ->where('is_active', true)
                ->orderBy('watershed_name')
                ->get();

            return $this->successResponse($watersheds, 'Data watershed aktif berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data watershed aktif');
        }
    }
}