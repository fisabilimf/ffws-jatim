<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'mas_river_basin_id',
        'latitude',
        'longitude',
        'elevation_m',
        'status'
    ];

    public function riverBasin()
    {
        return $this->belongsTo(MasRiverBasin::class, 'mas_river_basin_id');
    }
}
