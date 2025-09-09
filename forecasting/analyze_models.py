#!/usr/bin/env python3
"""
Analyze the ML models and scalers to understand their structure and requirements.
"""

import os
import sys
import pickle
import numpy as np
from dotenv import load_dotenv

# Add the app directory to Python path
sys.path.insert(0, os.path.join(os.path.dirname(__file__), 'app'))

def analyze_keras_models():
    """Analyze the Keras models structure."""
    print("🤖 Analyzing Keras Models")
    print("=" * 50)
    
    try:
        import tensorflow as tf
        from tensorflow import keras
        
        models_dir = "models"
        model_files = [f for f in os.listdir(models_dir) if f.endswith('.h5')]
        
        for model_file in model_files:
            print(f"\n📊 Model: {model_file}")
            model_path = os.path.join(models_dir, model_file)
            
            try:
                # Load model
                model = keras.models.load_model(model_path)
                
                # Get model info
                print(f"  Input shape: {model.input_shape}")
                print(f"  Output shape: {model.output_shape}")
                print(f"  Total parameters: {model.count_params():,}")
                print(f"  Layers: {len(model.layers)}")
                
                # Show layer details
                print("  Layer details:")
                for i, layer in enumerate(model.layers):
                    print(f"    {i+1}. {layer.__class__.__name__}: {layer.output_shape}")
                
                # Try to predict with dummy data to understand input requirements
                if model.input_shape[1:]:  # Skip batch dimension
                    input_shape = model.input_shape[1:]
                    print(f"  Expected input dimensions: {input_shape}")
                    
                    # Create dummy input
                    dummy_input = np.random.random((1,) + input_shape)
                    try:
                        dummy_output = model.predict(dummy_input, verbose=0)
                        print(f"  Prediction output shape: {dummy_output.shape}")
                        print(f"  Sample prediction: {dummy_output[0][:3]}...")  # First 3 values
                    except Exception as e:
                        print(f"  ⚠️  Prediction test failed: {e}")
                
            except Exception as e:
                print(f"  ❌ Error loading model: {e}")
    
    except ImportError:
        print("❌ TensorFlow not available for model analysis")
    except Exception as e:
        print(f"❌ Error analyzing models: {e}")

def analyze_scalers():
    """Analyze the scaler files to understand data preprocessing requirements."""
    print("\n\n🔧 Analyzing Scalers")
    print("=" * 50)
    
    scalers_dir = "scalers"
    scaler_files = [f for f in os.listdir(scalers_dir) if f.endswith('.pkl')]
    
    # Group scalers by model
    model_scalers = {}
    for scaler_file in scaler_files:
        parts = scaler_file.replace('.pkl', '').split('_')
        if len(parts) >= 4:  # e.g., dhompo_gru_x_scaler
            model_name = '_'.join(parts[:-2])  # dhompo_gru
            scaler_type = '_'.join(parts[-2:])  # x_scaler
            
            if model_name not in model_scalers:
                model_scalers[model_name] = {}
            model_scalers[model_name][scaler_type] = scaler_file
    
    for model_name, scalers in model_scalers.items():
        print(f"\n📈 Model: {model_name}")
        
        for scaler_type, scaler_file in scalers.items():
            scaler_path = os.path.join(scalers_dir, scaler_file)
            print(f"  {scaler_type}: {scaler_file}")
            
            try:
                with open(scaler_path, 'rb') as f:
                    scaler = pickle.load(f)
                
                print(f"    Type: {type(scaler).__name__}")
                
                # Analyze scaler properties
                if hasattr(scaler, 'n_features_in_'):
                    print(f"    Input features: {scaler.n_features_in_}")
                
                if hasattr(scaler, 'feature_names_in_'):
                    print(f"    Feature names: {scaler.feature_names_in_}")
                
                if hasattr(scaler, 'scale_'):
                    print(f"    Scale factors: {scaler.scale_[:5]}...")  # First 5
                
                if hasattr(scaler, 'mean_'):
                    print(f"    Mean values: {scaler.mean_[:5]}...")  # First 5
                
                if hasattr(scaler, 'data_min_'):
                    print(f"    Min values: {scaler.data_min_[:5]}...")  # First 5
                
                if hasattr(scaler, 'data_max_'):
                    print(f"    Max values: {scaler.data_max_[:5]}...")  # First 5
                
                # Test transformation with dummy data
                try:
                    if hasattr(scaler, 'n_features_in_'):
                        n_features = scaler.n_features_in_
                        dummy_data = np.random.random((10, n_features))
                        transformed = scaler.transform(dummy_data)
                        print(f"    ✅ Transform test: {dummy_data.shape} → {transformed.shape}")
                        print(f"    Sample input: {dummy_data[0][:3]}...")
                        print(f"    Sample output: {transformed[0][:3]}...")
                except Exception as e:
                    print(f"    ⚠️  Transform test failed: {e}")
                    
            except Exception as e:
                print(f"    ❌ Error loading scaler: {e}")

