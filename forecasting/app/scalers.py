from sqlalchemy import text
from joblib import load as joblib_load
from .utils import normalize_paths
from .config import Settings

def _pick_scaler_row(session, model_id: int, sensor_id: int | None, axis: str):
    sql = text("""
        SELECT file_path FROM mas_scalers
        WHERE mas_model_id=:model_id AND io_axis=:axis AND is_active=1
          AND (mas_sensor_id=:sid OR mas_sensor_id IS NULL)
        ORDER BY mas_sensor_id DESC
        LIMIT 1
    """)
    row = session.execute(sql, {"model_id": model_id, "axis": axis, "sid": sensor_id}).first()
    return None if not row else row[0]

def load_scalers(session, settings: Settings, model_id: int, sensor_id: int | None):
    x_path = _pick_scaler_row(session, model_id, sensor_id, "x")
    y_path = _pick_scaler_row(session, model_id, sensor_id, "y")
    x_scaler = joblib_load(normalize_paths(settings.MODELS_BASE_DIR, x_path)) if x_path else None
    y_scaler = joblib_load(normalize_paths(settings.MODELS_BASE_DIR, y_path)) if y_path else None
    return x_scaler, y_scaler
