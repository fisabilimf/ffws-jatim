from datetime import datetime, timedelta
import numpy as np
import pandas as pd
from sqlalchemy import select, desc
from sqlalchemy.orm import Session

from .models import MasSensors, MasModels, DataActual, DataPrediction, MasDevices
from .utils import ensure_3d_batch
from .loaders import load_keras_model_with_settings
from .scalers import load_scalers
from .thresholds import classify_threshold
from .confidence import calculate_confidence_score
from .config import Settings
from .preprocess import preprocess_series, PreprocessOptions

class ForecastError(Exception): pass

def get_model_feature_requirements():
    """Get the feature requirements for each model type."""
    return {
        'DHOMPO_GRU': {'x_features': 4, 'y_features': 5},
        'DHOMPO_LSTM': {'x_features': 4, 'y_features': 5}, 
        'DHOMPO_TCN': {'x_features': 4, 'y_features': 5},
        'PURWODADI_GRU': {'x_features': 3, 'y_features': 3},
        'PURWODADI_LSTM': {'x_features': 3, 'y_features': 3},
        'PURWODADI_TCN': {'x_features': 3, 'y_features': 3}
    }

def create_sequence_input(values, n_time_steps, n_features):
    """
    Create sequence input that matches the model's expected shape.
    Models expect: (batch_size, time_steps, features)
    
    Args:
        values: List of sensor values in chronological order
        n_time_steps: Number of time steps expected by model (e.g., 5)
        n_features: Number of features expected by model (e.g., 4)
    
    Returns:
        numpy array with shape (1, n_time_steps, n_features)
    """
    if len(values) < n_time_steps:
        raise ValueError(f"Need at least {n_time_steps} data points for sequence, got {len(values)}")
    
    # Take the last n_time_steps values
    sequence_values = values[-n_time_steps:]
    
    # Create features for each time step
    sequence_input = []
    for i in range(n_time_steps):
        # For each time step, create n_features by using lag values
        time_step_features = []
        
        # Use current and previous values as features
        for j in range(n_features):
            if i - j >= 0:
                time_step_features.append(sequence_values[i - j])
            else:
                # Pad with the first available value if we don't have enough history
                time_step_features.append(sequence_values[0])
        
        sequence_input.append(time_step_features)
    
    return np.array(sequence_input).reshape(1, n_time_steps, n_features)

def _fetch_series_multi_feature(session: Session, sensor_code: str, n_features: int, min_extra: int = 50) -> list:
    """
    Fetch historical data for multi-feature input creation.
    Returns chronologically ordered values (oldest first).
    """
    # Fetch more data than needed to ensure we have enough for feature creation
    q = (
        select(DataActual.value, DataActual.received_at)
        .where(DataActual.mas_sensor_code == sensor_code)
        .order_by(desc(DataActual.received_at))
        .limit(n_features + min_extra)
    )
    rows = session.execute(q).all()
    if not rows:
        raise ForecastError("No data points found")
    
    # Reverse to get chronological order (oldest first)
    rows = list(reversed(rows))
    values = [row.value for row in rows]
    
    if len(values) < n_features:
        raise ForecastError(f"Not enough data: need {n_features}, got {len(values)}")
    
    return values

def _get_sensor_and_model(session: Session, sensor_code: str, model_code: str | None):
    # Get sensor by sensor_code
    q_sensor = select(MasSensors).where(MasSensors.sensor_code == sensor_code)
    sensor = session.execute(q_sensor).scalar_one_or_none()
    if sensor is None:
        raise ForecastError("Sensor not found")
    
    # Get model by model_code
    if model_code is None:
        if sensor.mas_model_code is None:
            raise ForecastError("Sensor has no default model assigned")
        model_code = sensor.mas_model_code
    
    q_model = select(MasModels).where(MasModels.model_code == model_code)
    model = session.execute(q_model).scalar_one_or_none()
    if model is None:
        raise ForecastError("Model not found")
    
    if not model.file_path:
        raise ForecastError("Model file_path missing")
    return sensor, model

