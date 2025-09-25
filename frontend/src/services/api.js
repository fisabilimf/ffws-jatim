import { fetchWithAuth } from "./apiClient";

/**
 * Fetches test data from the API.
 * @returns {Promise<Object>} A promise that resolves to the JSON data.
 */
export const fetchTestData = async () => {
    return await fetchWithAuth("/test");
};
