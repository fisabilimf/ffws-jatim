from datetime import datetime
from enum import Enum
from sqlalchemy.orm import DeclarativeBase, mapped_column, Mapped
from sqlalchemy import String, BigInteger, DateTime, Enum as SAEnum, Boolean, ForeignKey, Double, Integer

class Base(DeclarativeBase): pass

class Role(str, Enum):
    admin="admin"; operator="operator"; viewer="viewer"

class Status(str, Enum):
    active="active"; inactive="inactive"

class Param(str, Enum):
    water_level="water_level"; rainfall="rainfall"

class Users(Base):
    __tablename__ = "users"
    id: Mapped[int] = mapped_column(BigInteger, primary_key=True, autoincrement=True)
    name: Mapped[str] = mapped_column(String(255))
    email: Mapped[str] = mapped_column(String(255), unique=True)
    password: Mapped[str] = mapped_column(String(255))
    role: Mapped[Role] = mapped_column(SAEnum(Role))
    created_at: Mapped[datetime] = mapped_column(DateTime)
    updated_at: Mapped[datetime] = mapped_column(DateTime)

class MasRiverBasins(Base):
    __tablename__ = "mas_river_basins"
    id: Mapped[int] = mapped_column(BigInteger, primary_key=True, autoincrement=True)
    name: Mapped[str] = mapped_column(String(255))
    code: Mapped[str] = mapped_column(String(255))

class MasModels(Base):
    __tablename__ = "mas_models"
    id: Mapped[int] = mapped_column(BigInteger, primary_key=True, autoincrement=True)
    name: Mapped[str] = mapped_column(String(255))
    model_type: Mapped[str] = mapped_column(String(255))
    version: Mapped[str | None] = mapped_column(String(255))
    description: Mapped[str | None] = mapped_column(String(255))
    file_path: Mapped[str | None] = mapped_column(String(255))
    n_steps_in: Mapped[int | None] = mapped_column(Integer)
    n_steps_out: Mapped[int | None] = mapped_column(Integer)
    is_active: Mapped[bool] = mapped_column(Boolean, default=True)

class MasDevices(Base):
    __tablename__ = "mas_devices"
    id: Mapped[int] = mapped_column(BigInteger, primary_key=True, autoincrement=True)
    mas_river_basin_id: Mapped[int] = mapped_column(BigInteger, ForeignKey("mas_river_basins.id"))
    name: Mapped[str] = mapped_column(String(255))
    code: Mapped[str] = mapped_column(String(255))
    latitude: Mapped[float] = mapped_column(Double)
    longitude: Mapped[float] = mapped_column(Double)
    elevation_m: Mapped[float] = mapped_column(Double)
    status: Mapped[Status] = mapped_column(SAEnum(Status))

class MasSensors(Base):
    __tablename__ = "mas_sensors"
    id: Mapped[int] = mapped_column(BigInteger, primary_key=True, autoincrement=True)
    device_id: Mapped[int] = mapped_column(BigInteger, ForeignKey("mas_devices.id", ondelete="CASCADE"))
    sensor_code: Mapped[str] = mapped_column(String(255))
    parameter: Mapped[Param] = mapped_column(SAEnum(Param))
    unit: Mapped[str] = mapped_column(String(50))
    description: Mapped[str | None] = mapped_column(String(255))
    mas_model_id: Mapped[int | None] = mapped_column(BigInteger, ForeignKey("mas_models.id", ondelete="SET NULL"))
    threshold_safe: Mapped[float | None] = mapped_column(Double)
    threshold_warning: Mapped[float | None] = mapped_column(Double)
    threshold_danger: Mapped[float | None] = mapped_column(Double)
    status: Mapped[Status] = mapped_column(SAEnum(Status))
    last_seen: Mapped[datetime | None] = mapped_column(DateTime)

class DataActual(Base):
    __tablename__ = "data_actual"
    id: Mapped[int] = mapped_column(BigInteger, primary_key=True, autoincrement=True)
    mas_sensor_id: Mapped[int] = mapped_column(BigInteger, ForeignKey("mas_sensors.id"))
    mas_sensor_code: Mapped[str] = mapped_column(String(255))
    value: Mapped[float] = mapped_column(Double)
    received_at: Mapped[datetime] = mapped_column(DateTime)
    threshold_status: Mapped[str | None] = mapped_column(String(10))

class DataPrediction(Base):
    __tablename__ = "data_prediction"
    id: Mapped[int] = mapped_column(BigInteger, primary_key=True, autoincrement=True)
    mas_sensor_id: Mapped[int] = mapped_column(BigInteger, ForeignKey("mas_sensors.id"))
    mas_sensor_code: Mapped[str] = mapped_column(String(255))
    mas_model_id: Mapped[int] = mapped_column(BigInteger, ForeignKey("mas_models.id"))
    prediction_run_at: Mapped[datetime] = mapped_column(DateTime)
    prediction_for_ts: Mapped[datetime] = mapped_column(DateTime)
    predicted_value: Mapped[float] = mapped_column(Double)
    confidence_score: Mapped[float | None] = mapped_column(Double)
    threshold_prediction_status: Mapped[str | None] = mapped_column(String(10))

class MasScalers(Base):
    __tablename__ = "mas_scalers"
    id: Mapped[int] = mapped_column(BigInteger, primary_key=True, autoincrement=True)
    mas_model_id: Mapped[int] = mapped_column(BigInteger, ForeignKey("mas_models.id", ondelete="CASCADE"))
    mas_sensor_id: Mapped[int | None] = mapped_column(BigInteger, ForeignKey("mas_sensors.id", ondelete="SET NULL"))
    name: Mapped[str] = mapped_column(String(255))
    io_axis: Mapped[str] = mapped_column(String(1))  # 'x' or 'y'
    technique: Mapped[str] = mapped_column(String(20))
    version: Mapped[str | None] = mapped_column(String(64))
    file_path: Mapped[str] = mapped_column(String(512))
    file_hash_sha256: Mapped[str | None] = mapped_column(String(64))
    is_active: Mapped[bool] = mapped_column(Boolean, default=True)
