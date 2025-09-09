#!/usr/bin/env python3
"""
Investigate the TensorFlow models to find the compatible version.
"""

import os
import sys
import h5py
import json
from datetime import datetime

def analyze_h5_model(model_path):
    """Analyze an HDF5 model file to extract version information."""
    print(f"\n🔍 Analyzing: {os.path.basename(model_path)}")
    print("-" * 40)
    
    try:
        with h5py.File(model_path, 'r') as f:
            # Check for version information in attributes
            def print_attrs(name, obj):
                if hasattr(obj, 'attrs'):
                    for attr_name, attr_value in obj.attrs.items():
                        if isinstance(attr_value, bytes):
                            attr_value = attr_value.decode('utf-8', errors='ignore')
                        if any(keyword in str(attr_name).lower() for keyword in ['version', 'keras', 'tensorflow', 'backend']):
                            print(f"  {name}/{attr_name}: {attr_value}")
            
            print("📋 Model Metadata:")
            f.visititems(print_attrs)
            
            # Check root level attributes
            print("\n🔧 Root Attributes:")
            for attr_name, attr_value in f.attrs.items():
                if isinstance(attr_value, bytes):
                    try:
                        attr_value = attr_value.decode('utf-8', errors='ignore')
                    except:
                        attr_value = str(attr_value)
                print(f"  {attr_name}: {attr_value}")
            
            # Check for model config
            if 'model_config' in f.attrs:
                model_config = f.attrs['model_config']
                if isinstance(model_config, bytes):
                    try:
                        config_str = model_config.decode('utf-8')
                        config_json = json.loads(config_str)
                        print(f"\n⚙️  Model Architecture:")
                        print(f"  Class: {config_json.get('class_name', 'Unknown')}")
                        if 'config' in config_json:
                            layers = config_json['config'].get('layers', [])
                            print(f"  Layers: {len(layers)}")
                            for i, layer in enumerate(layers[:3]):  # First 3 layers
                                layer_name = layer.get('class_name', 'Unknown')
                                print(f"    {i+1}. {layer_name}")
                                # Check for time_major in layer config
                                if 'config' in layer and 'time_major' in layer['config']:
                                    print(f"       time_major: {layer['config']['time_major']}")
                    except Exception as e:
                        print(f"  Config parsing error: {e}")
            
            # Check for training config
            if 'training_config' in f.attrs:
                print(f"\n🎯 Training Config Found")
                
    except Exception as e:
        print(f"❌ Error analyzing {model_path}: {e}")

def test_tensorflow_versions():
    """Test different TensorFlow versions to find compatibility."""
    print(f"\n🧪 TensorFlow Version Compatibility Test")
    print("=" * 50)
    
    # Common TensorFlow versions that might work
    compatible_versions = [
        "2.12.0", "2.11.0", "2.10.1", "2.9.3", 
        "2.8.4", "2.7.4", "2.6.5", "2.5.3"
    ]
    
    print("🔍 Versions to try (in order of likelihood):")
    for i, version in enumerate(compatible_versions, 1):
        print(f"  {i}. TensorFlow {version}")
    
    print(f"\n💡 Installation commands:")
    print(f"  # Try the most likely compatible version first:")
    print(f"  pip install tensorflow==2.12.0")
    print(f"  # Or try with Keras compatibility:")
    print(f"  pip install tensorflow==2.11.0 keras==2.11.0")
    
