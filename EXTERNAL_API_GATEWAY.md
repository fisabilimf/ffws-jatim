# FFWS External API Gateway Documentation

## 🌐 Overview

The FFWS External API Gateway provides a comprehensive solution for fetching, storing, and managing data from external East Java Provincial Water Resources (DPU Air Jatimprov) APIs. This system automatically synchronizes rainfall and water level data from multiple external sources into the FFWS database.

## 📡 External Data Sources

### 1. ARR Pusda (Automatic Rain Recorder)
- **Endpoint**: `https://sih3.dpuair.jatimprov.go.id/api/cuaca-arr-pusda`
- **Parameter**: Rainfall data
- **Description**: Rainfall measurements from ARR stations across East Java
- **Update Frequency**: Hourly

### 2. Meteorologi Juanda
- **Endpoint**: `https://sih3.dpuair.jatimprov.go.id/api/meteorologi-juanda`
- **Parameter**: Rainfall data  
- **Description**: Grid-based meteorological rainfall data from Juanda area
- **Update Frequency**: Hourly

### 3. AWLR Pusda (Automatic Water Level Recorder)
- **Endpoint**: `https://sih3.dpuair.jatimprov.go.id/api/cuaca-awlr-pusda`
- **Parameter**: Rainfall data (Note: Despite the name, this endpoint provides rainfall data)
- **Description**: Rainfall data from AWLR-equipped stations
- **Update Frequency**: Hourly

## 🏗️ System Architecture

```
External APIs → Laravel API Gateway → Database → FFWS Application
     ↓                    ↓               ↓              ↓
  Raw Data      →    Processing    →   Storage    →  Visualization
```

### Components:

1. **ExternalApiService**: Handles HTTP requests, retry logic, and data processing
2. **ExternalApiData Model**: Database model with relationships and data validation
3. **ExternalApiController**: RESTful API endpoints for manual operations
4. **SyncExternalApiData Command**: Artisan command for automated synchronization
5. **Laravel Scheduler**: Automatic hourly synchronization

## 📊 Database Schema

### external_api_data Table

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `external_id` | bigint | ID from external API |
| `judul` | varchar | Station title/name |
| `kode` | varchar | Station code |
| `tipe_input` | varchar | Input type (jam, hari) |
| `alamat` | varchar | Station address |
| `longitude` | decimal(11,8) | Geographic longitude |
| `latitude` | decimal(10,8) | Geographic latitude |
| `tanggal` | date | Measurement date |
| `jam` | integer | Hour (0-23) |
| `value` | decimal(8,3) | Measured value |
| `label` | varchar | Status label |
| `icon` | varchar | Icon reference |
| `warna` | varchar | Color code |
| `api_source` | enum | API source identifier |
| `parameter_type` | enum | Parameter type (rainfall/water-level) |
| `raw_data` | json | Complete API response |
| `created_at_source` | timestamp | Original creation time |
| `updated_at_source` | timestamp | Original update time |
| `sync_status` | enum | Sync status (pending/success/failed) |
| `last_sync_at` | timestamp | Last synchronization time |

## 🚀 API Endpoints

### Base URL: `/api/external-api/`

#### 1. Get API Information
```http
GET /info
```

**Response:**
```json
{
  "success": true,
  "message": "External API information retrieved successfully",
  "data": {
    "sources": {
      "cuaca-arr-pusda": {
        "name": "ARR Pusda (Rainfall)",
        "endpoint": "https://sih3.dpuair.jatimprov.go.id/api/cuaca-arr-pusda",
        "parameter_type": "rainfall",
        "description": "Automatic Rain Recorder data from PU SDA stations"
      }
    },
    "total_sources": 3,
    "supported_parameters": ["rainfall", "water-level"]
  }
}
```

#### 2. Test Connectivity
```http
GET /test-connectivity
```

