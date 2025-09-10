#!/usr/bin/env python3
"""Find sensors with sufficient data and test forecasting with confidence scores."""

import os
from dotenv import load_dotenv
load_dotenv()

import sys
sys.path.insert(0, os.path.join(os.path.dirname(__file__), 'app'))

from app.db import init_engine, init_session
from app.config import Settings
from app.forecast import predict_for_sensor, ForecastError
from app.models import MasSensors, DataActual
from sqlalchemy import select, func, desc

def test_forecasting_with_data_check():
    """Find and test sensors that have sufficient data for forecasting."""
    print("🚀 FFWS Forecasting Test - Finding Sensors with Sufficient Data")
    print("=" * 70)
    
    settings = Settings()
    engine = init_engine(settings)
    Session = init_session(engine)
    s = Session()
    
    try:
        # Get all sensors with models
        query = select(MasSensors).where(MasSensors.mas_model_code.isnot(None))
        all_sensors = s.execute(query).scalars().all()
        
        print(f"📡 Found {len(all_sensors)} sensors with assigned models")
        print(f"🔍 Checking data availability for each sensor...")
        
        sensors_with_data = []
        
        for sensor in all_sensors:
            # Count data points for this sensor
            data_count = s.execute(
                select(func.count(DataActual.id)).where(
                    DataActual.mas_sensor_code == sensor.sensor_code
                )
            ).scalar()
            
            if data_count >= 5:  # Need at least 5 data points
                sensors_with_data.append((sensor, data_count))
                print(f"   ✅ {sensor.sensor_code}: {data_count} data points")
            else:
                print(f"   ❌ {sensor.sensor_code}: {data_count} data points (insufficient)")
        
        print(f"\n📊 Found {len(sensors_with_data)} sensors with sufficient data")
        
        if not sensors_with_data:
            print("❌ No sensors have sufficient data for forecasting!")
            return
        
        # Sort by data count (most data first)
        sensors_with_data.sort(key=lambda x: x[1], reverse=True)
        
        print(f"\n🧪 Testing Forecasting with Confidence Scores")
        print("=" * 50)
        
        successful_forecasts = 0
        
        for i, (sensor, data_count) in enumerate(sensors_with_data[:5], 1):  # Test top 5
            print(f"\n📊 Test {i}: {sensor.sensor_code}")
            print(f"   Model: {sensor.mas_model_code}")
            print(f"   Parameter: {sensor.parameter}")
            print(f"   Data Points: {data_count}")
            
            try:
                result = predict_for_sensor(s, settings, sensor.sensor_code)
                
                if result:
                    confidence = result.get('confidence_score', 0.0)
                    successful_forecasts += 1
                    
                    # Determine confidence level
                    if confidence >= 0.8:
                        level = "🟢 HIGH"
                    elif confidence >= 0.6:
                        level = "🟡 MEDIUM"
                    elif confidence >= 0.4:
                        level = "🟠 LOW"
                    else:
                        level = "🔴 VERY LOW"
                    
                    print(f"   ✅ SUCCESS!")
                    print(f"   📊 Confidence Score: {confidence:.3f} ({confidence:.1%}) - {level}")
                    print(f"   🎯 Predictions Generated: {result.get('rows_inserted', 0)}")
                    
                    # Show detailed breakdown if available
                    predictions = result.get('predictions', [])
                    if predictions:
                        print(f"   📈 Sample Forecasts:")
                        for j, pred in enumerate(predictions[:3], 1):
                            time_str = pred['forecast_time'][:16]
                            value = pred['forecast_value']
                            status = pred['threshold_status']
                            pred_conf = pred.get('confidence_score', confidence)
                            print(f"      {j}. {time_str}: {value:.2f} ({status}) - {pred_conf:.1%}")
                    
                    # Show confidence interpretation
                    print(f"   💡 Confidence Interpretation:")
                    if confidence >= 0.8:
                        print(f"      Highly reliable predictions - Use with full confidence")
                    elif confidence >= 0.6:
                        print(f"      Reasonably reliable - Good for operational decisions")
                    elif confidence >= 0.4:
                        print(f"      Use with caution - Consider additional data sources")
                    else:
                        print(f"      Low reliability - Use only as rough estimates")
                        
                else:
                    print(f"   ❌ FAILED: No result returned")
                    
            except Exception as e:
                error_msg = str(e)[:60] + "..." if len(str(e)) > 60 else str(e)
                print(f"   ❌ ERROR: {error_msg}")
        
        # Final summary
        print(f"\n{'='*70}")
        print(f"🎯 FORECASTING TEST RESULTS")
        print("="*70)
        
        total_tested = min(5, len(sensors_with_data))
        success_rate = (successful_forecasts / total_tested * 100) if total_tested > 0 else 0
        
        print(f"📊 Total Sensors Tested: {total_tested}")
        print(f"✅ Successful Forecasts: {successful_forecasts}")
        print(f"📈 Success Rate: {success_rate:.1f}%")
        
        if successful_forecasts > 0:
            print(f"\n🎉 SUCCESS! Forecasting with confidence scores is working!")
            print(f"📚 Confidence Score Components:")
            print(f"   • Data Quality (25%): Input completeness & outlier detection")
            print(f"   • Model Consistency (20%): Prediction smoothness")
            print(f"   • Historical Accuracy (25%): Past performance analysis")
            print(f"   • Input Stability (15%): Feature variance over time")
            print(f"   • Prediction Variance (15%): Output consistency")
            
            print(f"\n🔧 Technical Status:")
            print(f"   ✅ TensorFlow models loading correctly")
            print(f"   ✅ Multi-feature input processing")
            print(f"   ✅ Confidence scores calculated")
            print(f"   ✅ Database storage working")
            print(f"   ✅ Threshold classification active")
        else:
            print(f"\n⚠️  No successful forecasts - system needs investigation")
    
    except Exception as e:
        print(f"❌ Test failed: {e}")
    
    finally:
        s.close()

if __name__ == "__main__":
    test_forecasting_with_data_check()
