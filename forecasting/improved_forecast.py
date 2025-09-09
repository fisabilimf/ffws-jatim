import numpy as np


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