def analyze_data_requirements():
    """Analyze what data format the models expect based on scalers."""
    print("\n\n📊 Data Requirements Analysis")
    print("=" * 50)
    
    scalers_dir = "scalers"
    
    # Check a few x_scalers to understand input requirements
    x_scalers = [f for f in os.listdir(scalers_dir) if 'x_scaler' in f]
    
    input_features_summary = {}
    
    for scaler_file in x_scalers[:3]:  # Check first 3
        model_name = scaler_file.replace('_x_scaler.pkl', '')
        scaler_path = os.path.join(scalers_dir, scaler_file)
        
        try:
            with open(scaler_path, 'rb') as f:
                scaler = pickle.load(f)
            
            if hasattr(scaler, 'n_features_in_'):
                n_features = scaler.n_features_in_
                input_features_summary[model_name] = n_features
                print(f"📈 {model_name}: Expects {n_features} input features")
                
        except Exception as e:
            print(f"❌ {model_name}: Error - {e}")
    
    # Summary
    if input_features_summary:
        print(f"\n💡 Summary:")
        unique_features = set(input_features_summary.values())
        if len(unique_features) == 1:
            n_features = list(unique_features)[0]
            print(f"   All models expect {n_features} input features")
            print(f"   Current data provides only 1 feature (single sensor value)")
            print(f"   ⚠️  MISMATCH: Need to provide {n_features} features, not 1")
            
            print(f"\n🔧 Possible Solutions:")
            print(f"   1. Create {n_features} features from time series (lag values, moving averages, etc.)")
            print(f"   2. Retrain models with single-feature input")
            print(f"   3. Use multiple sensors as input features")
            
        else:
            print(f"   Different models expect different feature counts: {unique_features}")

def suggest_data_preprocessing():
    """Suggest how to create the required input features."""
    print("\n\n🛠️  Data Preprocessing Suggestions")
    print("=" * 50)
    
    # Check one scaler to see expected features
    scalers_dir = "scalers"
    x_scaler_files = [f for f in os.listdir(scalers_dir) if 'x_scaler' in f]
    
    if x_scaler_files:
        scaler_path = os.path.join(scalers_dir, x_scaler_files[0])
        try:
            with open(scaler_path, 'rb') as f:
                scaler = pickle.load(f)
            
            if hasattr(scaler, 'n_features_in_'):
                n_features = scaler.n_features_in_
                
                print(f"Models expect {n_features} features. Here's how to create them:")
                print(f"")
                print(f"For time series forecasting, common features include:")
                print(f"  1. Current value: sensor_value(t)")
                print(f"  2. Lag values: sensor_value(t-1), sensor_value(t-2), ...")
                print(f"  3. Moving averages: MA_3h, MA_6h, MA_24h")
                print(f"  4. Seasonal features: hour_of_day, day_of_week")
                print(f"  5. Weather features: temperature, humidity, pressure")
                print(f"")
                print(f"Example for {n_features} features:")
                for i in range(min(n_features, 10)):
                    if i == 0:
                        print(f"  Feature {i+1}: Current sensor value")
                    elif i < 4:
                        print(f"  Feature {i+1}: Lag-{i} value (t-{i*15}min)")
                    elif i < 7:
                        print(f"  Feature {i+1}: Moving average ({(i-3)*3}h)")
                    else:
                        print(f"  Feature {i+1}: Additional feature (seasonal/weather)")
                
                if n_features > 10:
                    print(f"  ... and {n_features - 10} more features")
        
        except Exception as e:
            print(f"❌ Error analyzing scaler: {e}")

if __name__ == "__main__":
    print("🔍 FFWS ML Models & Scalers Analysis")
    print("=" * 60)
    
    # Change to forecasting directory
    os.chdir(os.path.dirname(__file__) or '.')
    
    # Analyze models
    analyze_keras_models()
    
    # Analyze scalers  
    analyze_scalers()
    
    # Analyze data requirements
    analyze_data_requirements()
    
    # Suggest preprocessing
    suggest_data_preprocessing()
    
    print("\n" + "=" * 60)
    print("✅ Analysis complete!")
