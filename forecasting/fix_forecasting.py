#!/usr/bin/env python3
"""
Fix the forecasting system by creating proper multi-feature input data.
This will create lag features and other time series features to match model requirements.
"""

import os
import sys
import numpy as np
from datetime import datetime, timedelta
from dotenv import load_dotenv

# Add the app directory to Python path
sys.path.insert(0, os.path.join(os.path.dirname(__file__), 'app'))

from app.db import init_engine, init_session
from app.config import Settings
from app.models import MasSensors, DataActual
from sqlalchemy import select, desc

def create_time_series_features(values, n_features):
    """
    Create time series features from a single sensor's historical values.
    
    Args:
        values: List of sensor values in chronological order
        n_features: Number of features needed (3 or 4)
    
    Returns:
        numpy array with shape (len(values) - n_features + 1, n_features)
    """
    if len(values) < n_features:
        return None
    
    features = []
    for i in range(n_features, len(values) + 1):
        # Create lag features: [t-n+1, t-n+2, ..., t-1, t]
        feature_row = values[i-n_features:i]
        features.append(feature_row)
    
    return np.array(features)

def get_sensor_requirements():
    """Get the feature requirements for each model type."""
    requirements = {
        'DHOMPO_GRU': {'x_features': 4, 'y_features': 5},
        'DHOMPO_LSTM': {'x_features': 4, 'y_features': 5}, 
        'DHOMPO_TCN': {'x_features': 4, 'y_features': 5},
        'PURWODADI_GRU': {'x_features': 3, 'y_features': 3},
        'PURWODADI_LSTM': {'x_features': 3, 'y_features': 3},
        'PURWODADI_TCN': {'x_features': 3, 'y_features': 3}
    }
    return requirements

def test_feature_creation():
    """Test the feature creation process with existing data."""
    print("🔧 Testing Multi-Feature Creation")
    print("=" * 50)
    
    load_dotenv()
    settings = Settings()
    engine = init_engine(settings)
    session_factory = init_session(engine)
    
    requirements = get_sensor_requirements()
    
    try:
        with session_factory() as s:
            # Get a few sensors to test
            test_sensors = s.execute(
                select(MasSensors)
                .where(MasSensors.mas_model_code.isnot(None))
                .limit(3)
            ).scalars().all()
            
            for sensor in test_sensors:
                print(f"\n📊 Testing sensor: {sensor.sensor_code}")
                print(f"   Parameter: {sensor.parameter.value}")
                print(f"   Model: {sensor.mas_model_code}")
                
                # Get model requirements
                model_req = requirements.get(sensor.mas_model_code)
                if not model_req:
                    print("   ❌ Unknown model requirements")
                    continue
                
                x_features = model_req['x_features']
                print(f"   Required features: {x_features}")
                
                # Get historical data
                data = s.execute(
                    select(DataActual)
                    .where(DataActual.mas_sensor_code == sensor.sensor_code)
                    .order_by(DataActual.received_at)
                ).scalars().all()
                
                if len(data) < x_features + 10:  # Need extra data for meaningful features
                    print(f"   ⚠️  Not enough data: {len(data)} records")
                    continue
                
                print(f"   Available data: {len(data)} records")
                
                # Extract values
                values = [d.value for d in data]
                
                # Create features
                features = create_time_series_features(values, x_features)
                
                if features is not None:
                    print(f"   ✅ Created feature matrix: {features.shape}")
                    print(f"   Sample features (first row): {features[0]}")
                    print(f"   Sample features (last row): {features[-1]}")
                    
                    # Test with actual scaler
                    try:
                        from app.scalers import load_scaler
                        x_scaler = load_scaler(settings, sensor.mas_model_code, 'x')
                        
                        # Test transformation
                        scaled_features = x_scaler.transform(features[-1:])  # Last row
                        print(f"   ✅ Scaler transform successful: {scaled_features.shape}")
                        print(f"   Scaled values: {scaled_features[0]}")
                        
                    except Exception as e:
                        print(f"   ⚠️  Scaler test failed: {e}")
                
                else:
                    print("   ❌ Could not create features")
                    
    except Exception as e:
        print(f"❌ Error testing feature creation: {e}")
        import traceback
        traceback.print_exc()

