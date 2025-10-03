<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasRegency extends Model
{
    use HasFactory;

    protected $fillable = [
        'regencies_name',
        'regencies_code',
    ];

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