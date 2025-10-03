<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SensorValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'mas_sensor_code',
        'mas_sensor_parameter_code',
        'mas_sensor_threshold_code',
        'sensor_name',
        'sensor_unit',
        'sensor_description',
        'sensor_icon_path',
        'status',
        'last_seen',
    ];

    protected $casts = [
        'last_seen' => 'datetime',
    ];

    /**
     * Get the sensor that owns the sensor value.
     */
    public function sensor(): BelongsTo
    {
        return $this->belongsTo(MasSensor::class, 'mas_sensor_code', 'sensor_code');
    }

    /**
     * Get the sensor parameter that owns the sensor value.
     */
    public function sensorParameter(): BelongsTo
    {
        return $this->belongsTo(MasSensorParameter::class, 'mas_sensor_parameter_code', 'code');
    }

    /**
     * Get the sensor threshold that owns the sensor value.
     */
    public function sensorThreshold(): BelongsTo
    {
        return $this->belongsTo(MasSensorThreshold::class, 'mas_sensor_threshold_code', 'sensor_thresholds_code');
    }
}