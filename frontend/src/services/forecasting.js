import { fetchWithAuth } from "./apiClient";

/**
 * Forecasting Service
 * Handles ML predictions and flood forecasting integration
 */

/**
 * Check health status of the forecasting service
 * @returns {Promise<Object>} Health status and service information
 */
export const checkForecastingHealth = async () => {
    try {
        return await fetchWithAuth("/forecasting/health");
    } catch (error) {
        console.error('Forecasting health check failed:', error);
        return {
            status: 'unhealthy',
            error: error.message,
            integration: 'inactive'
        };
    }
};

/**
 * Run ML forecast for a specific sensor
 * @param {string} sensorCode - The sensor code to forecast
 * @param {Object} options - Forecasting options
 * @param {string} options.modelCode - Specific ML model to use (optional)
 * @param {number} options.predictionHours - Hours to predict (1-24, default 24)
 * @param {number} options.stepHours - Time step in hours (0.1-6.0, default 1.0)
 * @param {boolean} options.saveToDatabase - Save predictions to database (default true)
 * @returns {Promise<Object>} Forecast results
 */
export const runForecast = async (sensorCode, options = {}) => {
    const payload = {
        sensor_code: sensorCode,
        model_code: options.modelCode,
        prediction_hours: options.predictionHours || 24,
        step_hours: options.stepHours || 1.0,
        save_to_database: options.saveToDatabase !== false
    };

    try {
        const result = await fetchWithAuth("/forecasting/run", "POST", payload);
        console.log(`Forecast completed for sensor ${sensorCode}:`, {
            model_used: result.model_used,
            prediction_count: result.prediction_count
        });
        return result;
    } catch (error) {
        console.error('Forecast failed:', error);
        throw error;
    }
};

/**
 * Run forecast for all sensors in a river basin
 * @param {string} riverBasinCode - The river basin code
 * @param {boolean} onlyActive - Only forecast for active sensors (default true)
 * @returns {Promise<Object>} Basin forecast results
 */
export const runBasinForecast = async (riverBasinCode, onlyActive = true) => {
    const payload = {
        river_basin_code: riverBasinCode,
        only_active: onlyActive
    };

    try {
        const result = await fetchWithAuth("/forecasting/run-basin", "POST", payload);
        console.log(`Basin forecast completed for ${riverBasinCode}:`, {
            forecasts_generated: result.forecasts_generated
        });
        return result;
    } catch (error) {
        console.error('Basin forecast failed:', error);
        throw error;
    }
};

/**
 * Run fallback forecast for sensors without ML models
 * @param {string} sensorCode - The sensor code
 * @param {number} hoursAhead - Hours to predict ahead (1-24, default 6)
 * @returns {Promise<Object>} Fallback forecast results
 */
export const runFallbackForecast = async (sensorCode, hoursAhead = 6) => {
    const payload = {
        sensor_code: sensorCode,
        hours_ahead: hoursAhead
    };

    try {
        const result = await fetchWithAuth("/forecasting/fallback", "POST", payload);
        console.log(`Fallback forecast completed for sensor ${sensorCode}`);
        return result;
    } catch (error) {
        console.error('Fallback forecast failed:', error);
        throw error;
    }
};

/**
 * Get latest predictions for a sensor from database
 * @param {string} sensorCode - The sensor code
 * @returns {Promise<Object>} Prediction data
 */
export const getLatestPredictions = async (sensorCode) => {
    try {
        return await fetchWithAuth(`/forecasting/predictions/${sensorCode}`);
    } catch (error) {
        console.error('Failed to get predictions:', error);
        return {
            sensor_code: sensorCode,
            predictions: [],
            message: 'No predictions available'
        };
    }
};

/**
 * Get predictions with associated GeoJSON mappings
 * @param {string} sensorCode - The sensor code
 * @returns {Promise<Object>} Predictions with GeoJSON mapping info
 */
export const getPredictionsWithGeojson = async (sensorCode) => {
    try {
        return await fetchWithAuth(`/forecasting/predictions/${sensorCode}/with-geojson`);
    } catch (error) {
        console.error('Failed to get predictions with geojson:', error);
        return {
            sensor_code: sensorCode,
            predictions: [],
            geojson_mappings: [],
            message: 'No predictions available'
        };
    }
};

/**
 * Get available ML models
 * @returns {Promise<Array>} List of available models
 */
export const getForecastingModels = async () => {
    try {
        return await fetchWithAuth("/forecasting/models");
    } catch (error) {
        console.error('Failed to get forecasting models:', error);
        return [];
    }
};

/**
 * Get sensors available for forecasting
 * @returns {Promise<Array>} List of forecasting-enabled sensors
 */
export const getForecastingSensors = async () => {
    try {
        return await fetchWithAuth("/forecasting/sensors");
    } catch (error) {
        console.error('Failed to get forecasting sensors:', error);
        return [];
    }
};

/**
 * Smart forecast runner - automatically chooses best forecast method
 * @param {string} sensorCode - The sensor code
 * @param {Object} options - Forecast options
 * @returns {Promise<Object>} Forecast results
 */