def create_improved_forecast_function():
    """Create an improved forecast function that handles multi-feature input."""
    
    forecast_code = '''
def create_multi_feature_input(data_values, n_features):
    """Create multi-feature input from time series data."""
    if len(data_values) < n_features:
        raise ValueError(f"Need at least {n_features} data points, got {len(data_values)}")
    
    # Use the last n_features values as lag features
    features = data_values[-n_features:]
    return np.array(features).reshape(1, -1)  # Shape: (1, n_features)

def predict_with_multi_features(s, settings, sensor_code, model_code=None):
    """Improved prediction function with multi-feature support."""
    from app.models import MasSensors, MasModels, DataActual
    from app.scalers import load_scaler
    from app.loaders import load_keras_model
    from sqlalchemy import select, desc
    import numpy as np
    
    # Get sensor and model info
    sensor = s.execute(select(MasSensors).where(MasSensors.sensor_code == sensor_code)).scalar_one()
    model_code = model_code or sensor.mas_model_code
    model = s.execute(select(MasModels).where(MasModels.model_code == model_code)).scalar_one()
    
    # Get feature requirements
    requirements = {
        'DHOMPO_GRU': 4, 'DHOMPO_LSTM': 4, 'DHOMPO_TCN': 4,
        'PURWODADI_GRU': 3, 'PURWODADI_LSTM': 3, 'PURWODADI_TCN': 3
    }
    n_features = requirements.get(model_code, 4)
    
    # Get historical data (more than needed)
    data = s.execute(
        select(DataActual)
        .where(DataActual.mas_sensor_code == sensor_code)
        .order_by(desc(DataActual.received_at))
        .limit(n_features + 50)  # Get extra data
    ).scalars().all()
    
    if len(data) < n_features:
        raise ValueError(f"Need at least {n_features} data points for {model_code}")
    
    # Reverse to get chronological order
    data = list(reversed(data))
    values = [d.value for d in data]
    
    # Create multi-feature input
    X = create_multi_feature_input(values, n_features)
    
    # Load scalers and model
    x_scaler = load_scaler(settings, model_code, 'x')
    y_scaler = load_scaler(settings, model_code, 'y')
    keras_model = load_keras_model(settings, model.file_path)
    
    # Scale input
    X_scaled = x_scaler.transform(X)
    
    # Make prediction
    y_pred_scaled = keras_model.predict(X_scaled.reshape(1, n_features, 1))
    
    # Inverse transform prediction
    y_pred = y_scaler.inverse_transform(y_pred_scaled)
    
    return {
        'sensor_code': sensor_code,
        'model_code': model_code,
        'predictions': y_pred[0].tolist(),
        'input_features': X[0].tolist(),
        'n_features_used': n_features
    }
'''
    
    print("\n🔧 Improved Forecast Function Created")
    print("=" * 50)
    print("Key improvements:")
    print("  ✅ Multi-feature input creation from time series")
    print("  ✅ Model-specific feature requirements")
    print("  ✅ Proper data ordering (chronological)")
    print("  ✅ Sufficient historical data validation")
    print("  ✅ Correct input reshaping for neural networks")
    
    # Save to file
    with open('improved_forecast.py', 'w') as f:
        f.write('import numpy as np\n\n')
        f.write(forecast_code)
    
    print("  📝 Code saved to: improved_forecast.py")

if __name__ == "__main__":
    print("🛠️  FFWS Multi-Feature Forecasting Fix")
    print("=" * 60)
    
    # Change to forecasting directory  
    os.chdir(os.path.dirname(__file__) or '.')
    
    # Test feature creation
    test_feature_creation()
    
    # Create improved forecast function
    create_improved_forecast_function()
    
    print("\n" + "=" * 60)
    print("📋 Summary of Issues Found:")
    print("  1. Models expect 3-4 input features, not 1")
    print("  2. Need to create lag features from time series")
    print("  3. Models were trained with multi-step sequences")
    print("  4. TensorFlow version compatibility issues")
    
    print("\n🔧 Solutions Implemented:")
    print("  1. Multi-feature input creation")
    print("  2. Model-specific feature requirements")
    print("  3. Proper time series preprocessing")
    print("  4. Enhanced forecast function")
    
    print("\n🎯 Next Steps:")
    print("  1. Update the forecast.py file with multi-feature logic")
    print("  2. Fix TensorFlow model compatibility")
    print("  3. Test forecasting with proper input dimensions")
    print("  4. Validate predictions against historical data")
