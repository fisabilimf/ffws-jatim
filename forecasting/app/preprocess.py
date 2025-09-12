from __future__ import annotations
from dataclasses import dataclass
from typing import Optional, Tuple
import numpy as np
import pandas as pd

@dataclass
class PreprocessOptions:
    resample_rule: Optional[str] = None  # mis: "60T"; None = pakai interval asli
    limit_ffill: int = 3
    clip_iqr: bool = True
    iqr_k: float = 3.0
    use_external_pipeline: bool = False  # hook kalau Anda simpan sklearn Pipeline .pkl

def _clip_iqr(s: pd.Series, k: float) -> pd.Series:
    q1, q3 = s.quantile(0.25), s.quantile(0.75)
    iqr = (q3 - q1) if pd.notna(q3) and pd.notna(q1) else None
    if not iqr or iqr == 0:
        return s
    lo, hi = q1 - k * iqr, q3 + k * iqr
    return s.clip(lower=lo, upper=hi)

def infer_step_minutes_from_index(idx: pd.DatetimeIndex) -> int:
    if len(idx) < 2:
        return 60
    diffs = np.diff(idx.view("i8")) / 1e9  # seconds
    if len(diffs) == 0:
        return 60
    minutes = int(np.median(diffs) // 60) or 60
    return minutes

def preprocess_series(
    df_raw: pd.DataFrame,
    n_steps_in: int,
    options: PreprocessOptions = PreprocessOptions(),
) -> Tuple[np.ndarray, int, pd.DatetimeIndex]:
    if df_raw.empty:
        raise ValueError("empty timeseries")
    df = df_raw.sort_values("received_at").reset_index(drop=True)
    df["received_at"] = pd.to_datetime(df["received_at"], utc=True)
    if options.resample_rule:
        s = df.set_index("received_at")["value"].astype("float32")
        s = s.resample(options.resample_rule).mean()
        s = s.ffill(limit=options.limit_ffill)
        df = s.rename("value").to_frame()
        df["received_at"] = df.index
    else:
        df["value"] = df["value"].astype("float32")
    if options.clip_iqr:
        df["value"] = _clip_iqr(df["value"], options.iqr_k)
    if len(df) < n_steps_in:
        raise ValueError(f"Not enough points after preprocess: need {n_steps_in}, got {len(df)}")
    df = df.iloc[-n_steps_in:]
    idx = pd.DatetimeIndex(df["received_at"].to_numpy())
    step_minutes = infer_step_minutes_from_index(idx)
    x = df["value"].to_numpy(dtype="float32")
    return x, step_minutes, idx
