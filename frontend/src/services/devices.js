    import { fetchWithAuth } from "./apiClient";

    /**
     * Fetches devices data from the API.
     * @returns {Promise<Array>} A promise that resolves to an array of device objects.
     */
    export const fetchDevices = async () => {
        const data = await fetchWithAuth("/devices");
        return data.data;
    };
