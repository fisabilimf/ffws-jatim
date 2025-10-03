<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RatingCurve extends Model
{
    use HasFactory;

    protected $fillable = [
        'mas_sensor_code',
        'formula_type',
        'a',
        'b',
        'c',
        'effective_date',
    ];

    protected $casts = [
        'a' => 'double',
        'b' => 'double',
        'c' => 'double',
        'effective_date' => 'date',
    ];

    /**
     * Get the sensor that owns the rating curve.
     */
    public function sensor(): BelongsTo
    {
        return $this->belongsTo(MasSensor::class, 'mas_sensor_code', 'sensor_code');
    }

    /**
     * Get the calculated discharges for the rating curve.
     */
    public function calculatedDischarges(): HasMany
    {
        return $this->hasMany(CalculatedDischarge::class);
    }

    /**
     * Get the predicted calculated discharges for the rating curve.
     */
    public function predictedCalculatedDischarges(): HasMany
    {
        return $this->hasMany(PredictedCalculatedDischarge::class);
    }

    /**
     * Calculate discharge based on sensor value
     */
    public function calculateDischarge(float $sensorValue): float
    {
        return match($this->formula_type) {
            'power' => $this->a * pow($sensorValue, $this->b ?? 1),
            'polynomial' => $this->a + ($this->b ?? 0) * $sensorValue + ($this->c ?? 0) * pow($sensorValue, 2),
            'exponential' => $this->a * exp(($this->b ?? 1) * $sensorValue),
            default => $this->a * $sensorValue
        };
    }
}