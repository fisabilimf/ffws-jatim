<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataActualsLatest extends Model
{
    use HasFactory;

    protected $table = 'data_actuals_latest';
    
    // This table doesn't have auto-incrementing ID, primary key is mas_sensor_code
    protected $primaryKey = 'mas_sensor_code';
    public $incrementing = false;
    protected $keyType = 'string';
    
    // Only updated_at timestamp
    public $timestamps = false;

    protected $fillable = [
        'mas_sensor_code',
        'id',
        'value',
        'received_at',
        'threshold_status',
        'updated_at',
    ];

    protected $casts = [
        'value' => 'double',
        'received_at' => 'datetime',
        'updated_at' => 'timestamp',
    ];

    /**
     * Get threshold status badge class
     */
    public function getThresholdStatusBadgeClass(): string
    {
        return match($this->threshold_status) {
            'normal' => 'bg-green-100 text-green-800',
            'watch' => 'bg-blue-100 text-blue-800',
            'warning' => 'bg-yellow-100 text-yellow-800',
            'danger' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}