def predict_for_sensor(session: Session, settings: Settings, sensor_code: str, model_code: str | None = None, 
                      prediction_hours: int = 5, step_hours: float = 1.0) -> dict:
    """
    Improved prediction function with multi-feature support and configurable time intervals.
    
    Args:
        session: Database session
        settings: Application settings
        sensor_code: Sensor identifier
        model_code: Model to use (optional, uses sensor's default if None)
        prediction_hours: Number of hours to predict into the future (default: 5)
        step_hours: Time step between predictions in hours (default: 1.0)
    
    Returns:
        Dictionary with prediction results and confidence scores
    """
    sensor, model = _get_sensor_and_model(session, sensor_code, model_code)
    
    # Get model feature requirements
    requirements = get_model_feature_requirements()
    model_req = requirements.get(model.model_code)
    
    if not model_req:
        raise ForecastError(f"Unknown model requirements for {model.model_code}")
    
    n_features = model_req['x_features']
    
    # Fetch historical data for multi-feature input
    try:
        values = _fetch_series_multi_feature(session, sensor_code, n_features)
    except ForecastError:
        raise
    
    # Create sequence input that matches model's expected shape
    # Models expect: (batch_size, time_steps, features) = (1, 5, 4) for DHOMPO
    try:
        # From model analysis: DHOMPO expects (1, 5, 4), PURWODADI expects (1, 5, 3)
        n_time_steps = 5  # All models use 5 time steps
        X = create_sequence_input(values, n_time_steps, n_features)
        print(f"   ✅ Created sequence input: {X.shape}")
    except ValueError as e:
        raise ForecastError(str(e))
    
    # Load scalers
    x_scaler, y_scaler = load_scalers(session, settings, model.model_code, sensor.sensor_code)
    
    if x_scaler is None:
        raise ForecastError(f"No X scaler found for model {model.model_code}")
    
    # Scale input features
    try:
        # X is now shape (1, 5, n_features), but scaler expects (n_samples, n_features)
        # We need to reshape for scaling, then reshape back
        X_reshaped = X.reshape(-1, n_features)  # (5, n_features)
        X_scaled_reshaped = x_scaler.transform(X_reshaped)  # (5, n_features)
        X_scaled = X_scaled_reshaped.reshape(1, 5, n_features)  # (1, 5, n_features)
        print(f"   ✅ Scaled input: {X_scaled.shape}")
    except Exception as e:
        raise ForecastError(f"Input scaling failed: {e}")
    
    # Load and run model
    try:
        if not model.file_path:
            raise ForecastError("Model file_path is None")
        mdl = load_keras_model_with_settings(settings, model.file_path)
    except Exception as e:
        raise ForecastError(f"Model loading failed: {e}")
    
    # Prepare input for neural network
    try:
        # Models expect: (batch_size, time_steps, features)
        # X_scaled is already in the correct shape: (1, 5, n_features)
        X_input = X_scaled
        
        print(f"   🔧 Final input shape for model: {X_input.shape}")
        
        # Make prediction
        yhat = mdl.predict(X_input, verbose=0)
        yhat = np.asarray(yhat).astype(np.float32)
        print(f"   ✅ Model prediction shape: {yhat.shape}")
    except Exception as e:
        raise ForecastError(f"Model prediction failed: {e}")
    
    # Normalize prediction shape and handle sequence outputs
    print(f"   🔧 Raw prediction shape: {yhat.shape}")
    
    # Handle different output shapes from sequence models
    if yhat.ndim == 3:  # (1, time_steps, features) - sequence-to-sequence output
        yhat = yhat[0]  # Remove batch dimension -> (time_steps, features)
        
        # For y-scaler compatibility, we need to reshape correctly
        # DHOMPO models expect (1, 5), PURWODADI models expect (1, 3)
        if yhat.shape[1] == 1:  # (time_steps, 1) -> (1, time_steps)
            yhat = yhat.T  # Transpose to (1, time_steps)
        
    elif yhat.ndim == 2:
        if yhat.shape[0] == 1:  # (1, n) - keep as is for scaler
            pass  
        elif yhat.shape[1] == 1:  # (n, 1) - transpose for scaler
            yhat = yhat.T
            
    print(f"   🔧 Normalized prediction shape: {yhat.shape}")
    
    # Inverse transform predictions
    if y_scaler is not None:
        try:
            # The scaler expects specific shapes:
            # DHOMPO: (n_samples, 5_features)
            # PURWODADI: (n_samples, 3_features)
            print(f"   🔧 Y-scaler expects {y_scaler.n_features_in_} features")
            
            if yhat.shape[1] != y_scaler.n_features_in_:
                raise ValueError(f"Prediction shape {yhat.shape} doesn't match y_scaler expected features {y_scaler.n_features_in_}")
            
            print(f"   🔧 Scaling input shape: {yhat.shape}")
            yhat_scaled = y_scaler.inverse_transform(yhat)
            print(f"   ✅ Scaled output shape: {yhat_scaled.shape}")
            
            # Convert back to 1D array for final processing
            yhat = yhat_scaled.reshape(-1)
            print(f"   ✅ Final prediction shape: {yhat.shape}")
            
        except Exception as e:
            raise ForecastError(f"Output scaling failed: {e}")
    
    # Calculate confidence score for the predictions
    try:
        # Get feature names for confidence calculation
        feature_names = None
        if model_req and 'features' in model_req:
            # Create generic feature names based on requirements
            n_features = model_req.get('x_features', 4)
            feature_names = [f'feature_{i+1}' for i in range(n_features)]
        
        confidence = calculate_confidence_score(
            session=session,
            sensor_code=sensor_code,
            model_code=model.model_code,
            input_data=X,  # Original input data before scaling
            predictions=yhat,
            feature_names=feature_names
        )
        print(f"   📊 Confidence score: {confidence:.3f}")
    except Exception as e:
        print(f"   ⚠️  Confidence calculation failed: {e}")
        confidence = 0.5  # Default neutral confidence
    
    # Create prediction records
    now = datetime.utcnow()
    
    # Use the last timestamp from our data as base
    latest_data = session.execute(
        select(DataActual.received_at)
        .where(DataActual.mas_sensor_code == sensor_code)
        .order_by(desc(DataActual.received_at))
        .limit(1)
    ).scalar_one()
    
    base_ts = latest_data
    step_minutes = int(step_hours * 60)  # Convert hours to minutes
    max_predictions = int(prediction_hours / step_hours)  # Calculate number of predictions needed
    
    # Create prediction records for specified time horizon
    n_steps_out = min(len(yhat), max_predictions)  # Use available predictions or requested amount
    rows_out = []
    
    for i in range(n_steps_out):
        pred_ts = base_ts + timedelta(minutes=step_minutes * (i + 1))
        val = float(yhat[i] if i < len(yhat) else yhat[-1])
        
        status = classify_threshold(val, sensor.threshold_safe, sensor.threshold_warning, sensor.threshold_danger)
        rows_out.append(DataPrediction(
            mas_sensor_code=sensor.sensor_code,
            mas_model_code=model.model_code,
            prediction_run_at=now,
            prediction_for_ts=pred_ts,
            predicted_value=val,
            confidence_score=confidence,
            threshold_prediction_status=status,
            created_at=now,
            updated_at=now
        ))
    
    session.add_all(rows_out)
    session.commit()

    return {
        "sensor_code": sensor.sensor_code,
        "model_code": model.model_code,
        "model_type": model.model_type,
        "model_algorithm": model.model_type,
        "step_minutes": step_minutes,
        "step_hours": step_hours,
        "prediction_horizon_hours": prediction_hours,
        "rows_inserted": len(rows_out),
        "input_features_used": n_features,
        "confidence_score": confidence,
        "predictions": [
            {
                "forecast_time": row.prediction_for_ts.isoformat(),
                "forecast_value": row.predicted_value,
                "confidence_score": row.confidence_score,
                "threshold_status": row.threshold_prediction_status,
                "hours_ahead": step_hours * (i + 1)
            }
            for i, row in enumerate(rows_out[:5])  # Return first 5 predictions as sample
        ]
    }

