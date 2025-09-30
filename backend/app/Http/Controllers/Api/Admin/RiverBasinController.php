<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTraits;
use Illuminate\Http\Request;
use App\Models\MasRiverBasin;

class RiverBasinController extends Controller
{
    use ApiResponseTraits;

    public function show(Request $request)
    {
        try {
        $riverBasin = MasRiverBasin::findOrFail($request->id);
            return $this->successResponse($riverBasin, 'River Basin retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Terjadi kesalahan saat mengambil data river basin');
        }
    }
}
