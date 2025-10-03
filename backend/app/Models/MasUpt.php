<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasUpt extends Model
{
    use HasFactory;

    protected $fillable = [
        'river_basin_code',
        'cities_code',
        'upts_name',
        'upts_code',
    ];

    /**
     * Get the river basin that owns the UPT.
     */
    public function riverBasin(): BelongsTo
    {
        return $this->belongsTo(MasRiverBasin::class, 'river_basin_code', 'river_basins_code');
    }

    /**
     * Get the city that owns the UPT.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(MasCity::class, 'cities_code', 'cities_code');
    }

    /**
     * Get the UPTDs for the UPT.
     */
    public function uptds(): HasMany
    {
        return $this->hasMany(MasUptd::class, 'upt_code', 'upts_code');
    }

    /**
     * Get the device values for the UPT.
     */
    public function deviceValues(): HasMany
    {
        return $this->hasMany(DeviceValue::class, 'mas_upt_code', 'upts_code');
    }

    /**
     * Get the geojson mappings for the UPT.
     */
    public function geojsonMappings(): HasMany
    {
        return $this->hasMany(GeojsonMapping::class, 'mas_upt_code', 'upts_code');
    }

    /**
     * Get the user by roles for the UPT.
     */
    public function userByRoles(): HasMany
    {
        return $this->hasMany(UserByRole::class, 'upt_code', 'upts_code');
    }
}