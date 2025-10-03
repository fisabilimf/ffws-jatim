# 🌊 FFWS External API Gateway

> **Real-time integration with East Java Provincial Water Resources APIs**

## Overview

The FFWS External API Gateway is a comprehensive system that automatically fetches and stores hydrological data from East Java's provincial water resources APIs. This system enables real-time monitoring of rainfall and water levels across multiple monitoring stations throughout East Java province.

## 🎯 Key Features

- **Multi-Source Integration**: Connects to 3 different DPU Air Jatimprov APIs
- **Automated Synchronization**: Hourly automated data collection via Laravel scheduler
- **Manual Controls**: Full API endpoints for manual operations and monitoring
- **Robust Error Handling**: Comprehensive retry logic and failure recovery
- **Data Validation**: Smart data processing with status categorization
- **Real-time Monitoring**: Live connectivity testing and sync status tracking
- **RESTful API**: 9 comprehensive endpoints for external integration

## 📡 Data Sources

| Source | Parameter | Status | Description |
|--------|-----------|--------|-------------|
| `cuaca-arr-pusda` | Rainfall | ✅ Active | Automatic Rain Recorder stations across East Java |
| `meteorologi-juanda` | Rainfall | ❌ Timeout | Meteorological data from Juanda Airport |
| `cuaca-awlr-pusda` | Water Level | ✅ Active | Automatic Water Level Recorder stations |

## 🗄️ Database Schema

The system uses the `external_api_data` table with the following structure:

```sql
- id (Primary Key)
- api_source (cuaca-arr-pusda, meteorologi-juanda, cuaca-awlr-pusda)
- parameter_type (rainfall, water-level)
- kode (Station code)
- judul (Station name)
- alamat (Station address)
- latitude & longitude (GPS coordinates)
- value (Measured value)
- status (no_rain, light_rain, moderate_rain, heavy_rain, normal, warning, alert, danger)
- datetime (Measurement timestamp)
- sensor_id (Foreign key to mas_sensor table)
- created_at & updated_at (Laravel timestamps)
```

## 🔧 System Architecture

### Core Components

1. **ExternalApiData Model** (`app/Models/ExternalApiData.php`)
   - Eloquent model with relationships
   - Smart status detection and categorization
   - Geographic data handling

2. **ExternalApiService** (`app/Services/ExternalApiService.php`)
   - HTTP client with retry logic
   - Data processing and validation
   - Multi-source synchronization

3. **ExternalApiController** (`app/Http/Controllers/ExternalApiController.php`)
   - 9 RESTful endpoints
   - Comprehensive error handling
   - JSON API responses

4. **SyncExternalApiData Command** (`app/Console/Commands/SyncExternalApiData.php`)
   - Artisan command for manual sync
   - Progress tracking with CLI output
   - Source-specific operations

5. **Laravel Scheduler Integration** (`routes/console.php`)
   - Automated hourly synchronization
   - Background processing

## 🚀 Quick Start

### 1. Run Database Migration
```bash
php artisan migrate
```

### 2. Manual Data Sync
```bash
# Sync all sources
php artisan sync:external-api

# Sync specific source
php artisan sync:external-api --source=cuaca-arr-pusda

# Test connectivity
php artisan sync:external-api --test-only
```

### 3. Start Laravel Server
```bash
php artisan serve
```

### 4. Test API Endpoints
```bash
# Get system info
curl http://127.0.0.1:8000/api/external-api/info

# Test connectivity
curl http://127.0.0.1:8000/api/external-api/test-connectivity

# Get sync status
curl http://127.0.0.1:8000/api/external-api/status
```

## 📋 API Endpoints

### System Information
- `GET /api/external-api/info` - System and source information
- `GET /api/external-api/test-connectivity` - Test all external API connections
- `GET /api/external-api/status` - Current synchronization status

### Data Synchronization
- `POST /api/external-api/sync-all` - Sync all sources
- `POST /api/external-api/sync/{source}` - Sync specific source

### Data Retrieval
- `GET /api/external-api/latest-data` - Latest synchronized data
- `GET /api/external-api/stations` - List all monitoring stations
- `GET /api/external-api/sensor/{code}` - Data for specific sensor

### Maintenance
- `POST /api/external-api/cleanup` - Remove old records

## 🎮 Interactive Demo

