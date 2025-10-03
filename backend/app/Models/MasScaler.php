<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasScaler extends Model
{
    use HasFactory;

    protected $table = 'mas_scalers';

    protected $fillable = [
        'mas_model_id',
        'mas_model_code',
        'mas_sensor_id',
        'name',
        'scaler_code',
        'io_axis',
        'technique',
        'version',
        'file_path',
        'file_hash_sha256',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the model that owns the scaler.
     */
    public function masModel(): BelongsTo
    {
        return $this->belongsTo(MasModel::class, 'mas_model_id');
    }

    /**
     * Get the model by code that owns the scaler.
     */
    public function masModelByCode(): BelongsTo
    {
        return $this->belongsTo(MasModel::class, 'mas_model_code', 'model_code');
    }

    /**
     * Get the sensor that owns the scaler.
     */
    public function masSensor(): BelongsTo
    {
        return $this->belongsTo(MasSensor::class, 'mas_sensor_id');
    }

    /**
     * Scope a query to only include active scalers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by technique.
     */
    public function scopeByTechnique($query, $technique)
    {
        return $query->where('technique', $technique);
    }

    /**
     * Scope a query to filter by io_axis.
     */
    public function scopeByAxis($query, $axis)
    {
        return $query->where('io_axis', $axis);
    }

    /**
     * Get the technique options.
     */
    public static function getTechniqueOptions(): array
    {
        return [
            'standard' => 'Standard Scaler',
            'minmax' => 'Min-Max Scaler',
            'robust' => 'Robust Scaler',
            'custom' => 'Custom Scaler',
        ];
    }

    /**
     * Get the IO axis options.
     */
    public static function getAxisOptions(): array
    {
        return [
            'x' => 'X Axis (Input)',
            'y' => 'Y Axis (Output)',
        ];
    }
}