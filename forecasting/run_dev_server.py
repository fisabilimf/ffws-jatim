#!/usr/bin/env python3
"""
Enhanced FFWS Forecasting API Server
Production-ready server with comprehensive monitoring and integration
"""

from app import create_app
import os
import sys
import logging
from datetime import datetime

def setup_logging():
    """Setup logging configuration"""
    log_level = os.getenv('LOG_LEVEL', 'INFO').upper()
    log_file = os.getenv('LOG_FILE', './logs/forecasting.log')
    
    # Create logs directory if it doesn't exist
    os.makedirs(os.path.dirname(log_file) if os.path.dirname(log_file) else './logs', exist_ok=True)
    
    logging.basicConfig(
        level=getattr(logging, log_level),
        format='%(asctime)s - %(name)s - %(levelname)s - %(message)s',
        handlers=[
            logging.FileHandler(log_file),
            logging.StreamHandler(sys.stdout)
        ]
    )

def print_startup_info():
    """Print comprehensive startup information"""
    host = os.getenv('FLASK_HOST', '0.0.0.0')
    port = int(os.getenv('FLASK_PORT', '5000'))
    env = os.getenv('FLASK_ENV', 'production')
    
    print("=" * 80)
    print("🌊 FFWS Enhanced Forecasting API Server")
    print("=" * 80)
    print(f"📅 Started at: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    print(f"🌐 Server URL: http://{host}:{port}")
    print(f"🔧 Environment: {env}")
    print(f"💾 Database: {os.getenv('DB_NAME', 'ffws_jatim')} @ {os.getenv('DB_HOST', 'localhost')}")
    print("-" * 80)
    print("🔗 Available API Endpoints:")
    print("   GET  /health                      - Health check and status")
    print("   GET  /health/fallback            - Fallback system health")
    print("   GET  /api/models                 - List ML models")
    print("   GET  /api/sensors                - List all sensors")
    print("   GET  /api/sensors/no-models      - Sensors without ML models")
    print("   GET  /api/river-basins           - List river basins")
    print("   POST /api/forecast/run           - Run ML prediction")
    print("   POST /api/forecast/hourly        - Run hourly prediction")
    print("   POST /api/forecast/run-basin     - Run basin-wide predictions")
    print("   POST /api/forecast/fallback      - Run fallback prediction")
    print("-" * 80)
    print("📊 ML Models Available:")
    print("   • DHOMPO_GRU, DHOMPO_LSTM, DHOMPO_TCN")
    print("   • PURWODADI_GRU, PURWODADI_LSTM, PURWODADI_TCN")
    print("-" * 80)
    print("🔧 Integration Features:")
    print("   • Laravel Backend Integration")
    print("   • Database Synchronization")
    print("   • GeoJSON Mapping Support")
    print("   • Fallback Forecasting System")
    print("   • Real-time Predictions")
    print("   • Health Monitoring")
    print("-" * 80)
    print("💡 Press Ctrl+C to stop the server")
    print("=" * 80)

def check_prerequisites():
    """Check if all prerequisites are met"""
    checks = []
    
    # Check database connection
    try:
        from app.config import Settings
        import mysql.connector
        settings = Settings()
        conn = mysql.connector.connect(
            host=settings.DB_HOST,
            port=settings.DB_PORT,
            user=settings.DB_USER,
            password=settings.DB_PASS,
            database=settings.DB_NAME
        )
        conn.close()
        checks.append(("✅ Database Connection", "OK"))
    except Exception as e:
        checks.append(("❌ Database Connection", f"FAILED: {str(e)}"))
    
    # Check model files
    script_dir = os.path.dirname(os.path.abspath(__file__))
    models_dir = os.getenv('MODELS_BASE_DIR', os.path.join(script_dir, 'models'))
    expected_models = [
        'dhompo_gru.h5', 'dhompo_lstm.h5', 'dhompo_tcn.h5',
        'purwodadi_gru.h5', 'purwodadi_lstm.h5', 'purwodadi_tcn.h5'
    ]
    
    missing_models = []
    for model in expected_models:
        if not os.path.exists(os.path.join(models_dir, model)):
            missing_models.append(model)
    
    if missing_models:
        checks.append(("⚠️  ML Models", f"Missing: {', '.join(missing_models)}"))
    else:
        checks.append(("✅ ML Models", "All models available"))
    
    # Check scalers
    scalers_dir = os.getenv('SCALERS_BASE_DIR', os.path.join(script_dir, 'scalers'))
    if os.path.exists(scalers_dir) and len(os.listdir(scalers_dir)) > 0:
        checks.append(("✅ Model Scalers", "Available"))
    else:
        checks.append(("⚠️  Model Scalers", "Directory empty or missing"))
    
    print("🔍 System Prerequisites Check:")
    for check, status in checks:
        print(f"   {check}: {status}")
    print("-" * 80)
    
    return all("✅" in check[0] for check in checks)

if __name__ == "__main__":
    # Setup logging
    setup_logging()
    logger = logging.getLogger(__name__)
    
    try:
        # Check prerequisites
        print_startup_info()
        prerequisites_ok = check_prerequisites()
        
        if not prerequisites_ok:
            print("⚠️  Some prerequisites failed. Server will start but some features may not work.")
            print("   Check the logs for detailed error information.")
            print("-" * 80)
        
        # Create the Flask app
        logger.info("Creating Flask application...")
        app = create_app()
        
        # Get configuration
        host = os.getenv('FLASK_HOST', '0.0.0.0')
        port = int(os.getenv('FLASK_PORT', '5000'))
        debug = os.getenv('FLASK_DEBUG', 'False').lower() == 'true'
        
        logger.info(f"Starting server on {host}:{port} (debug={debug})")
        
        # Start the server
        app.run(
            host=host,
            port=port,
            debug=debug,
            threaded=True,
            use_reloader=False  # Disable reloader to avoid issues with TensorFlow
        )
        
    except KeyboardInterrupt:
        logger.info("Server stopped by user")
        print("\n🛑 Server stopped gracefully")
    except Exception as e:
        logger.error(f"Server startup failed: {str(e)}")
        print(f"❌ Server startup failed: {str(e)}")
        sys.exit(1)
