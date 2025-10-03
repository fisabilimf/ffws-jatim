<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalculatedDischarge extends Model
{
    use HasFactory;

    protected $fillable = [
        'mas_sensor_code',
        'sensor_value',
        'sensor_discharge',
        'rating_curve_id',
        'calculated_at',
    ];

    protected $casts = [
        'sensor_value' => 'double',
        'sensor_discharge' => 'double',
        'calculated_at' => 'datetime',
    ];

    /**
     * Get the sensor that owns the calculated discharge.
     */
    public function sensor(): BelongsTo
    {
        return $this->belongsTo(MasSensor::class, 'mas_sensor_code', 'sensor_code');
    }

    /**
     * Get the rating curve that owns the calculated discharge.
     */
    public function ratingCurve(): BelongsTo
    {
        return $this->belongsTo(RatingCurve::class);
    }
}