# FFWS Forecasting System Integration Guide

## 🎯 Overview

The FFWS Forecasting System has been enhanced and fully integrated with the Laravel backend and React frontend. This document provides a comprehensive guide for using the complete forecasting ecosystem.

## 🏗️ System Architecture

```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   Frontend      │◄──►│  Laravel Backend │◄──►│ Python Flask    │
│   (React)       │    │   (API Proxy)    │    │  (ML Service)   │
│                 │    │                  │    │                 │
│ • Forecasting   │    │ • Proxy Routes   │    │ • ML Models     │
│ • GeoJSON Maps  │    │ • Database Sync  │    │ • Predictions   │
│ • Real-time UI  │    │ • Cache Layer    │    │ • Fallback      │
└─────────────────┘    └──────────────────┘    └─────────────────┘
                                │
                                ▼
                       ┌──────────────────┐
                       │   MySQL Database │
                       │                  │
                       │ • Sensor Data    │
                       │ • Predictions    │
                       │ • GeoJSON Maps   │
                       └──────────────────┘
```

## 🔧 Components Overview

### 1. Python Flask Service (Port 5000)
- **Location**: `/forecasting/`
- **Purpose**: ML model inference and predictions
- **Features**:
  - 6 ML models (DHOMPO & PURWODADI: GRU, LSTM, TCN)
  - Fallback forecasting for sensors without models
  - Real-time predictions up to 24 hours
  - Health monitoring and error handling

### 2. Laravel Backend Integration
- **Location**: `/backend/app/Http/Controllers/Api/Admin/ForecastingController.php`
- **Purpose**: API proxy and database integration
- **Features**:
  - Proxy to Python service
  - Database synchronization
  - GeoJSON integration
  - Caching and error handling

### 3. Frontend Services
- **Location**: `/frontend/src/services/forecasting.js`
- **Purpose**: UI integration and real-time updates
- **Features**:
  - Smart forecasting (auto-selects ML or fallback)
  - Real-time prediction updates
  - Timeline visualization
  - GeoJSON map integration

## 🚀 Getting Started

### 1. Start Python Forecasting Service

```bash
cd forecasting
python run_dev_server.py
```

**Expected Output:**
```
🌊 FFWS Enhanced Forecasting API Server
📅 Started at: 2024-10-03 15:30:00
🌐 Server URL: http://0.0.0.0:5000
🔍 System Prerequisites Check:
   ✅ Database Connection: OK
   ✅ ML Models: All models available
   ✅ Model Scalers: Available
```

### 2. Start Laravel Backend

```bash
cd backend
php artisan serve --host=127.0.0.1 --port=8000
```

### 3. Start Frontend

```bash
cd frontend
npm run dev
```

## 📡 API Endpoints

### Laravel Proxy Endpoints (`/api/forecasting/`)

| Method | Endpoint | Purpose |
|--------|----------|---------|
| `GET` | `/health` | Check forecasting service health |
| `GET` | `/models` | List available ML models |
| `GET` | `/sensors` | List forecasting-enabled sensors |
| `POST` | `/run` | Run ML forecast for sensor |
| `POST` | `/run-basin` | Run forecast for entire basin |
| `POST` | `/fallback` | Run fallback forecast |
| `GET` | `/predictions/{sensorCode}` | Get latest predictions |
| `GET` | `/predictions/{sensorCode}/with-geojson` | Get predictions with GeoJSON |

### Python Flask Endpoints (`http://localhost:5000/api/`)

| Method | Endpoint | Purpose |
|--------|----------|---------|
| `GET` | `/health` | Service health check |
| `POST` | `/forecast/run` | Direct ML prediction |
| `POST` | `/forecast/hourly` | Hourly predictions |
| `POST` | `/forecast/run-basin` | Basin-wide forecasting |
| `POST` | `/forecast/fallback` | Fallback predictions |

## 💻 Frontend Integration Examples

### 1. Basic Forecasting

```javascript
import { runSmartForecast, getLatestPredictions } from '../services/forecasting';

// Run forecast for a sensor (automatically chooses ML or fallback)
const forecast = await runSmartForecast('SENSOR_001', {
    predictionHours: 24,
    stepHours: 1.0
});

// Get stored predictions
const predictions = await getLatestPredictions('SENSOR_001');
```

### 2. Real-time Forecasting with GeoJSON

```javascript
import { getPredictionsWithGeojson } from '../services/forecasting';
import { fetchGeojsonByLatestPredictedDischarge } from '../services/geojsonMapping';

// Get predictions with GeoJSON mapping info
const data = await getPredictionsWithGeojson('SENSOR_001');

// Load GeoJSON for predicted discharge levels
const geojsonResult = await fetchGeojsonByLatestPredictedDischarge('SENSOR_001');
if (geojsonResult?.data) {
    map.getSource('forecast-layer').setData(geojsonResult.data);
}
```

### 3. Auto-Forecast Setup

```javascript
import { setupAutoForecast } from '../services/forecasting';

// Setup automatic forecasting for multiple sensors
const stopAutoForecast = setupAutoForecast(
    ['SENSOR_001', 'SENSOR_002'], 
    (sensorCode, predictions) => {
        console.log(`New predictions for ${sensorCode}:`, predictions);
        updateMap(sensorCode, predictions);
    },
    600000 // Update every 10 minutes
);

// Stop auto-forecasting when component unmounts
// stopAutoForecast();
```

### 4. Forecast Timeline Visualization

