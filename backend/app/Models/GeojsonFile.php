<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeojsonFile extends Model
{
    protected $fillable = [
        'original_name',
        'stored_path',
        'disk',
        'size',
        'mime_type',
        'sha256',
        'label',
    ];

    /**
     * Get the geojson mappings that reference this file.
     */
    public function mappings(): HasMany
    {
        return $this->hasMany(GeojsonMapping::class, 'geojson_code', 'id');
    }
}