Open `external_api_demo.html` in your browser to access the interactive demo interface:

1. **System Status**: Monitor API connectivity and sync status
2. **Data Synchronization**: Manual sync controls with real-time feedback
3. **Data Exploration**: Browse latest data, stations, and sensor readings
4. **Maintenance**: System cleanup and maintenance operations

Features:
- Real-time API testing
- Interactive data visualization
- Comprehensive status monitoring
- One-click operations

## ⚙️ Configuration

### Scheduler Setup (Production)
Add to your crontab:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### Environment Variables
The system uses Laravel's default HTTP client configuration. Optionally configure:
```env
# HTTP timeout settings
EXTERNAL_API_TIMEOUT=30
EXTERNAL_API_RETRY_ATTEMPTS=3
```

### Logging
All operations are logged to Laravel's default log files:
- `storage/logs/laravel.log` - General application logs
- Sync operations include detailed progress tracking
- Error handling with full stack traces

## 📊 Data Processing Logic

### Status Categorization

**Rainfall Data:**
- `no_rain`: 0mm
- `light_rain`: 0.1-10mm
- `moderate_rain`: 10.1-50mm
- `heavy_rain`: >50mm

**Water Level Data:**
- `normal`: Standard operating levels
- `warning`: Elevated but safe levels
- `alert`: Concerning levels requiring attention
- `danger`: Critical levels requiring immediate action

### Data Validation
- Duplicate detection based on station code and timestamp
- Invalid data filtering (negative values, extreme outliers)
- Geographic coordinate validation
- Timestamp normalization to UTC

## 🔍 Monitoring & Troubleshooting

### Health Checks
```bash
# Check system status
curl http://127.0.0.1:8000/api/external-api/status

# Test external API connectivity
curl http://127.0.0.1:8000/api/external-api/test-connectivity

# View latest sync results
php artisan sync:external-api --dry-run
```

### Common Issues

**External API Timeouts:**
- Check internet connectivity
- Verify external API endpoints are accessible
- Review timeout settings in service configuration

**No New Data:**
- Verify external APIs are returning data
- Check for duplicate prevention logic
- Review timestamp processing

**Sync Failures:**
- Check Laravel logs for detailed error messages
- Verify database connectivity
- Ensure sufficient disk space

### Performance Optimization
- Database indexes on `api_source`, `datetime`, and `kode` columns
- Batch processing for large datasets
- Configurable cleanup of old records
- Connection pooling for HTTP requests

## 📈 Production Deployment

### Pre-deployment Checklist
- [ ] Database migration completed
- [ ] Cron job configured for scheduler
- [ ] Log rotation configured
- [ ] Monitoring alerts set up
- [ ] Backup strategy in place

### Scaling Considerations
- Consider database partitioning for large datasets
- Implement Redis caching for frequently accessed data
- Use queue workers for async processing
- Set up load balancing for high availability

### Security
- API endpoints use Laravel's built-in security features
- Input validation and sanitization
- Rate limiting can be implemented via Laravel middleware
- HTTPS recommended for production deployment

## 🤝 Integration Examples

### Laravel Controller Integration
```php
use App\Models\ExternalApiData;

// Get latest rainfall data
$rainfallData = ExternalApiData::where('parameter_type', 'rainfall')
    ->whereDate('datetime', today())
    ->orderBy('datetime', 'desc')
    ->get();

// Get station data for mapping
$stations = ExternalApiData::select('kode', 'judul', 'latitude', 'longitude')
    ->distinct()
    ->whereNotNull('latitude')
    ->get();
```

### Frontend Integration
```javascript
// Fetch latest data via API
fetch('/api/external-api/latest-data?limit=10')
    .then(response => response.json())
    .then(data => {
        // Process real-time data
        console.log('Latest readings:', data.data);
    });
```

## 📞 Support & Maintenance

This system is designed for reliable, autonomous operation with comprehensive logging and monitoring capabilities. For issues or enhancements:

1. Check the interactive demo for system status
2. Review Laravel logs for detailed error information
3. Use the built-in API endpoints for troubleshooting
4. Consult the comprehensive documentation in `EXTERNAL_API_GATEWAY.md`

---

**Built with Laravel 11 | Real-time Hydrological Data Integration | East Java Provincial Water Resources**