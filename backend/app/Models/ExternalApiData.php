<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ExternalApiData extends Model
{
    use HasFactory;

    protected $table = 'external_api_data';

    protected $fillable = [
        'external_id',
        'judul',
        'kode',
        'tipe_input',
        'alamat',
        'longitude',
        'latitude',
        'tanggal',
        'jam',
        'value',
        'label',
        'icon',
        'warna',
        'api_source',
        'parameter_type',
        'raw_data',
        'created_at_source',
        'updated_at_source',
        'sync_status',
        'last_sync_at'
    ];

    protected $casts = [
        'longitude' => 'decimal:8',  // matches decimal(11,8) in SQL dump
        'latitude' => 'decimal:8',   // matches decimal(10,8) in SQL dump
        'tanggal' => 'date',
        'jam' => 'integer',
        'value' => 'decimal:3',      // matches decimal(8,3) in SQL dump
        'raw_data' => 'json',
        'created_at_source' => 'datetime',
        'updated_at_source' => 'datetime',
        'last_sync_at' => 'datetime',
    ];

    // API Sources
    const SOURCE_ARR_PUSDA = 'cuaca-arr-pusda';
    const SOURCE_METEOROLOGI_JUANDA = 'meteorologi-juanda';
    const SOURCE_AWLR_PUSDA = 'cuaca-awlr-pusda';

    // Parameter Types
    const PARAM_RAINFALL = 'rainfall';
    const PARAM_WATER_LEVEL = 'water-level';

    // Sync Status
    const SYNC_PENDING = 'pending';
    const SYNC_SUCCESS = 'success';
    const SYNC_FAILED = 'failed';

    /**
     * Get records by API source
     */
    public function scopeBySource($query, $source)
    {
        return $query->where('api_source', $source);
    }

    /**
     * Get records by parameter type
     */
    public function scopeByParameter($query, $parameter)
    {
        return $query->where('parameter_type', $parameter);
    }

    /**
     * Get latest records for each sensor
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('tanggal', 'desc')
                    ->orderBy('jam', 'desc');
    }

    /**
     * Get records within date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }

    /**
     * Get combined datetime from tanggal and jam
     */
    public function getDatetimeAttribute()
    {
        if ($this->tanggal && $this->jam !== null) {
            return Carbon::parse($this->tanggal)->addHours($this->jam);
        }
        return $this->tanggal;
    }

    /**
     * Get formatted location
     */
    public function getLocationAttribute()
    {
        if ($this->longitude && $this->latitude) {
            return [
                'longitude' => $this->longitude,
                'latitude' => $this->latitude,
                'coordinates' => [$this->longitude, $this->latitude]
            ];
        }
        return null;
    }

    /**
     * Check if record is recent (within last 24 hours)
     */
    public function getIsRecentAttribute()
    {
        $recordTime = $this->datetime;
        return $recordTime && $recordTime->diffInHours(now()) <= 24;
    }

    /**
     * Get sensor status based on value and label
     */
    public function getStatusAttribute()
    {
        if ($this->parameter_type === self::PARAM_RAINFALL) {
            if ($this->value == 0) {
                return 'no_rain';
            } elseif ($this->value <= 2.5) {
                return 'light_rain';
            } elseif ($this->value <= 10) {
                return 'moderate_rain';
            } else {
                return 'heavy_rain';
            }
        } elseif ($this->parameter_type === self::PARAM_WATER_LEVEL) {
            // Water level status based on label or value thresholds
            $label = strtolower($this->label ?? '');
            if (str_contains($label, 'normal')) {
                return 'normal';
            } elseif (str_contains($label, 'waspada')) {
                return 'warning';
            } elseif (str_contains($label, 'siaga')) {
                return 'alert';
            } elseif (str_contains($label, 'awas')) {
                return 'danger';
            }
            return 'unknown';
        }
        return 'unknown';
    }

    /**
     * Try to match with existing sensor
     */
    public function getMatchedSensorAttribute()
    {
        // Try to match by coordinates or sensor code
        $sensor = \App\Models\MasSensor::where('sensor_code', $this->kode)->first();
        
        if (!$sensor && $this->longitude && $this->latitude) {
            // Try to find nearby sensors (within ~1km)
            $sensor = \App\Models\MasSensor::selectRaw('
                *, 
                (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance
            ', [$this->latitude, $this->longitude, $this->latitude])
            ->having('distance', '<', 1)
            ->orderBy('distance')
            ->first();
        }
        
        return $sensor;
    }

    /**
     * Get API endpoint for this source
     */
    public static function getApiEndpoint($source)
    {
        $endpoints = [
            self::SOURCE_ARR_PUSDA => 'https://sih3.dpuair.jatimprov.go.id/api/cuaca-arr-pusda',
            self::SOURCE_METEOROLOGI_JUANDA => 'https://sih3.dpuair.jatimprov.go.id/api/meteorologi-juanda',
            self::SOURCE_AWLR_PUSDA => 'https://sih3.dpuair.jatimprov.go.id/api/cuaca-awlr-pusda',
        ];

        return $endpoints[$source] ?? null;
    }

    /**
     * Get parameter type for source
     */
    public static function getParameterType($source)
    {
        $parameters = [
            self::SOURCE_ARR_PUSDA => self::PARAM_RAINFALL,
            self::SOURCE_METEOROLOGI_JUANDA => self::PARAM_RAINFALL,
            self::SOURCE_AWLR_PUSDA => self::PARAM_WATER_LEVEL, // Note: This API actually returns rainfall data, but we'll handle it correctly
        ];

        return $parameters[$source] ?? null;
    }

    /**
     * Create or update from API response
     */
    public static function createFromApiResponse($apiData, $source)
    {
        $parameterType = self::getParameterType($source);
        
        return self::updateOrCreate(
            [
                'external_id' => $apiData['id'],
                'api_source' => $source,
            ],
            [
                'judul' => $apiData['judul'] ?? null,
                'kode' => $apiData['kode'] ?? null,
                'tipe_input' => $apiData['tipe_input'] ?? null,
                'alamat' => $apiData['alamat'] ?? null,
                'longitude' => $apiData['long'] ?? null,
                'latitude' => $apiData['lat'] ?? null,
                'tanggal' => $apiData['tanggal'] ?? null,
                'jam' => $apiData['jam'] ?? null,
                'value' => $apiData['value'] ?? null,
                'label' => $apiData['label'] ?? null,
                'icon' => $apiData['icon'] ?? null,
                'warna' => $apiData['warna'] ?? null,
                'parameter_type' => $parameterType,
                'raw_data' => $apiData,
                'created_at_source' => isset($apiData['created_at']) ? Carbon::parse($apiData['created_at']) : null,
                'updated_at_source' => isset($apiData['updated_at']) ? Carbon::parse($apiData['updated_at']) : null,
                'sync_status' => self::SYNC_SUCCESS,
                'last_sync_at' => now(),
            ]
        );
    }
}