<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasDevice;
use App\Http\Traits\ApiResponseTraits;

class MasDeviceController extends Controller
{
    use ApiResponseTraits;

    public function show($id)
    {
        try {
            $device = MasDevice::select('name', 'code', 'latitude', 'longitude', 'elevation_m', 'status', 'mas_river_basin_id')
                ->with('riverBasin:id,name')
                ->findOrFail($id);

            // Format data untuk response
            $formattedDevice = [
                'name' => $device->name,
                'code' => $device->code,
                'latitude' => number_format($device->latitude, 6),
                'longitude' => number_format($device->longitude, 6),
                'elevation_m' => $device->elevation_m ? number_format($device->elevation_m, 2) : '-',
                'status' => $device->status,
                'mas_river_basin_id' => $device->mas_river_basin_id,
                'river_basin_name' => $device->riverBasin->name ?? '-'
            ];
            
            return $this->successResponse($formattedDevice, 'Device retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Terjadi kesalahan saat mengambil data device');
        }
    }
}
