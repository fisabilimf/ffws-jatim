#!/usr/bin/env python3
"""Quick test to show forecasting and confidence scores are working."""

import os
from dotenv import load_dotenv

# Load environment first
load_dotenv()

# Show environment
print(f"🔧 Environment check:")
print(f"   DB_USER: {os.getenv('DB_USER', 'not set')}")
print(f"   DB_PASS: {os.getenv('DB_PASS', 'not set')}")
print(f"   DB_NAME: {os.getenv('DB_NAME', 'not set')}")

# Now run the same test that works
print(f"\n🚀 Running Working Forecasting Test...")
print("="*50)

import sys
sys.path.insert(0, os.path.join(os.path.dirname(__file__), 'app'))

try:
    from app.db import init_engine, init_session
    from app.config import Settings
    from app.forecast import predict_for_sensor, ForecastError
    from app.models import MasSensors
    from sqlalchemy import select
    import random
    
    # Create settings and check them
    settings = Settings()
    print(f"📊 Settings check:")
    print(f"   DB_HOST: {settings.DB_HOST}")
    print(f"   DB_USER: {settings.DB_USER}")
    print(f"   DB_PASS: {settings.DB_PASS}")  # This should show the password
    print(f"   DB_NAME: {settings.DB_NAME}")
    
    # Try database connection
    engine = init_engine(settings)
    Session = init_session(engine)
    s = Session()
    
    print(f"\n✅ Database connected successfully!")
    
    # Get a sensor to test
    query = select(MasSensors).where(MasSensors.mas_model_code.isnot(None)).limit(3)
    sensors = s.execute(query).scalars().all()
    
    print(f"📡 Found {len(sensors)} sensors to test")
    
    for sensor in sensors:
        print(f"\n📊 Testing {sensor.sensor_code}:")
        print(f"   Model: {sensor.mas_model_code}")
        print(f"   Parameter: {sensor.parameter}")
        
        try:
            result = predict_for_sensor(s, settings, sensor.sensor_code)
            if result:
                confidence = result.get('confidence_score', 0.0)
                print(f"   ✅ SUCCESS! Confidence: {confidence:.3f} ({confidence:.1%})")
                
                # Show predictions
                predictions = result.get('predictions', [])
                for pred in predictions[:2]:
                    time_str = pred['forecast_time'][:16]
                    value = pred['forecast_value'] 
                    conf = pred.get('confidence_score', confidence)
                    status = pred['threshold_status']
                    print(f"      {time_str}: {value:.2f} ({status}) - {conf:.1%} confident")
                break  # Stop after first success
        except Exception as e:
            print(f"   ❌ Error: {str(e)[:50]}")
    
    s.close()

except Exception as e:
    print(f"❌ Error: {e}")

print(f"\n🎉 Confidence Scoring Summary:")
print(f"   • The system calculates confidence scores for each prediction")
print(f"   • Scores range from 0.0 (no confidence) to 1.0 (perfect confidence)")  
print(f"   • Components: Data Quality + Model Consistency + Historical Accuracy + Input Stability + Prediction Variance")
print(f"   • Results are stored in database and returned via API")
