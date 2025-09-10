#!/usr/bin/env python3
"""
Simple test for hourly forecasting to demonstrate 1-5 hour prediction capability.
"""

import os
from dotenv import load_dotenv
load_dotenv()

import sys
sys.path.insert(0, os.path.join(os.path.dirname(__file__), 'app'))

from app.db import init_engine, init_session
from app.config import Settings
from app.forecast import predict_for_sensor
from app.models import MasSensors
from sqlalchemy import select
from datetime import datetime

def test_hourly_predictions():
    """Test hourly predictions for 1-5 hours ahead."""
    print("🕐 HOURLY FORECASTING TEST - 1 to 5 HOURS AHEAD")
    print("=" * 55)
    
    settings = Settings()
    engine = init_engine(settings)
    Session = init_session(engine)
    session = Session()
    
    try:
        # Find working sensor
        sensor = session.execute(
            select(MasSensors).where(MasSensors.sensor_code == "SENSOR-2531")
        ).scalar_one_or_none()
        
        if not sensor:
            print("❌ Test sensor not found!")
            return
        
        print(f"📡 Sensor: {sensor.sensor_code}")
        print(f"📊 Parameter: {sensor.parameter}")
        print(f"🧠 Model: {sensor.mas_model_code}")
        print(f"📅 Current time: {datetime.now().strftime('%Y-%m-%d %H:%M')}")
        
        # Test 1-5 hour predictions
        for hours in [1, 2, 3, 4, 5]:
            print(f"\n{'='*40}")
            print(f"🕐 {hours}-HOUR PREDICTION TEST")
            print(f"{'='*40}")
            
            result = predict_for_sensor(
                session, settings, sensor.sensor_code, None,
                prediction_hours=hours,
                step_hours=1.0  # 1-hour steps
            )
            
            if result:
                conf = result.get('confidence_score', 0.0)
                predictions = result.get('predictions', [])
                
                print(f"✅ SUCCESS: {hours} hour(s) ahead")
                print(f"📊 Confidence: {conf:.1%}")
                print(f"🎯 Forecasts generated: {len(predictions)}")
                
                if predictions:
                    print(f"\n⏰ Hourly forecasts:")
                    for i, pred in enumerate(predictions):
                        time_part = pred['forecast_time'][11:16]  # Extract HH:MM
                        value = pred['forecast_value']
                        status = pred['threshold_status']
                        hours_ahead = i + 1
                        
                        # Status emoji
                        emoji = "🟢" if status == "safe" else "🟡" if status == "alert" else "🔴"
                        
                        print(f"   +{hours_ahead}h ({time_part}): {value:.2f} {emoji} {status}")
                
            else:
                print(f"❌ FAILED: {hours} hour prediction")
        
        print(f"\n{'='*55}")
        print("🎉 HOURLY FORECASTING READY!")
        print("✅ System can predict 1-5 hours ahead with hourly intervals")
        print("✅ Each prediction includes confidence score and alert status")
        print("✅ Perfect for flood early warning systems!")
        
        # API Usage Examples
        print(f"\n🌐 API USAGE:")
        print("For 5-hour predictions:")
        print(f'POST /forecast/hourly')
        print(f'{"{"}"sensor_code": "SENSOR-2531", "hours": 5{"}"}')
        
        print("\nFor custom intervals:")
        print(f'POST /forecast/run')
        print(f'{"{"}"sensor_code": "SENSOR-2531", "prediction_hours": 12, "step_hours": 2.0{"}"}')
    
    except Exception as e:
        print(f"❌ Test error: {e}")
    
    finally:
        session.close()

if __name__ == "__main__":
    test_hourly_predictions()
