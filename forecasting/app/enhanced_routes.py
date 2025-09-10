"""
Enhanced API routes with multi-parameter forecasting support.
Adds rainfall-enhanced predictions to the existing water level forecasting.
"""

from flask import Blueprint, request, jsonify
from sqlalchemy.orm import Session
from datetime import datetime
import logging

from .db import init_session_from_settings
from .config import Settings
from .forecast import predict_for_sensor
from .models import MasSensors, DataActual, MasDevices
from sqlalchemy import select, and_, desc
from datetime import timedelta
import numpy as np

# Create blueprint for enhanced routes
enhanced_bp = Blueprint('enhanced', __name__, url_prefix='/api/enhanced')

logger = logging.getLogger(__name__)

@enhanced_bp.route('/forecast/multi-parameter', methods=['POST'])
def forecast_multi_parameter():
    """
    Generate predictions using both water level and rainfall data.
    
    Request body:
    {
        "sensor_code": "SENSOR-2531",
        "prediction_hours": 5,
        "step_hours": 1.0,
        "include_rainfall": true
    }
    """
    try:
        data = request.get_json()
        sensor_code = data.get('sensor_code')
        prediction_hours = data.get('prediction_hours', 5)
        step_hours = data.get('step_hours', 1.0)
        include_rainfall = data.get('include_rainfall', True)
        
        if not sensor_code:
            return jsonify({'error': 'sensor_code is required'}), 400
        
        settings = Settings()
        session = init_session_from_settings(settings)()
        
        try:
            # Get base water level prediction
            result = predict_for_sensor(
                session, settings, sensor_code, None, 
                prediction_hours, step_hours
            )
            
            if not result:
                return jsonify({'error': 'Failed to generate base prediction'}), 500
            
            # Enhance with rainfall data if requested and available
            if include_rainfall:
                enhanced_result = enhance_with_rainfall_data(session, sensor_code, result)
                result.update(enhanced_result)
            
            # Add metadata
            result.update({
                'forecast_type': 'multi_parameter' if include_rainfall else 'water_level_only',
                'generated_at': datetime.now().isoformat(),
                'api_version': '2.0'
            })
            
            return jsonify(result), 200
        
        finally:
            session.close()
    
    except Exception as e:
        logger.error(f"Multi-parameter forecasting error: {e}")
        return jsonify({'error': 'internal_error', 'detail': str(e)}), 500

@enhanced_bp.route('/forecast/rainfall-impact', methods=['POST'])
def analyze_rainfall_impact():
    """
    Analyze how rainfall affects water level predictions.
    
    Request body:
    {
        "sensor_code": "SENSOR-2531",
        "hours_ahead": 5
    }
    """
    try:
        data = request.get_json()
        sensor_code = data.get('sensor_code')
        hours_ahead = data.get('hours_ahead', 5)
        
        if not sensor_code:
            return jsonify({'error': 'sensor_code is required'}), 400
        
        settings = Settings()
        session = init_session_from_settings(settings)()
        
        try:
            # Get sensor and device info
            sensor = session.execute(
                select(MasSensors).where(MasSensors.sensor_code == sensor_code)
            ).scalar_one_or_none()
            
            if not sensor:
                return jsonify({'error': 'Sensor not found'}), 404
            
            # Get rainfall impact analysis
            impact_analysis = get_rainfall_impact_analysis(session, sensor.mas_device_code)
            
            if not impact_analysis:
                return jsonify({
                    'sensor_code': sensor_code,
                    'has_rainfall_data': False,
                    'message': 'No rainfall sensors found at this location'
                }), 200
            
            # Calculate impact scenarios
            scenarios = []
            base_water_level = 5.0  # Example base level
            
            for hour in range(1, hours_ahead + 1):
                impact = calculate_rainfall_water_impact(
                    base_water_level, impact_analysis, hour
                )
                scenarios.append({
                    'hours_ahead': hour,
                    'base_water_level': base_water_level,
                    'rainfall_impact': impact['adjustment'],
                    'enhanced_level': base_water_level + impact['adjustment'],
                    'risk_level': impact['risk_level']
                })
            
            return jsonify({
                'sensor_code': sensor_code,
                'has_rainfall_data': True,
                'rainfall_analysis': impact_analysis,
                'impact_scenarios': scenarios,
                'generated_at': datetime.now().isoformat()
            }), 200
        
        finally:
            session.close()
    
    except Exception as e:
        logger.error(f"Rainfall impact analysis error: {e}")
        return jsonify({'error': 'internal_error', 'detail': str(e)}), 500

