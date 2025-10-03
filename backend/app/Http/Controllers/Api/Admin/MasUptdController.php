<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTraits;
use App\Models\MasUptd;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasUptdController extends Controller
{
    use ApiResponseTraits;

    /**
     * Display a listing of UPTDs
     */
    public function index()
    {
        try {
            $uptds = MasUptd::with('upt')
                ->select(['id', 'upt_code', 'name', 'code', 'created_at'])
                ->orderBy('name')
                ->get();

            return $this->successResponse($uptds, 'Data UPTDs berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data UPTDs');
        }
    }

    /**
     * Store a newly created UPTD
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'upt_code' => 'required|string|max:100|exists:mas_upts,upts_code',
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:100|unique:mas_uptds,code',
            ]);

            $uptd = MasUptd::create($validated);

            return $this->successResponse($uptd->load('upt'), 'UPTD berhasil dibuat', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal membuat UPTD');
        }
    }

    /**
     * Display the specified UPTD
     */
    public function show($id)
    {
        try {
            $uptd = MasUptd::with(['upt', 'deviceValues', 'geojsonMappings'])
                ->findOrFail($id);

            return $this->successResponse($uptd, 'Data UPTD berhasil diambil');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('UPTD tidak ditemukan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data UPTD');
        }
    }

    /**
     * Update the specified UPTD
     */
    public function update(Request $request, $id)
    {
        try {
            $uptd = MasUptd::findOrFail($id);

            $validated = $request->validate([
                'upt_code' => 'required|string|max:100|exists:mas_upts,upts_code',
                'name' => 'required|string|max:255',
                'code' => [
                    'required',
                    'string',
                    'max:100',
                    Rule::unique('mas_uptds', 'code')->ignore($uptd->id)
                ],
            ]);

            $uptd->update($validated);

            return $this->successResponse($uptd->load('upt'), 'UPTD berhasil diupdate');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('UPTD tidak ditemukan');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengupdate UPTD');
        }
    }

    /**
     * Remove the specified UPTD
     */
    public function destroy($id)
    {
        try {
            $uptd = MasUptd::findOrFail($id);
            $uptd->delete();

            return $this->successResponse(null, 'UPTD berhasil dihapus');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('UPTD tidak ditemukan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal menghapus UPTD');
        }
    }

    /**
     * Get UPTDs by UPT
     */
    public function getByUpt($uptCode)
    {
        try {
            $uptds = MasUptd::with('upt')
                ->where('upt_code', $uptCode)
                ->orderBy('name')
                ->get();

            return $this->successResponse($uptds, 'Data UPTDs berdasarkan UPT berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data UPTDs berdasarkan UPT');
        }
    }
}