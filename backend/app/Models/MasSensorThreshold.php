<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasSensorThreshold extends Model
{
    use HasFactory;

    protected $fillable = [
        'sensor_thresholds_name',
        'sensor_thresholds_code',
        'sensor_thresholds_value_1',
        'sensor_thresholds_value_1_color',
        'sensor_thresholds_value_2',
        'sensor_thresholds_value_2_color',
        'sensor_thresholds_value_3',
        'sensor_thresholds_value_3_color',
        'sensor_thresholds_value_4',
        'sensor_thresholds_value_4_color',
    ];

    /**
     * Get the sensor values for the sensor threshold.
     */
    public function sensorValues(): HasMany
    {
        return $this->hasMany(SensorValue::class, 'mas_sensor_threshold_code', 'sensor_thresholds_code');
    }
}