**Response:**
```json
{
  "success": true,
  "all_healthy": true,
  "data": {
    "cuaca-arr-pusda": {
      "success": true,
      "status_code": 200,
      "response_time_ms": 1305.62,
      "data_available": true
    }
  }
}
```

#### 3. Get Sync Status
```http
GET /status
```

**Response:**
```json
{
  "success": true,
  "data": {
    "cuaca-arr-pusda": {
      "total_records": 21,
      "recent_records": 21,
      "last_sync": "2025-10-03T02:48:59.000000Z",
      "last_sync_human": "2 hours ago",
      "is_recent": false
    }
  }
}
```

#### 4. Sync All Sources
```http
POST /sync-all
```

**Response:**
```json
{
  "success": true,
  "message": "Data synchronization completed successfully",
  "data": {
    "total_sources": 3,
    "successful_sources": 2,
    "total_records": 42,
    "new_records": 42,
    "updated_records": 0
  }
}
```

#### 5. Sync Specific Source
```http
POST /sync/{source}
```

**Parameters:**
- `source`: `cuaca-arr-pusda`, `meteorologi-juanda`, or `cuaca-awlr-pusda`

**Response:**
```json
{
  "success": true,
  "message": "Data synchronization from cuaca-arr-pusda completed successfully",
  "data": {
    "total_records": 21,
    "new_records": 21,
    "updated_records": 0,
    "latest_record_time": "2025-09-30T00:00:00.000000Z"
  }
}
```

#### 6. Get Latest Data
```http
GET /latest-data?limit=10&source=cuaca-arr-pusda&parameter=rainfall
```

**Query Parameters:**
- `limit` (optional): Number of records (1-100, default: 10)
- `source` (optional): Specific API source
- `parameter` (optional): Parameter type (rainfall/water-level)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "external_id": 3060,
      "judul": "ARR Cendono",
      "kode": "BE10124",
      "alamat": "Pasuruan",
      "location": {
        "longitude": 112.6925315,
        "latitude": -7.7579799,
        "coordinates": [112.6925315, -7.7579799]
      },
      "datetime": "2025-09-30T00:00:00.000000Z",
      "value": 0.0,
      "status": "no_rain",
      "parameter_type": "rainfall"
    }
  ]
}
```

#### 7. Get Stations List
```http
GET /stations?source=cuaca-arr-pusda&parameter=rainfall
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "kode": "BE10124",
      "judul": "ARR Cendono",
      "alamat": "Pasuruan",
      "location": {
        "longitude": 112.6925315,
        "latitude": -7.7579799
      },
      "api_source": "cuaca-arr-pusda",
      "parameter_type": "rainfall"
    }
  ]
}
```

#### 8. Get Data by Sensor Code
```http
GET /sensor/{sensorCode}?days=7&parameter=rainfall
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "datetime": "2025-09-30T00:00:00.000000Z",
      "value": 0.0,
      "status": "no_rain",
      "api_source": "cuaca-arr-pusda",
      "parameter_type": "rainfall"
    }
  ],
  "meta": {
    "sensor_code": "BE10124",
    "count": 1,
    "days": 7
  }
}
```

#### 9. Cleanup Old Records
```http
POST /cleanup?days=30
```

**Response:**
```json
{
  "success": true,
  "message": "Successfully cleaned up 0 old records",
  "data": {
    "deleted_records": 0,
    "days_threshold": 30
  }
}
```

## 🔧 Command Line Interface

### Artisan Commands

#### 1. Sync External API Data
```bash
# Sync all sources
php artisan external-api:sync

# Sync specific source
php artisan external-api:sync --source=cuaca-arr-pusda

# Test connectivity only
php artisan external-api:sync --test

# Sync with cleanup
php artisan external-api:sync --cleanup --days=30
```

#### 2. View Command Help
```bash
php artisan external-api:sync --help
```

## ⏰ Automated Scheduling

The system automatically runs hourly synchronization via Laravel's task scheduler:

```php
// Configured in routes/console.php
Schedule::command('external-api:sync')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground();
```

### To enable the scheduler:
```bash
# Add to crontab (Linux/macOS)
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1

