import { fetchWithAuth } from "./apiClient";

/**
 * GeoJSON Mapping Service
 * Handles dynamic loading of GeoJSON files based on discharge values
 */

/**
 * Fetches all geojson mappings from the API.
 * @returns {Promise<Array>} A promise that resolves to an array of geojson mapping objects.
 */
export const fetchGeojsonMappings = async () => {
    return await fetchWithAuth("/geojson-mapping");
};

/**
 * Fetches geojson file based on sensor discharge value
 * @param {string} sensorCode - The sensor code to get geojson for
 * @param {number} dischargeValue - The discharge value to match against ranges
 * @returns {Promise<Object>} A promise that resolves to geojson data
 */
export const fetchGeojsonByDischarge = async (sensorCode, dischargeValue) => {
    const response = await fetch(`${process.env.REACT_APP_API_BASE_URL || '/api'}/geojson-mapping/by-discharge`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            sensor_code: sensorCode,
            discharge_value: dischargeValue
        })
    });

    if (!response.ok) {
        if (response.status === 404) {
            return null; // No geojson found for this discharge value
        }
        throw new Error(`Failed to fetch geojson: ${response.statusText}`);
    }

    return await response.json();
};

/**
 * Fetches geojson file based on latest calculated discharge for a sensor
 * @param {string} sensorCode - The sensor code to get latest discharge for
 * @returns {Promise<Object|null>} A promise that resolves to geojson data or null if not found
 */
export const fetchGeojsonByLatestDischarge = async (sensorCode) => {
    try {
        const response = await fetch(`${process.env.REACT_APP_API_BASE_URL || '/api'}/geojson-mapping/sensor/${sensorCode}/latest`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            if (response.status === 404) {
                return null; // No discharge data or geojson mapping found
            }
            throw new Error(`Failed to fetch geojson: ${response.statusText}`);
        }

        // Get metadata from response headers
        const geojsonData = await response.json();
        const metadata = {
            source: response.headers.get('X-Geojson-Source'),
            label: response.headers.get('X-Geojson-Label'),
            description: response.headers.get('X-Geojson-Description')
        };

        return {
            data: geojsonData,
            metadata
        };
    } catch (error) {
        console.error('Error fetching geojson by latest discharge:', error);
        return null;
    }
};

/**
 * Fetches geojson file based on latest predicted discharge for a sensor
 * @param {string} sensorCode - The sensor code to get latest predicted discharge for
 * @returns {Promise<Object|null>} A promise that resolves to geojson data or null if not found
 */
export const fetchGeojsonByLatestPredictedDischarge = async (sensorCode) => {
    try {
        const response = await fetch(`${process.env.REACT_APP_API_BASE_URL || '/api'}/geojson-mapping/sensor/${sensorCode}/predicted`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            if (response.status === 404) {
                return null;
            }
            throw new Error(`Failed to fetch predicted geojson: ${response.statusText}`);
        }

        const geojsonData = await response.json();
        const metadata = {
            source: response.headers.get('X-Geojson-Source'),
            label: response.headers.get('X-Geojson-Label'),
            description: response.headers.get('X-Geojson-Description')
        };

        return {
            data: geojsonData,
            metadata
        };
    } catch (error) {
        console.error('Error fetching geojson by predicted discharge:', error);
        return null;
    }
};

/**
 * Fetches all available geojson mappings for a specific sensor
 * @param {string} sensorCode - The sensor code to get mappings for
 * @returns {Promise<Object>} A promise that resolves to mappings data
 */
export const fetchGeojsonMappingsForSensor = async (sensorCode) => {
    return await fetchWithAuth(`/geojson-mapping/sensor/${sensorCode}/mappings`);
};

/**
 * Auto-update geojson based on real-time discharge data
 * @param {string} sensorCode - The sensor code to monitor
 * @param {Function} onUpdate - Callback function when geojson is updated
 * @param {number} intervalMs - Update interval in milliseconds (default 30 seconds)
 * @returns {Function} Function to stop the auto-update
 */
export const setupAutoGeojsonUpdate = (sensorCode, onUpdate, intervalMs = 30000) => {
    let intervalId;
    
    const updateGeojson = async () => {
        try {
            const result = await fetchGeojsonByLatestDischarge(sensorCode);
            if (result && result.data) {
                onUpdate(result.data, result.metadata);
                console.log(`Updated GeoJSON for sensor ${sensorCode}`, result.metadata);
            }
        } catch (error) {
            console.error('Error in auto geojson update:', error);
        }
    };

    // Initial load
    updateGeojson();
    
    // Set up periodic updates
    intervalId = setInterval(updateGeojson, intervalMs);
    
    // Return cleanup function
    return () => {
        if (intervalId) {
            clearInterval(intervalId);
        }
    };
};

/**
 * Get appropriate geojson styling based on discharge range
 * @param {number} dischargeValue - The discharge value
 * @returns {Object} Mapbox styling configuration
 */
export const getGeojsonStyle = (dischargeValue) => {
    if (dischargeValue <= 10) {
        return {
            'fill-color': '#10B981', // Green - Normal
            'fill-opacity': 0.3,
            'line-color': '#10B981',
            'line-width': 2
        };
    } else if (dischargeValue <= 25) {
        return {
            'fill-color': '#F59E0B', // Yellow - Warning
            'fill-opacity': 0.5,
            'line-color': '#F59E0B',
            'line-width': 2
        };
    } else {
        return {
            'fill-color': '#EF4444', // Red - Danger
            'fill-opacity': 0.7,
            'line-color': '#EF4444',
            'line-width': 3
        };
    }
};

/**
 * Hook for managing geojson state in React components
 * @param {string} sensorCode - The sensor code to monitor
 * @returns {Object} State and functions for geojson management
 */
export const useGeojsonMapping = (sensorCode) => {
    const [geojsonData, setGeojsonData] = React.useState(null);
    const [metadata, setMetadata] = React.useState(null);
    const [loading, setLoading] = React.useState(false);
    const [error, setError] = React.useState(null);

    const loadGeojson = React.useCallback(async (dischargeValue = null) => {
        if (!sensorCode) return;
        
        setLoading(true);
        setError(null);
        
        try {
            let result;
            if (dischargeValue !== null) {
                result = await fetchGeojsonByDischarge(sensorCode, dischargeValue);
                result = result ? { data: result, metadata: {} } : null;
            } else {
                result = await fetchGeojsonByLatestDischarge(sensorCode);
            }
            
            if (result) {
                setGeojsonData(result.data);
                setMetadata(result.metadata);
            } else {
                setGeojsonData(null);
                setMetadata(null);
            }
        } catch (err) {
            setError(err.message);
            console.error('Error loading geojson:', err);
        } finally {
            setLoading(false);
        }
    }, [sensorCode]);

    React.useEffect(() => {
        loadGeojson();
    }, [loadGeojson]);

    return {
        geojsonData,
        metadata,
        loading,
        error,
        loadGeojson,
        reload: () => loadGeojson()
    };
};