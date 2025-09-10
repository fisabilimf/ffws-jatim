from flask import Blueprint, current_app, request
from sqlalchemy.orm import Session
from .forecast import predict_for_sensor, predict_for_basin, ForecastError
from .models import MasModels, MasSensors, MasRiverBasins

bp = Blueprint("api", __name__)

@bp.get("/models")
def list_models():
    Session = current_app.config["DB_SESSION"]
    with Session() as s:
        rows = s.query(MasModels).all()
        return [{"id": m.id, "code": m.model_code, "name": m.name, "type": m.model_type, 
                 "file_path": m.file_path, "is_active": m.is_active} for m in rows]

@bp.get("/sensors")
def list_sensors():
    Session = current_app.config["DB_SESSION"]
    with Session() as s:
        rows = s.query(MasSensors).all()
        return [{"id": x.id, "code": x.sensor_code, "parameter": x.parameter.value,
                 "model_code": x.mas_model_code, "device_code": x.mas_device_code,
                 "status": x.status.value} for x in rows]

@bp.get("/river-basins")
def list_river_basins():
    Session = current_app.config["DB_SESSION"]
    with Session() as s:
        rows = s.query(MasRiverBasins).all()
        return [{"id": b.id, "name": b.name, "code": b.code} for b in rows]

@bp.post("/forecast/run")
def run_forecast_single():
    """
    Jalankan forecast untuk 1 sensor dengan opsi konfigurasi waktu.
    Body:
    { 
        "sensor_code": "DHOMPO_WL_01", 
        "model_code": "DHOMPO_LSTM" (opsional),
        "prediction_hours": 5 (opsional, default 5),
        "step_hours": 1.0 (opsional, default 1.0)
    }
    """
    payload = request.get_json(force=True, silent=True) or {}
    sensor_code = payload.get("sensor_code")
    model_code = payload.get("model_code")
    prediction_hours = payload.get("prediction_hours", 5)
    step_hours = payload.get("step_hours", 1.0)
    
    if not sensor_code:
        return {"error": "sensor_code is required"}, 400

    # Validate parameters
    try:
        prediction_hours = int(prediction_hours)
        step_hours = float(step_hours)
        
        if prediction_hours < 1 or prediction_hours > 24:
            return {"error": "prediction_hours must be between 1 and 24"}, 400
        
        if step_hours <= 0 or step_hours > 6:
            return {"error": "step_hours must be between 0.1 and 6.0"}, 400
            
    except (ValueError, TypeError):
        return {"error": "Invalid prediction_hours or step_hours format"}, 400

    Session = current_app.config["DB_SESSION"]
    settings = current_app.config["SETTINGS"]
    try:
        with Session() as s:
            out = predict_for_sensor(s, settings, sensor_code, model_code, 
                                   prediction_hours, step_hours)
        return out
    except ForecastError as e:
        return {"error": str(e)}, 400
    except Exception as e:
        return {"error": "internal_error", "detail": str(e)}, 500

@bp.post("/forecast/hourly")
def run_forecast_hourly():
    """
    Jalankan forecast hourly untuk 1-5 jam ke depan (khusus untuk data hourly).
    Body:
    { 
        "sensor_code": "DHOMPO_WL_01",
        "hours": 5 (opsional, default 5, max 24)
    }
    """
    payload = request.get_json(force=True, silent=True) or {}
    sensor_code = payload.get("sensor_code")
    hours = payload.get("hours", 5)
    
    if not sensor_code:
        return {"error": "sensor_code is required"}, 400

    # Validate hours parameter
    try:
        hours = int(hours)
        if hours < 1 or hours > 24:
            return {"error": "hours must be between 1 and 24"}, 400
    except (ValueError, TypeError):
        return {"error": "Invalid hours format"}, 400

    Session = current_app.config["DB_SESSION"]
    settings = current_app.config["SETTINGS"]
    try:
        with Session() as s:
            # Use 1-hour steps for hourly predictions
            out = predict_for_sensor(s, settings, sensor_code, None, hours, 1.0)
        return out
    except ForecastError as e:
        return {"error": str(e)}, 400
    except Exception as e:
        return {"error": "internal_error", "detail": str(e)}, 500

@bp.post("/forecast/run-basin")
def run_forecast_basin():
    """
    Jalankan forecast untuk SEMUA sensor dalam 1 river basin.
    Body:
    { "river_basin_code": "DHOMPO", "only_active": true }  // only_active default true
    """
    payload = request.get_json(force=True, silent=True) or {}
    rbcode = payload.get("river_basin_code")
    only_active = bool(payload.get("only_active", True))
    if not rbcode:
        return {"error": "river_basin_code is required"}, 400

    Session = current_app.config["DB_SESSION"]
    settings = current_app.config["SETTINGS"]
    try:
        with Session() as s:
            out = predict_for_basin(s, settings, rbcode, only_active=only_active)
        return out
    except Exception as e:
        return {"error": "internal_error", "detail": str(e)}, 500
