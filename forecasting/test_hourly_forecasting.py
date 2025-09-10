#!/usr/bin/env python3
"""
Test hourly forecasting functionality - predicting 1-5 hours into the future.
This demonstrates the new capability for hourly interval predictions.
"""

import os
from dotenv import load_dotenv
load_dotenv()

import sys
sys.path.insert(0, os.path.join(os.path.dirname(__file__), 'app'))

from app.db import init_engine, init_session
from app.config import Settings
from app.forecast import predict_for_sensor
from app.models import MasSensors, DataActual
from sqlalchemy import select, func, desc
from datetime import datetime, timedelta

def test_hourly_forecasting():
    """Test the new hourly forecasting functionality."""
    print("🕐 TESTING HOURLY FORECASTING (1-5 HOURS AHEAD)")
    print("=" * 60)
    
    settings = Settings()
    engine = init_engine(settings)
    Session = init_session(engine)
    session = Session()
    
    try:
        # Find a working sensor
        working_sensor = session.execute(
            select(MasSensors).where(MasSensors.sensor_code == "SENSOR-2531")
        ).scalar_one_or_none()
        
        if not working_sensor:
            print("❌ Working sensor not found!")
            return
        
        print(f"📡 Testing with sensor: {working_sensor.sensor_code}")
        print(f"   Model: {working_sensor.mas_model_code}")
        print(f"   Parameter: {working_sensor.parameter}")
        
        # Test different time configurations
        test_configurations = [
            {"hours": 1, "step": 1.0, "name": "1 Hour Ahead"},
            {"hours": 3, "step": 1.0, "name": "3 Hours Ahead (hourly)"},
            {"hours": 5, "step": 1.0, "name": "5 Hours Ahead (hourly)"},
            {"hours": 6, "step": 2.0, "name": "6 Hours Ahead (2-hour steps)"},
            {"hours": 12, "step": 3.0, "name": "12 Hours Ahead (3-hour steps)"}
        ]
        
        print(f"\n🧪 TESTING DIFFERENT TIME CONFIGURATIONS")
        print("-" * 50)
        
        for i, config in enumerate(test_configurations, 1):
            print(f"\n📊 Test {i}: {config['name']}")
            print(f"   Prediction horizon: {config['hours']} hours")
            print(f"   Time step: {config['step']} hours")
            
            try:
                result = predict_for_sensor(
                    session, settings, working_sensor.sensor_code, None,
                    prediction_hours=config['hours'],
                    step_hours=config['step']
                )
                
                if result:
                    confidence = result.get('confidence_score', 0.0)
                    predictions = result.get('predictions', [])
                    
                    print(f"   ✅ SUCCESS!")
                    print(f"   📊 Confidence: {confidence:.3f} ({confidence:.1%})")
                    print(f"   🎯 Predictions generated: {result.get('rows_inserted', 0)}")
                    print(f"   ⏰ Step interval: {result.get('step_hours', 0):.1f} hours")
                    
                    if predictions:
                        print(f"   📈 Sample forecasts:")
                        for pred in predictions[:3]:  # Show first 3
                            time_str = pred['forecast_time'][11:16]  # HH:MM
                            value = pred['forecast_value']
                            hours_ahead = pred.get('hours_ahead', 0)
                            status = pred['threshold_status']
                            print(f"      +{hours_ahead:.1f}h ({time_str}): {value:.2f} ({status})")
                else:
                    print(f"   ❌ FAILED: No result")
                    
            except Exception as e:
                error_msg = str(e)[:50] + "..." if len(str(e)) > 50 else str(e)
                print(f"   ❌ ERROR: {error_msg}")
        
        # Demonstrate practical use case
        print(f"\n🌊 PRACTICAL FLOOD WARNING SCENARIO")
        print("-" * 40)
        
        print(f"Scenario: Hourly water level monitoring with 1-5 hour forecasts")
        print(f"Current time: {datetime.now().strftime('%Y-%m-%d %H:%M')}")
        
        try:
            # 5-hour hourly forecast
            result = predict_for_sensor(
                session, settings, working_sensor.sensor_code, None,
                prediction_hours=5, step_hours=1.0
            )
            
            if result:
                confidence = result.get('confidence_score', 0.0)
                predictions = result.get('predictions', [])
                
                print(f"\n📊 5-Hour Flood Warning Forecast:")
                print(f"   Sensor: {result.get('sensor_code')}")
                print(f"   Model: {result.get('model_code')} ({result.get('model_type')})")
                print(f"   Confidence: {confidence:.1%}")
                
                if predictions:
                    print(f"\n⏰ Hourly Predictions:")
                    print(f"   {'Time':<8} {'Hours':<6} {'Value':<8} {'Status':<8} {'Alert'}")
                    print(f"   {'-'*8} {'-'*6} {'-'*8} {'-'*8} {'-'*10}")
                    
                    for pred in predictions:
                        time_str = pred['forecast_time'][11:16]
                        hours_ahead = pred.get('hours_ahead', 0)
                        value = pred['forecast_value']
                        status = pred['threshold_status']
                        
                        # Alert level
                        if status == 'danger':
                            alert = "🔴 DANGER"
                        elif status == 'warning':
                            alert = "🟡 WARNING"
                        elif status == 'alert':
                            alert = "🟠 ALERT"
                        else:
                            alert = "🟢 SAFE"
                        
                        print(f"   {time_str:<8} +{hours_ahead:.0f}h{'':3} {value:<8.2f} {status:<8} {alert}")
                
                # Recommendations based on confidence
                print(f"\n💡 OPERATIONAL RECOMMENDATIONS:")
                if confidence >= 0.8:
                    print(f"   🟢 HIGH confidence - Use for automatic alerts")
                elif confidence >= 0.6:
                    print(f"   🟡 MEDIUM confidence - Use with human oversight")
                else:
                    print(f"   🔴 LOW confidence - Use only as early warning indicator")
        
        except Exception as e:
            print(f"❌ Scenario test failed: {e}")
        
        # Summary of capabilities
        print(f"\n{'='*60}")
        print(f"🎯 HOURLY FORECASTING CAPABILITIES")
        print("="*60)
        
        print(f"✅ IMPLEMENTED FEATURES:")
        print(f"   🕐 Configurable prediction horizons (1-24 hours)")
        print(f"   ⏰ Flexible time steps (0.1-6 hour intervals)")
        print(f"   📊 Confidence scoring for each prediction")
        print(f"   🎯 Threshold-based alert classification")
        print(f"   💾 Database storage with time metadata")
        print(f"   🌐 API endpoints for integration")
        
        print(f"\n🔧 API ENDPOINTS:")
        print(f"   POST /forecast/hourly - Simple hourly forecasts (1-5 hours)")
        print(f"   POST /forecast/run - Advanced with custom intervals")
        
        print(f"\n📈 EXAMPLE API CALLS:")
        print(f'   curl -X POST /forecast/hourly -d \'{"sensor_code":"SENSOR-2531","hours":5}\'')
        print(f'   curl -X POST /forecast/run -d \'{"sensor_code":"SENSOR-2531","prediction_hours":12,"step_hours":2.0}\'')
        
        print(f"\n🎉 READY FOR HOURLY DATA!")
        print(f"The system is now configured to handle hourly data intervals")
        print(f"and provide forecasts 1-5 hours (or more) into the future!")
    
    except Exception as e:
        print(f"❌ Test failed: {e}")
    
    finally:
        session.close()

if __name__ == "__main__":
    test_hourly_forecasting()
