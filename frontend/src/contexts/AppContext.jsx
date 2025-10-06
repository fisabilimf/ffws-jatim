    import React, { createContext, useState, useEffect } from "react";
import { fetchTestData } from "@/services/api";

export const AppContext = createContext();

export const AppProvider = ({ children }) => {
    const [testData, setTestData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        const getTestData = async () => {
            try {
                setLoading(true);
                const data = await fetchTestData();
                setTestData(data);
                setError(null);
            } catch (err) {
                setError(err.message);
                setTestData(null);
            } finally {
                setLoading(false);
            }
        };

        getTestData();
    }, []);

    const value = {
        testData,
        loading,
        error,
    };

    return <AppContext.Provider value={value}>{children}</AppContext.Provider>;
};
