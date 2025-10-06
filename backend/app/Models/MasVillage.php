<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasVillage extends Model
{
    use HasFactory;

    protected $fillable = [
        'villages_name',
        'villages_code',
        'cities_code',
    ];

    /**
     * Get the city that owns the village.
     */
    public function city(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MasCity::class, 'cities_code', 'cities_code');
    }

    /**
     * Get the device values for the village.
     */
    public function deviceValues(): HasMany
    {
        return $this->hasMany(DeviceValue::class, 'mas_village_code', 'villages_code');
    }

    /**
     * Get the geojson mappings for the village.
     */
    public function geojsonMappings(): HasMany
    {
        return $this->hasMany(GeojsonMapping::class, 'mas_village_code', 'villages_code');
    }
}