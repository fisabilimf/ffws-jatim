#!/usr/bin/env python3
"""Debug script to analyze scaler shapes and requirements."""

import os
import pickle
import numpy as np
from pathlib import Path

def analyze_scaler(scaler_path):
    """Analyze a scaler file to understand its expected input/output shape."""
    try:
        with open(scaler_path, 'rb') as f:
            scaler = pickle.load(f)
        
        print(f"📊 Analyzing: {Path(scaler_path).name}")
        print(f"   Type: {type(scaler).__name__}")
        print(f"   N Features In: {getattr(scaler, 'n_features_in_', 'Unknown')}")
        print(f"   Feature Names: {getattr(scaler, 'feature_names_in_', 'Unknown')}")
        
        if hasattr(scaler, 'scale_'):
            print(f"   Scale shape: {scaler.scale_.shape}")
            print(f"   Mean shape: {scaler.mean_.shape}")
            
        # Test with different shapes
        test_shapes = [(1, 1), (5, 1), (1, 5)]
        for shape in test_shapes:
            try:
                test_data = np.random.random(shape)
                result = scaler.transform(test_data)
                print(f"   ✅ Shape {shape} -> {result.shape}")
            except Exception as e:
                print(f"   ❌ Shape {shape} -> Error: {str(e)[:50]}")
        
        print()
        
    except Exception as e:
        print(f"   ❌ Failed to load: {e}")
        print()

def main():
    """Main function."""
    print("🔍 Analyzing Y-Scalers for Output Shape Compatibility")
    print("=" * 60)
    
    scalers_dir = Path("scalers")
    if not scalers_dir.exists():
        print("❌ Scalers directory not found!")
        return
    
    # Analyze Y scalers specifically
    y_scaler_files = list(scalers_dir.glob("*_y_scaler.pkl"))
    
    for scaler_file in y_scaler_files:
        analyze_scaler(scaler_file)
    
    print("🔧 Key Findings:")
    print("   • Y-scalers are trained on specific output shapes")
    print("   • We need to match the exact shape they expect")
    print("   • Different models may have different output dimensions")

if __name__ == "__main__":
    main()
