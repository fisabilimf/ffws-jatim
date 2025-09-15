# FFWS Forecasting Service

A Flask-based forecasting service that uses Keras deep learning models (GRU/LSTM/TCN) to predict time series data for flood early warning systems. The service processes sensor data from river basins and generates forecasts with confidence scores and threshold classifications.

## Features

- **Multi-Model Support**: GRU, LSTM, and TCN models
- **Basin-wide Forecasting**: Process all sensors in a river basin simultaneously
- **Data Preprocessing**: Automatic outlier detection, missing data imputation, and data resampling
- **Threshold Classification**: Automatic classification of prediction results (Normal, Alert, Warning, Danger)
- **Confidence Scoring**: Statistical confidence measures for predictions
- **RESTful API**: Easy integration with web applications and other services

## Technology Stack

- **Backend**: Flask 2.3+
- **ML Framework**: TensorFlow 2.13+ with Keras
- **Database**: MySQL with SQLAlchemy 2.0+
- **Data Processing**: NumPy, Pandas, Scikit-learn
- **Production Server**: Gunicorn

---

## Quick Start

### Prerequisites

- Python 3.10 or 3.11 (recommended)
- MySQL Server 8.0+
- Git

### Installation

1. **Clone and Navigate**:
   ```bash
   cd forecasting
   ```

2. **Create Virtual Environment**:
   ```bash
   python -m venv venv
   
   # Windows
   venv\Scripts\activate
   
   # Linux/Mac
   source venv/bin/activate
   ```

3. **Install Dependencies**:
   ```bash
   pip install -r requirements.txt
   ```

4. **Environment Configuration**:
   ```bash
   cp .env.example .env
   ```
   
   Edit `.env` file with your configuration:
   ```bash
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_USER=root
   DB_PASS=your_password
   DB_NAME=ffws_jatim
   MODELS_BASE_DIR=./models
   FLASK_ENV=development
   ```

5. **Database Setup**:
   - Ensure your MySQL server is running
   - Create the database: `CREATE DATABASE ffws_jatim;`
   - The application will use existing tables from your Laravel backend

### Running the Application

#### Development Mode
```bash
python run_dev_server.py
```
The server will start at `http://localhost:5000`

#### Production Mode
```bash
gunicorn -w 4 -b 0.0.0.0:8000 wsgi:app
```

---

## API Documentation

### Base URL
- Development: `http://localhost:5000`
- Production: `http://your-server:8000`

### Authentication
Currently, the API doesn't require authentication, but you can easily integrate it with your existing Laravel backend authentication system.

### Endpoints

#### Health Check
- **GET** `/health`
  - **Description**: Check if the service is running
  - **Response**:
    ```json
    {
      "status": "healthy",
      "service": "FFWS Forecasting API",
      "timestamp": "2025-09-11T10:30:00Z",
      "database": "connected"
    }
    ```

#### Model Management
- **GET** `/api/models`
  - **Description**: Get list of available ML models
  - **Response**:
    ```json
    [
      {
        "id": 1,
        "model_name": "GRU_v1",
        "model_type": "GRU",
        "file_path": "models/purwodadi_gru.h5",
        "n_steps_in": 24,
        "n_steps_out": 12,
        "is_active": true
      }
    ]
    ```

#### Sensor Management
- **GET** `/api/sensors`
  - **Description**: Get list of available sensors
  - **Response**:
    ```json
    [
      {
        "id": 101,
        "device_name": "Sensor Purwodadi",
        "sensor_type": "water_level",
        "river_basin_id": 1,
        "is_active": true,
        "model_id": 5
      }
    ]
    ```

- **GET** `/api/river-basins`
  - **Description**: Get list of river basins
  - **Response**:
    ```json
    [
      {
        "id": 1,
        "basin_name": "DAS Bengawan Solo",
        "description": "River basin coverage area",
        "sensor_count": 15
      }
    ]
    ```

#### Forecasting

