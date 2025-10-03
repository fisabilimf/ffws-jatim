<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasDeviceParameter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
    ];

    /**
     * Get the device values for the device parameter.
     */
    public function deviceValues(): HasMany
    {
        return $this->hasMany(DeviceValue::class, 'mas_device_parameter_code', 'code');
    }

    /**
     * Get the geojson mappings for the device parameter.
     */
    public function geojsonMappings(): HasMany
    {
        return $this->hasMany(GeojsonMapping::class, 'mas_device_parameter_code', 'code');
    }
}