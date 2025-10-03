import React, { createContext, useState, useEffect } from "react";
import { fetchProvinces, fetchCities, fetchDevices } from "@/services/api";

export const AppContext = createContext();

export const AppProvider = ({ children }) => {
    const [provinces, setProvinces] = useState([]);
    const [cities, setCities] = useState([]);
    const [devices, setDevices] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        const loadData = async () => {
            try {
                setLoading(true);
                setError(null);
                
                console.log('Loading data from backend...');
                
                // Load all data in parallel
                const [provincesData, citiesData, devicesData] = await Promise.all([
                    fetchProvinces(),
                    fetchCities(),
                    fetchDevices().catch(() => []) // Devices might be empty, don't fail
                ]);

                console.log('Provinces loaded:', provincesData);
                console.log('Cities loaded:', citiesData);
                console.log('Devices loaded:', devicesData);

                setProvinces(provincesData || []);
                setCities(citiesData || []);
                setDevices(devicesData || []);
                
            } catch (err) {
                console.error('Error loading data:', err);
                setError(err.message);
            } finally {
                setLoading(false);
            }
        };

        loadData();
    }, []);

    const value = {
        provinces,
        cities,
        devices,
        loading,
        error,
        // Utility functions
        refreshData: () => window.location.reload(),
    };

    return <AppContext.Provider value={value}>{children}</AppContext.Provider>;
};
