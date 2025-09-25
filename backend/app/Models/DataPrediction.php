<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataPrediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'mas_sensor_id',
        'mas_sensor_code',
        'mas_model_id',
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
    public function masSensor(): BelongsTo
    {
        return $this->belongsTo(MasSensor::class);
    }

    /**
     * Get the model that owns the prediction.
     */
    public function masModel(): BelongsTo
    {
        return $this->belongsTo(MasModel::class);
    }
}