# Or run manually for testing
php artisan schedule:run
```

## 🔍 Data Processing Features

### 1. Smart Status Detection
- **Rainfall Status**: Automatically categorizes rainfall intensity
  - `no_rain`: 0mm
  - `light_rain`: 0.1-2.5mm
  - `moderate_rain`: 2.6-10mm
  - `heavy_rain`: >10mm

### 2. Geographic Matching
- Attempts to match external stations with existing FFWS sensors
- Uses coordinate proximity matching (within 1km radius)
- Matches by sensor code when available

### 3. Data Validation
- Validates API response structure
- Handles missing or invalid coordinates
- Preserves original data in `raw_data` JSON field

### 4. Duplicate Prevention
- Unique constraint on `external_id` + `api_source`
- Updates existing records instead of creating duplicates

## 🚨 Error Handling

### 1. Network Issues
- Automatic retry with exponential backoff
- Configurable timeout settings
- Comprehensive logging of failures

### 2. Data Issues
- Skips invalid records with logging
- Continues processing remaining records
- Detailed error reporting in responses

### 3. API Downtime
- Graceful degradation when APIs are unavailable
- Status tracking for each source
- Alert logging for extended outages

## 📈 Monitoring & Logging

### 1. Sync Status Tracking
- Last sync time for each source
- Success/failure rates
- Record counts and statistics

### 2. Performance Metrics
- API response times
- Processing duration
- Memory usage tracking

### 3. Log Channels
- Successful syncs: INFO level
- Failed syncs: ERROR level
- Performance issues: WARNING level

## 🔧 Configuration

### Environment Variables
```env
# API Gateway Settings
EXTERNAL_API_TIMEOUT=30
EXTERNAL_API_RETRY_ATTEMPTS=3
EXTERNAL_API_RETRY_DELAY=1000

# Cleanup Settings  
EXTERNAL_API_CLEANUP_DAYS=30
EXTERNAL_API_CLEANUP_ENABLED=true
```

### Service Configuration
Located in `app/Services/ExternalApiService.php`:
- Timeout settings
- Retry logic
- Data processing rules

## 🎯 Integration with FFWS

### 1. Forecasting System Integration
External API data can be used as input for ML forecasting models:
```php
// Get recent rainfall data for forecasting
$rainfallData = ExternalApiData::byParameter('rainfall')
    ->where('tanggal', '>=', now()->subHours(24))
    ->get();
```

### 2. GeoJSON Mapping
Rainfall data can be integrated with the existing GeoJSON mapping system for flood visualization.

### 3. Real-time Monitoring
External API data provides additional sensor coverage for comprehensive flood monitoring across East Java.

## 🏆 Best Practices

### 1. Data Freshness
- Monitor sync status regularly
- Set up alerts for sync failures
- Review data quality metrics

### 2. Performance Optimization
- Regular cleanup of old records
- Monitor API response times
- Use caching for frequently accessed data

### 3. Error Recovery
- Implement manual sync procedures
- Monitor log files for patterns
- Set up automated recovery processes

## 🔮 Future Enhancements

1. **Real-time WebSocket Integration**: Live data streaming to frontend
2. **Data Quality Scoring**: Automated assessment of data reliability
3. **Predictive Sync Scheduling**: Smart scheduling based on data patterns
4. **Advanced Mapping**: Enhanced sensor matching algorithms
5. **Multi-region Support**: Expansion to other provincial APIs

---

## 📞 Support

For technical support or questions about the External API Gateway:
- Check the Laravel logs: `storage/logs/laravel.log`
- Run connectivity tests: `php artisan external-api:sync --test`
- Review sync status: `/api/external-api/status`

The External API Gateway provides robust, reliable integration with external data sources, enhancing the FFWS with comprehensive rainfall and water level monitoring capabilities across East Java! 🌊🚀