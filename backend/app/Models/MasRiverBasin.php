<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasRiverBasin extends Model
{
    use HasFactory;

    protected $table = 'mas_river_basins';

    protected $fillable = [
        'river_basins_name',
        'river_basins_code',
        'cities_code',
    ];

    /**
     * Get the city that owns the river basin.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(MasCity::class, 'cities_code', 'cities_code');
    }

    /**
     * Get the watersheds for the river basin.
     */
    public function watersheds(): HasMany
    {
        return $this->hasMany(MasWatershed::class, 'river_basin_code', 'river_basins_code');
    }
    
    /**
     * Get the UPTs for the river basin.
     */
    public function upts(): HasMany
    {
        return $this->hasMany(MasUpt::class, 'river_basin_code', 'river_basins_code');
    }

    /**
     * Get the device values for the river basin.
     */
    public function deviceValues(): HasMany
    {
        return $this->hasMany(DeviceValue::class, 'mas_river_basin_code', 'river_basins_code');
    }

    /**
     * Get the geojson mappings for the river basin.
     */
    public function geojsonMappings(): HasMany
    {
        return $this->hasMany(GeojsonMapping::class, 'mas_river_basin_code', 'river_basins_code');
    }
}
