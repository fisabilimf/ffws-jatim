<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_code',
    ];

    /**
     * Get the sensors for the device.
     */
    public function sensors(): HasMany
    {
        return $this->hasMany(MasSensor::class, 'mas_device_code', 'device_code');
    }

    /**
     * Get the device values for the device.
     */
    public function deviceValues(): HasMany
    {
        return $this->hasMany(DeviceValue::class, 'mas_device_code', 'device_code');
    }

    /**
     * Get the geojson mappings for the device.
     */
    public function geojsonMappings(): HasMany
    {
        return $this->hasMany(GeojsonMapping::class, 'mas_device_code', 'device_code');
    }
}
