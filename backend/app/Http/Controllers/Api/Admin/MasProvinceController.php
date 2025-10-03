<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTraits;
use App\Models\MasProvince;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasProvinceController extends Controller
{
    use ApiResponseTraits;

    /**
     * Display a listing of provinces
     */
    public function index()
    {
        try {
            $provinces = MasProvince::select(['id', 'provinces_name', 'provinces_code', 'created_at'])
                ->orderBy('provinces_name')
                ->get();

            return $this->successResponse($provinces, 'Data provinces berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data provinces');
        }
    }

    /**
     * Store a newly created province
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'provinces_name' => 'required|string|max:255',
                'provinces_code' => 'required|string|max:100|unique:mas_provinces,provinces_code',
            ]);

            $province = MasProvince::create($validated);

            return $this->successResponse($province, 'Province berhasil dibuat', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal membuat province');
        }
    }

    /**
     * Display the specified province
     */
    public function show($id)
    {
        try {
            $province = MasProvince::findOrFail($id);

            return $this->successResponse($province, 'Data province berhasil diambil');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Province tidak ditemukan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data province');
        }
    }

    /**
     * Update the specified province
     */
    public function update(Request $request, $id)
    {
        try {
            $province = MasProvince::findOrFail($id);

            $validated = $request->validate([
                'provinces_name' => 'required|string|max:255',
                'provinces_code' => [
                    'required',
                    'string',
                    'max:100',
                    Rule::unique('mas_provinces', 'provinces_code')->ignore($province->id)
                ],
            ]);

            $province->update($validated);

            return $this->successResponse($province, 'Province berhasil diupdate');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Province tidak ditemukan');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengupdate province');
        }
    }

    /**
     * Remove the specified province
     */
    public function destroy($id)
    {
        try {
            $province = MasProvince::findOrFail($id);
            $province->delete();

            return $this->successResponse(null, 'Province berhasil dihapus');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Province tidak ditemukan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal menghapus province');
        }
    }
}