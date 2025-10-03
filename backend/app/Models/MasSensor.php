<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasSensor extends Model
{
    use HasFactory;

    protected $fillable = [
        'mas_device_code',
        'sensor_code',
    ];

    /**
     * Get the device that owns the sensor.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(MasDevice::class, 'mas_device_code', 'device_code');
    }

    /**
     * Get the data actuals for the sensor.
     */
    public function dataActuals(): HasMany
    {
        return $this->hasMany(DataActual::class, 'mas_sensor_code', 'sensor_code');
    }

    /**
     * Get the data predictions for the sensor.
     */
    public function dataPredictions(): HasMany
    {
        return $this->hasMany(DataPrediction::class, 'mas_sensor_code', 'sensor_code');
    }

    /**
     * Get the rating curves for the sensor.
     */
    public function ratingCurves(): HasMany
    {
        return $this->hasMany(RatingCurve::class, 'mas_sensor_code', 'sensor_code');
    }

    /**
     * Get the calculated discharges for the sensor.
     */
    public function calculatedDischarges(): HasMany
    {
        return $this->hasMany(CalculatedDischarge::class, 'mas_sensor_code', 'sensor_code');
    }

    /**
     * Get the predicted calculated discharges for the sensor.
     */
    public function predictedCalculatedDischarges(): HasMany
    {
        return $this->hasMany(PredictedCalculatedDischarge::class, 'mas_sensor_code', 'sensor_code');
    }

    /**
     * Get the sensor values for the sensor.
     */
    public function sensorValues(): HasMany
    {
        return $this->hasMany(SensorValue::class, 'mas_sensor_code', 'sensor_code');
    }
}
