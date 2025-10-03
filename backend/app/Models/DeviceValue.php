<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'mas_device_code',
        'mas_river_basin_code',
        'mas_watershed_code',
        'mas_city_code',
        'mas_regency_code',
        'mas_village_code',
        'mas_upt_code',
        'mas_uptd_code',
        'mas_device_parameter_code',
        'name',
        'icon_path',
        'latitude',
        'longitude',
        'elevation',
        'status',
    ];

    protected $casts = [
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6',
        'elevation' => 'double',
    ];

    /**
     * Get the device that owns the device value.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(MasDevice::class, 'mas_device_code', 'device_code');
    }

    /**
     * Get the river basin that owns the device value.
     */
    public function riverBasin(): BelongsTo
    {
        return $this->belongsTo(MasRiverBasin::class, 'mas_river_basin_code', 'river_basins_code');
    }

    /**
     * Get the watershed that owns the device value.
     */
    public function watershed(): BelongsTo
    {
        return $this->belongsTo(MasWatershed::class, 'mas_watershed_code', 'watersheds_code');
    }

    /**
     * Get the city that owns the device value.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(MasCity::class, 'mas_city_code', 'cities_code');
    }

    /**
     * Get the regency that owns the device value.
     */
    public function regency(): BelongsTo
    {
        return $this->belongsTo(MasRegency::class, 'mas_regency_code', 'regencies_code');
    }

    /**
     * Get the village that owns the device value.
     */
    public function village(): BelongsTo
    {
        return $this->belongsTo(MasVillage::class, 'mas_village_code', 'villages_code');
    }

    /**
     * Get the UPT that owns the device value.
     */
    public function upt(): BelongsTo
    {
        return $this->belongsTo(MasUpt::class, 'mas_upt_code', 'upts_code');
    }

    /**
     * Get the UPTD that owns the device value.
     */
    public function uptd(): BelongsTo
    {
        return $this->belongsTo(MasUptd::class, 'mas_uptd_code', 'code');
    }

    /**
     * Get the device parameter that owns the device value.
     */
    public function deviceParameter(): BelongsTo
    {
        return $this->belongsTo(MasDeviceParameter::class, 'mas_device_parameter_code', 'code');
    }
}