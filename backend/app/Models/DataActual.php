<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataActual extends Model
{
    use HasFactory;

    protected $fillable = [
        'mas_sensor_id',
        'mas_sensor_code',
        'value',
        'received_at',
        'threshold_status'
    ];

    protected $casts = [
        'value' => 'double',
        'received_at' => 'datetime',
    ];

    /**
     * Get the sensor that owns the data actual.
     */
    public function sensor(): BelongsTo
    {
        return $this->belongsTo(MasSensor::class, 'mas_sensor_id');
    }

    /**
     * Get threshold status badge class
     */
    public function getThresholdStatusBadgeClass(): string
    {
        return match($this->threshold_status) {
            'safe' => 'bg-green-100 text-green-800',
            'warning' => 'bg-yellow-100 text-yellow-800',
            'danger' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get threshold status label
     */
    public function getThresholdStatusLabel(): string
    {
        return match($this->threshold_status) {
            'safe' => 'Aman',
            'warning' => 'Waspada',
            'danger' => 'Bahaya',
            default => 'Tidak Diketahui'
        };
    }

    /**
     * Scope untuk filter berdasarkan status threshold
     */
    public function scopeByThresholdStatus($query, $status)
    {
        return $query->where('threshold_status', $status);
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('received_at', [$startDate, $endDate]);
    }
}
