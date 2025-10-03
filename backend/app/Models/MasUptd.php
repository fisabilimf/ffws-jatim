<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasUptd extends Model
{
    use HasFactory;

    protected $fillable = [
        'upt_code',
        'name',
        'code',
    ];

    /**
     * Get the UPT that owns the UPTD.
     */
    public function upt(): BelongsTo
    {
        return $this->belongsTo(MasUpt::class, 'upt_code', 'upts_code');
    }

    /**
     * Get the device values for the UPTD.
     */
    public function deviceValues(): HasMany
    {
        return $this->hasMany(DeviceValue::class, 'mas_uptd_code', 'code');
    }

    /**
     * Get the geojson mappings for the UPTD.
     */
    public function geojsonMappings(): HasMany
    {
        return $this->hasMany(GeojsonMapping::class, 'mas_uptd_code', 'code');
    }
}