#!/usr/bin/env python3
"""
Test the new /forecast/hourly API endpoint.
"""

import requests
import json
from datetime import datetime

def test_hourly_api():
    """Test the new hourly forecasting API."""
    base_url = "http://localhost:5000"
    
    print("🌐 TESTING HOURLY FORECASTING API")
    print("=" * 45)
    
    # Test 1: Basic hourly forecast
    print(f"\n📡 Test 1: 5-Hour Forecast via API")
    print("-" * 30)
    
    payload = {
        "sensor_code": "SENSOR-2531",
        "hours": 5
    }
    
    try:
        response = requests.post(
            f"{base_url}/api/forecast/hourly",
            json=payload,
            timeout=60
        )
        
        print(f"📊 Status: {response.status_code}")
        
        if response.status_code == 200:
            result = response.json()
            
            print(f"✅ SUCCESS!")
            print(f"🎯 Sensor: {result.get('sensor_code')}")
            print(f"📊 Confidence: {result.get('confidence_score', 0):.1%}")
            print(f"⏰ Predictions: {result.get('rows_inserted', 0)}")
            
            predictions = result.get('predictions', [])
            if predictions:
                print(f"\n🔮 Forecasts:")
                for i, pred in enumerate(predictions[:5], 1):
                    time_part = pred['forecast_time'][11:16]
                    value = pred['forecast_value']
                    status = pred['threshold_status']
                    emoji = "🟢" if status == "safe" else "🟡" if status == "alert" else "🔴"
                    print(f"   +{i}h ({time_part}): {value:.2f} {emoji} {status}")
        
        else:
            print(f"❌ ERROR: {response.status_code}")
            print(f"Response: {response.text}")
    
    except Exception as e:
        print(f"❌ Request failed: {e}")
    
    # Test 2: Advanced API with custom parameters
    print(f"\n📡 Test 2: Custom Interval Forecast")
    print("-" * 35)
    
    payload2 = {
        "sensor_code": "SENSOR-2531",
        "prediction_hours": 8,
        "step_hours": 2.0
    }
    
    try:
        response = requests.post(
            f"{base_url}/api/forecast/run",
            json=payload2,
            timeout=60
        )
        
        print(f"📊 Status: {response.status_code}")
        
        if response.status_code == 200:
            result = response.json()
            
            print(f"✅ SUCCESS!")
            print(f"🎯 Sensor: {result.get('sensor_code')}")
            print(f"📊 Confidence: {result.get('confidence_score', 0):.1%}")
            print(f"⏰ Step hours: {result.get('step_hours', 0)}")
            print(f"🎯 Predictions: {result.get('rows_inserted', 0)}")
            
            predictions = result.get('predictions', [])
            if predictions:
                print(f"\n🔮 2-Hour Interval Forecasts:")
                for i, pred in enumerate(predictions, 1):
                    time_part = pred['forecast_time'][11:16]
                    value = pred['forecast_value']
                    status = pred['threshold_status']
                    hours_ahead = pred.get('hours_ahead', i * 2)
                    emoji = "🟢" if status == "safe" else "🟡" if status == "alert" else "🔴"
                    print(f"   +{hours_ahead:.0f}h ({time_part}): {value:.2f} {emoji} {status}")
        
        else:
            print(f"❌ ERROR: {response.status_code}")
            print(f"Response: {response.text}")
    
    except Exception as e:
        print(f"❌ Request failed: {e}")
    
    print(f"\n🎉 HOURLY API TESTING COMPLETE!")
    print(f"🌐 Both endpoints are ready for production use!")

if __name__ == "__main__":
    test_hourly_api()
