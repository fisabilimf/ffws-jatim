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
        return [{"id": m.id, "name": m.name, "type": m.model_type, "file_path": m.file_path,
                 "n_steps_in": m.n_steps_in, "n_steps_out": m.n_steps_out, "is_active": m.is_active} for m in rows]

@bp.get("/sensors")
def list_sensors():
    Session = current_app.config["DB_SESSION"]
    with Session() as s:
        rows = s.query(MasSensors).all()
        return [{"id": x.id, "code": x.sensor_code, "parameter": x.parameter.value,
                 "model_id": x.mas_model_id, "status": x.status.value} for x in rows]

@bp.get("/river-basins")
def list_river_basins():
    Session = current_app.config["DB_SESSION"]
    with Session() as s:
        rows = s.query(MasRiverBasins).all()
        return [{"id": b.id, "name": b.name, "code": b.code} for b in rows]

@bp.post("/forecast/run")
def run_forecast_single():
    """
    Jalankan forecast untuk 1 sensor.
    Body:
    { "sensor_id": 101, "model_id": 5 (opsional) }
    """
    payload = request.get_json(force=True, silent=True) or {}
    sensor_id = payload.get("sensor_id")
    model_id = payload.get("model_id")
    if not sensor_id:
        return {"error": "sensor_id is required"}, 400

    Session = current_app.config["DB_SESSION"]
    settings = current_app.config["SETTINGS"]
    try:
        with Session() as s:
            out = predict_for_sensor(s, settings, int(sensor_id), int(model_id) if model_id else None)
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
    { "river_basin_id": 1, "only_active": true }  // only_active default true
    """
    payload = request.get_json(force=True, silent=True) or {}
    rbid = payload.get("river_basin_id")
    only_active = bool(payload.get("only_active", True))
    if not rbid:
        return {"error": "river_basin_id is required"}, 400

    Session = current_app.config["DB_SESSION"]
    settings = current_app.config["SETTINGS"]
    try:
        with Session() as s:
            out = predict_for_basin(s, settings, int(rbid), only_active=only_active)
        return out
    except Exception as e:
        return {"error": "internal_error", "detail": str(e)}, 500
