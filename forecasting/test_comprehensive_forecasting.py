#!/usr/bin/env python3
"""
Comprehensive test for FFWS forecasting with detailed confidence analysis.
This script tests multiple sensors and shows confidence score breakdowns.
"""

import os
import sys
from dotenv import load_dotenv

# Add the app directory to the path
sys.path.insert(0, os.path.join(os.path.dirname(__file__), 'app'))

from app.db import init_engine, init_session
from app.config import Settings
from app.forecast import predict_for_sensor
from app.confidence import ConfidenceScorer
from app.models import MasSensors, DataActual
from sqlalchemy import select, func, desc
import numpy as np
from datetime import datetime

def test_comprehensive_forecasting():
    """Test forecasting with comprehensive confidence analysis."""
    print("🚀 COMPREHENSIVE FFWS FORECASTING & CONFIDENCE TEST")
    print("=" * 70)
    
    # Load environment using dotenv
    load_dotenv()
    
    # Initialize database
    settings = Settings()
    engine = init_engine(settings)
    Session = init_session(engine)
    session = Session()
    
    try:
        print(f"📊 Database: {settings.DB_NAME} on {settings.DB_HOST}:{settings.DB_PORT}")
        
        # Get available sensors with models
        query = select(MasSensors).where(MasSensors.mas_model_code.isnot(None)).limit(10)
        sensors = session.execute(query).scalars().all()
        
        if not sensors:
            print("❌ No sensors with models found!")
            return
        
        print(f"📡 Found {len(sensors)} sensors with assigned models")
        
        # Check data availability
        total_data = session.execute(select(func.count(DataActual.id))).scalar()
        print(f"💾 Historical data points: {total_data:,}")
        
        print(f"\n{'='*70}")
        print("🧪 TESTING FORECASTING & CONFIDENCE SCORES")
        print("="*70)
        
        successful_tests = []
        failed_tests = []
        confidence_scores = []
        
        for i, sensor in enumerate(sensors, 1):
            print(f"\n📊 Test {i}/{len(sensors)}: {sensor.sensor_code}")
            print(f"   🏷️  Model: {sensor.mas_model_code}")
            print(f"   📏 Parameter: {sensor.parameter}")
            print(f"   📍 Device: {sensor.mas_device_code or 'Unknown'}")
            
            try:
                # Test prediction
                result = predict_for_sensor(session, settings, sensor.sensor_code)
                
                if result:
                    confidence = result.get('confidence_score', 0.0)
                    confidence_scores.append(confidence)
                    
                    # Interpret confidence level
                    if confidence >= 0.8:
                        level = "🟢 HIGH"
                        interpretation = "Highly reliable"
                    elif confidence >= 0.6:
                        level = "🟡 MEDIUM"
                        interpretation = "Reasonably reliable"
                    elif confidence >= 0.4:
                        level = "🟠 LOW"
                        interpretation = "Use with caution"
                    else:
                        level = "🔴 VERY LOW"
                        interpretation = "Unreliable"
                    
                    print(f"   ✅ SUCCESS!")
                    print(f"   📊 Confidence: {confidence:.3f} ({confidence:.1%}) - {level}")
                    print(f"   💡 Interpretation: {interpretation}")
                    print(f"   🎯 Predictions: {result.get('rows_inserted', 0)} time steps")
                    
                    # Show sample forecasts
                    predictions = result.get('predictions', [])
                    if predictions:
                        print(f"   📈 Sample Forecasts:")
                        for j, pred in enumerate(predictions[:3], 1):
                            time_str = pred['forecast_time'][:16]
                            value = pred['forecast_value']
                            status = pred['threshold_status']
                            print(f"      {j}. {time_str}: {value:.2f} ({status})")
                    
                    successful_tests.append({
                        'sensor': sensor.sensor_code,
                        'model': sensor.mas_model_code,
                        'confidence': confidence,
                        'level': level.split()[1],
                        'predictions': len(predictions)
                    })
                    
                else:
                    print(f"   ❌ FAILED: No result returned")
                    failed_tests.append({
                        'sensor': sensor.sensor_code,
                        'error': 'No result returned'
                    })
                    
            except Exception as e:
                error_msg = str(e)[:60] + "..." if len(str(e)) > 60 else str(e)
                print(f"   ❌ ERROR: {error_msg}")
                failed_tests.append({
                    'sensor': sensor.sensor_code,
                    'error': error_msg
                })
        
        # Summary
        print(f"\n{'='*70}")
        print("📋 TEST SUMMARY")
        print("="*70)
        
        total_tests = len(successful_tests) + len(failed_tests)
        success_rate = len(successful_tests) / total_tests * 100 if total_tests > 0 else 0
        
        print(f"🎯 Total Tests: {total_tests}")
        print(f"✅ Successful: {len(successful_tests)}")
        print(f"❌ Failed: {len(failed_tests)}")
        print(f"📊 Success Rate: {success_rate:.1f}%")
        
        if confidence_scores:
            avg_confidence = np.mean(confidence_scores)
            min_confidence = min(confidence_scores)
            max_confidence = max(confidence_scores)
            
            print(f"\n📊 CONFIDENCE STATISTICS:")
            print(f"   Average: {avg_confidence:.3f} ({avg_confidence:.1%})")
            print(f"   Range: {min_confidence:.3f} - {max_confidence:.3f}")
            print(f"   Distribution:")
            
            high_conf = sum(1 for c in confidence_scores if c >= 0.8)
            med_conf = sum(1 for c in confidence_scores if 0.6 <= c < 0.8)
            low_conf = sum(1 for c in confidence_scores if 0.4 <= c < 0.6)
            very_low_conf = sum(1 for c in confidence_scores if c < 0.4)
            
            print(f"     🟢 HIGH (≥0.8): {high_conf}")
            print(f"     🟡 MEDIUM (0.6-0.79): {med_conf}")
            print(f"     🟠 LOW (0.4-0.59): {low_conf}")
            print(f"     🔴 VERY LOW (<0.4): {very_low_conf}")
        
        if successful_tests:
            print(f"\n✅ SUCCESSFUL PREDICTIONS:")
            for test in successful_tests:
                print(f"   • {test['sensor']}: {test['confidence']:.3f} ({test['level']}) - {test['predictions']} forecasts")
        
        if failed_tests:
            print(f"\n❌ FAILED TESTS:")
            for test in failed_tests[:5]:  # Show first 5 failures
                print(f"   • {test['sensor']}: {test['error']}")
            if len(failed_tests) > 5:
                print(f"   ... and {len(failed_tests) - 5} more")
        
        # Final assessment
        if success_rate >= 50:
            print(f"\n🎉 SYSTEM STATUS: OPERATIONAL")
            print(f"   The forecasting system with confidence scores is working well!")
        elif success_rate >= 25:
            print(f"\n⚠️  SYSTEM STATUS: PARTIALLY OPERATIONAL")
            print(f"   Some forecasts are working, but improvements needed.")
        else:
            print(f"\n🔧 SYSTEM STATUS: NEEDS ATTENTION")
            print(f"   Most forecasts are failing, system needs debugging.")
    
    except Exception as e:
        print(f"❌ Test setup failed: {e}")
    
    finally:
        session.close()

