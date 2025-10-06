<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeojsonMapping extends Model
{
    use HasFactory;

    protected $table = 'geojson_mapping';

    protected $fillable = [
        'geojson_code',
        'mas_device_code',
        'mas_river_basin_code',
        'mas_watershed_code',
        'mas_city_code',
        'mas_regency_code',
        'mas_village_code',
        'mas_upt_code',
        'mas_uptd_code',
        'mas_device_parameter_code',
        'value_min',
        'value_max',
        'file_path',
        'version',
        'description',
        'properties_content',
    ];

    /**
     * Get the device that owns the geojson mapping.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(MasDevice::class, 'mas_device_code', 'code');
    }

    /**
     * Get the river basin that owns the geojson mapping.
     */
    public function riverBasin(): BelongsTo
    {
        return $this->belongsTo(MasRiverBasin::class, 'mas_river_basin_code', 'river_basins_code');
    }

    /**
     * Get the watershed that owns the geojson mapping.
     */
    public function watershed(): BelongsTo
    {
        return $this->belongsTo(MasWatershed::class, 'mas_watershed_code', 'watersheds_code');
    }

    /**
     * Get the city that owns the geojson mapping.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(MasCity::class, 'mas_city_code', 'cities_code');
    }

    /**
     * Get the regency that owns the geojson mapping.
     */
    public function regency(): BelongsTo
    {
        return $this->belongsTo(MasRegency::class, 'mas_regency_code', 'regencies_code');
    }

    /**
     * Get the village that owns the geojson mapping.
     */
    public function village(): BelongsTo
    {
        return $this->belongsTo(MasVillage::class, 'mas_village_code', 'villages_code');
    }

    /**
     * Get the UPT that owns the geojson mapping.
     */
    public function upt(): BelongsTo
    {
        return $this->belongsTo(MasUpt::class, 'mas_upt_code', 'upts_code');
    }

    /**
     * Get the UPTD that owns the geojson mapping.
     */
    public function uptd(): BelongsTo
    {
        return $this->belongsTo(MasUptd::class, 'mas_uptd_code', 'code');
    }

    /**
     * Get the device parameter that owns the geojson mapping.
     */
    public function deviceParameter(): BelongsTo
    {
        return $this->belongsTo(MasDeviceParameter::class, 'mas_device_parameter_code', 'code');
    }

    /**
     * Get the geojson file that this mapping references.
     */
    public function geojsonFile(): BelongsTo
    {
        return $this->belongsTo(GeojsonFile::class, 'geojson_code', 'id');
    }

    /**
     * Scope to find mappings for a specific discharge value
     */
    public function scopeForDischargeValue($query, $dischargeValue)
    {
        return $query->where('value_min', '<=', $dischargeValue)
                    ->where('value_max', '>=', $dischargeValue);
    }

    /**
     * Scope to find mappings for a specific device/sensor
     */
    public function scopeForDevice($query, $deviceCode)
    {
        return $query->where('mas_device_code', $deviceCode);
    }

    /**
     * Static method to find geojson mapping by device and discharge value
     */
    public static function findByDeviceAndDischarge($deviceCode, $dischargeValue)
    {
        return static::forDevice($deviceCode)
                    ->forDischargeValue($dischargeValue)
                    ->first();
    }
}