<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTraits;
use App\Models\MasCity;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasCityController extends Controller
{
    use ApiResponseTraits;

    /**
     * Display a listing of cities
     */
    public function index()
    {
        try {
            $cities = MasCity::select(['id', 'cities_name', 'cities_code', 'created_at'])
                ->orderBy('cities_name')
                ->get();

            return $this->successResponse($cities, 'Data cities berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data cities');
        }
    }

    /**
     * Store a newly created city
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'cities_name' => 'required|string|max:255',
                'cities_code' => 'required|string|max:100|unique:mas_cities,cities_code',
            ]);

            $city = MasCity::create($validated);

            return $this->successResponse($city, 'City berhasil dibuat', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal membuat city');
        }
    }

    /**
     * Display the specified city
     */
    public function show($id)
    {
        try {
            $city = MasCity::with(['riverBasins', 'upts'])
                ->findOrFail($id);

            return $this->successResponse($city, 'Data city berhasil diambil');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('City tidak ditemukan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data city');
        }
    }

    /**
     * Update the specified city
     */
    public function update(Request $request, $id)
    {
        try {
            $city = MasCity::findOrFail($id);

            $validated = $request->validate([
                'cities_name' => 'required|string|max:255',
                'cities_code' => [
                    'required',
                    'string',
                    'max:100',
                    Rule::unique('mas_cities', 'cities_code')->ignore($city->id)
                ],
            ]);

            $city->update($validated);

            return $this->successResponse($city, 'City berhasil diupdate');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('City tidak ditemukan');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengupdate city');
        }
    }

    /**
     * Remove the specified city
     */
    public function destroy($id)
    {
        try {
            $city = MasCity::findOrFail($id);
            $city->delete();

            return $this->successResponse(null, 'City berhasil dihapus');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('City tidak ditemukan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal menghapus city');
        }
    }
}