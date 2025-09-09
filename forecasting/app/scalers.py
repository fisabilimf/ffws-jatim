from sqlalchemy import text
from joblib import load as joblib_load
from .utils import normalize_paths
from .config import Settings

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
    x_path = _pick_scaler_row(session, model_code, sensor_code, "x")
    y_path = _pick_scaler_row(session, model_code, sensor_code, "y")
    x_scaler = joblib_load(normalize_paths(settings.MODELS_BASE_DIR, x_path)) if x_path else None
    y_scaler = joblib_load(normalize_paths(settings.MODELS_BASE_DIR, y_path)) if y_path else None
    return x_scaler, y_scaler
