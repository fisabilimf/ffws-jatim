# Dynamic GeoJSON Loading System

## Overview

This system enables the frontend to dynamically load different GeoJSON files based on calculated discharge values from sensors. When a sensor's discharge value falls within a specific range, the corresponding GeoJSON file will be loaded to visualize flood mapping or water level visualization.

## System Architecture

The system consists of three main components:

1. **GeojsonMapping Table**: Maps discharge value ranges to specific GeoJSON files
2. **API Endpoints**: Provide access to appropriate GeoJSON files based on discharge values
3. **Frontend Integration**: Dynamically loads GeoJSON files based on current conditions

## Database Structure

### GeojsonMapping Table Fields

- `geojson_code`: Reference to GeojsonFile or identifier for direct file path
- `mas_device_code`: Device/sensor code this mapping applies to
- `value_min`: Minimum discharge value for this mapping (VARCHAR)
- `value_max`: Maximum discharge value for this mapping (VARCHAR)
- `file_path`: Direct path to GeoJSON file (if not using geojson_files table)
- `description`: Human-readable description of this mapping
- Location fields: `mas_river_basin_code`, `mas_watershed_code`, etc.

## API Endpoints

### Base URL: `/api/geojson-mapping`

### 1. Get All Mappings
```http
GET /api/geojson-mapping
```
Returns all geojson mappings with related data.

### 2. Get GeoJSON by Discharge Value
```http
POST /api/geojson-mapping/by-discharge
Content-Type: application/json

{
    "sensor_code": "SENSOR_001",
    "discharge_value": 25.5
}
```
Returns the appropriate GeoJSON file content based on the discharge value.

### 3. Get GeoJSON by Latest Calculated Discharge
```http
GET /api/geojson-mapping/sensor/{sensorCode}/latest
```
Automatically gets the latest `calculated_discharge.sensor_discharge` value and returns the corresponding GeoJSON file.

**Example**: `/api/geojson-mapping/sensor/SENSOR_001/latest`

### 4. Get GeoJSON by Latest Predicted Discharge
```http
GET /api/geojson-mapping/sensor/{sensorCode}/predicted
```
Gets the latest predicted discharge value and returns the corresponding GeoJSON file.

### 5. Get Available Mappings for Sensor
```http
GET /api/geojson-mapping/sensor/{sensorCode}/mappings
```
Returns all available discharge ranges and their corresponding GeoJSON mappings for a specific sensor.

## Response Format

### Successful GeoJSON Response
- **Content-Type**: `application/json` or `application/geo+json`
- **Headers**:
  - `X-Geojson-Source`: `geojson_files_table` or `direct_path`
  - `X-Geojson-Label`: Label from geojson_files table
  - `X-Geojson-Description`: Description from mapping
  - `Cache-Control`: `public, max-age=60`

### Error Response
```json
{
    "error": "No geojson mapping found for the given discharge value",
    "sensor_code": "SENSOR_001",
    "discharge_value": 25.5
}
```

## Frontend Integration Guide

### Basic Usage Example

```javascript
// Method 1: Get GeoJSON by latest discharge (Recommended)
async function loadLatestDischargeGeoJSON(sensorCode) {
    try {
        const response = await fetch(`/api/geojson-mapping/sensor/${sensorCode}/latest`);
        
        if (response.ok) {
            const geojsonData = await response.json();
            
            // Get metadata from headers
            const source = response.headers.get('X-Geojson-Source');
            const label = response.headers.get('X-Geojson-Label');
            
            // Load GeoJSON into map
            map.getSource('flood-layer').setData(geojsonData);
            
            return geojsonData;
        } else {
            console.log('No GeoJSON available for current discharge level');
            return null;
        }
    } catch (error) {
        console.error('Error loading GeoJSON:', error);
        return null;
    }
}

// Method 2: Get GeoJSON by specific discharge value
async function loadGeoJSONByDischarge(sensorCode, dischargeValue) {
    try {
        const response = await fetch('/api/geojson-mapping/by-discharge', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                sensor_code: sensorCode,
                discharge_value: dischargeValue
            })
        });

        if (response.ok) {
            const geojsonData = await response.json();
            return geojsonData;
        }
        return null;
    } catch (error) {
        console.error('Error loading GeoJSON:', error);
        return null;
    }
}

// Method 3: Auto-update based on real-time data
async function setupAutoGeoJSONUpdate(sensorCode, intervalMs = 30000) {
    const updateGeoJSON = async () => {
        const geojsonData = await loadLatestDischargeGeoJSON(sensorCode);
        if (geojsonData) {
            console.log(`Updated GeoJSON for sensor ${sensorCode}`);
        }
    };

    // Initial load
    await updateGeoJSON();
    
    // Set up periodic updates
    setInterval(updateGeoJSON, intervalMs);
}
```

