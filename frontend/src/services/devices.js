import { fetchWithAuth } from "./apiClient";

/**
 * Fetches devices data from the API.
 * @returns {Promise<Array>} A promise that resolves to an array of device objects.
 */
export const fetchDevices = async () => {
    const data = await fetchWithAuth("/public/devices");
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
 * Fetches device by code from the API.
 * @param {string} deviceCode - The device code to fetch
 * @returns {Promise<Object>} A promise that resolves to the device object.
 */
export const fetchDeviceByCode = async (deviceCode) => {
    const data = await fetchWithAuth(`/devices/code/${deviceCode}`);
    return data.data;
};

/**
 * Fetches device statistics from the API.
 * @returns {Promise<Object>} A promise that resolves to device statistics.
 */
export const fetchDeviceStatistics = async () => {
    const data = await fetchWithAuth("/devices/statistics");
    return data.data;
};

/**
 * Fetches sensors for a specific device from the API.
 * @param {string} deviceCode - The device code
 * @returns {Promise<Array>} A promise that resolves to an array of sensor objects.
 */
export const fetchDeviceSensors = async (deviceCode) => {
    const data = await fetchWithAuth(`/devices/${deviceCode}/sensors`);
    return data.data;
};

/**
 * Fetches latest data for a specific device from the API.
 * @param {string} deviceCode - The device code
 * @returns {Promise<Array>} A promise that resolves to an array of latest data objects.
 */
export const fetchDeviceLatestData = async (deviceCode) => {
    const data = await fetchWithAuth(`/data-actuals/device/${deviceCode}/latest`);
    return data.data;
};