def enhance_with_rainfall_data(session: Session, sensor_code: str, base_result: dict) -> dict:
    """Enhance water level predictions with rainfall data."""
    
    # Get sensor and device
    sensor = session.execute(
        select(MasSensors).where(MasSensors.sensor_code == sensor_code)
    ).scalar_one_or_none()
    
    if not sensor:
        return {'rainfall_enhancement': 'sensor_not_found'}
    
    # Get rainfall context
    rainfall_context = get_rainfall_context(session, sensor.mas_device_code)
    
    if not rainfall_context['has_rainfall_data']:
        return {
            'rainfall_enhancement': 'no_rainfall_data',
            'rainfall_sensors_found': 0
        }
    
    # Enhance predictions
    enhanced_predictions = []
    base_predictions = base_result.get('predictions', [])
    
    for pred in base_predictions:
        base_value = pred['forecast_value']
        hours_ahead = pred.get('hours_ahead', 1)
        
        # Calculate rainfall impact
        impact = calculate_rainfall_water_impact(base_value, rainfall_context, hours_ahead)
        
        enhanced_value = base_value + impact['adjustment']
        enhanced_value = max(0.1, enhanced_value)  # Ensure positive
        
        # Re-classify threshold
        if enhanced_value > 12:
            status = 'danger'
        elif enhanced_value > 8:
            status = 'warning'
        elif enhanced_value > 5:
            status = 'alert'
        else:
            status = 'safe'
        
        enhanced_pred = pred.copy()
        enhanced_pred.update({
            'original_forecast': base_value,
            'forecast_value': enhanced_value,
            'threshold_status': status,
            'rainfall_adjustment': impact['adjustment'],
            'rainfall_risk_level': impact['risk_level']
        })
        
        enhanced_predictions.append(enhanced_pred)
    
    return {
        'rainfall_enhancement': 'applied',
        'rainfall_context': rainfall_context,
        'predictions': enhanced_predictions,
        'enhancement_summary': {
            'avg_adjustment': np.mean([p['rainfall_adjustment'] for p in enhanced_predictions]),
            'max_adjustment': max([p['rainfall_adjustment'] for p in enhanced_predictions]),
            'enhanced_predictions': len(enhanced_predictions)
        }
    }

def get_rainfall_context(session: Session, device_code: str) -> dict:
    """Get rainfall context for a device location."""
    
    # Find rainfall sensors at the same device
    rainfall_sensors = session.execute(
        select(MasSensors)
        .where(and_(
            MasSensors.mas_device_code == device_code,
            MasSensors.parameter == 'rainfall'
        ))
    ).scalars().all()
    
    if not rainfall_sensors:
        return {'has_rainfall_data': False}
    
    # Get recent rainfall data (last 24 hours)
    cutoff_time = datetime.now() - timedelta(hours=24)
    rainfall_data = []
    
    for sensor in rainfall_sensors:
        recent_rainfall = session.execute(
            select(DataActual)
            .where(and_(
                DataActual.mas_sensor_code == sensor.sensor_code,
                DataActual.received_at >= cutoff_time
            ))
            .order_by(DataActual.received_at.desc())
            .limit(10)
        ).scalars().all()
        
        rainfall_data.extend(recent_rainfall)
    
    if not rainfall_data:
        return {'has_rainfall_data': False}
    
    # Calculate rainfall statistics
    values = [d.value for d in rainfall_data]
    
    return {
        'has_rainfall_data': True,
        'rainfall_sensors': len(rainfall_sensors),
        'data_points': len(rainfall_data),
        'latest_rainfall': values[0] if values else 0,
        'avg_rainfall': np.mean(values),
        'total_rainfall_24h': sum(values),
        'max_rainfall': max(values),
        'latest_timestamp': max(d.received_at for d in rainfall_data).isoformat()
    }

def get_rainfall_impact_analysis(session: Session, device_code: str) -> dict:
    """Get detailed rainfall impact analysis."""
    rainfall_context = get_rainfall_context(session, device_code)
    
    if not rainfall_context['has_rainfall_data']:
        return None
    
    return rainfall_context

def calculate_rainfall_water_impact(base_water_level: float, rainfall_context: dict, hours_ahead: int) -> dict:
    """Calculate how rainfall impacts water level predictions."""
    
    recent_rain = rainfall_context.get('latest_rainfall', 0)
    total_rain = rainfall_context.get('total_rainfall_24h', 0)
    
    # Impact calculation based on rainfall intensity
    if recent_rain > 15:
        intensity_factor = 1.5
        risk_level = 'high'
    elif recent_rain > 10:
        intensity_factor = 1.2
        risk_level = 'moderate'
    elif recent_rain > 5:
        intensity_factor = 1.1
        risk_level = 'low'
    else:
        intensity_factor = 1.0
        risk_level = 'minimal'
    
    # Time decay factor
    time_decay = 1.0 / (1 + hours_ahead * 0.2)
    
    # Calculate adjustment
    immediate_impact = recent_rain * 0.1 * time_decay * intensity_factor
    cumulative_impact = total_rain * 0.02 * time_decay
    
    total_adjustment = immediate_impact + cumulative_impact
    
    return {
        'adjustment': total_adjustment,
        'immediate_impact': immediate_impact,
        'cumulative_impact': cumulative_impact,
        'risk_level': risk_level,
        'time_decay_factor': time_decay
    }
