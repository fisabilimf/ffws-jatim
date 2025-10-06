<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasRegency extends Model
{
    use HasFactory;

    protected $fillable = [
        'regencies_name',
        'regencies_code',
        'provinces_code',
    ];

    /**
     * Get the province that owns the regency.
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(MasProvince::class, 'provinces_code', 'provinces_code');
    }

    /**
     * Get the device values for the regency.
     */
    public function deviceValues(): HasMany
    {
        return $this->hasMany(DeviceValue::class, 'mas_regency_code', 'regencies_code');
    }

    /**
     * Get the geojson mappings for the regency.
     */
    public function geojsonMappings(): HasMany
    {
        return $this->hasMany(GeojsonMapping::class, 'mas_regency_code', 'regencies_code');
    }
}