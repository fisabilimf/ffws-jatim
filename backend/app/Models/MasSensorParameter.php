<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasSensorParameter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
    ];

    /**
     * Get the sensor values for the sensor parameter.
     */
    public function sensorValues(): HasMany
    {
        return $this->hasMany(SensorValue::class, 'mas_sensor_parameter_code', 'code');
    }
}