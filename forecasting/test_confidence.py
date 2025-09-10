#!/usr/bin/env python3
"""Test confidence scoring functionality in detail."""

import os
import sys

# Add the app directory to the path
sys.path.insert(0, os.path.join(os.path.dirname(__file__), 'app'))

from app.db import init_engine, init_session
from app.config import Settings
from app.forecast import predict_for_sensor
from app.confidence import ConfidenceScorer
from app.models import MasSensors
from sqlalchemy import select
import numpy as np

def get_test_session():
    """Create a test database session."""
    settings = Settings()
    engine = init_engine(settings)
    Session = init_session(engine)
    return Session(), settings

def test_confidence_details():
    """Test confidence scoring with detailed breakdown."""
    print("🔍 Testing Confidence Score Details")
    print("=" * 50)
    
    session, settings = get_test_session()
    
    try:
        # Get a few test sensors
        query = select(MasSensors).where(MasSensors.mas_model_code.isnot(None)).limit(3)
        sensors = session.execute(query).scalars().all()
        
        scorer = ConfidenceScorer()
        
        for sensor in sensors:
            try:
                print(f"\n📊 Testing {sensor.sensor_code}:")
                print(f"   Model: {sensor.mas_model_code}")
                print(f"   Parameter: {sensor.parameter}")
                
                # Run prediction
                result = predict_for_sensor(session, settings, sensor.sensor_code)
                
                if result and 'confidence_score' in result:
                    confidence = result['confidence_score']
                    
                    print(f"   📈 Overall Confidence: {confidence:.3f}")
                    
                    # Show confidence interpretation
                    if confidence >= 0.8:
                        print(f"   🟢 HIGH confidence - Predictions are highly reliable")
                    elif confidence >= 0.6:
                        print(f"   🟡 MEDIUM confidence - Predictions are reasonably reliable")
                    elif confidence >= 0.4:
                        print(f"   🟠 LOW confidence - Predictions should be used with caution")
                    else:
                        print(f"   🔴 VERY LOW confidence - Predictions are unreliable")
                    
                    # Show sample predictions with confidence
                    print(f"   🎯 Sample Forecasts:")
                    for pred in result.get('predictions', [])[:3]:
                        forecast_time = pred['forecast_time'][:16]  # Remove seconds
                        value = pred['forecast_value']
                        status = pred['threshold_status']
                        print(f"      {forecast_time}: {value:.2f} ({status}) - Confidence: {confidence:.1%}")
                    
                else:
                    print(f"   ❌ Prediction failed")
                    
            except Exception as e:
                print(f"   ❌ Error: {e}")
    
    finally:
        session.close()
    
    print(f"\n🎊 Confidence Scoring Test Complete!")
    print(f"\n📚 Confidence Score Components:")
    print(f"   • Data Quality (25%): Completeness and outlier detection")
    print(f"   • Model Consistency (20%): Smoothness of predictions")
    print(f"   • Historical Accuracy (25%): Past model performance")
    print(f"   • Input Stability (15%): Variance in input features")
    print(f"   • Prediction Variance (15%): Consistency of outputs")

def test_confidence_components():
    """Test individual confidence components."""
    print(f"\n🧪 Testing Individual Confidence Components")
    print("=" * 50)
    
    scorer = ConfidenceScorer()
    
    # Create test data
    test_input = np.array([
        [1.0, 2.0, 3.0, 1.5],
        [1.1, 2.1, 3.1, 1.6],
        [1.2, 2.0, 3.0, 1.4],
        [1.0, 2.2, 3.2, 1.7],
        [1.1, 1.9, 2.9, 1.3]
    ])  # (5 time_steps, 4 features)
    
    test_predictions = np.array([2.1, 2.0, 2.2, 2.1, 2.3])
    
    print(f"📊 Test Input Shape: {test_input.shape}")
    print(f"📊 Test Predictions Shape: {test_predictions.shape}")
    
    # Test individual components
    components = {
        'Data Quality': scorer._calculate_data_quality_score(test_input),
        'Model Consistency': scorer._calculate_model_consistency_score(test_predictions),
        'Input Stability': scorer._calculate_input_stability_score(test_input),
        'Prediction Variance': scorer._calculate_prediction_variance_score(test_predictions)
    }
    
    print(f"\n🔍 Component Scores:")
    for component, score in components.items():
        print(f"   {component}: {score:.3f}")
        if score >= 0.8:
            print(f"      🟢 Excellent")
        elif score >= 0.6:
            print(f"      🟡 Good")
        elif score >= 0.4:
            print(f"      🟠 Fair")
        else:
            print(f"      🔴 Poor")

if __name__ == "__main__":
    test_confidence_details()
    test_confidence_components()