def try_load_with_different_approaches():
    """Try different approaches to load the models."""
    print(f"\n🛠️  Alternative Loading Approaches")
    print("=" * 50)
    
    model_files = [f for f in os.listdir('models') if f.endswith('.h5')]
    
    for model_file in model_files[:2]:  # Test first 2 models
        model_path = os.path.join('models', model_file)
        print(f"\n📊 Testing: {model_file}")
        
        # Approach 1: Try with older Keras
        print("  🔧 Approach 1: Legacy loading...")
        try:
            import tensorflow.keras.models
            # Disable some warnings
            import warnings
            warnings.filterwarnings('ignore')
            
            # Try loading with different compile settings
            model = tensorflow.keras.models.load_model(model_path, compile=False)
            print("    ✅ SUCCESS with compile=False")
            print(f"    Input shape: {model.input_shape}")
            print(f"    Output shape: {model.output_shape}")
            
        except Exception as e:
            print(f"    ❌ Failed: {e}")
        
        # Approach 2: Try with custom objects
        print("  🔧 Approach 2: Custom objects...")
        try:
            from tensorflow.keras.models import load_model
            from tensorflow.keras.layers import LSTM, GRU
            
            # Create custom layer classes that ignore time_major
            class CompatibleLSTM(LSTM):
                def __init__(self, *args, **kwargs):
                    kwargs.pop('time_major', None)  # Remove time_major
                    super().__init__(*args, **kwargs)
            
            class CompatibleGRU(GRU):
                def __init__(self, *args, **kwargs):
                    kwargs.pop('time_major', None)  # Remove time_major
                    super().__init__(*args, **kwargs)
            
            custom_objects = {
                'LSTM': CompatibleLSTM,
                'GRU': CompatibleGRU,
            }
            
            model = load_model(model_path, compile=False, custom_objects=custom_objects)
            print("    ✅ SUCCESS with custom objects")
            
        except Exception as e:
            print(f"    ❌ Failed: {e}")
        
        # Approach 3: Manual reconstruction
        print("  🔧 Approach 3: Check if we can extract weights...")
        try:
            import h5py
            with h5py.File(model_path, 'r') as f:
                if 'model_weights' in f:
                    print("    ✅ Model weights accessible")
                else:
                    print("    ⚠️  No model_weights group found")
                    print(f"    Available groups: {list(f.keys())}")
                    
        except Exception as e:
            print(f"    ❌ Failed: {e}")

def create_version_testing_script():
    """Create a script to test different TensorFlow versions."""
    script_content = '''#!/usr/bin/env python3
"""
Test script for different TensorFlow versions.
Run this after installing different TensorFlow versions.
"""

def test_model_loading():
    import tensorflow as tf
    print(f"Testing with TensorFlow {tf.__version__}")
    
    try:
        from tensorflow.keras.models import load_model
        from tensorflow.keras.layers import LSTM, GRU
        
        # Test loading a model
        model_path = "models/dhompo_gru.h5"  # Change this path as needed
        
        # Method 1: Standard loading
        try:
            model = load_model(model_path, compile=False)
            print("✅ SUCCESS: Standard loading worked!")
            return True
        except Exception as e:
            print(f"❌ Standard loading failed: {e}")
        
        # Method 2: With custom objects
        try:
            class CompatibleGRU(GRU):
                def __init__(self, *args, **kwargs):
                    kwargs.pop('time_major', None)
                    super().__init__(*args, **kwargs)
            
            model = load_model(model_path, compile=False, custom_objects={'GRU': CompatibleGRU})
            print("✅ SUCCESS: Custom objects loading worked!")
            return True
        except Exception as e:
            print(f"❌ Custom objects loading failed: {e}")
        
        return False
        
    except Exception as e:
        print(f"❌ TensorFlow {tf.__version__} test failed: {e}")
        return False

if __name__ == "__main__":
    test_model_loading()
'''
    
    with open('test_tf_versions.py', 'w') as f:
        f.write(script_content)
    
    print(f"\n📝 Created test_tf_versions.py")
    print(f"   Use this script to test different TensorFlow versions")

if __name__ == "__main__":
    print("🔍 TensorFlow Model Compatibility Analysis")
    print("=" * 60)
    
    os.chdir(os.path.dirname(__file__) or '.')
    
    # Step 1: Analyze model files
    print("📋 Step 1: Analyzing Model Files")
    model_files = [f for f in os.listdir('models') if f.endswith('.h5')][:3]  # Analyze first 3
    
    for model_file in model_files:
        model_path = os.path.join('models', model_file)
        analyze_h5_model(model_path)
    
    # Step 2: Test version compatibility
    test_tensorflow_versions()
    
    # Step 3: Try different loading approaches
    try_load_with_different_approaches()
    
    # Step 4: Create testing script
    create_version_testing_script()
    
    print(f"\n" + "=" * 60)
    print(f"🎯 NEXT STEPS:")
    print(f"1. Try installing TensorFlow 2.12.0:")
    print(f"   pip install tensorflow==2.12.0")
    print(f"2. Test with the generated script:")
    print(f"   python test_tf_versions.py")
    print(f"3. If that doesn't work, try 2.11.0, then 2.10.1")
    print(f"4. Consider retraining models if compatibility issues persist")
