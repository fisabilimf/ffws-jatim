import os
from dataclasses import dataclass

@dataclass
class Settings:
    DB_HOST: str = os.getenv("DB_HOST", "127.0.0.1")
    DB_PORT: int = int(os.getenv("DB_PORT", "3306"))
    DB_USER: str = os.getenv("DB_USER", "root")
    DB_PASS: str = os.getenv("DB_PASS", "1234")
    DB_NAME: str = os.getenv("DB_NAME", "ffws_jatim")  # Changed default
    DB_POOL_SIZE: int = int(os.getenv("DB_POOL_SIZE", "10"))
    DB_POOL_RECYCLE: int = int(os.getenv("DB_POOL_RECYCLE", "1800"))
    MODELS_BASE_DIR: str = os.getenv("MODELS_BASE_DIR", ".")

    @property
    def sqlalchemy_url(self) -> str:
        return (
            f"mysql+mysqldb://{self.DB_USER}:{self.DB_PASS}"
            f"@{self.DB_HOST}:{self.DB_PORT}/{self.DB_NAME}"
            f"?charset=utf8mb4"
        )
