<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTraits;
use App\Models\MasSensor;
use Illuminate\Http\Request;

class MasSensorController extends Controller
{
    use ApiResponseTraits;
    /**
     * Menampilkan semua data sensor
     */
    public function index()
    {
        try {
            $sensors = MasSensor::with(['device', 'masModel'])
                ->select([
                    'device_id',
                    'sensor_code',
                    'parameter',
                    'unit',
                    'description',
                    'mas_model_id',
                    'threshold_safe',
                    'threshold_warning',
                    'threshold_danger',
                    'status',
                    'last_seen'
                ])
                ->get();

            return $this->successResponse($sensors, 'Data sensor berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data sensor');
        }
    }

    /**
     * Menampilkan data sensor berdasarkan ID
     */
    public function show($id)
    {
        try {
            $sensor = MasSensor::with(['device', 'masModel'])
                ->select([
                    'device_id',
                    'sensor_code',
                    'parameter',
                    'unit',
                    'description',
                    'mas_model_id',
                    'threshold_safe',
                    'threshold_warning',
                    'threshold_danger',
                    'status',
                    'last_seen'
                ])
                ->find($id);

            if (!$sensor) {
                return $this->notFoundResponse('Sensor tidak ditemukan');
            }

            return $this->successResponse($sensor, 'Data sensor berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data sensor');
        }
    }

    /**
     * Menampilkan data sensor berdasarkan device ID
     */
    public function getByDevice($deviceId)
    {
        try {
            $sensors = MasSensor::with(['device', 'masModel'])
                ->select([
                    'device_id',
                    'sensor_code',
                    'parameter',
                    'unit',
                    'description',
                    'mas_model_id',
                    'threshold_safe',
                    'threshold_warning',
                    'threshold_danger',
                    'status',
                    'last_seen'
                ])
                ->where('device_id', $deviceId)
                ->get();

            return $this->successResponse($sensors, 'Data sensor berdasarkan device berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data sensor berdasarkan device');
        }
    }

    /**
     * Menampilkan data sensor berdasarkan parameter
     */
    public function getByParameter($parameter)
    {
        try {
            $sensors = MasSensor::with(['device', 'masModel'])
                ->select([
                    'device_id',
                    'sensor_code',
                    'parameter',
                    'unit',
                    'description',
                    'mas_model_id',
                    'threshold_safe',
                    'threshold_warning',
                    'threshold_danger',
                    'status',
                    'last_seen'
                ])
                ->where('parameter', $parameter)
                ->get();

            return $this->successResponse($sensors, 'Data sensor berdasarkan parameter berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data sensor berdasarkan parameter');
        }
    }

    /**
     * Menampilkan data sensor berdasarkan status
     */
    public function getByStatus($status)
    {
        try {
            $sensors = MasSensor::with(['device', 'masModel'])
                ->select([
                    'device_id',
                    'sensor_code',
                    'parameter',
                    'unit',
                    'description',
                    'mas_model_id',
                    'threshold_safe',
                    'threshold_warning',
                    'threshold_danger',
                    'status',
                    'last_seen'
                ])
                ->where('status', $status)
                ->get();

            return $this->successResponse($sensors, 'Data sensor berdasarkan status berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data sensor berdasarkan status');
        }
    }
}