def demonstrate_confidence_components():
    """Demonstrate how confidence score components work."""
    print(f"\n{'='*70}")
    print("🧮 CONFIDENCE SCORE COMPONENTS DEMONSTRATION")
    print("="*70)
    
    scorer = ConfidenceScorer()
    
    print(f"📊 Confidence Score Weights:")
    for component, weight in scorer.weights.items():
        print(f"   • {component.replace('_', ' ').title()}: {weight:.1%}")
    
    # Test with sample data
    print(f"\n🧪 Testing with Sample Data:")
    
    # Create sample input data (5 time steps, 3 features)
    sample_input = np.array([
        [1.2, 25.5, 0.8],
        [1.3, 25.7, 0.9],
        [1.1, 25.3, 0.7],
        [1.4, 25.9, 1.0],
        [1.2, 25.6, 0.8]
    ])
    
    # Sample predictions
    sample_predictions = np.array([1.3, 1.2, 1.4])
    
    print(f"   Input shape: {sample_input.shape} (time_steps=5, features=3)")
    print(f"   Predictions shape: {sample_predictions.shape}")
    
    # Test individual components
    data_quality = scorer._calculate_data_quality_score(sample_input)
    model_consistency = scorer._calculate_model_consistency_score(sample_predictions)
    input_stability = scorer._calculate_input_stability_score(sample_input)
    prediction_variance = scorer._calculate_prediction_variance_score(sample_predictions)
    
    print(f"\n📈 Component Scores:")
    print(f"   Data Quality: {data_quality:.3f}")
    print(f"   Model Consistency: {model_consistency:.3f}")
    print(f"   Input Stability: {input_stability:.3f}")
    print(f"   Prediction Variance: {prediction_variance:.3f}")
    
    # Calculate overall
    overall = (data_quality * scorer.weights['data_quality'] +
               model_consistency * scorer.weights['model_consistency'] +
               0.6 * scorer.weights['historical_accuracy'] +  # Default for no history
               input_stability * scorer.weights['input_stability'] +
               prediction_variance * scorer.weights['prediction_variance'])
    
    print(f"   Overall (estimated): {overall:.3f}")

if __name__ == "__main__":
    test_comprehensive_forecasting()
    demonstrate_confidence_components()
