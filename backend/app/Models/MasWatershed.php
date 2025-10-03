<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasWatershed extends Model
{
    use HasFactory;

    protected $fillable = [
        'river_basin_code',
        'watersheds_name',
        'watersheds_code',
    ];

    /**
     * Get the river basin that owns the watershed.
     */
    public function riverBasin(): BelongsTo
    {
        return $this->belongsTo(MasRiverBasin::class, 'river_basin_code', 'river_basins_code');
    }

    /**
     * Get the device values for the watershed.
     */
    public function deviceValues(): HasMany
    {
        return $this->hasMany(DeviceValue::class, 'mas_watershed_code', 'watersheds_code');
    }

    /**
     * Get the geojson mappings for the watershed.
     */
    public function geojsonMappings(): HasMany
    {
        return $this->hasMany(GeojsonMapping::class, 'mas_watershed_code', 'watersheds_code');
    }
}