def predict_for_basin(session: Session, settings: Settings, river_basin_code: str, only_active: bool = True) -> dict:
    """
    Jalankan forecast berurutan untuk seluruh sensor yang berada di mas_river_basins.code = river_basin_code.
    Mengembalikan ringkasan sukses/gagal per sensor.
    """
    # cari sensors lewat devices di basin tsb
    # hanya sensor yang punya model (sensor.mas_model_code IS NOT NULL)
    from sqlalchemy import text
    base_sql = """
        SELECT s.sensor_code AS sensor_code
        FROM mas_devices d
        JOIN mas_sensors s ON s.mas_device_code = d.device_code
        WHERE d.mas_river_basin_code = :rbcode
    """
    if only_active:
        base_sql += " AND d.status = 'active' AND s.status = 'active'"
    base_sql += " AND s.mas_model_code IS NOT NULL"

    sensors = [row[0] for row in session.execute(text(base_sql), {"rbcode": river_basin_code}).all()]

    summary = {"river_basin_code": river_basin_code, "total_sensors": len(sensors),
               "ok": 0, "failed": 0, "details": []}

    for sensor_code in sensors:
        try:
            result = predict_for_sensor(session, settings, sensor_code, None)
            summary["ok"] += 1
            summary["details"].append({"sensor_code": sensor_code, "status": "ok", "rows_inserted": result["rows_inserted"]})
        except ForecastError as e:
            summary["failed"] += 1
            summary["details"].append({"sensor_code": sensor_code, "status": "error", "error": str(e)})
        except Exception as e:
            summary["failed"] += 1
            summary["details"].append({"sensor_code": sensor_code, "status": "error", "error": f"internal: {e}"})
            # lanjut ke sensor berikutnya (jangan hentikan batch)
    return summary
