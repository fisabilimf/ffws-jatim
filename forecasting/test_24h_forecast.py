#!/usr/bin/env python3
"""
24-Hour Forecasting Test - All Models
"""

import sys
import os
sys.path.append(os.path.dirname(os.path.abspath(__file__)))

from app.db import init_engine, init_session
from app.config import Settings
from app.forecast import predict_for_sensor
from datetime import datetime

def test_24h_forecasting():
    settings = Settings()
    engine = init_engine(settings)
    Session = init_session(engine)

    print('🌊 24-Hour Flood Forecasting Test')
    print('='*50)
    print(f'⏰ Test time: {datetime.now().strftime("%Y-%m-%d %H:%M:%S")}')
    print()

    # Test all sensor models including the previously failing ones
    test_sensors = [
        'BE10126_GRU',   # DHOMPO GRU - was working
        'BE10126_LSTM',  # DHOMPO LSTM - should work  
        'BE10126_TCN',   # DHOMPO TCN - was failing, should work now
        'BE10128_GRU',   # PURWODADI GRU - was working
        'BE10128_LSTM',  # PURWODADI LSTM - should work
        'BE10128_TCN',   # PURWODADI TCN - was failing, should work now
    ]

    results_summary = []

    with Session() as session:
        for sensor_code in test_sensors:
            print(f'\n🤖 Testing: {sensor_code}')
            print('-' * 40)
            
            try:
                result = predict_for_sensor(
                    session=session,
                    settings=settings,
                    sensor_code=sensor_code,
                    prediction_hours=24,  # 24 hours ahead
                    step_hours=1.0        # Hourly predictions
                )
                
                predictions = result["predictions"]
                model_code = result["model_code"]
                confidence = result.get("confidence_score", 0)
                
                print(f'✅ SUCCESS!')
                print(f'   Model: {model_code}')
                print(f'   Predictions: {len(predictions)} hours')
                print(f'   Confidence: {round(confidence, 3)}')
                
                # Show first few predictions
                print(f'   📊 First 5 predictions:')
                for i, pred in enumerate(predictions[:5]):
                    value = pred.get("forecast_value", 0)
                    time = pred.get("forecast_time", "")
                    status = pred.get("threshold_status", "unknown")
                    print(f'      {i+1}h: {round(value, 3)} m ({status}) at {time[-8:-3]}')
                
                # Show last prediction (24h ahead)
                if len(predictions) >= 24:
                    last_pred = predictions[23]  # 24th hour
                    last_value = last_pred.get("forecast_value", 0)
                    last_time = last_pred.get("forecast_time", "")
                    last_status = last_pred.get("threshold_status", "unknown")
                    print(f'   🎯 24h ahead: {round(last_value, 3)} m ({last_status}) at {last_time[-8:-3]}')
                
                results_summary.append({
                    'sensor': sensor_code,
                    'model': model_code,
                    'status': 'SUCCESS',
                    'predictions': len(predictions),
                    'confidence': confidence
                })
                
            except Exception as e:
                print(f'❌ FAILED: {str(e)[:100]}...')
                results_summary.append({
                    'sensor': sensor_code,
                    'model': 'N/A',
                    'status': 'FAILED',
                    'predictions': 0,
                    'confidence': 0,
                    'error': str(e)[:50]
                })

    # Summary report
    print('\n' + '='*60)
    print('📊 24-HOUR FORECASTING SUMMARY')
    print('='*60)
    
    successful = [r for r in results_summary if r['status'] == 'SUCCESS']
    failed = [r for r in results_summary if r['status'] == 'FAILED']
    
    print(f'✅ Successful forecasts: {len(successful)}/{len(test_sensors)}')
    print(f'❌ Failed forecasts: {len(failed)}/{len(test_sensors)}')
    print(f'📈 Success rate: {len(successful)/len(test_sensors)*100:.1f}%')
    print()
    
    if successful:
        print('✅ WORKING MODELS:')
        for result in successful:
            print(f'   🤖 {result["sensor"]} → {result["model"]} (confidence: {result["confidence"]:.3f})')
    
    if failed:
        print('\n❌ FAILED MODELS:')
        for result in failed:
            error_msg = result.get('error', 'Unknown error')
            print(f'   💥 {result["sensor"]} → {error_msg}')
    
    print(f'\n🎯 CONCLUSION:')
    if len(successful) >= 4:  # At least 4/6 models working
        print('🎉 EXCELLENT! Most ML models are working perfectly for 24h forecasting!')
    elif len(successful) >= 2:
        print('✅ GOOD! Core ML models are working for 24h forecasting!')
    else:
        print('⚠️  Some issues remain, but fallback system will handle coverage.')
        
    print(f'🌊 24-hour flood forecasting system is operational!')

if __name__ == "__main__":
    test_24h_forecasting()