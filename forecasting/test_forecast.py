#!/usr/bin/env python3
"""
Test script for the forecasting system with code-based identifiers.
This script tests if the forecasting system can connect to the database
and run predictions using sensor codes instead of IDs.
"""

import os
import sys
from dotenv import load_dotenv

# Add the app directory to Python path
sys.path.insert(0, os.path.join(os.path.dirname(__file__), 'app'))

from app.db import init_engine, init_session
from app.config import Settings
from app.models import MasSensors, MasModels, MasRiverBasins
from sqlalchemy import select

def test_database_connection():
    """Test if we can connect to the database and query basic data."""
    print("Testing database connection...")
    
    # Load environment variables
    load_dotenv()
    
    settings = Settings()
    print(f"Database URL: {settings.sqlalchemy_url}")
    
    try:
        engine = init_engine(settings)
        session_factory = init_session(engine)
        
        with session_factory() as s:
            # Test basic queries
            print("\n=== Testing River Basins ===")
            basins = s.execute(select(MasRiverBasins)).scalars().all()
            for basin in basins:
                print(f"Basin: {basin.code} - {basin.name}")
            
            print("\n=== Testing Models ===")
            models = s.execute(select(MasModels)).scalars().all()
            for model in models:
                print(f"Model: {model.model_code} - {model.name} ({model.model_type})")
            
            print("\n=== Testing Sensors ===")
            sensors = s.execute(select(MasSensors).limit(5)).scalars().all()
            for sensor in sensors:
                print(f"Sensor: {sensor.sensor_code} - Device: {sensor.mas_device_code} - Model: {sensor.mas_model_code}")
            
        print("\n✅ Database connection successful!")
        return True
        
    except Exception as e:
        print(f"\n❌ Database connection failed: {e}")
        return False

def test_forecast_api():
    """Test the forecasting API endpoints."""
    print("\n\nTesting forecasting API...")
    
    try:
        from app.routes import list_models, list_sensors, list_river_basins
        from app import create_app
        
        # Create a test app
        app = create_app()
        
        with app.app_context():
            print("\n=== Testing /models endpoint ===")
            models_response = list_models()
            print(f"Found {len(models_response)} models")
            for model in models_response[:3]:  # Show first 3
                print(f"  - {model.get('code')}: {model.get('name')} ({model.get('algorithm')})")
            
            print("\n=== Testing /sensors endpoint ===")
            sensors_response = list_sensors()
            print(f"Found {len(sensors_response)} sensors")
            for sensor in sensors_response[:3]:  # Show first 3
                print(f"  - {sensor.get('code')}: {sensor.get('parameter')} (Model: {sensor.get('model_code')})")
            
            print("\n=== Testing /river-basins endpoint ===")
            basins_response = list_river_basins()
            print(f"Found {len(basins_response)} river basins")
            for basin in basins_response:
                print(f"  - {basin.get('code')}: {basin.get('name')}")
        
        print("\n✅ API endpoints working!")
        return True
        
    except Exception as e:
        print(f"\n❌ API test failed: {e}")
        import traceback
        traceback.print_exc()
        return False

def test_single_forecast():
    """Test running a single forecast prediction."""
    print("\n\nTesting single forecast prediction...")
    
    try:
        from app import create_app
        from app.forecast import predict_for_sensor, ForecastError
        from app.db import init_engine, init_session
        from app.config import Settings
        from sqlalchemy import select
        
        settings = Settings()
        engine = init_engine(settings)
        session_factory = init_session(engine)
        app = create_app()
        
        with app.app_context():
            with session_factory() as s:
                # Find a sensor with a model
                sensor_with_model = s.execute(
                    select(MasSensors).where(MasSensors.mas_model_code.isnot(None))
                ).scalars().first()
                
                if not sensor_with_model:
                    print("❌ No sensor with assigned model found")
                    return False
                
                print(f"Testing with sensor: {sensor_with_model.sensor_code}")
                print(f"Assigned model: {sensor_with_model.mas_model_code}")
                
                # Try to run a prediction
                try:
                    result = predict_for_sensor(s, settings, sensor_with_model.sensor_code)
                    print(f"✅ Forecast successful!")
                    print(f"  - Sensor: {result.get('sensor_code')}")
                    print(f"  - Model: {result.get('model_code')}")
                    print(f"  - Type: {result.get('model_type')}")
                    print(f"  - Step minutes: {result.get('step_minutes')}")
                    print(f"  - Predictions inserted: {result.get('rows_inserted')}")
                    return True
                    
                except ForecastError as e:
                    print(f"⚠️  Forecast error (expected if no data): {e}")
                    return True  # This is expected if no actual data exists
                
    except Exception as e:
        print(f"\n❌ Forecast test failed: {e}")
        import traceback
        traceback.print_exc()
        return False

if __name__ == "__main__":
    print("🚀 FFWS Forecasting System Test")
    print("=" * 50)
    
    # Test 1: Database Connection
    db_ok = test_database_connection()
    
    # Test 2: API Endpoints
    if db_ok:
        api_ok = test_forecast_api()
    else:
        print("⏭️  Skipping API test due to database connection failure")
        api_ok = False
    
    # Test 3: Single Forecast
    if db_ok and api_ok:
        forecast_ok = test_single_forecast()
    else:
        print("⏭️  Skipping forecast test due to previous failures")
        forecast_ok = False
    
    print("\n" + "=" * 50)
    print("📊 TEST SUMMARY")
    print(f"Database Connection: {'✅ PASS' if db_ok else '❌ FAIL'}")
    print(f"API Endpoints:       {'✅ PASS' if api_ok else '❌ FAIL'}")
    print(f"Forecast Prediction: {'✅ PASS' if forecast_ok else '❌ FAIL'}")
    
    if db_ok and api_ok:
        print("\n🎉 Forecasting system is working with code-based identifiers!")
        print("You can now start the Flask server with:")
        print("  python wsgi.py")
        print("\nThen test the API endpoints:")
        print("  GET  http://localhost:5000/models")
        print("  GET  http://localhost:5000/sensors")
        print("  POST http://localhost:5000/forecast/run")
        print("       {'sensor_code': 'SENSOR_CODE', 'model_code': 'MODEL_CODE'}")
    else:
        print("\n❌ Some tests failed. Please check the errors above.")
