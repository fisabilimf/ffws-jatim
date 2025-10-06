# Device and Sensor Data Import System

## Overview

This system automatically imports device and sensor data from external APIs provided by Dinas PU SDA Jawa Timur into the FFWS-JATIM database. The system maps external API data to the internal MasDevice and MasSensor models.

## External APIs Integrated

1. **ARR Pusda API** - Automatic Rain Recorder stations
   - URL: `https://sih3.dpuair.jatimprov.go.id/api/cuaca-arr-pusda`
   - Type: Water level monitoring (AWLR - Automatic Water Level Recorder)
   - Data: Water level measurements in meters

2. **AWLR Pusda API** - Automatic Water Level Recorder stations
   - URL: `https://sih3.dpuair.jatimprov.go.id/api/cuaca-awlr-pusda`
   - Type: Rainfall monitoring (ARR - Automatic Rain Recorder)
   - Data: Rainfall measurements in millimeters

3. **Meteorologi Juanda API** - Meteorological stations
   - URL: `https://sih3.dpuair.jatimprov.go.id/api/meteorologi-juanda`
   - Type: Meteorological data
   - Data: Rainfall measurements in millimeters

## Components Implemented

### 1. DeviceSensorImportService
- **Location**: `backend/app/Services/DeviceSensorImportService.php`
- **Purpose**: Core service that handles fetching data from external APIs and mapping to internal models
- **Features**:
  - Fetches data from all three APIs
  - Creates/updates MasDevice records
  - Creates/updates MasSensor records
  - Creates default river basin and models for imported data
  - Error handling and transaction management

### 2. ImportDevicesAndSensors Command
- **Location**: `backend/app/Console/Commands/ImportDevicesAndSensors.php`
- **Usage**: `php artisan import:devices-sensors`
- **Options**:
  - `--dry-run`: Preview what would be imported without making changes
  - `--source=<source>`: Import from specific source only
- **Features**:
  - Console output with detailed results
  - Dry run mode for testing
  - Error reporting

### 3. DeviceSensorImportController
- **Location**: `backend/app/Http/Controllers/Api/Admin/DeviceSensorImportController.php`
- **Purpose**: REST API endpoints for import functionality
- **Endpoints**:
  - `GET /api/external-api/import/preview` - Preview available data
  - `POST /api/external-api/import/devices-sensors` - Run import
  - `GET /api/external-api/import/status` - Get import statistics

## Data Mapping

### Device Mapping
- **External Field** → **Internal Field**
- `kode` → `code` (Device code/identifier)
- `judul` → `name` (Device name/title)
- `lat` → `latitude` (Latitude coordinates)
- `long` → `longitude` (Longitude coordinates)
- `alamat` → Used in sensor description (Location/address)

### Sensor Mapping
- **Parameter Types**:
  - ARR Pusda → `water_level` (meters)
  - AWLR Pusda → `rainfall` (millimeters)
  - Meteorologi Juanda → `rainfall` (millimeters)
- **Thresholds**: Default safety thresholds applied based on parameter type
- **Status**: All imported sensors set to `active` with `stopped` forecasting status

### Default Records Created
- **River Basin**: "Imported from External APIs" (Code: EXT_API)
- **Models**: 
  - AWLR External Model (Code: AWLR_EXT)
  - ARR External Model (Code: ARR_EXT)
  - Meteorologi External Model (Code: METEO_EXT)

## Usage Examples

### Command Line Import
```bash
# Dry run to see what would be imported
php artisan import:devices-sensors --dry-run

# Full import from all APIs
php artisan import:devices-sensors

# Import from specific source
php artisan import:devices-sensors --source=arr-pusda
```

### API Usage
```bash
# Preview available data
curl http://127.0.0.1:8000/api/external-api/import/preview

# Run import
curl -X POST http://127.0.0.1:8000/api/external-api/import/devices-sensors

# Check import status
curl http://127.0.0.1:8000/api/external-api/import/status
```

## Import Results (Last Run)

**Summary**: Successfully imported data from all 3 external APIs
- **Total Devices Created**: 77
- **Total Devices Updated**: 1  
- **Total Sensors Created**: 77
- **Total Sensors Updated**: 1

**Source Breakdown**:
- **ARR Pusda**: 21 records (20 created, 1 updated)
- **AWLR Pusda**: 20 records (20 created, 0 updated)
- **Meteorologi Juanda**: 37 records (37 created, 0 updated)

## Viewing Imported Data

The imported devices and sensors are now available through the public API endpoints:

- **Devices**: `GET /api/public/devices`
- **Sensors**: `GET /api/public/sensors`

All imported devices are linked to the "Imported from External APIs" river basin and can be identified by their river basin association.

## Notes

- The system automatically handles duplicate detection using device codes
- Default thresholds are applied based on parameter types
- All imported sensors are initially set with `forecasting_status = 'stopped'`
- The import process uses database transactions to ensure data integrity
- Error handling includes detailed logging and rollback on failures

## Future Enhancements

1. Scheduled automatic imports using Laravel's task scheduler
2. Data validation and quality checks
3. Historical data import capabilities
4. Custom mapping configurations
5. Real-time data synchronization
6. Import notification system