from flask import Flask
from .config import Settings
from .db import init_engine, init_session
from .routes import bp as api_bp
from .enhanced_routes import enhanced_bp

def create_app() -> Flask:
    settings = Settings()
    app = Flask(__name__)
    app.config["SETTINGS"] = settings

    # DB engine/session
    engine = init_engine(settings)
    Session = init_session(engine)
    app.config["DB_SESSION"] = Session

    # Blueprints
    app.register_blueprint(api_bp, url_prefix="/api")
    app.register_blueprint(enhanced_bp)

    @app.get("/health")
    def health():
        return {"status": "ok"}

    return app