```javascript
import { getForecastTimeline } from '../services/forecasting';

const timelineData = await getForecastTimeline('SENSOR_001', 24);

// timelineData.timeline contains:
// [
//   {
//     time: "2024-10-03T16:00:00Z",
//     discharge: 15.2,
//     confidence: 0.87,
//     threshold: "warning",
//     status: "warning"
//   },
//   ...
// ]

// Use with Chart.js or similar
const chartData = {
    labels: timelineData.timeline.map(t => t.time),
    datasets: [{
        label: 'Predicted Discharge',
        data: timelineData.timeline.map(t => t.discharge),
        borderColor: 'rgb(75, 192, 192)',
        tension: 0.1
    }]
};
```

## 🎛️ Configuration

### Laravel Configuration (`config/services.php`)

```php
'forecasting' => [
    'url' => env('FORECASTING_API_URL', 'http://localhost:5000'),
    'timeout' => env('FORECASTING_API_TIMEOUT', 30),
    'enabled' => env('FORECASTING_ENABLED', true),
],
```

### Environment Variables (`.env`)

```bash
# Laravel Backend
FORECASTING_API_URL=http://localhost:5000
FORECASTING_API_TIMEOUT=30
FORECASTING_ENABLED=true

# Python Service (forecasting/.env)
FLASK_HOST=0.0.0.0
FLASK_PORT=5000
DEFAULT_PREDICTION_HOURS=24
ENABLE_DATABASE_SYNC=True
CACHE_PREDICTIONS=True
```

## 🔄 Data Flow

### 1. Sensor Reading → Prediction Pipeline

```
1. Sensor measures water level (e.g., 2.5m)
2. Laravel calculates discharge using rating curve (e.g., 15.0 m³/s)
3. Python service generates predictions for next 24 hours
4. Predictions saved to `predicted_calculated_discharges` table
5. Frontend loads predictions and appropriate GeoJSON
6. Map displays flood visualization based on predicted levels
```

### 2. Real-time Update Cycle

```
Every 10 minutes:
1. Auto-forecast checks for outdated predictions
2. Runs ML forecast if needed
3. Updates database with new predictions
4. Frontend retrieves updated data
5. Map updates with new flood visualization
```

## 📊 Available ML Models

### DHOMPO Station
- **DHOMPO_GRU**: GRU neural network (4 input features, 5 output features)
- **DHOMPO_LSTM**: LSTM neural network (4 input features, 5 output features)
- **DHOMPO_TCN**: Temporal Convolutional Network (4 input features, 5 output features)

### PURWODADI Station
- **PURWODADI_GRU**: GRU neural network (3 input features, 3 output features)
- **PURWODADI_LSTM**: LSTM neural network (3 input features, 3 output features)
- **PURWODADI_TCN**: Temporal Convolutional Network (3 input features, 3 output features)

### Fallback System
- **Statistical Forecasting**: For sensors without ML models
- **Trend Extrapolation**: Based on historical patterns
- **Confidence Scoring**: Reliability assessment

## 🎯 Integration with GeoJSON System

The forecasting system is fully integrated with the dynamic GeoJSON mapping:

```javascript
// Example: Show predicted flood areas for next 6 hours
const predictions = await getLatestPredictions('SENSOR_001');
const peakPrediction = predictions.predictions.reduce((max, p) => 
    p.predicted_discharge > max.predicted_discharge ? p : max
);

// Load appropriate GeoJSON for peak predicted discharge
const geojsonResult = await fetchGeojsonByDischarge('SENSOR_001', peakPrediction.predicted_discharge);
if (geojsonResult) {
    // Display flood map for predicted peak
    map.addLayer({
        id: 'predicted-flood',
        type: 'fill',
        source: {
            type: 'geojson',
            data: geojsonResult
        },
        paint: {
            'fill-color': '#ff6b6b', // Red for predicted flood
            'fill-opacity': 0.6
        }
    });
}
```

## 🚨 Error Handling

### Graceful Degradation
1. **ML Service Down**: Automatically uses fallback forecasting
2. **Database Issues**: Caches predictions locally
3. **Model Loading Errors**: Falls back to statistical methods
4. **Network Issues**: Returns cached data with timestamps

### Monitoring
- **Health Checks**: Regular service availability checks
- **Error Logging**: Comprehensive error tracking
- **Performance Metrics**: Response time monitoring
- **Alert System**: Automatic notification of issues

## 🔧 Troubleshooting

### Common Issues

1. **"Forecasting service not responding"**
   ```bash
   # Check if Python service is running
   curl http://localhost:5000/health
   
   # Restart if needed
   cd forecasting
   python run_dev_server.py
   ```

2. **"ML models not found"**
   ```bash
   # Check model files exist
   ls forecasting/models/
   ls forecasting/scalers/
   ```

3. **"Database connection failed"**
   ```bash
   # Check database credentials in forecasting/.env
   # Ensure MySQL is running and accessible
   ```

### Debug Commands

```bash
# Test Laravel forecasting integration
curl -X GET "http://localhost:8000/api/forecasting/health"

# Test Python service directly
curl -X POST "http://localhost:5000/api/forecast/run" \
  -H "Content-Type: application/json" \
  -d '{"sensor_code": "SENSOR_001", "prediction_hours": 6}'

# Check logs
tail -f forecasting/logs/forecasting.log
tail -f backend/storage/logs/laravel.log
```

## 🎉 Success Indicators

When everything is working correctly, you should see:

1. **Python Service**: `✅ Database Connection: OK`, `✅ ML Models: All models available`
2. **Laravel Backend**: Forecasting health endpoint returns `"status": "healthy"`
3. **Frontend**: Predictions load without errors, maps update automatically
4. **Database**: New records appear in `predicted_calculated_discharges` table

The FFWS Forecasting System is now fully operational and integrated! 🌊🚀