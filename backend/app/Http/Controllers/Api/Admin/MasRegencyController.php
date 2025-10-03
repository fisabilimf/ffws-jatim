<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTraits;
use App\Models\MasRegency;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasRegencyController extends Controller
{
    use ApiResponseTraits;

    /**
     * Display a listing of regencies
     */
    public function index()
    {
        try {
            $regencies = MasRegency::select(['id', 'regencies_name', 'regencies_code', 'created_at'])
                ->orderBy('regencies_name')
                ->get();

            return $this->successResponse($regencies, 'Data regencies berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data regencies');
        }
    }

    /**
     * Store a newly created regency
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'regencies_name' => 'required|string|max:255',
                'regencies_code' => 'required|string|max:100|unique:mas_regencies,regencies_code',
            ]);

            $regency = MasRegency::create($validated);

            return $this->successResponse($regency, 'Regency berhasil dibuat', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal membuat regency');
        }
    }

    /**
     * Display the specified regency
     */
    public function show($id)
    {
        try {
            $regency = MasRegency::with(['deviceValues', 'geojsonMappings'])
                ->findOrFail($id);

            return $this->successResponse($regency, 'Data regency berhasil diambil');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Regency tidak ditemukan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data regency');
        }
    }

    /**
     * Update the specified regency
     */
    public function update(Request $request, $id)
    {
        try {
            $regency = MasRegency::findOrFail($id);

            $validated = $request->validate([
                'regencies_name' => 'required|string|max:255',
                'regencies_code' => [
                    'required',
                    'string',
                    'max:100',
                    Rule::unique('mas_regencies', 'regencies_code')->ignore($regency->id)
                ],
            ]);

            $regency->update($validated);

            return $this->successResponse($regency, 'Regency berhasil diupdate');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Regency tidak ditemukan');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengupdate regency');
        }
    }

    /**
     * Remove the specified regency
     */
    public function destroy($id)
    {
        try {
            $regency = MasRegency::findOrFail($id);
            $regency->delete();

            return $this->successResponse(null, 'Regency berhasil dihapus');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Regency tidak ditemukan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal menghapus regency');
        }
    }
}