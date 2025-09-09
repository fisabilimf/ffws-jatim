from sqlalchemy import text
from joblib import load as joblib_load
import os
from .utils import normalize_paths
from .config import Settings

def load_scaler_direct(scalers_dir: str, model_code: str, axis: str):
    """
    Load scaler directly from file system.
    This is a fallback when database lookup fails.
    """
    scaler_filename = f"{model_code.lower()}_{axis}_scaler.pkl"
    scaler_path = os.path.join(scalers_dir, scaler_filename)
    
    if os.path.exists(scaler_path):
        try:
            return joblib_load(scaler_path)
        except Exception as e:
            raise ValueError(f"Failed to load scaler {scaler_path}: {e}")
    else:
        raise ValueError(f"Scaler file not found: {scaler_path}")

def _pick_scaler_row(session, model_code: str, sensor_code: str | None, axis: str):
    sql = text("""
        SELECT file_path FROM mas_scalers
        WHERE mas_model_code=:model_code AND io_axis=:axis AND is_active=1
          AND (mas_sensor_code=:sensor_code OR mas_sensor_code IS NULL)
        ORDER BY mas_sensor_code DESC
        LIMIT 1
    """)
    row = session.execute(sql, {"model_code": model_code, "axis": axis, "sensor_code": sensor_code}).first()
    return None if not row else row[0]

def load_scalers(session, settings: Settings, model_code: str, sensor_code: str | None):
    # Try database first
    try:
        x_path = _pick_scaler_row(session, model_code, sensor_code, "x")
        y_path = _pick_scaler_row(session, model_code, sensor_code, "y")
        x_scaler = joblib_load(normalize_paths(settings.MODELS_BASE_DIR, x_path)) if x_path else None
        y_scaler = joblib_load(normalize_paths(settings.MODELS_BASE_DIR, y_path)) if y_path else None
        
        if x_scaler is not None and y_scaler is not None:
            return x_scaler, y_scaler
    except Exception:
        pass  # Fall through to direct loading
    
    # Fallback to direct file system loading
    try:
        scalers_dir = os.path.join(settings.MODELS_BASE_DIR, "..", "scalers")
        x_scaler = load_scaler_direct(scalers_dir, model_code, "x")
        y_scaler = load_scaler_direct(scalers_dir, model_code, "y")
        return x_scaler, y_scaler
    except Exception as e:
        raise ValueError(f"Failed to load scalers for {model_code}: {e}")