export const runSmartForecast = async (sensorCode, options = {}) => {
    try {
        // First try to get available models for this sensor
        const sensors = await getForecastingSensors();
        const sensor = sensors.find(s => s.code === sensorCode);
        
        if (sensor && sensor.model_code) {
            // Sensor has ML model - use ML forecast
            console.log(`Using ML forecast for sensor ${sensorCode} with model ${sensor.model_code}`);
            return await runForecast(sensorCode, {
                ...options,
                modelCode: sensor.model_code
            });
        } else {
            // Sensor doesn't have ML model - use fallback
            console.log(`Using fallback forecast for sensor ${sensorCode}`);
            return await runFallbackForecast(sensorCode, options.predictionHours || 6);
        }
    } catch (error) {
        console.error('Smart forecast failed:', error);
        throw error;
    }
};

/**
 * Auto-forecast system for real-time predictions
 * @param {Array} sensorCodes - List of sensor codes to monitor
 * @param {Function} onUpdate - Callback when new predictions are available
 * @param {number} intervalMs - Update interval in milliseconds (default 10 minutes)
 * @returns {Function} Cleanup function to stop auto-forecasting
 */
export const setupAutoForecast = (sensorCodes, onUpdate, intervalMs = 600000) => {
    let intervalId;
    
    const runAutoForecast = async () => {
        try {
            for (const sensorCode of sensorCodes) {
                const predictions = await getLatestPredictions(sensorCode);
                
                // Check if we need new predictions (no predictions or oldest prediction is too old)
                const needsNewForecast = predictions.predictions.length === 0 || 
                    new Date(predictions.predictions[0].predicted_at) < new Date(Date.now() + 30 * 60 * 1000);
                
                if (needsNewForecast) {
                    console.log(`Running auto-forecast for sensor ${sensorCode}`);
                    await runSmartForecast(sensorCode, { predictionHours: 6 });
                    
                    // Get updated predictions
                    const updatedPredictions = await getLatestPredictions(sensorCode);
                    onUpdate(sensorCode, updatedPredictions);
                }
            }
        } catch (error) {
            console.error('Auto-forecast error:', error);
        }
    };

    // Initial run
    runAutoForecast();
    
    // Set up periodic updates
    intervalId = setInterval(runAutoForecast, intervalMs);
    
    // Return cleanup function
    return () => {
        if (intervalId) {
            clearInterval(intervalId);
        }
    };
};

/**
 * Get forecast timeline data for visualization
 * @param {string} sensorCode - The sensor code
 * @param {number} hoursAhead - Hours to include in timeline (default 24)
 * @returns {Promise<Object>} Timeline data for charts
 */
export const getForecastTimeline = async (sensorCode, hoursAhead = 24) => {
    try {
        const predictions = await getLatestPredictions(sensorCode);
        
        if (!predictions.predictions || predictions.predictions.length === 0) {
            return {
                sensor_code: sensorCode,
                timeline: [],
                summary: {
                    max_discharge: null,
                    min_discharge: null,
                    avg_discharge: null,
                    peak_time: null,
                    trend: 'unknown'
                }
            };
        }

        // Filter predictions within time range
        const cutoffTime = new Date(Date.now() + hoursAhead * 60 * 60 * 1000);
        const filteredPredictions = predictions.predictions.filter(
            p => new Date(p.predicted_at) <= cutoffTime
        );

        // Create timeline data
        const timeline = filteredPredictions.map(p => ({
            time: p.predicted_at,
            discharge: p.predicted_discharge,
            confidence: p.confidence_score,
            threshold: p.threshold_status,
            status: getDischargeStatus(p.predicted_discharge)
        }));

        // Calculate summary statistics
        const dischargeValues = filteredPredictions.map(p => p.predicted_discharge);
        const maxDischarge = Math.max(...dischargeValues);
        const minDischarge = Math.min(...dischargeValues);
        const avgDischarge = dischargeValues.reduce((a, b) => a + b, 0) / dischargeValues.length;
        
        const peakPrediction = filteredPredictions.find(p => p.predicted_discharge === maxDischarge);
        const trend = calculateTrend(dischargeValues);

        return {
            sensor_code: sensorCode,
            timeline,
            summary: {
                max_discharge: maxDischarge,
                min_discharge: minDischarge,
                avg_discharge: avgDischarge,
                peak_time: peakPrediction?.predicted_at,
                trend,
                total_predictions: timeline.length
            }
        };
    } catch (error) {
        console.error('Failed to get forecast timeline:', error);
        throw error;
    }
};

/**
 * Helper function to determine discharge status
 */
const getDischargeStatus = (discharge) => {
    if (discharge <= 10) return 'normal';
    if (discharge <= 25) return 'warning';
    return 'danger';
};

/**
 * Helper function to calculate trend from discharge values
 */
const calculateTrend = (values) => {
    if (values.length < 2) return 'stable';
    
    const firstHalf = values.slice(0, Math.floor(values.length / 2));
    const secondHalf = values.slice(Math.floor(values.length / 2));
    
    const firstAvg = firstHalf.reduce((a, b) => a + b, 0) / firstHalf.length;
    const secondAvg = secondHalf.reduce((a, b) => a + b, 0) / secondHalf.length;
    
    const diff = secondAvg - firstAvg;
    
    if (Math.abs(diff) < 1) return 'stable';
    return diff > 0 ? 'increasing' : 'decreasing';
};