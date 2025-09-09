#!/usr/bin/env python3
"""
Script to assign models to sensors and run forecast tests with existing data.
"""

import os
import sys
from dotenv import load_dotenv

# Add the app directory to Python path
sys.path.insert(0, os.path.join(os.path.dirname(__file__), 'app'))

from app.db import init_engine, init_session
from app.config import Settings
from app.models import MasSensors, MasModels, MasRiverBasins
from sqlalchemy import select, update
from app.forecast import predict_for_sensor, ForecastError

def assign_models_to_sensors():
    """Assign models to sensors based on naming convention."""
    print("🔧 Assigning models to sensors...")
    
    load_dotenv()
    settings = Settings()
    engine = init_engine(settings)
    session_factory = init_session(engine)
    
    try:
        with session_factory() as s:
            # Get all sensors and models
            sensors = s.execute(select(MasSensors)).scalars().all()
            models = s.execute(select(MasModels)).scalars().all()
            
            print(f"Found {len(sensors)} sensors and {len(models)} models")
            
            # Create model mapping
            model_map = {model.model_code: model for model in models}
            
            assignments = 0
            
            # Assign models to sensors based on location patterns
            for sensor in sensors:
                if sensor.mas_model_code:  # Already has a model
                    continue
                
                # Try to match sensor location with model
                sensor_code = sensor.sensor_code.upper()
                
                # Simple assignment logic - you can customize this
                if 'DHOMPO' in sensor_code or sensor.id % 2 == 0:
                    # Assign DHOMPO models to even-ID sensors or DHOMPO named sensors
                    if sensor.parameter.value == 'water_level':
                        assigned_model = 'DHOMPO_LSTM'
                    else:  # rainfall
                        assigned_model = 'DHOMPO_GRU'
                else:
                    # Assign PURWODADI models to odd-ID sensors
                    if sensor.parameter.value == 'water_level':
                        assigned_model = 'PURWODADI_LSTM'
                    else:  # rainfall
                        assigned_model = 'PURWODADI_GRU'
                
                if assigned_model in model_map:
                    # Update sensor with model assignment
                    s.execute(
                        update(MasSensors)
                        .where(MasSensors.id == sensor.id)
                        .values(mas_model_code=assigned_model)
                    )
                    print(f"  ✅ Assigned {assigned_model} to {sensor.sensor_code}")
                    assignments += 1
            
            s.commit()
            print(f"\n✅ Successfully assigned models to {assignments} sensors!")
            return True
            
    except Exception as e:
        print(f"❌ Error assigning models: {e}")
        return False

def test_forecast_with_assigned_models():
    """Test forecasting with the newly assigned models."""
    print("\n🚀 Testing forecasts with assigned models...")
    
    load_dotenv()
    settings = Settings()
    engine = init_engine(settings)
    session_factory = init_session(engine)
    
    try:
        from app import create_app
        app = create_app()
        
        with app.app_context():
            with session_factory() as s:
                # Get sensors with assigned models
                sensors_with_models = s.execute(
                    select(MasSensors).where(MasSensors.mas_model_code.isnot(None)).limit(5)
                ).scalars().all()
                
                if not sensors_with_models:
                    print("❌ No sensors with assigned models found")
                    return False
                
                successful_forecasts = 0
                
                for sensor in sensors_with_models:
                    print(f"\n📊 Testing forecast for sensor: {sensor.sensor_code}")
                    print(f"   Parameter: {sensor.parameter.value}")
                    print(f"   Assigned model: {sensor.mas_model_code}")
                    
                    try:
                        result = predict_for_sensor(s, settings, sensor.sensor_code)
                        
                        print(f"   ✅ Forecast successful!")
                        print(f"      Model type: {result.get('model_type')}")
                        print(f"      Step minutes: {result.get('step_minutes')}")
                        print(f"      Predictions: {result.get('rows_inserted')} rows")
                        
                        successful_forecasts += 1
                        
                    except ForecastError as e:
                        print(f"   ⚠️  Forecast error: {e}")
                        print("      This is expected if no historical data exists for training")
                    
                    except Exception as e:
                        print(f"   ❌ Unexpected error: {e}")
                
                print(f"\n📈 Forecast Summary:")
                print(f"   Tested sensors: {len(sensors_with_models)}")
                print(f"   Successful forecasts: {successful_forecasts}")
                print(f"   Success rate: {successful_forecasts/len(sensors_with_models)*100:.1f}%")
                
                return successful_forecasts > 0
                
    except Exception as e:
        print(f"❌ Error running forecast tests: {e}")
        import traceback
        traceback.print_exc()
        return False

def show_system_status():
    """Show current system status with models and sensors."""
    print("\n📋 Current System Status:")
    print("=" * 60)
    
    load_dotenv()
    settings = Settings()
    engine = init_engine(settings)
    session_factory = init_session(engine)
    
    try:
        with session_factory() as s:
            # Show models
            models = s.execute(select(MasModels)).scalars().all()
            print(f"\n🤖 Available Models ({len(models)}):")
            for model in models:
                print(f"  • {model.model_code} - {model.name} ({model.model_type})")
            
            # Show river basins
            basins = s.execute(select(MasRiverBasins)).scalars().all()
            print(f"\n🏞️  River Basins ({len(basins)}):")
            for basin in basins:
                print(f"  • {basin.code} - {basin.name}")
            
            # Show sensors with/without models
            sensors = s.execute(select(MasSensors)).scalars().all()
            sensors_with_models = [s for s in sensors if s.mas_model_code]
            sensors_without_models = [s for s in sensors if not s.mas_model_code]
            
            print(f"\n📡 Sensors Status:")
            print(f"  Total sensors: {len(sensors)}")
            print(f"  With assigned models: {len(sensors_with_models)}")
            print(f"  Without models: {len(sensors_without_models)}")
            
            if sensors_with_models:
                print(f"\n✅ Sensors ready for forecasting:")
                for sensor in sensors_with_models[:10]:  # Show first 10
                    print(f"  • {sensor.sensor_code} ({sensor.parameter.value}) → {sensor.mas_model_code}")
                if len(sensors_with_models) > 10:
                    print(f"  ... and {len(sensors_with_models) - 10} more")
            
    except Exception as e:
        print(f"❌ Error getting system status: {e}")

if __name__ == "__main__":
    print("🌊 FFWS Forecasting System - Model Assignment & Testing")
    print("=" * 60)
    
    # Step 1: Show current status
    show_system_status()
    
    # Step 2: Assign models to sensors
    assignment_success = assign_models_to_sensors()
    
    # Step 3: Test forecasting
    if assignment_success:
        forecast_success = test_forecast_with_assigned_models()
        
        # Step 4: Show updated status
        show_system_status()
        
        print("\n" + "=" * 60)
        print("🎯 Next Steps:")
        print("1. Start the Flask server:")
        print("   python wsgi.py")
        print("\n2. Test API endpoints:")
        print("   GET  http://localhost:5000/models")
        print("   GET  http://localhost:5000/sensors")
        print("   POST http://localhost:5000/forecast/run")
        print("        {'sensor_code': 'SENSOR-XXXX'}")
        print("\n3. Run basin-wide forecasts:")
        print("   POST http://localhost:5000/forecast/run-basin")
        print("        {'river_basin_code': 'RB001'}")
        
    else:
        print("\n❌ Model assignment failed. Please check the errors above.")
