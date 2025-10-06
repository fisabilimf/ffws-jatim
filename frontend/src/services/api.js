import { fetchWithAuth } from "./apiClient";

/**
 * Fetches test data from the API.
 * @returns {Promise<Object>} A promise that resolves to the JSON data.
 */
export const fetchTestData = async () => {
    return await fetchWithAuth("/test");
};

// ============= GEOGRAPHIC DATA SERVICES =============

/**
 * Fetches all provinces from the API.
 * @returns {Promise<Array>} A promise that resolves to an array of province objects.
 */
export const fetchProvinces = async () => {
    const data = await fetchWithAuth("/public/provinces");
    return data.data;
};

/**
 * Fetches all cities from the API.
 * @returns {Promise<Array>} A promise that resolves to an array of city objects.
 */
export const fetchCities = async () => {
    const data = await fetchWithAuth("/public/cities");
    return data.data;
};

/**
 * Fetches all regencies from the API.
 * @returns {Promise<Array>} A promise that resolves to an array of regency objects.
 */
export const fetchRegencies = async () => {
    const data = await fetchWithAuth("/public/regencies");
    return data.data;
};

/**
 * Fetches all villages from the API.
 * @returns {Promise<Array>} A promise that resolves to an array of village objects.
 */
export const fetchVillages = async () => {
    const data = await fetchWithAuth("/public/villages");
    return data.data;
};

/**
 * Fetches all watersheds from the API.
 * @returns {Promise<Array>} A promise that resolves to an array of watershed objects.
 */
export const fetchWatersheds = async () => {
    const data = await fetchWithAuth("/watersheds");
    return data.data;
};

/**
 * Fetches all river basins from the API.
 * @returns {Promise<Array>} A promise that resolves to an array of river basin objects.
 */
export const fetchRiverBasins = async () => {
    const data = await fetchWithAuth("/public/river-basins");
    return data.data;
};

// ============= DEVICE AND SENSOR SERVICES =============

/**
 * Fetches all devices from the API.
 * @returns {Promise<Array>} A promise that resolves to an array of device objects.
 */
export const fetchDevices = async () => {
    const data = await fetchWithAuth("/public/devices");
    return data.data;
};

/**
 * Fetches all sensors from the API.
 * @returns {Promise<Array>} A promise that resolves to an array of sensor objects.
 */
export const fetchSensors = async () => {
    const data = await fetchWithAuth("/public/sensors");
    return data.data;
};

/**
 * Fetches active devices from the API.
 * @returns {Promise<Array>} A promise that resolves to an array of active device objects.
 */
export const fetchActiveDevices = async () => {
    const data = await fetchWithAuth("/devices/active/list");
    return data.data;
};

/**
 * Fetches active sensors from the API.
 * @returns {Promise<Array>} A promise that resolves to an array of active sensor objects.
 */
export const fetchActiveSensors = async () => {
    const data = await fetchWithAuth("/sensors/active/list");
    return data.data;
};

// ============= DATA SERVICES =============

/**
 * Fetches actual sensor data from the API.
 * @returns {Promise<Array>} A promise that resolves to an array of actual data objects.
 */
export const fetchDataActuals = async () => {
    const data = await fetchWithAuth("/data-actuals");
    return data.data;
};

/**
 * Fetches latest actual data from the API.
 * @returns {Promise<Array>} A promise that resolves to an array of latest actual data objects.
 */
export const fetchLatestActuals = async () => {
    const data = await fetchWithAuth("/data-actuals/latest/list");
    return data.data;
};

/**
 * Fetches prediction data from the API.
 * @returns {Promise<Array>} A promise that resolves to an array of prediction objects.
 */
export const fetchPredictions = async () => {
    const data = await fetchWithAuth("/predictions");
    return data.data;
};

/**
 * Fetches latest predictions from the API.
 * @returns {Promise<Array>} A promise that resolves to an array of latest prediction objects.
 */
export const fetchLatestPredictions = async () => {
    const data = await fetchWithAuth("/predictions/latest/list");
    return data.data;
};

// ============= AUTHENTICATION SERVICES =============

/**
 * Login user
 * @param {Object} credentials - User credentials (email, password)
 * @returns {Promise<Object>} A promise that resolves to the login response.
 */
export const loginUser = async (credentials) => {
    return await fetchWithAuth("/auth/login", {
        method: "POST",
        body: JSON.stringify(credentials),
    });
};

/**
 * Get current user info
 * @returns {Promise<Object>} A promise that resolves to the user object.
 */
export const getCurrentUser = async () => {
    return await fetchWithAuth("/auth/me");
};

// ============= STATISTICS SERVICES =============

/**
 * Fetches device statistics from the API.
 * @returns {Promise<Object>} A promise that resolves to device statistics.
 */
export const fetchDeviceStatistics = async () => {
    const data = await fetchWithAuth("/devices/statistics");
    return data.data;
};

/**
 * Fetches sensor statistics from the API.
 * @returns {Promise<Object>} A promise that resolves to sensor statistics.
 */
export const fetchSensorStatistics = async () => {
    const data = await fetchWithAuth("/sensors/statistics");
    return data.data;
};

// ============= GEOJSON MAPPING SERVICES =============

/**
 * Fetches all geojson mappings from the API.
 * @returns {Promise<Array>} A promise that resolves to an array of geojson mapping objects.
 */
export const fetchGeojsonMappings = async () => {
    return await fetchWithAuth("/geojson-mapping");
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
                return null;
            }
            throw new Error(`Failed to fetch geojson: ${response.statusText}`);
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
        console.error('Error fetching geojson by latest discharge:', error);
        return null;
    }
};