##### Single Sensor Forecast
- **POST** `/api/forecast/run`
  - **Description**: Generate forecast for a single sensor
  - **Request Body**:
    ```json
    {
      "sensor_id": 101,
      "model_id": 5  // optional, uses sensor's default model if not specified
    }
    ```
  - **Response**:
    ```json
    {
      "success": true,
      "sensor_id": 101,
      "model_used": "GRU_v1",
      "forecast_time": "2025-09-11T10:30:00Z",
      "predictions": [
        {
          "timestamp": "2025-09-11T11:00:00Z",
          "value": 2.45,
          "confidence_score": 0.87,
          "threshold_status": "normal"
        }
      ],
      "metadata": {
        "n_historical_points": 24,
        "preprocessing_applied": ["outlier_clipping", "interpolation"],
        "prediction_horizon_hours": 12
      }
    }
    ```

##### Basin-wide Forecast
- **POST** `/api/forecast/run-basin`
  - **Description**: Generate forecasts for all sensors in a river basin
  - **Request Body**:
    ```json
    {
      "river_basin_id": 1,
      "only_active": true  // optional, default: true
    }
    ```
  - **Response**:
    ```json
    {
      "success": true,
      "river_basin_id": 1,
      "basin_name": "DAS Bengawan Solo",
      "forecast_time": "2025-09-11T10:30:00Z",
      "results": [
        {
          "sensor_id": 101,
          "success": true,
          "predictions": [...],
          "metadata": {...}
        },
        {
          "sensor_id": 102,
          "success": false,
          "error": "Insufficient historical data"
        }
      ],
      "summary": {
        "total_sensors": 15,
        "successful": 13,
        "failed": 2,
        "processing_time_seconds": 45.2
      }
    }
    ```

---

## System Architecture

### Data Flow
1. **Input**: Historical sensor data from MySQL database (`data_actual` table)
2. **Preprocessing**: Data cleaning, outlier detection, missing value imputation
3. **Scaling**: Normalization using pre-trained scalers from `mas_scalers` table
4. **Prediction**: Deep learning model inference using TensorFlow/Keras
5. **Post-processing**: Inverse scaling, confidence scoring, threshold classification
6. **Output**: Predictions stored in `data_prediction` table and returned via API

### Model Types
- **GRU (Gated Recurrent Unit)**: Good balance of performance and speed
- **LSTM (Long Short-Term Memory)**: Better for complex temporal patterns
- **TCN (Temporal Convolutional Network)**: Excellent for parallel processing

### Database Schema
The service expects the following tables from your Laravel backend:
- `mas_models`: ML model configurations
- `mas_sensors`: Sensor definitions and configurations
- `mas_devices`: Device information linked to sensors
- `mas_river_basins`: River basin definitions
- `data_actual`: Historical sensor readings
- `data_prediction`: Generated forecast results
- `mas_scalers`: Data normalization scalers

---

## Workflow

### Single Sensor Prediction
1. Validate sensor ID and retrieve sensor configuration
2. Load the associated ML model and scalers
3. Fetch historical data from `data_actual` table
4. Apply preprocessing (sorting, outlier clipping, interpolation)
5. Transform data using X scaler
6. Generate predictions using the ML model
7. Apply inverse transformation using Y scaler
8. Calculate confidence scores and threshold classifications
9. Save results to `data_prediction` table
10. Return formatted response

### Basin-wide Prediction
1. Retrieve all active sensors in the specified river basin
2. Process each sensor sequentially (can be parallelized in future versions)
3. Handle individual sensor failures gracefully
4. Aggregate results and provide summary statistics
5. Return comprehensive basin forecast report

---

## Configuration

### Environment Variables
- `DB_HOST`: MySQL server hostname
- `DB_PORT`: MySQL server port (default: 3306)
- `DB_USER`: Database username
- `DB_PASS`: Database password
- `DB_NAME`: Database name
- `MODELS_BASE_DIR`: Base directory for model and scaler files
- `FLASK_ENV`: Environment mode (development/production)
- `DB_POOL_SIZE`: Connection pool size (default: 10)
- `DB_POOL_RECYCLE`: Connection recycle time in seconds (default: 1800)

### Model Configuration
Models are configured in the `mas_models` table with:
- `model_name`: Descriptive name
- `model_type`: GRU, LSTM, or TCN
- `file_path`: Path to the .h5 model file
- `n_steps_in`: Number of historical time steps required
- `n_steps_out`: Number of future time steps to predict
- `is_active`: Whether the model is available for use

