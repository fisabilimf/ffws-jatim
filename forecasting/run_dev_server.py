#!/usr/bin/env python3
"""
Development server for FFWS Forecasting API
Run this script to start the Flask development server for testing.
"""

from app import create_app
import os

if __name__ == "__main__":
    # Create the Flask app
    app = create_app()
    
    # Run in development mode
    print("🚀 Starting FFWS Forecasting API Server...")
    print("📡 Server will be available at: http://localhost:5000")
    print("🔗 API Endpoints:")
    print("   GET  /health                - Health check")
    print("   GET  /api/models           - List all ML models")
    print("   GET  /api/sensors          - List all sensors")
    print("   GET  /api/river-basins     - List all river basins")
    print("   POST /api/forecast/run     - Run prediction for sensor")
    print("   POST /api/forecast/run-basin - Run predictions for basin")
    print("💡 Press Ctrl+C to stop the server")
    print("-" * 60)
    
    app.run(
        host='0.0.0.0',
        port=5000,
        debug=True,
        use_reloader=False  # Disable reloader to avoid issues with TensorFlow
    )
