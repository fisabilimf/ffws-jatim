"""
Fallback forecasting system for sensors without models or scalers
Integrated into FFWS main application
"""

import numpy as np
import pandas as pd
from datetime import datetime, timedelta
from sqlalchemy import select, desc
from sqlalchemy.orm import Session
from typing import Dict, List, Any, Optional, Tuple
import logging

from .models import MasSensors, MasDevices, MasRiverBasins, DataActual
from .config import Settings

logger = logging.getLogger(__name__)


class FallbackForecastingSystem:
    """Fallback forecasting system for sensors without models"""
    
    def __init__(self, session: Session, settings: Settings):
        self.session = session
        self.settings = settings
        
    def get_sensor_info(self, sensor_code: str) -> Optional[Dict[str, Any]]:
        """Get comprehensive sensor information"""
        try:
            stmt = select(
                MasSensors.sensor_code,
                MasSensors.parameter,
                MasSensors.name.label('sensor_name'),
                MasSensors.mas_model_code,
                MasDevices.name.label('device_name'),
                MasDevices.latitude,
                MasDevices.longitude,
                MasRiverBasins.name.label('basin_name')
            ).select_from(
                MasSensors
            ).join(
                MasDevices, MasSensors.mas_device_code == MasDevices.device_code
            ).join(
                MasRiverBasins, MasDevices.mas_river_basin_code == MasRiverBasins.code
            ).where(
                MasSensors.sensor_code == sensor_code
            )
            
            result = self.session.execute(stmt).first()
            
            if result:
                return {
                    'sensor_code': result.sensor_code,
                    'parameter': result.parameter,
                    'sensor_name': result.sensor_name,
                    'mas_model_code': result.mas_model_code,
                    'device_name': result.device_name,
                    'latitude': result.latitude,
                    'longitude': result.longitude,
                    'basin_name': result.basin_name
                }
            
            return None
            
        except Exception as e:
            logger.error(f"Failed to get sensor info for {sensor_code}: {e}")
            return None
    
    def get_historical_data(self, sensor_code: str, hours_back: int = 168) -> Optional[pd.DataFrame]:
        """Get historical data for sensor (last N hours)"""
        try:
            cutoff_time = datetime.utcnow() - timedelta(hours=hours_back)
            
            stmt = select(
                DataActual.datetime,
                DataActual.value
            ).where(
                DataActual.mas_sensor_code == sensor_code,
                DataActual.datetime >= cutoff_time
            ).order_by(DataActual.datetime.asc())
            
            results = self.session.execute(stmt).all()
            
            if not results:
                return None
            
            data = [{
                'datetime': row.datetime,
                'value': row.value
            } for row in results]
            
            df = pd.DataFrame(data)
            df['datetime'] = pd.to_datetime(df['datetime'])
            df.set_index('datetime', inplace=True)
            
            return df
            
        except Exception as e:
            logger.error(f"Failed to get historical data for {sensor_code}: {e}")
            return None
    
    def find_similar_sensors(self, sensor_code: str, max_distance_km: float = 50.0) -> List[Dict[str, Any]]:
        """Find sensors with similar characteristics that have models"""
        sensor_info = self.get_sensor_info(sensor_code)
        if not sensor_info:
            return []
        
        try:
            # For now, find sensors with same parameter type and active models
            # In production, you might want to add geographic distance calculation
            stmt = select(
                MasSensors.sensor_code,
                MasSensors.parameter,
                MasSensors.name.label('sensor_name'),
                MasSensors.mas_model_code,
                MasDevices.name.label('device_name'),
                MasDevices.latitude,
                MasDevices.longitude,
                MasRiverBasins.name.label('basin_name')
            ).select_from(
                MasSensors
            ).join(
                MasDevices, MasSensors.mas_device_code == MasDevices.device_code
            ).join(
                MasRiverBasins, MasDevices.mas_river_basin_code == MasRiverBasins.code
            ).where(
                MasSensors.parameter == sensor_info['parameter'],
                MasSensors.mas_model_code.isnot(None),
                MasSensors.sensor_code != sensor_code,
                MasSensors.status == 'active'
            ).limit(5)
            
            results = self.session.execute(stmt).all()
            
            similar_sensors = []
            for row in results:
                # Simple distance calculation (approximate)
                lat_diff = abs(row.latitude - sensor_info['latitude'])
                lon_diff = abs(row.longitude - sensor_info['longitude'])
                distance = np.sqrt(lat_diff**2 + lon_diff**2) * 111  # Rough km conversion
                
                if distance <= max_distance_km:
                    similar_sensors.append({
                        'sensor_code': row.sensor_code,
                        'parameter': row.parameter,
                        'sensor_name': row.sensor_name,
                        'mas_model_code': row.mas_model_code,
                        'device_name': row.device_name,
                        'basin_name': row.basin_name,
                        'distance_km': distance
                    })
            
            # Sort by distance
            similar_sensors.sort(key=lambda x: x['distance_km'])
            
            return similar_sensors
            
        except Exception as e:
            logger.error(f"Failed to find similar sensors for {sensor_code}: {e}")
            return []
    
    def calculate_correlation_factor(self, sensor1_code: str, sensor2_code: str) -> float:
        """Calculate correlation factor between two sensors"""
        try:
            # Get overlapping historical data for both sensors
            data1 = self.get_historical_data(sensor1_code, hours_back=720)  # 30 days
            data2 = self.get_historical_data(sensor2_code, hours_back=720)
            
            if data1 is None or data2 is None:
                return 1.0  # Default correlation
            
            # Merge on datetime index
            merged = pd.merge(data1, data2, left_index=True, right_index=True, 
                            suffixes=('_1', '_2'), how='inner')
            
            if len(merged) < 10:
                return 1.0  # Not enough overlapping data
            
            # Calculate mean ratio as adjustment factor
            mean1 = merged['value_1'].mean()
            mean2 = merged['value_2'].mean()
            
            if mean2 == 0:
                return 1.0
            
            ratio = mean1 / mean2
            
            return ratio if not pd.isna(ratio) and ratio > 0 else 1.0
            
        except Exception as e:
            logger.error(f"Failed to calculate correlation between {sensor1_code} and {sensor2_code}: {e}")
            return 1.0
    
    def statistical_forecast(self, historical_data: pd.DataFrame, hours_ahead: int = 6) -> List[Dict[str, Any]]:
        """Statistical forecasting fallback"""
        try:
            if len(historical_data) < 3:
                # Fall back to last known value
                last_value = historical_data.iloc[-1]['value'] if len(historical_data) > 0 else 0.0
                return self.persistence_forecast(last_value, hours_ahead)
            
            values = historical_data['value'].values
            
            # Simple trend analysis
            if len(values) >= 24:
                # Use last 24 hours for trend calculation
                recent_values = values[-24:]
                trend = np.polyfit(range(len(recent_values)), recent_values, 1)[0]
                base_value = recent_values[-1]
            else:
                # Use all available data
                trend = np.polyfit(range(len(values)), values, 1)[0] if len(values) > 1 else 0
                base_value = values[-1]
            
            # Apply seasonal adjustment if enough data
            seasonal_factor = 1.0
            if len(values) >= 168:  # 7 days of data
                try:
                    current_hour = datetime.utcnow().hour
                    # Simple daily pattern analysis
                    daily_pattern = np.zeros(24)
                    pattern_counts = np.zeros(24)
                    
                    for i, value in enumerate(values):
                        hour = (current_hour - (len(values) - 1 - i)) % 24
                        daily_pattern[hour] += value
                        pattern_counts[hour] += 1
                    
                    # Average pattern
                    for h in range(24):
                        if pattern_counts[h] > 0:
                            daily_pattern[h] /= pattern_counts[h]
                    
                    if daily_pattern[current_hour] > 0 and np.mean(daily_pattern) > 0:
                        seasonal_factor = daily_pattern[current_hour] / np.mean(daily_pattern)
                    
                except Exception:
                    seasonal_factor = 1.0
            
            predictions = []
            for i in range(1, hours_ahead + 1):
                # Apply trend and seasonal adjustment
                pred_value = base_value + (trend * i) * seasonal_factor
                
                # Ensure non-negative for certain parameters
                if pred_value < 0:
                    pred_value = max(0, base_value * 0.9)  # Slight decay if negative
                
                # Confidence decreases with time horizon
                confidence = max(0.1, 0.6 - (i * 0.05))
                
                predictions.append({
                    'forecast_time': datetime.utcnow() + timedelta(hours=i),
                    'forecast_value': pred_value,
                    'confidence_score': confidence,
                    'method': 'statistical_fallback'
                })
            
            return predictions
            
        except Exception as e:
            logger.error(f"Statistical forecast failed: {e}")
            # Fall back to persistence
            last_value = historical_data.iloc[-1]['value'] if len(historical_data) > 0 else 0.0
            return self.persistence_forecast(last_value, hours_ahead)
    
    def persistence_forecast(self, current_value: float, hours_ahead: int = 6) -> List[Dict[str, Any]]:
        """Simple persistence model - assume current value continues"""
        predictions = []
        for i in range(1, hours_ahead + 1):
            # Confidence decreases with time
            confidence = max(0.05, 0.3 - (i * 0.03))
            
            predictions.append({
                'forecast_time': datetime.utcnow() + timedelta(hours=i),
                'forecast_value': current_value,
                'confidence_score': confidence,
                'method': 'persistence_fallback'
            })
        
        return predictions
    
    def get_default_value_by_parameter(self, parameter_type: str) -> float:
        """Get default value based on parameter type"""
        defaults = {
            'water_level': 1.0,
            'rainfall': 0.0,
            'temperature': 25.0,
            'humidity': 70.0,
            'wind_speed': 5.0,
            'pressure': 1013.0
        }
        return defaults.get(parameter_type, 0.0)
    
    def run_fallback_forecast(self, sensor_code: str, hours_ahead: int = 6) -> Dict[str, Any]:
        """Main fallback forecasting method"""
        try:
            logger.info(f"Running fallback forecast for sensor {sensor_code}")
            
            # Get sensor information
            sensor_info = self.get_sensor_info(sensor_code)
            if not sensor_info:
                return {
                    'success': False,
                    'error': f'Sensor {sensor_code} not found',
                    'method': 'fallback_error'
                }
            
            # Try to get historical data
            historical_data = self.get_historical_data(sensor_code, hours_back=168)
            
            predictions = None
            method_used = 'unknown'
            fallback_reason = 'no_model_assigned'
            
            # Strategy 1: Statistical forecast if we have sufficient historical data
            if historical_data is not None and len(historical_data) >= 10:
                predictions = self.statistical_forecast(historical_data, hours_ahead)
                method_used = 'statistical_fallback'
                logger.info(f"Using statistical forecast for {sensor_code}")
                
            # Strategy 2: Try to find a similar sensor with a model (proxy model)
            elif historical_data is None or len(historical_data) < 10:
                similar_sensors = self.find_similar_sensors(sensor_code)
                
                if similar_sensors:
                    # For now, use statistical forecast from similar sensor
                    # In production, you would run the actual ML model
                    similar_sensor_code = similar_sensors[0]['sensor_code']
                    similar_data = self.get_historical_data(similar_sensor_code, hours_back=168)
                    
                    if similar_data is not None and len(similar_data) >= 10:
                        # Get correlation factor
                        correlation_factor = self.calculate_correlation_factor(sensor_code, similar_sensor_code)
                        
                        # Run forecast on similar sensor data
                        proxy_predictions = self.statistical_forecast(similar_data, hours_ahead)
                        
                        # Adjust with correlation factor
                        predictions = []
                        for pred in proxy_predictions:
                            adjusted_pred = pred.copy()
                            adjusted_pred['forecast_value'] *= correlation_factor
                            adjusted_pred['confidence_score'] *= 0.7  # Reduce confidence for proxy
                            adjusted_pred['method'] = f'proxy_model_{similar_sensor_code}'
                            predictions.append(adjusted_pred)
                        
                        method_used = f'proxy_model_{similar_sensor_code}'
                        fallback_reason = 'using_similar_sensor'
                        logger.info(f"Using proxy model from {similar_sensor_code} for {sensor_code}")
            
            # Strategy 3: Persistence model as last resort
            if predictions is None:
                if historical_data is not None and len(historical_data) > 0:
                    current_value = historical_data.iloc[-1]['value']
                else:
                    # Use default value based on parameter type
                    current_value = self.get_default_value_by_parameter(sensor_info['parameter'])
                
                predictions = self.persistence_forecast(current_value, hours_ahead)
                method_used = 'persistence_fallback'
                fallback_reason = 'insufficient_data'
                logger.info(f"Using persistence model for {sensor_code}")
            
            if predictions:
                return {
                    'success': True,
                    'sensor_code': sensor_code,
                    'predictions': predictions,
                    'method': method_used,
                    'fallback_reason': fallback_reason,
                    'sensor_info': sensor_info,
                    'confidence_note': 'Fallback predictions have lower confidence than ML models'
                }
            else:
                return {
                    'success': False,
                    'error': 'All fallback methods failed',
                    'sensor_code': sensor_code,
                    'method': 'fallback_error'
                }
                
        except Exception as e:
            logger.error(f"Fallback forecast failed for {sensor_code}: {e}")
            return {
                'success': False,
                'error': str(e),
                'sensor_code': sensor_code,
                'method': 'fallback_error'
            }