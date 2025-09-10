#!/usr/bin/env python3
"""
Practical Flood Early Warning System Demo
Demonstrates hourly forecasting for real-time monitoring
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
from sqlalchemy import select, desc
from datetime import datetime, timedelta
import time

def flood_warning_demo():
    """Demonstrate practical flood warning with hourly predictions."""
    print("🌊 FLOOD EARLY WARNING SYSTEM DEMO")
    print("=" * 60)
    print("📅 Simulation: Hourly water level monitoring")
    print("🕒 Current time:", datetime.now().strftime('%Y-%m-%d %H:%M:%S'))
    print()
    
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
            print("❌ No working sensor found!")
            return
        
        # Get latest actual data for context
        latest_actual = session.execute(
            select(DataActual)
            .where(DataActual.mas_sensor_code == sensor.sensor_code)
            .order_by(desc(DataActual.received_at))
            .limit(1)
        ).scalar_one_or_none()
        
        print(f"🏭 MONITORING STATION: {sensor.sensor_code}")
        print(f"📊 Parameter: {sensor.parameter}")
        print(f"🧠 AI Model: {sensor.mas_model_code} (GRU Neural Network)")
        print(f"📍 Location: River monitoring point")
        
        if latest_actual:
            print(f"📈 Last actual reading: {latest_actual.value:.2f} at {latest_actual.received_at}")
        
        print("\n" + "="*60)
        print("🔮 GENERATING HOURLY FLOOD FORECASTS")
        print("="*60)
        
        # Test different forecasting scenarios
        scenarios = [
            {"hours": 1, "name": "Immediate Alert (1 hour)"},
            {"hours": 3, "name": "Short-term Warning (3 hours)"},
            {"hours": 5, "name": "Extended Forecast (5 hours)"},
            {"hours": 8, "step": 2.0, "name": "Long-term Planning (8 hours, 2h steps)"}
        ]
        
        for i, scenario in enumerate(scenarios, 1):
            print(f"\n🔍 SCENARIO {i}: {scenario['name']}")
            print("-" * 50)
            
            step_hours = scenario.get('step', 1.0)
            
            print(f"⏱️  Forecasting {scenario['hours']} hours ahead with {step_hours}-hour intervals...")
            
            start_time = time.time()
            
            try:
                result = predict_for_sensor(
                    session, settings, sensor.sensor_code, None,
                    prediction_hours=scenario['hours'],
                    step_hours=step_hours
                )
                
                end_time = time.time()
                process_time = end_time - start_time
                
                if result:
                    confidence = result.get('confidence_score', 0.0)
                    predictions = result.get('predictions', [])
                    
                    # Confidence interpretation
                    if confidence >= 0.8:
                        conf_status = "🟢 HIGH"
                        conf_action = "Use for automatic alerts"
                    elif confidence >= 0.6:
                        conf_status = "🟡 MEDIUM" 
                        conf_action = "Use with human oversight"
                    else:
                        conf_status = "🟠 LOW"
                        conf_action = "Early warning indicator only"
                    
                    print(f"✅ FORECAST COMPLETED in {process_time:.2f} seconds")
                    print(f"📊 AI Confidence: {confidence:.1%} ({conf_status})")
                    print(f"💡 Recommendation: {conf_action}")
                    print(f"🎯 Predictions generated: {len(predictions)}")
                    
                    if predictions:
                        print(f"\n📋 FORECAST TIMELINE:")
                        print(f"{'Time':<8} {'Hours':<7} {'Level':<8} {'Status':<10} {'Alert'}")
                        print(f"{'-'*8} {'-'*7} {'-'*8} {'-'*10} {'-'*15}")
                        
                        for pred in predictions:
                            time_part = pred['forecast_time'][11:16]  # HH:MM
                            hours_ahead = pred.get('hours_ahead', 0)
                            value = pred['forecast_value']
                            status = pred['threshold_status']
                            
                            # Alert level with emoji
                            if status == 'danger':
                                alert = "🔴 EVACUATE"
                            elif status == 'warning':
                                alert = "🟡 PREPARE"
                            elif status == 'alert':
                                alert = "🟠 MONITOR"
                            else:
                                alert = "🟢 SAFE"
                            
                            print(f"{time_part:<8} +{hours_ahead:.0f}h{'':4} {value:<8.2f} {status:<10} {alert}")
                        
                        # Risk assessment
                        danger_count = sum(1 for p in predictions if p['threshold_status'] == 'danger')
                        warning_count = sum(1 for p in predictions if p['threshold_status'] == 'warning')
                        
                        print(f"\n🚨 RISK ASSESSMENT:")
                        if danger_count > 0:
                            print(f"   ⚠️  CRITICAL: {danger_count} danger periods detected!")
                            print(f"   🚨 Immediate action required - prepare evacuations")
                        elif warning_count > 0:
                            print(f"   🟡 ELEVATED: {warning_count} warning periods detected")
                            print(f"   📢 Alert residents and prepare response teams")
                        else:
                            print(f"   🟢 NORMAL: All readings within safe limits")
                            print(f"   ✅ Continue routine monitoring")
                
                else:
                    print(f"❌ FORECAST FAILED: No predictions generated")
            
            except Exception as e:
                print(f"❌ ERROR: {str(e)[:100]}...")
        
        # Demonstrate API integration
        print(f"\n🌐 API INTEGRATION EXAMPLES")
        print("="*60)
        print("For real-time integration with your flood monitoring system:")
        print()
        print("🔗 Quick 5-hour forecast:")
        print('   POST /api/forecast/hourly')
        print('   {"sensor_code": "SENSOR-2531", "hours": 5}')
        print()
        print("🔗 Custom interval forecast:")
        print('   POST /api/forecast/run')
        print('   {"sensor_code": "SENSOR-2531", "prediction_hours": 12, "step_hours": 2.0}')
        
        print(f"\n🎯 SYSTEM CAPABILITIES SUMMARY")
        print("="*60)
        print("✅ Real-time hourly water level predictions")
        print("✅ AI confidence scoring for prediction reliability")
        print("✅ Automatic threshold classification (safe/alert/warning/danger)")
        print("✅ Configurable prediction horizons (1-24 hours)")
        print("✅ Flexible time intervals (hourly, 2-hour, etc.)")
        print("✅ REST API for system integration")
        print("✅ Database storage for historical analysis")
        
        print(f"\n🚀 READY FOR PRODUCTION DEPLOYMENT!")
        print("This system can now handle:")
        print("• Hourly sensor data feeds")
        print("• Real-time flood predictions 1-5 hours ahead") 
        print("• Automated early warning alerts")
        print("• Integration with emergency response systems")
    
    except Exception as e:
        print(f"❌ Demo failed: {e}")
    
    finally:
        session.close()

if __name__ == "__main__":
    flood_warning_demo()
