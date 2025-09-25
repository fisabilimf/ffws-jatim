const API_BASE_URL = "https://ffws-backend.rachmanesa.com/api";
const AUTH_TOKEN = "4|xjDilbywJdpuubd1HCasVs5AJ2xGVsGn4zK7g5Nd241a1f1d";

/**
 * A custom fetch wrapper that adds the authentication token to the request headers.
 * @param {string} endpoint - The API endpoint to call.
 * @param {object} options - Optional fetch options (e.g., method, body).
 * @returns {Promise<any>} A promise that resolves to the JSON response.
 */
export const fetchWithAuth = async (endpoint, options = {}) => {
    const headers = {
        "Content-Type": "application/json",
        Authorization: `Bearer ${AUTH_TOKEN}`,
        ...options.headers,
    };

    try {
        const response = await fetch(`${API_BASE_URL}${endpoint}`, { ...options, headers });
        if (!response.ok) {
            throw new Error("Network response was not ok");
        }
        return response.json();
    } catch (error) {
        console.error(`There was a problem with the fetch operation for endpoint ${endpoint}:`, error);
        throw error;
    }
};
