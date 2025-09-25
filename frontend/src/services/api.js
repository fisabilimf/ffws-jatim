const API_BASE_URL = "https://ffws-backend.rachmanesa.com/api";

/**
 * Fetches test data from the API.
 * @returns {Promise<Object>} A promise that resolves to the JSON data.
 * @throws {Error} Throws an error if the network response is not ok.
 */
export const fetchTestData = async () => {
    try {
        const response = await fetch(`${API_BASE_URL}/test`);
        if (!response.ok) {
            throw new Error("Network response was not ok");
        }
        const data = await response.json();
        return data;
    } catch (error) {
        console.error("There was a problem with the fetch operation:", error);
        throw error;
    }
};
