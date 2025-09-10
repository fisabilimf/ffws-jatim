#!/usr/bin/env python3
"""
Check the actual data intervals in the database to understand time patterns.
"""

import os
from dotenv import load_dotenv
load_dotenv()

import sys
sys.path.insert(0, os.path.join(os.path.dirname(__file__), 'app'))

from app.db import init_engine, init_session
from app.config import Settings
from app.models import DataActual
from sqlalchemy import select, func, desc
from datetime import datetime, timedelta

def analyze_data_intervals():
    """Analyze the time intervals in actual data."""
    print("🕐 ANALYZING DATA TIME INTERVALS")
    print("=" * 50)
    
    settings = Settings()
    engine = init_engine(settings)
    Session = init_session(engine)
    session = Session()
    
    try:
        # Get data ordered by time for each sensor
        sensors_data = session.execute(
            select(DataActual.mas_sensor_code, func.count(DataActual.id).label('count'))
            .group_by(DataActual.mas_sensor_code)
            .having(func.count(DataActual.id) >= 3)
            .order_by(desc('count'))
        ).all()
        
        print(f"📊 Found {len(sensors_data)} sensors with sufficient data")
        
        for sensor_code, count in sensors_data[:5]:  # Check top 5 sensors
            print(f"\n📡 Sensor: {sensor_code} ({count} data points)")
            
            # Get chronological data for this sensor
            data_points = session.execute(
                select(DataActual.received_at, DataActual.value)
                .where(DataActual.mas_sensor_code == sensor_code)
                .order_by(DataActual.received_at)
            ).all()
            
            if len(data_points) >= 2:
                intervals = []
                for i in range(1, len(data_points)):
                    interval = data_points[i][0] - data_points[i-1][0]
                    intervals.append(interval.total_seconds() / 3600)  # Convert to hours
                
                if intervals:
                    avg_interval = sum(intervals) / len(intervals)
                    min_interval = min(intervals)
                    max_interval = max(intervals)
                    
                    print(f"   ⏰ Average interval: {avg_interval:.2f} hours")
                    print(f"   📏 Range: {min_interval:.2f} - {max_interval:.2f} hours")
                    
                    # Show sample timestamps
                    print(f"   📅 Sample timestamps:")
                    for i, (timestamp, value) in enumerate(data_points[:3]):
                        print(f"      {i+1}. {timestamp} - {value}")
        
        # Overall analysis
        print(f"\n📊 OVERALL DATA ANALYSIS:")
        total_data = session.execute(select(func.count(DataActual.id))).scalar()
        earliest = session.execute(select(func.min(DataActual.received_at))).scalar()
        latest = session.execute(select(func.max(DataActual.received_at))).scalar()
        
        print(f"   Total data points: {total_data}")
        print(f"   Date range: {earliest} to {latest}")
        
        if earliest and latest:
            total_duration = (latest - earliest).total_seconds() / 3600
            avg_frequency = total_data / total_duration if total_duration > 0 else 0
            print(f"   Average frequency: {avg_frequency:.2f} points per hour")
    
    except Exception as e:
        print(f"❌ Error: {e}")
    
    finally:
        session.close()

if __name__ == "__main__":
    analyze_data_intervals()
