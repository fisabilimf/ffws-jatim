#!/usr/bin/env python3
"""
Check database tables and structure.
"""

import pymysql

def check_database_structure():
    """Check what tables exist in the database."""
    
    print("🔍 DATABASE STRUCTURE ANALYSIS")
    print("=" * 32)
    
    try:
        connection = pymysql.connect(
            host='127.0.0.1',
            port=3306,
            user='root',
            password='1234',
            database='ffws_jatim'
        )
        
        print("✅ Connected to ffws_jatim database")
        
        # List all tables
        with connection.cursor() as cursor:
            cursor.execute("SHOW TABLES")
            tables = cursor.fetchall()
            
            print(f"\n📊 Found {len(tables)} tables:")
            for table in tables:
                print(f"   • {table[0]}")
        
        # Check for prediction-related tables
        prediction_tables = []
        actual_tables = []
        
        for table in tables:
            table_name = table[0]
            if 'prediction' in table_name.lower():
                prediction_tables.append(table_name)
            if 'actual' in table_name.lower() or 'data' in table_name.lower():
                actual_tables.append(table_name)
        
        print(f"\n🎯 Prediction-related tables:")
        for table in prediction_tables:
            print(f"   • {table}")
        
        print(f"\n📊 Data-related tables:")
        for table in actual_tables:
            print(f"   • {table}")
        
        # Check sensors table structure
        sensor_tables = [t[0] for t in tables if 'sensor' in t[0].lower()]
        if sensor_tables:
            print(f"\n🔧 Sensor table: {sensor_tables[0]}")
            with connection.cursor() as cursor:
                cursor.execute(f"DESCRIBE {sensor_tables[0]}")
                columns = cursor.fetchall()
                for col in columns:
                    print(f"   • {col[0]} ({col[1]})")
        
        # Test data availability
        if actual_tables:
            print(f"\n📈 Testing data availability in {actual_tables[0]}:")
            with connection.cursor() as cursor:
                cursor.execute(f"SELECT COUNT(*) FROM {actual_tables[0]}")
                count = cursor.fetchone()
                print(f"   • Total records: {count[0]}")
                
                if count[0] > 0:
                    cursor.execute(f"SELECT * FROM {actual_tables[0]} ORDER BY id DESC LIMIT 3")
                    samples = cursor.fetchall()
                    print("   • Sample records:")
                    for sample in samples:
                        print(f"     {sample}")
        
        connection.close()
        
        print(f"\n✅ Database analysis complete!")
        print(f"\n💡 RECOMMENDATIONS:")
        
        if not prediction_tables:
            print("   • Create data_predictions table for storing forecasts")
        else:
            print(f"   • Use {prediction_tables[0]} for storing predictions")
        
        if not actual_tables:
            print("   • Create data_actual table for sensor readings")
        else:
            print(f"   • Use {actual_tables[0]} for sensor data")
        
        print("   • Update app/models.py table names if needed")
        print("   • Ensure table structures match model definitions")
        
    except Exception as e:
        print(f"❌ Database analysis failed: {e}")

if __name__ == "__main__":
    check_database_structure()
