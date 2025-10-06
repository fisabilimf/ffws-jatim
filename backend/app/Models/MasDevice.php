<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'mas_river_basin_id',
        'name',
        'code',
        'latitude',
        'longitude',
        'elevation_m',
        'status',
    ];

    /**
     * Get the river basin that owns the device.
     */
    public function riverBasin(): BelongsTo
    {
        return $this->belongsTo(MasRiverBasin::class, 'mas_river_basin_id');
    }

    /**
     * Get the sensors for the device.
     */
    public function sensors(): HasMany
    {
        return $this->hasMany(MasSensor::class, 'mas_device_code', 'code');
    }

    /**
     * Get the device values for the device.
     */
    public function deviceValues(): HasMany
    {
        return $this->hasMany(DeviceValue::class, 'mas_device_code', 'code');
    }

    /**
     * Get the geojson mappings for the device.
     */
    public function geojsonMappings(): HasMany
    {
        return $this->hasMany(GeojsonMapping::class, 'mas_device_code', 'code');
    }
}
