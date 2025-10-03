<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTraits;
use App\Models\MasVillage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasVillageController extends Controller
{
    use ApiResponseTraits;

    /**
     * Display a listing of villages
     */
    public function index()
    {
        try {
            $villages = MasVillage::select(['id', 'villages_name', 'villages_code', 'created_at'])
                ->orderBy('villages_name')
                ->get();

            return $this->successResponse($villages, 'Data villages berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data villages');
        }
    }

    /**
     * Store a newly created village
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'villages_name' => 'required|string|max:255',
                'villages_code' => 'required|string|max:100|unique:mas_villages,villages_code',
            ]);

            $village = MasVillage::create($validated);

            return $this->successResponse($village, 'Village berhasil dibuat', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal membuat village');
        }
    }

    /**
     * Display the specified village
     */
    public function show($id)
    {
        try {
            $village = MasVillage::with(['deviceValues', 'geojsonMappings'])
                ->findOrFail($id);

            return $this->successResponse($village, 'Data village berhasil diambil');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Village tidak ditemukan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data village');
        }
    }

    /**
     * Update the specified village
     */
    public function update(Request $request, $id)
    {
        try {
            $village = MasVillage::findOrFail($id);

            $validated = $request->validate([
                'villages_name' => 'required|string|max:255',
                'villages_code' => [
                    'required',
                    'string',
                    'max:100',
                    Rule::unique('mas_villages', 'villages_code')->ignore($village->id)
                ],
            ]);

            $village->update($validated);

            return $this->successResponse($village, 'Village berhasil diupdate');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Village tidak ditemukan');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengupdate village');
        }
    }

    /**
     * Remove the specified village
     */
    public function destroy($id)
    {
        try {
            $village = MasVillage::findOrFail($id);
            $village->delete();

            return $this->successResponse(null, 'Village berhasil dihapus');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Village tidak ditemukan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal menghapus village');
        }
    }
}