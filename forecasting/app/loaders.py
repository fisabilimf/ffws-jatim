from functools import lru_cache
from tensorflow.keras.models import load_model
from tcn import TCN
from .utils import normalize_paths
from .config import Settings

@lru_cache(maxsize=64)
def load_keras_model(settings: Settings, file_path: str):
    path = normalize_paths(settings.MODELS_BASE_DIR, file_path)
    try:
        mdl = load_model(path, compile=False, custom_objects={"TCN": TCN})
    except Exception:
        mdl = load_model(path, compile=False)
    return mdl
