<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasCity extends Model
{
    use HasFactory;

    protected $fillable = [
        'cities_name',
        'cities_code',
        'regencies_code',
    ];

    /**
     * Get the regency that owns the city.
     */
    public function regency(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MasRegency::class, 'regencies_code', 'regencies_code');
    }

    /**
     * Get the river basins for the city.
     */
    public function riverBasins(): HasMany
    {
        return $this->hasMany(MasRiverBasin::class, 'cities_code', 'cities_code');
    }

    /**
     * Get the UPTs for the city.
     */
    public function upts(): HasMany
    {
        return $this->hasMany(MasUpt::class, 'cities_code', 'cities_code');
    }

    /**
     * Get the device values for the city.
     */
    public function deviceValues(): HasMany
    {
        return $this->hasMany(DeviceValue::class, 'mas_city_code', 'cities_code');
    }

    /**
     * Get the geojson mappings for the city.
     */
    public function geojsonMappings(): HasMany
    {
        return $this->hasMany(GeojsonMapping::class, 'mas_city_code', 'cities_code');
    }
}