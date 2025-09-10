#!/usr/bin/env python3
"""Test the forecasting system with confidence scores using the existing test framework."""

import os
import sys

# Add the app directory to the path  
sys.path.insert(0, os.path.join(os.path.dirname(__file__), 'app'))

from app.db import init_engine, init_session
from app.config import Settings
from app.forecast import predict_for_sensor
from app.models import MasSensors
from sqlalchemy import select
import random

def load_env():
    """Load environment variables from .env file if present."""
    env_file = os.path.join(os.path.dirname(__file__), '.env')
    if os.path.exists(env_file):
        with open(env_file, 'r') as f:
            for line in f:
                line = line.strip()
                if line and not line.startswith('#') and '=' in line:
                    key, value = line.split('=', 1)
                    os.environ[key] = value

def test_forecasting_with_confidence():
    """Test forecasting with detailed confidence analysis."""
    print("🚀 Testing FFWS Forecasting with Confidence Scores")
    print("=" * 60)
    
    # Load environment
    load_env()
    
    # Initialize
    settings = Settings()
    engine = init_engine(settings)
    Session = init_session(engine)
    session = Session()
    
    try:
        # Get sensors with assigned models
        query = select(MasSensors).where(MasSensors.mas_model_code.isnot(None)).limit(5)
        sensors = session.execute(query).scalars().all()
        
        if not sensors:
            print("❌ No sensors with assigned models found!")
            return
        
        success_count = 0
        total_count = len(sensors)
        
        print(f"📊 Found {total_count} sensors with assigned models")
        
        for i, sensor in enumerate(sensors, 1):
            print(f"\n📊 Testing Sensor {i}/{total_count}: {sensor.sensor_code}")
            print(f"   Model: {sensor.mas_model_code}")
            print(f"   Parameter: {sensor.parameter}")
            print(f"   Location: {sensor.location}")
            
            try:
                # Run prediction
                result = predict_for_sensor(session, settings, sensor.sensor_code)
                
                if result:
                    confidence = result.get('confidence_score', 0.0)
                    
                    print(f"   ✅ SUCCESS!")
                    print(f"   📈 Confidence Score: {confidence:.3f} ({confidence:.1%})")
                    
                    # Interpret confidence level
                    if confidence >= 0.8:
                        confidence_level = "🟢 HIGH"
                        interpretation = "Highly reliable predictions"
                    elif confidence >= 0.6:
                        confidence_level = "🟡 MEDIUM" 
                        interpretation = "Reasonably reliable predictions"
                    elif confidence >= 0.4:
                        confidence_level = "🟠 LOW"
                        interpretation = "Use with caution"
                    else:
                        confidence_level = "🔴 VERY LOW"
                        interpretation = "Unreliable predictions"
                    
                    print(f"   {confidence_level} confidence - {interpretation}")
                    
                    # Show model details
                    print(f"   🤖 Model: {result.get('model_code')} ({result.get('model_type')})")
                    print(f"   🔧 Features: {result.get('input_features_used')} input features")
                    print(f"   📊 Predictions: {result.get('rows_inserted')} time steps inserted")
                    
                    # Show sample forecasts
                    predictions = result.get('predictions', [])
                    if predictions:
                        print(f"   🎯 Sample Forecasts:")
                        for pred in predictions[:3]:
                            forecast_time = pred['forecast_time'][:16]
                            value = pred['forecast_value']
                            conf_score = pred.get('confidence_score', confidence)
                            status = pred['threshold_status']
                            print(f"      {forecast_time}: {value:.2f} ({status}) - {conf_score:.1%}")
                    
                    success_count += 1
                    
                else:
                    print(f"   ❌ Prediction failed - no result returned")
                    
            except Exception as e:
                print(f"   ❌ Error: {str(e)[:100]}...")
        
        # Summary
        print(f"\n{'='*60}")
        print(f"🎯 FORECAST TEST RESULTS:")
        print(f"   Tested: {total_count} sensors")
        print(f"   Successful: {success_count}")
        print(f"   Success Rate: {success_count/total_count:.1%}")
        
        print(f"\n📚 Confidence Score Guide:")
        print(f"   🟢 0.80-1.00: HIGH confidence - Predictions highly reliable")
        print(f"   🟡 0.60-0.79: MEDIUM confidence - Reasonably reliable")
        print(f"   🟠 0.40-0.59: LOW confidence - Use with caution")
        print(f"   🔴 0.00-0.39: VERY LOW confidence - Unreliable")
        
        print(f"\n🧮 Confidence Components:")
        print(f"   • Data Quality (25%): Input completeness & outlier detection")
        print(f"   • Model Consistency (20%): Prediction smoothness")
        print(f"   • Historical Accuracy (25%): Past model performance")
        print(f"   • Input Stability (15%): Feature variance over time")
        print(f"   • Prediction Variance (15%): Output consistency")
        
        if success_count > 0:
            print(f"\n🎉 SUCCESS! Forecasting with confidence scores is working!")
        else:
            print(f"\n⚠️  No successful predictions - check data and models")
    
    except Exception as e:
        print(f"❌ Test failed: {e}")
    
    finally:
        session.close()

if __name__ == "__main__":
    test_forecasting_with_confidence()
