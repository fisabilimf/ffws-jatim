#!/usr/bin/env python3
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
            print("SUCCESS: Standard loading worked!")
            return True
        except Exception as e:
            print(f"Standard loading failed: {e}")
        
        # Method 2: With custom objects
        try:
            class CompatibleGRU(GRU):
                def __init__(self, *args, **kwargs):
                    kwargs.pop('time_major', None)
                    super().__init__(*args, **kwargs)
            
            class CompatibleLSTM(LSTM):
                def __init__(self, *args, **kwargs):
                    kwargs.pop('time_major', None)
                    super().__init__(*args, **kwargs)
            
            custom_objects = {
                'GRU': CompatibleGRU,
                'LSTM': CompatibleLSTM
            }
            
            model = load_model(model_path, compile=False, custom_objects=custom_objects)
            print("SUCCESS: Custom objects loading worked!")
            return True
        except Exception as e:
            print(f"Custom objects loading failed: {e}")
        
        return False
        
    except Exception as e:
        print(f"TensorFlow {tf.__version__} test failed: {e}")
        return False

if __name__ == "__main__":
    test_model_loading()