### Integration with Existing MapboxMap Component

```javascript
// In your MapboxMap component
useEffect(() => {
    if (selectedSensor && map.current) {
        // Load GeoJSON based on latest discharge
        loadLatestDischargeGeoJSON(selectedSensor.sensor_code)
            .then(geojsonData => {
                if (geojsonData) {
                    // Add or update the flood layer
                    if (map.current.getSource('flood-visualization')) {
                        map.current.getSource('flood-visualization').setData(geojsonData);
                    } else {
                        map.current.addSource('flood-visualization', {
                            type: 'geojson',
                            data: geojsonData
                        });
                        
                        map.current.addLayer({
                            id: 'flood-visualization-layer',
                            type: 'fill',
                            source: 'flood-visualization',
                            paint: {
                                'fill-color': '#0080ff',
                                'fill-opacity': 0.5
                            }
                        });
                    }
                }
            });
    }
}, [selectedSensor]);
```

## Data Setup Requirements

### Sample GeojsonMapping Records

To use this system, you need to populate the `geojson_mapping` table with appropriate records:

```sql
-- Example: Low discharge level (0-10 m³/s) - Normal conditions
INSERT INTO geojson_mapping (
    geojson_code, 
    mas_device_code, 
    value_min, 
    value_max, 
    file_path, 
    description
) VALUES (
    '1', 
    'SENSOR_001', 
    '0', 
    '10', 
    NULL, 
    'Normal water level - no flood risk'
);

-- Example: Medium discharge level (10-25 m³/s) - Warning level
INSERT INTO geojson_mapping (
    geojson_code, 
    mas_device_code, 
    value_min, 
    value_max, 
    file_path, 
    description
) VALUES (
    '2', 
    'SENSOR_001', 
    '10', 
    '25', 
    'geojson/flood_warning.geojson', 
    'Warning level - potential flooding'
);

-- Example: High discharge level (25+ m³/s) - Danger level
INSERT INTO geojson_mapping (
    geojson_code, 
    mas_device_code, 
    value_min, 
    value_max, 
    file_path, 
    description
) VALUES (
    '3', 
    'SENSOR_001', 
    '25', 
    '999', 
    'geojson/flood_danger.geojson', 
    'Danger level - high flood risk'
);
```

## Model Enhancements

### New GeojsonMapping Model Methods

```php
// Find mapping by device and discharge value
$mapping = GeojsonMapping::findByDeviceAndDischarge('SENSOR_001', 15.5);

// Use scopes for flexible queries
$mappings = GeojsonMapping::forDevice('SENSOR_001')
                         ->forDischargeValue(20.0)
                         ->get();
```

### GeojsonFile Relationship

The `GeojsonFile` model now includes a relationship to track which mappings reference each file:

```php
$file = GeojsonFile::find(1);
$mappings = $file->mappings; // Get all mappings that use this file
```

## Error Handling

The API includes comprehensive error handling:

- **404**: No mapping found for discharge value
- **404**: Referenced GeoJSON file not found on disk
- **400**: Invalid request parameters
- **500**: Server errors

## Performance Considerations

- **Caching**: Responses include cache headers (`max-age=60`)
- **Indexing**: Ensure database indexes on `mas_device_code`, `value_min`, and `value_max`
- **File Storage**: GeoJSON files are stored efficiently and served with appropriate MIME types

## Future Enhancements

1. **Multiple Mappings**: Support for multiple GeoJSON files per discharge range
2. **Time-based Mappings**: Different mappings based on time of day/season
3. **Interpolation**: Smooth transitions between discharge ranges
4. **WebSocket Updates**: Real-time GeoJSON updates via WebSocket connections

## Testing

To test the system:

1. Add sample data to `geojson_mapping` table
2. Add corresponding GeoJSON files or `geojson_files` records
3. Create test discharge data in `calculated_discharges` table
4. Call the API endpoints to verify correct GeoJSON files are returned

The system is now ready for frontend integration and will automatically serve the appropriate GeoJSON files based on your sensor discharge values!