---

## Error Handling

The service includes comprehensive error handling for:
- **Database Connection Issues**: Automatic retry with exponential backoff
- **Model Loading Errors**: Clear error messages for missing or corrupted models
- **Insufficient Data**: Graceful handling when historical data is inadequate
- **Preprocessing Failures**: Fallback mechanisms for data quality issues
- **Prediction Errors**: Model inference error handling and logging

### Common Error Responses
```json
{
  "success": false,
  "error": "Insufficient historical data",
  "details": "Need at least 24 data points, found 15",
  "sensor_id": 101
}
```

---

## Performance Optimization

### Recommendations
- Use connection pooling for database access
- Implement model caching to avoid repeated loading
- Consider using Redis for caching frequently accessed data
- Use Gunicorn with multiple workers for production deployment
- Monitor memory usage, especially with large models

### Monitoring
- All predictions are logged with timestamps
- Database query performance is tracked
- Model inference times are recorded
- Failed predictions are logged with error details

---

## Development

### Project Structure
```
forecasting/
├── app/
│   ├── __init__.py          # Flask app factory
│   ├── config.py            # Configuration management
│   ├── db.py               # Database connection handling
│   ├── models.py           # SQLAlchemy models
│   ├── routes.py           # API endpoints
│   ├── forecast.py         # Core forecasting logic
│   ├── preprocess.py       # Data preprocessing
│   ├── loaders.py          # Model loading utilities
│   ├── scalers.py          # Data scaling utilities
│   ├── thresholds.py       # Threshold classification
│   ├── confidence.py       # Confidence scoring
│   └── utils.py            # Utility functions
├── models/                  # Pre-trained ML models (.h5 files)
├── scalers/                # Data scalers (.pkl files)
├── requirements.txt        # Python dependencies
├── run_dev_server.py      # Development server
├── wsgi.py                # Production WSGI entry point
└── .env                   # Environment configuration
```

### Testing
```bash
# Run all tests
pytest

# Run with coverage
pytest --cov=app

# Run specific test file
pytest tests/test_forecast.py
```

### Contributing
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Ensure all tests pass
6. Submit a pull request

---

## Deployment

### Docker Deployment
```dockerfile
FROM python:3.11-slim

WORKDIR /app
COPY requirements.txt .
RUN pip install -r requirements.txt

COPY . .
EXPOSE 8000

CMD ["gunicorn", "-w", "4", "-b", "0.0.0.0:8000", "wsgi:app"]
```

### Systemd Service
```ini
[Unit]
Description=FFWS Forecasting Service
After=network.target

[Service]
Type=exec
User=www-data
Group=www-data
WorkingDirectory=/opt/ffws-forecasting
Environment=PATH=/opt/ffws-forecasting/venv/bin
ExecStart=/opt/ffws-forecasting/venv/bin/gunicorn -w 4 -b 0.0.0.0:8000 wsgi:app
Restart=always

[Install]
WantedBy=multi-user.target
```

---

## Troubleshooting

### Common Issues

1. **ModuleNotFoundError: No module named 'tcn'**
   ```bash
   pip install keras-tcn
   ```

2. **Database Connection Errors**
   - Verify MySQL server is running
   - Check database credentials in `.env` file
   - Ensure database exists and tables are created

3. **Model Loading Errors**
   - Verify model files exist in the specified path
   - Check file permissions
   - Ensure model files are not corrupted

4. **Memory Issues**
   - Reduce the number of Gunicorn workers
   - Implement model caching strategies
   - Monitor system resources

### Logging
The application uses Python's built-in logging. Check logs for detailed error information:
```bash
tail -f /var/log/ffws-forecasting.log
```

---

## License

This project is part of the FFWS (Flood Forecasting and Warning System) for East Java and is developed for educational and research purposes.

## Support

For technical support and questions:
- Create an issue in the repository
- Contact the development team
- Check the documentation for common solutions

---

**Note**: This service is designed to work alongside the Laravel backend system and requires proper database setup and model training before use.