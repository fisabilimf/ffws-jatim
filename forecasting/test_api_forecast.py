#!/usr/bin/env python3
"""
Simple API test to demonstrate forecasting with existing data.
"""

import requests
import json
from datetime import datetime

def test_api_forecast():
    """Test the forecasting API with the existing data."""
    base_url = "http://localhost:5000/api"
    
    print("🌊 FFWS Forecasting API Test")
    print("=" * 50)
    
    try:
        # Test 1: Check models
        print("📋 Available Models:")
        response = requests.get(f"{base_url}/models")
        if response.status_code == 200:
            models = response.json()
            for model in models[:3]:
                print(f"  • {model['code']} ({model['type']})")
            print(f"  ... and {len(models) - 3} more models")
        
        # Test 2: Check active sensors
        print("\n📡 Active Sensors with Models:")
        response = requests.get(f"{base_url}/sensors")
        if response.status_code == 200:
            sensors = response.json()
            active_sensors = [s for s in sensors if s['status'] == 'active' and s['model_code']]
            
            for sensor in active_sensors[:5]:
                print(f"  • {sensor['code']} ({sensor['parameter']}) → {sensor['model_code']}")
            print(f"  ... {len(active_sensors)} total active sensors ready for forecasting")
        
        # Test 3: Try to run forecasts
        print("\n🚀 Testing Forecasts:")
        test_sensors = ['SENSOR-4060', 'SENSOR-3714', 'SENSOR-0793']  # Mix of water level and rainfall
        
        successful_forecasts = 0
        
        for sensor_code in test_sensors:
            print(f"\n📊 Forecasting for {sensor_code}...")
            
            payload = {"sensor_code": sensor_code}
            
            try:
                response = requests.post(
                    f"{base_url}/forecast/run",
                    json=payload,
                    timeout=30
                )
                
                if response.status_code == 200:
                    result = response.json()
                    print(f"   ✅ SUCCESS!")
                    print(f"      Model: {result.get('model_code')} ({result.get('model_type')})")
                    print(f"      Predictions: {result.get('rows_inserted', 0)} time steps")
                    print(f"      Forecast horizon: {result.get('step_minutes', 'Unknown')} minutes")
                    
                    successful_forecasts += 1
                    
                else:
                    error_info = response.json() if response.headers.get('content-type') == 'application/json' else response.text
                    print(f"   ❌ FAILED: {response.status_code}")
                    print(f"      Error: {error_info}")
            
            except requests.exceptions.Timeout:
                print(f"   ⏱️  TIMEOUT: Request took too long (>30s)")
            except Exception as e:
                print(f"   ❌ ERROR: {e}")
        
        # Summary
        print("\n" + "=" * 50)
        print(f"📈 Forecast Results:")
        print(f"   Tested: {len(test_sensors)} sensors")
        print(f"   Successful: {successful_forecasts}")
        print(f"   Success Rate: {successful_forecasts/len(test_sensors)*100:.1f}%")
        
        # Test 4: Try a basin-wide forecast
        if successful_forecasts > 0:
            print(f"\n🏞️  Testing Basin-wide Forecast:")
            response = requests.get(f"{base_url}/river-basins")
            if response.status_code == 200:
                basins = response.json()
                if basins:
                    test_basin = basins[0]['code']
                    print(f"   Testing with basin: {test_basin}")
                    
                    try:
                        response = requests.post(
                            f"{base_url}/forecast/run-basin",
                            json={"river_basin_code": test_basin, "only_active": True},
                            timeout=60
                        )
                        
                        if response.status_code == 200:
                            result = response.json()
                            print(f"   ✅ Basin forecast successful!")
                            print(f"      Sensors processed: {len(result.get('results', []))}")
                        else:
                            print(f"   ❌ Basin forecast failed: {response.status_code}")
                    except Exception as e:
                        print(f"   ❌ Basin forecast error: {e}")
        
        return successful_forecasts > 0
        
    except requests.exceptions.ConnectionError:
        print("❌ Cannot connect to API server!")
        print("Make sure the Flask server is running on http://localhost:5000")
        return False
    except Exception as e:
        print(f"❌ Unexpected error: {e}")
        return False

if __name__ == "__main__":
    success = test_api_forecast()
    
    if success:
        print("\n🎉 Forecasting system is working!")
        print("\n💡 Next steps:")
        print("   • Integrate with your frontend application")
        print("   • Set up real sensor data feeds")
        print("   • Configure threshold alerts")
        print("   • Schedule periodic forecasts")
    else:
        print("\n⚠️  Some issues were found. Check the logs above.")
