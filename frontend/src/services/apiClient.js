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

    const fullUrl = `${API_BASE_URL}${endpoint}`;

    try {
        const response = await fetch(fullUrl, { ...options, headers });
        
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`Network response was not ok: ${response.status} - ${errorText}`);
        }
        
        const data = await response.json();
        return data;
    } catch (error) {
        throw error;
    }
};
