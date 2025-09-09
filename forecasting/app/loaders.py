from functools import lru_cache
from tensorflow.keras.models import load_model  # type: ignore
from tensorflow.keras.layers import LSTM, GRU  # type: ignore
import warnings
from .utils import normalize_paths
from .config import Settings

# Compatible layer classes that ignore time_major parameter
class CompatibleLSTM(LSTM):
    """LSTM layer compatible with older model files."""
    def __init__(self, *args, **kwargs):
        # Remove the incompatible time_major parameter
        kwargs.pop('time_major', None)
        super().__init__(*args, **kwargs)

class CompatibleGRU(GRU):
    """GRU layer compatible with older model files."""
    def __init__(self, *args, **kwargs):
        # Remove the incompatible time_major parameter
        kwargs.pop('time_major', None)
        super().__init__(*args, **kwargs)

@lru_cache(maxsize=64)
def load_keras_model(cache_key: str, file_path: str):
    """
    Load a Keras model with caching and compatibility fixes.
    
    Args:
        cache_key: A string key for caching (e.g., model_code or unique identifier)
        file_path: Full path to the model file
    """
    try:
        with warnings.catch_warnings():
            warnings.simplefilter("ignore")
            
            # Create custom objects for backward compatibility
            custom_objects = {
                'GRU': CompatibleGRU,
                'LSTM': CompatibleLSTM,
            }
            
            # Try to add TCN if available
            try:
                from tcn import TCN
                custom_objects['TCN'] = TCN
            except ImportError:
                # TCN not available, but that's okay for GRU/LSTM models
                pass
            
            # Load model with custom objects
            model = load_model(file_path, compile=False, custom_objects=custom_objects)
            return model
            
    except Exception as e:
        raise ValueError(f"Failed to load model from {file_path}: {e}")

def load_keras_model_with_settings(settings: Settings, file_path: str):
    """Wrapper function to maintain backward compatibility."""
    full_path = normalize_paths(settings.MODELS_BASE_DIR, file_path)
    cache_key = f"{settings.MODELS_BASE_DIR}_{file_path}"
    return load_keras_model(cache_key, full_path)
