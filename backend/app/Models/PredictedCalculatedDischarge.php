<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PredictedCalculatedDischarge extends Model
{
    use HasFactory;

    protected $fillable = [
        'mas_sensor_code',
        'predicted_value',
        'predicted_discharge',
        'rating_curve_id',
        'calculated_at',
    ];

    protected $casts = [
        'predicted_value' => 'double',
        'predicted_discharge' => 'double',
        'calculated_at' => 'datetime',
    ];

    /**
     * Get the sensor that owns the predicted calculated discharge.
     */
    public function sensor(): BelongsTo
    {
        return $this->belongsTo(MasSensor::class, 'mas_sensor_code', 'sensor_code');
    }

    /**
     * Get the rating curve that owns the predicted calculated discharge.
     */
    public function ratingCurve(): BelongsTo
    {
        return $this->belongsTo(RatingCurve::class);
    }
}