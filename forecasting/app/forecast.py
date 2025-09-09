from datetime import datetime, timedelta
import numpy as np
import pandas as pd
from sqlalchemy import select, desc
from sqlalchemy.orm import Session

from .models import MasSensors, MasModels, DataActual, DataPrediction, MasDevices
from .utils import ensure_3d_batch
from .loaders import load_keras_model
from .scalers import load_scalers
from .thresholds import classify_threshold
from .config import Settings
from .preprocess import preprocess_series, PreprocessOptions

class ForecastError(Exception): pass

def _fetch_series(session: Session, sensor_code: str, min_multiple: int) -> pd.DataFrame:
    # ambil lebih panjang (×4) untuk jaga-jaga preprocess (resample/trim)
    q = (
        select(DataActual.value, DataActual.received_at)
        .where(DataActual.mas_sensor_code == sensor_code)
        .order_by(desc(DataActual.received_at))
        .limit(min_multiple)
    )
    rows = session.execute(q).all()
    if not rows:
        raise ForecastError("No data points found")
    return pd.DataFrame(rows, columns=["value", "received_at"])

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

def predict_for_sensor(session: Session, settings: Settings, sensor_code: str, model_code: str | None = None) -> dict:
    sensor, model = _get_sensor_and_model(session, sensor_code, model_code)
    
    # For now, assume 48 as min steps in (we'll need to get this from model configuration)
    min_steps = 48
    df = _fetch_series(session, sensor_code, min_steps * 4)

    # === PREPROCESS ===
    pp_opts = PreprocessOptions(
        resample_rule=None,   # set ke "60T" jika ingin seragam 1 jam
        limit_ffill=3,
        clip_iqr=True,
        iqr_k=3.0,
        use_external_pipeline=False,
    )
    try:
        x, step_minutes, idx = preprocess_series(df, min_steps, pp_opts)
    except ValueError as e:
        raise ForecastError(str(e))

    # Scalers
    x_scaler, y_scaler = load_scalers(session, settings, model.model_code, sensor.sensor_code)
    if x_scaler is not None:
        x = x_scaler.transform(x.reshape(-1, 1)).reshape(-1)

    X = ensure_3d_batch(x)
    mdl = load_keras_model(settings, model.file_path)
    yhat = mdl.predict(X, verbose=0)
    yhat = np.asarray(yhat).astype(np.float32)

    # normalisasi shape ke (n_steps_out,)
    if yhat.ndim == 2 and yhat.shape[0] == 1:
        yhat = yhat[0]
    if yhat.ndim == 2 and yhat.shape[1] == 1:
        yhat = yhat[:, 0]
    if yhat.ndim > 1:
        yhat = yhat.reshape(-1)

    if y_scaler is not None:
        yhat = y_scaler.inverse_transform(yhat.reshape(-1, 1)).reshape(-1)

    now = datetime.utcnow()
    base_ts = idx[-1].to_pydatetime()
    # For now, assume 24 steps out (we'll need to get this from model configuration)
    n_steps_out = min(24, len(yhat))
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
            confidence_score=None,
            threshold_prediction_status=status
        ))
    session.add_all(rows_out)
    session.commit()

    return {
        "sensor_code": sensor.sensor_code,
        "model_code": model.model_code,
        "model_type": model.model_type,
        "step_minutes": step_minutes,
        "rows_inserted": len(rows_out),
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
