<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'model_type',
        'version',
        'model_code',
        'description',
        'file_path',
        'n_steps_in',
        'n_steps_out',
        'x_features',
        'y_features',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'n_steps_in' => 'integer',
        'n_steps_out' => 'integer',
        'x_features' => 'integer',
        'y_features' => 'integer',
    ];

    /**
     * Get the data predictions for the model.
     */
    public function dataPredictions(): HasMany
    {
        return $this->hasMany(DataPrediction::class, 'mas_model_code', 'model_code');
    }

    /**
     * Get the scalers for the model.
     */
    public function scalers(): HasMany
    {
        return $this->hasMany(MasScaler::class, 'mas_model_code', 'model_code');
    }

    /**
     * Get the sensors for the model.
     */
    public function sensors(): HasMany
    {
        return $this->hasMany(MasSensor::class, 'mas_model_id');
    }

    /**
     * Scope a query to only include active models.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the model name attribute.
     */
    public function getModelNameAttribute()
    {
        return $this->name;
    }

    /**
     * Get the manufacturer attribute (assuming it's part of the name or description).
     */
    public function getManufacturerAttribute()
    {
        // This is a placeholder - you might want to add a manufacturer field to the table
        return 'Unknown';
    }
}
