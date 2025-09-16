#!/usr/bin/env python3
"""
Flood Forecasting Visualization - Compare ML Model Predictions
"""

import sys
import os
sys.path.append(os.path.dirname(os.path.abspath(__file__)))

import matplotlib.pyplot as plt
import matplotlib.dates as mdates
import numpy as np
import pandas as pd
from datetime import datetime, timedelta
from app.db import init_engine, init_session
from app.config import Settings
from app.forecast import predict_for_sensor

def create_forecasting_graphs():
    """Create comprehensive graphs showing ML model prediction differences"""
    
    settings = Settings()
    engine = init_engine(settings)
    Session = init_session(engine)

    print('📊 Creating Flood Forecasting Visualization')
    print('='*50)
    
    # Define all sensors and their models
    sensors_config = {
        'DHOMPO Basin': {
            'BE10126_GRU': 'DHOMPO_GRU',
            'BE10126_LSTM': 'DHOMPO_LSTM', 
            'BE10126_TCN': 'DHOMPO_TCN'
        },
        'PURWODADI Basin': {
            'BE10128_GRU': 'PURWODADI_GRU',
            'BE10128_LSTM': 'PURWODADI_LSTM',
            'BE10128_TCN': 'PURWODADI_TCN'
        }
    }
    
    # Collect prediction data
    prediction_data = {}
    
    with Session() as session:
        for basin_name, sensors in sensors_config.items():
            prediction_data[basin_name] = {}
            
            print(f'\n🌊 Collecting predictions for {basin_name}')
            
            for sensor_code, model_name in sensors.items():
                try:
                    result = predict_for_sensor(
                        session=session,
                        settings=settings,
                        sensor_code=sensor_code,
                        prediction_hours=12,  # 12 hours for better visualization
                        step_hours=1.0
                    )
                    
                    # Extract prediction data
                    predictions = result["predictions"]
                    times = [datetime.fromisoformat(p["forecast_time"]) for p in predictions]
                    values = [p["forecast_value"] for p in predictions]
                    confidence = result.get("confidence_score", 0)
                    
                    prediction_data[basin_name][sensor_code] = {
                        'model': model_name,
                        'times': times,
                        'values': values,
                        'confidence': confidence
                    }
                    
                    print(f'   ✅ {sensor_code}: {len(predictions)} predictions (confidence: {confidence:.3f})')
                    
                except Exception as e:
                    print(f'   ❌ {sensor_code}: Failed - {str(e)[:50]}...')
    
    # Create comprehensive visualization
    fig, axes = plt.subplots(2, 2, figsize=(16, 12))
    fig.suptitle('🌊 Flood Forecasting Model Comparison\n24-Hour Water Level Predictions', 
                 fontsize=16, fontweight='bold')
    
    # Color schemes for different model types
    colors = {
        'GRU': '#2E86C1',    # Blue
        'LSTM': '#28B463',   # Green  
        'TCN': '#E74C3C'     # Red
    }
    
    line_styles = {
        'GRU': '-',
        'LSTM': '--',
        'TCN': '-.'
    }
    
    # Plot 1: DHOMPO Basin Comparison
    ax1 = axes[0, 0]
    dhompo_data = prediction_data.get('DHOMPO Basin', {})
    
    for sensor_code, data in dhompo_data.items():
        model_type = data['model'].split('_')[1]  # Extract GRU/LSTM/TCN
        
        ax1.plot(data['times'], data['values'], 
                color=colors[model_type], 
                linestyle=line_styles[model_type],
                linewidth=2,
                marker='o', 
                markersize=4,
                label=f"{model_type} (conf: {data['confidence']:.2f})",
                alpha=0.8)
    
    ax1.set_title('🏞️ DHOMPO Basin - Model Comparison', fontweight='bold')
    ax1.set_xlabel('Time (Hours)')
    ax1.set_ylabel('Water Level (m)')
    ax1.legend()
    ax1.grid(True, alpha=0.3)
    ax1.xaxis.set_major_formatter(mdates.DateFormatter('%H:%M'))
    ax1.tick_params(axis='x', rotation=45)
    
    # Plot 2: PURWODADI Basin Comparison  
    ax2 = axes[0, 1]
    purwodadi_data = prediction_data.get('PURWODADI Basin', {})
    
    for sensor_code, data in purwodadi_data.items():
        model_type = data['model'].split('_')[1]  # Extract GRU/LSTM/TCN
        
        ax2.plot(data['times'], data['values'],
                color=colors[model_type],
                linestyle=line_styles[model_type], 
                linewidth=2,
                marker='s',
                markersize=4,
                label=f"{model_type} (conf: {data['confidence']:.2f})",
                alpha=0.8)
    
    ax2.set_title('🏔️ PURWODADI Basin - Model Comparison', fontweight='bold')
    ax2.set_xlabel('Time (Hours)')
    ax2.set_ylabel('Water Level (m)')
    ax2.legend()
    ax2.grid(True, alpha=0.3)
    ax2.xaxis.set_major_formatter(mdates.DateFormatter('%H:%M'))
    ax2.tick_params(axis='x', rotation=45)
    
    # Plot 3: Model Type Comparison (All GRU vs All LSTM vs All TCN)
    ax3 = axes[1, 0]
    
    model_aggregates = {'GRU': [], 'LSTM': [], 'TCN': []}
    time_base = None
    
    for basin_name, basin_data in prediction_data.items():
        for sensor_code, data in basin_data.items():
            model_type = data['model'].split('_')[1]
            if time_base is None:
                time_base = data['times']
            model_aggregates[model_type].extend(data['values'])
    
    # Calculate statistics for each model type
    for model_type, all_values in model_aggregates.items():
        if all_values:
            # Group values by time step (assuming same time steps for all)
            values_per_timestep = len(all_values) // len(time_base) if time_base else 1
            
            if values_per_timestep > 1:
                # Calculate mean and std for multiple sensors of same type
                timestep_means = []
                timestep_stds = []
                
                for i in range(len(time_base)):
                    timestep_values = []
                    for j in range(values_per_timestep):
                        idx = i + j * len(time_base)
                        if idx < len(all_values):
                            timestep_values.append(all_values[idx])
                    
                    if timestep_values:
                        timestep_means.append(np.mean(timestep_values))
                        timestep_stds.append(np.std(timestep_values))
                
                # Plot with error bars
                ax3.errorbar(time_base[:len(timestep_means)], timestep_means, 
                           yerr=timestep_stds,
                           color=colors[model_type],
                           linestyle=line_styles[model_type],
                           linewidth=2,
                           marker='D',
                           markersize=5,
                           label=f"{model_type} (avg ± std)",
                           alpha=0.8,
                           capsize=3)
            else:
                # Single sensor per type
                ax3.plot(time_base, all_values[:len(time_base)],
                        color=colors[model_type],
                        linestyle=line_styles[model_type],
                        linewidth=2,
                        marker='D',
                        markersize=5,
                        label=f"{model_type}",
                        alpha=0.8)
    
    ax3.set_title('🤖 Model Type Performance Comparison', fontweight='bold')
    ax3.set_xlabel('Time (Hours)')
    ax3.set_ylabel('Water Level (m)')
    ax3.legend()
    ax3.grid(True, alpha=0.3)
    ax3.xaxis.set_major_formatter(mdates.DateFormatter('%H:%M'))
    ax3.tick_params(axis='x', rotation=45)
    
    # Plot 4: Confidence Score Comparison
    ax4 = axes[1, 1]
    
    confidence_data = {'Basin': [], 'Model': [], 'Confidence': [], 'Sensor': []}
    
    for basin_name, basin_data in prediction_data.items():
        for sensor_code, data in basin_data.items():
            model_type = data['model'].split('_')[1]
            confidence_data['Basin'].append(basin_name.split()[0])  # DHOMPO/PURWODADI
            confidence_data['Model'].append(model_type)
            confidence_data['Confidence'].append(data['confidence'])
            confidence_data['Sensor'].append(sensor_code)
    
    # Create grouped bar chart
    df_conf = pd.DataFrame(confidence_data)
    
    if not df_conf.empty:
        basin_types = df_conf['Basin'].unique()
        model_types = df_conf['Model'].unique()
        
        x_pos = np.arange(len(basin_types))
        width = 0.25
        
        for i, model_type in enumerate(model_types):
            model_data = df_conf[df_conf['Model'] == model_type]
            confidences = []
            
            for basin in basin_types:
                basin_conf = model_data[model_data['Basin'] == basin]['Confidence']
                avg_conf = basin_conf.mean() if len(basin_conf) > 0 else 0
                confidences.append(avg_conf)
            
            ax4.bar(x_pos + i * width, confidences,
                   width, 
                   label=model_type,
                   color=colors[model_type],
                   alpha=0.8)
            
            # Add value labels on bars
            for j, conf in enumerate(confidences):
                ax4.text(x_pos[j] + i * width, conf + 0.01, 
                        f'{conf:.2f}', 
                        ha='center', va='bottom', fontsize=9)
    
    ax4.set_title('📊 Model Confidence Comparison by Basin', fontweight='bold')
    ax4.set_xlabel('River Basin')
    ax4.set_ylabel('Confidence Score')
    ax4.set_xticks(x_pos + width)
    ax4.set_xticklabels(basin_types)
    ax4.legend()
    ax4.grid(True, alpha=0.3, axis='y')
    ax4.set_ylim(0, 1)
    
    # Adjust layout and save
    plt.tight_layout()
    
    # Save the plot
    output_file = 'flood_forecasting_comparison.png'
    plt.savefig(output_file, dpi=300, bbox_inches='tight', 
                facecolor='white', edgecolor='none')
    
    print(f'\n📈 Visualization saved as: {output_file}')
    print('📊 Graph shows:')
    print('   1. DHOMPO Basin model comparison')
    print('   2. PURWODADI Basin model comparison') 
    print('   3. Model type performance analysis')
    print('   4. Confidence score comparison')
    
    # Show the plot
    plt.show()
    
    # Print summary statistics
    print('\n📋 PREDICTION SUMMARY:')
    print('='*50)
    
    for basin_name, basin_data in prediction_data.items():
        print(f'\n🌊 {basin_name}:')
        for sensor_code, data in basin_data.items():
            model = data['model']
            conf = data['confidence']
            values = data['values']
            avg_level = np.mean(values) if values else 0
            trend = "📈 Rising" if len(values) > 1 and values[-1] > values[0] else "📉 Falling"
            
            print(f'   {sensor_code:12} | {model:12} | Conf: {conf:.3f} | Avg: {avg_level:.2f}m | {trend}')

if __name__ == "__main__":
    create_forecasting_graphs()