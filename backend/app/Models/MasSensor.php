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
        'device_id',
        'mas_device_code',
        'sensor_code',
        'parameter',
        'unit',
        'description',
        'mas_model_id',
        'threshold_safe',
        'threshold_warning',
        'threshold_danger',
        'status',
        'forecasting_status',
        'is_active',
        'last_seen',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'threshold_safe' => 'double',
        'threshold_warning' => 'double',
        'threshold_danger' => 'double',
        'last_seen' => 'datetime',
    ];

    /**
     * Get the device that owns the sensor.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(MasDevice::class, 'mas_device_code', 'code');
    }

    /**
     * Get the model that owns the sensor.
     */
    public function masModel(): BelongsTo
    {
        return $this->belongsTo(MasModel::class, 'mas_model_id');
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

    /**
     * Get the available parameter options for sensors.
     */
    public static function getParameterOptions(): array
    {
        return [
            'water_level' => 'Water Level',
            'rainfall' => 'Rainfall'
        ];
    }

    /**
     * Get the available status options for sensors.
     */
    public static function getStatusOptions(): array
    {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive'
        ];
    }

    /**
     * Get the available forecasting status options for sensors.
     */
    public static function getForecastingStatusOptions(): array
    {
        return [
            'stopped' => 'Stopped',
            'running' => 'Running',
            'paused' => 'Paused'
        ];
    }

    /**
     * Get the parameter display name.
     */
    public function getParameterDisplayAttribute(): string
    {
        $options = self::getParameterOptions();
        return $options[$this->parameter] ?? $this->parameter;
    }

    /**
     * Get the status display name.
     */
    public function getStatusDisplayAttribute(): string
    {
        $options = self::getStatusOptions();
        return $options[$this->status] ?? $this->status;
    }

    /**
     * Get the forecasting status display name.
     */
    public function getForecastingStatusDisplayAttribute(): string
    {
        $options = self::getForecastingStatusOptions();
        return $options[$this->forecasting_status] ?? $this->forecasting_status;
    }

    /**
     * Scope a query to only include active sensors.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('is_active', true);
    }

    /**
     * Scope a query to filter by parameter type.
     */
    public function scopeByParameter($query, $parameter)
    {
        return $query->where('parameter', $parameter);
    }
}
