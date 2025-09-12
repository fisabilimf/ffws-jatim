def classify_threshold(value: float, safe: float | None, warn: float | None, danger: float | None) -> str | None:
    if danger is None or warn is None or safe is None:
        return None
    if value >= danger:
        return "danger"
    if value >= warn:
        return "warning"
    return "safe"
