#!/usr/bin/env python3
"""
Simple solution to prevent duplicated values in forecasting demo
"""

import requests
import mysql.connector
from datetime import datetime

def clean_recent_predictions():
    """Clean predictions from the last hour to prevent duplicates during demo"""
    try:
        connection = mysql.connector.connect(
            host='127.0.0.1',
            port=3306,
            database='ffws_jatim',
            user='root',
            password='1234'
        )
        
        cursor = connection.cursor()
        
        # Count current predictions
        cursor.execute("SELECT COUNT(*) FROM data_predictions")
        result = cursor.fetchone()
        current_count = result[0] if result else 0
        
        print(f"📊 Current predictions in database: {current_count}")
        
        if current_count > 255:  # If too many predictions, clean some
            # Delete predictions for SENSOR-9127 from today
            cursor.execute("""
                DELETE FROM data_predictions 
                WHERE mas_sensor_code = 'SENSOR-9127' 
                AND DATE(created_at) = CURDATE()
            """)
            
            deleted = cursor.rowcount
            connection.commit()
            
            if deleted > 0:
                print(f"🧹 Cleaned {deleted} recent predictions to prevent duplicates")
            
        cursor.close()
        connection.close()
        
    except Exception as e:
        print(f"⚠️  Database cleaning failed: {e}")

def run_single_forecast_demo():
    """Run a single clean forecast demonstration"""
    
    print("🌊 FFWS Single Forecast Demo (No Duplicates)")
    print("=" * 60)
    print(f"Timestamp: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    print("=" * 60)
    
    # Clean recent predictions first
    clean_recent_predictions()
    
    # Check system health
    try:
        response = requests.get("http://localhost:5000/health", timeout=5)
        if response.status_code == 200:
            print("✅ Forecasting system is healthy")
        else:
            print(f"❌ System health check failed: {response.status_code}")
            return
    except Exception as e:
        print(f"❌ Cannot connect to forecasting system: {e}")
        print("Make sure the server is running: python run_dev_server.py")
        return
    
    # Run a single forecast
    print("\n🔮 Generating Single Forecast")
    print("-" * 40)
    
    payload = {
        "sensor_code": "SENSOR-9127",
        "model_code": "DHOMPO_LSTM",
        "prediction_hours": 24,
        "step_hours": 1.0
    }
    
    try:
        response = requests.post("http://localhost:5000/api/forecast/run", json=payload)
        
        if response.status_code == 200:
            result = response.json()
            predictions = result.get('predictions', [])
            
            print(f"✅ SUCCESS: Generated {len(predictions)} predictions")
            print(f"🎯 Confidence Score: {result.get('confidence_score', 0):.3f}")
            print(f"🤖 Model: {result.get('model_code')} ({result.get('model_algorithm')})")
            print(f"📡 Sensor: {result.get('sensor_code')}")
            print(f"💾 New records in database: {result.get('rows_inserted', 0)}")
            
            print("\n📈 Forecast Results:")
            print("-" * 60)
            print(f"{'#':<3} {'Time':<20} {'Value':<10} {'Status':<8} {'Hours':<6}")
            print("-" * 60)
            
            for i, pred in enumerate(predictions, 1):
                time_str = pred['forecast_time']
                value = pred['forecast_value']
                status = pred['threshold_status']
                hours = pred['hours_ahead']
                
                print(f"{i:<3} {time_str:<20} {value:<10.4f} {status:<8} +{hours:<6.1f}h")
            
            print(f"\n📊 Summary:")
            print(f"   Parameter: Water Level")
            print(f"   Prediction Range: {min(p['forecast_value'] for p in predictions):.3f} - {max(p['forecast_value'] for p in predictions):.3f}")
            print(f"   All predictions: {'SAFE' if all(p['threshold_status'] == 'safe' for p in predictions) else 'MIXED STATUS'}")
            
        else:
            error = response.json()
            print(f"❌ Forecast failed: {error.get('error', 'Unknown error')}")
            
    except Exception as e:
        print(f"❌ Request failed: {e}")
    
    print(f"\n✅ Demo completed successfully!")
    print(f"🔗 API tested: http://localhost:5000/api/forecast/run")

def show_database_status():
    """Show current database prediction status"""
    try:
        connection = mysql.connector.connect(
            host='127.0.0.1',
            port=3306,
            database='ffws_jatim',
            user='root',
            password='1234'
        )
        
        cursor = connection.cursor()
        
        print("\n📊 Database Status")
        print("-" * 40)
        
        # Total predictions
        cursor.execute("SELECT COUNT(*) FROM data_predictions")
        result = cursor.fetchone()
        total = result[0] if result else 0
        print(f"Total predictions: {total}")
        
        # By sensor
        cursor.execute("""
            SELECT mas_sensor_code, COUNT(*) as count
            FROM data_predictions 
            GROUP BY mas_sensor_code
            ORDER BY count DESC
            LIMIT 5
        """)
        
        sensors = cursor.fetchall()
        if sensors:
            print("\nTop sensors by prediction count:")
            for sensor_data in sensors:
                sensor = sensor_data[0]
                count = sensor_data[1]
                print(f"  {sensor}: {count} predictions")
        
        cursor.close()
        connection.close()
        
    except Exception as e:
        print(f"Database status check failed: {e}")

if __name__ == "__main__":
    run_single_forecast_demo()
    show_database_status()
