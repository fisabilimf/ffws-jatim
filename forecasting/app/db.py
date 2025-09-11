from sqlalchemy import create_engine
from sqlalchemy.orm import sessionmaker, scoped_session
from .config import Settings

def init_engine(settings: Settings):
    engine = create_engine(
        settings.sqlalchemy_url,
        pool_pre_ping=True,
        pool_recycle=settings.DB_POOL_RECYCLE,
        pool_size=settings.DB_POOL_SIZE,
        max_overflow=10,
        future=True,
    )
    return engine

def init_session(engine):
    return scoped_session(sessionmaker(bind=engine, autoflush=False, future=True))
