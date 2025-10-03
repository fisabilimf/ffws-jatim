<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataPrediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'mas_sensor_code',
        'mas_model_code',
        'prediction_run_at',
        'prediction_for_ts',
        'predicted_value',
        'confidence_score',
        'threshold_prediction_status',
    ];

    protected $casts = [
        'prediction_run_at' => 'datetime',
        'prediction_for_ts' => 'datetime',
        'predicted_value' => 'double',
        'confidence_score' => 'double',
    ];

    /**
     * Get the sensor that owns the prediction.
     */
    public function sensor(): BelongsTo
    {
        return $this->belongsTo(MasSensor::class, 'mas_sensor_code', 'sensor_code');
    }

    /**
     * Get the model that owns the prediction.
     */
    public function model(): BelongsTo
    {
        return $this->belongsTo(MasModel::class, 'mas_model_code', 'model_code');
    }

    /**
     * Get threshold prediction status badge class
     */
    public function getThresholdPredictionStatusBadgeClass(): string
    {
        return match($this->threshold_prediction_status) {
            'normal' => 'bg-green-100 text-green-800',
            'watch' => 'bg-blue-100 text-blue-800',
            'warning' => 'bg-yellow-100 text-yellow-800',
            'danger' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get threshold prediction status label
     */
    public function getThresholdPredictionStatusLabel(): string
    {
        return match($this->threshold_prediction_status) {
            'normal' => 'Normal',
            'watch' => 'Siaga',
            'warning' => 'Waspada',
            'danger' => 'Bahaya',
            default => 'Tidak Diketahui'
        };
    }
}
