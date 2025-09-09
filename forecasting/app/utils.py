import numpy as np
import os

def ensure_3d_batch(seq: np.ndarray) -> np.ndarray:
    arr = np.asarray(seq, dtype=np.float32)
    if arr.ndim == 1:
        arr = arr.reshape(1, -1, 1)
    elif arr.ndim == 2:
        arr = arr.reshape(1, arr.shape[0], arr.shape[1])
    return arr

def normalize_paths(base_dir: str, path_from_db: str) -> str:
    if not base_dir:
        return path_from_db
    if os.path.isabs(path_from_db):
        return path_from_db
    return os.path.normpath(os.path.join(base_dir, path_from_db))
