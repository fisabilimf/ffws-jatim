#!/usr/bin/env python3
"""
Generate sample historical sensor data for demonstration purposes.
This will create realistic time series data for water level and rainfall sensors.
"""

import os
import sys
from dotenv import load_dotenv
import random
from datetime import datetime, timedelta
import numpy as np

# Add the app directory to Python path
sys.path.insert(0, os.path.join(os.path.dirname(__file__), 'app'))

from app.db import init_engine, init_session
from app.config import Settings
from app.models import MasSensors, DataActual
from sqlalchemy import select

def generate_realistic_data(sensor_type, days=7, interval_minutes=15):
    """Generate realistic sensor data."""
    data_points = []
    start_time = datetime.now() - timedelta(days=days)
    
    for i in range(int(days * 24 * 60 / interval_minutes)):
        timestamp = start_time + timedelta(minutes=i * interval_minutes)
        
        if sensor_type == 'water_level':
            # Generate realistic water level data (0-10 meters)
            # Base level with some variation and occasional peaks
            base_level = 2.0 + 1.5 * np.sin(2 * np.pi * i / (24 * 60 / interval_minutes))  # Daily cycle
            noise = random.uniform(-0.3, 0.3)
            
            # Occasional rain events (higher water levels)
            if random.random() < 0.05:  # 5% chance of rain event
                rain_boost = random.uniform(0.5, 2.0)
            else:
                rain_boost = 0
                
            value = max(0.1, base_level + noise + rain_boost)
            
        else:  # rainfall
            # Generate realistic rainfall data (0-50 mm/hour)
            if random.random() < 0.15:  # 15% chance of rain
                value = random.uniform(0.1, 25.0)
            else:
                value = 0.0
        
        data_points.append((timestamp, round(value, 2)))
    
    return data_points

def create_sample_data():
    """Create sample historical data for all sensors."""
    print("🌊 Generating sample historical data for forecasting...")
    
    load_dotenv()
    settings = Settings()
    engine = init_engine(settings)
    session_factory = init_session(engine)
    
    try:
        with session_factory() as s:
            # Get all sensors
            sensors = s.execute(select(MasSensors)).scalars().all()
            
            total_records = 0
            
            for sensor in sensors:
                print(f"📊 Generating data for {sensor.sensor_code} ({sensor.parameter.value})...")
                
                # Generate 7 days of data with 15-minute intervals
                data_points = generate_realistic_data(
                    sensor.parameter.value, 
                    days=7, 
                    interval_minutes=15
                )
                
                # Insert data into database
                for timestamp, value in data_points:
                    sensor_data = DataActual(
                        mas_sensor_code=sensor.sensor_code,
                        value=value,
                        received_at=timestamp,
                        threshold_status='safe',  # Default status
                        created_at=datetime.now(),
                        updated_at=datetime.now()
                    )
                    s.add(sensor_data)
                
                print(f"  ✅ Generated {len(data_points)} data points")
                total_records += len(data_points)
            
            s.commit()
            print(f"\n✅ Successfully generated {total_records} historical data records!")
            print(f"   Data spans 7 days with 15-minute intervals")
            print(f"   Each sensor now has sufficient data for forecasting")
            
            return True
            
    except Exception as e:
        print(f"❌ Error generating sample data: {e}")
        import traceback
        traceback.print_exc()
        return False

def test_forecasting_with_data():
    """Test forecasting now that we have sufficient data."""
    print("\n🚀 Testing forecasting with generated data...")
    
    load_dotenv()
    settings = Settings()
    engine = init_engine(settings)
    session_factory = init_session(engine)
    
    try:
        from app import create_app
        from app.forecast import predict_for_sensor, ForecastError
        
        app = create_app()
        
        with app.app_context():
            with session_factory() as s:
                # Get a few sensors to test
                test_sensors = s.execute(
                    select(MasSensors).limit(5)
                ).scalars().all()
                
                successful_forecasts = 0
                
                for sensor in test_sensors:
                    print(f"\n📈 Testing forecast for {sensor.sensor_code}...")
                    print(f"   Parameter: {sensor.parameter.value}")
                    print(f"   Model: {sensor.mas_model_code}")
                    
                    try:
                        result = predict_for_sensor(s, settings, sensor.sensor_code)
                        
                        print(f"   ✅ Forecast successful!")
                        print(f"      Model type: {result.get('model_type')}")
                        print(f"      Algorithm: {result.get('model_algorithm')}")
                        print(f"      Forecast steps: {result.get('step_minutes')} minutes ahead")
                        print(f"      Predictions generated: {result.get('rows_inserted')} rows")
                        
                        # Show sample predictions if available
                        if 'predictions' in result:
                            predictions = result['predictions'][:3]  # First 3 predictions
                            print("      Sample predictions:")
                            for pred in predictions:
                                print(f"        {pred['forecast_time']}: {pred['forecast_value']:.2f}")
                        
                        successful_forecasts += 1
                        
                    except ForecastError as e:
                        print(f"   ⚠️  Forecast error: {e}")
                    
                    except Exception as e:
                        print(f"   ❌ Unexpected error: {e}")
                        import traceback
                        traceback.print_exc()
                
                print(f"\n📊 Forecast Test Results:")
                print(f"   Sensors tested: {len(test_sensors)}")
                print(f"   Successful forecasts: {successful_forecasts}")
                print(f"   Success rate: {successful_forecasts/len(test_sensors)*100:.1f}%")
                
                return successful_forecasts > 0
                
    except Exception as e:
        print(f"❌ Error testing forecasts: {e}")
        import traceback
        traceback.print_exc()
        return False

if __name__ == "__main__":
    print("🎯 FFWS Sample Data Generator & Forecast Tester")
    print("=" * 60)
    
    # Step 1: Generate sample data
    data_success = create_sample_data()
    
    # Step 2: Test forecasting with the new data
    if data_success:
        forecast_success = test_forecasting_with_data()
        
        if forecast_success:
            print("\n" + "=" * 60)
            print("🎉 SUCCESS! Forecasting system is working with sample data!")
            print("\n🚀 Ready to use:")
            print("1. Start Flask server: python wsgi.py")
            print("2. API endpoints available at http://localhost:5000")
            print("\n📡 Test API calls:")
            print("   • GET /models - List all available models")
            print("   • GET /sensors - List all sensors with assigned models") 
            print("   • POST /forecast/run - Run forecast for specific sensor")
            print("   • POST /forecast/run-basin - Run forecast for entire basin")
            print("\n📊 Sample forecast request:")
            print("   POST http://localhost:5000/forecast/run")
            print("   {'sensor_code': 'SENSOR-4060'}")
        else:
            print("\n⚠️  Data generated but forecasting tests failed")
            print("Check the error messages above for troubleshooting")
    else:
        print("\n❌ Failed to generate sample data")
