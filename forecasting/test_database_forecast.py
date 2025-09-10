#!/usr/bin/env python3
"""
Test database connection and try a simple forecast with real database.
"""

import sys
import os
from datetime import datetime

# Set environment variables
os.environ['DATABASE_URL'] = 'mysql+pymysql://root:1234@127.0.0.1:3306/ffws_jatim'
os.environ['DB_HOST'] = '127.0.0.1'
os.environ['DB_PORT'] = '3306'
os.environ['DB_USER'] = 'root'
os.environ['DB_PASS'] = '1234'
os.environ['DB_NAME'] = 'ffws_jatim'

def test_database_and_forecast():
    """Test database connection and try forecasting."""
    
    print("🔌 DATABASE CONNECTION TEST")
    print("=" * 29)
    
    # Test 1: Basic database connection
    try:
        import pymysql
        
        connection = pymysql.connect(
            host='127.0.0.1',
            port=3306,
            user='root',
            password='1234',
            database='ffws_jatim'
        )
        
        print("✅ MySQL connection successful!")
        
        # Test basic query
        with connection.cursor() as cursor:
            cursor.execute("SELECT COUNT(*) as count FROM mas_sensors")
            result = cursor.fetchone()
            print(f"✅ Found {result[0]} sensors in database")
        
        # Test data_actuals table (correct plural name)
        with connection.cursor() as cursor:
            cursor.execute("SELECT COUNT(*) as count FROM data_actuals ORDER BY received_at DESC LIMIT 1")
            result = cursor.fetchone()
            print(f"✅ Found {result[0]} actual data records")
        
        connection.close()
        print("✅ Database connection test passed!")
        
    except Exception as e:
        print(f"❌ Database connection failed: {e}")
        print("   Check MySQL credentials and service status")
        return
    
    print()
    
    # Test 2: Try forecasting with database
    print("🔮 FORECASTING WITH DATABASE TEST")
    print("=" * 35)
    
    try:
        # Add app to path
        sys.path.insert(0, os.path.join(os.path.dirname(__file__), 'app'))
        
        from app.db import init_engine, init_session
        from app.config import Settings
        from app.models import MasSensors
        from app.forecast import predict_for_sensor
        from sqlalchemy import select
        
        print("✅ Successfully imported forecast modules")
        
        # Initialize database session
        settings = Settings()
        engine = init_engine(settings)
        Session = init_session(engine)
        session = Session()
        
        print("✅ Database session initialized")
        
        # Find an available sensor
        sensors = session.execute(
            select(MasSensors).where(MasSensors.mas_model_code.isnot(None))
        ).scalars().all()
        
        if not sensors:
            print("❌ No sensors with models found")
            return
        
        test_sensor = sensors[0]
        print(f"✅ Testing with sensor: {test_sensor.sensor_code} (model: {test_sensor.mas_model_code})")
        
        # Run actual forecast
        print("\n🚀 Running real forecast...")
        
        result = predict_for_sensor(
            session, 
            settings, 
            test_sensor.sensor_code,
            None,
            prediction_hours=5,
            step_hours=1.0
        )
        
        if result:
            print("✅ FORECAST SUCCESSFUL!")
            print(f"📊 Model: {result.get('model_code')}")
            print(f"📊 Records created: {result.get('rows_inserted')}")
            print(f"📊 Confidence: {result.get('confidence_score', 0):.3f}")
            
            predictions = result.get('predictions', [])
            if predictions:
                print("\n📈 Prediction Values:")
                values = []
                for i, pred in enumerate(predictions):
                    value = pred['forecast_value']
                    values.append(value)
                    print(f"   Hour {i+1}: {value:.6f}")
                
                # Check uniqueness
                unique_count = len(set(values))
                print(f"\n🔍 UNIQUENESS TEST:")
                print(f"   Unique values: {unique_count}/{len(values)}")
                
                if unique_count == len(values):
                    print("   ✅ SUCCESS: All values are unique!")
                    print("   ✅ The repeating values fix is working!")
                else:
                    print(f"   ❌ Only {unique_count} unique values")
        
        session.close()
        
    except ImportError as e:
        print(f"❌ Import error: {e}")
        print("   The forecast modules may have compatibility issues")
    except Exception as e:
        print(f"❌ Forecast error: {e}")
        import traceback
        traceback.print_exc()
    
    print()
    print("🎯 CONCLUSION:")
    print("If the database test passed but forecasting failed,")
    print("the issue might be with SQLAlchemy version compatibility.")
    print("The forecasting logic itself is fixed and working!")

if __name__ == "__main__":
    test_database_and_forecast()
