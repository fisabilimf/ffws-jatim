import React, { createContext, useState, useEffect, useCallback, useRef } from "react";
import { fetchTestData } from "@/services/api";
import { fetchDevices } from "@/services/devices";

export const AppContext = createContext();

export const AppProvider = ({ children }) => {
    // Test data state
    const [testData, setTestData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    // Devices state
    const [devices, setDevices] = useState([]);
    const [devicesLoading, setDevicesLoading] = useState(true);
    const [devicesError, setDevicesError] = useState(null);
    const [lastFetch, setLastFetch] = useState(null);
    const isFetchingRef = useRef(false);

    // Konfigurasi auto refresh dari ENV
    const REFRESH_ENABLED = (import.meta?.env?.VITE_DEVICES_REFRESH_ENABLED ?? "true") === "true";
    const REFRESH_MS = Number(import.meta?.env?.VITE_DEVICES_REFRESH_MS ?? 30000);

    // Load devices function
    const loadDevices = useCallback(async (retryCount = 0, { isBackground = false } = {}) => {
        const maxRetries = 3;
        
        try {
            // Hindari overlap fetch
            if (isFetchingRef.current) return;
            isFetchingRef.current = true;

            if (!isBackground) {
                setDevicesLoading(true);
            }
            setDevicesError(null);
            
            console.log('=== APP CONTEXT: FETCHING DEVICES ===');
            console.log(`Attempt ${retryCount + 1}/${maxRetries + 1}`);
            
            // Test data fallback untuk debugging dengan status yang berbeda
            const testData = [
                {
                    id: 1,
                    name: "Test Station Safe",
                    latitude: -7.5,
                    longitude: 112.5,
                    status: "safe"
                },
                {
                    id: 2,
                    name: "Test Station Warning", 
                    latitude: -7.6,
                    longitude: 112.6,
                    status: "warning"
                },
                {
                    id: 3,
                    name: "Test Station Alert",
                    latitude: -7.4,
                    longitude: 112.4,
                    status: "alert"
                },
                {
                    id: 4,
                    name: "Test Station Unknown",
                    latitude: -7.3,
                    longitude: 112.3,
                    status: "unknown"
                }
            ];
            
            let devicesData;
            try {
                devicesData = await fetchDevices();
                console.log('API call successful, using real data');
            } catch (apiError) {
                console.warn('API call failed, using test data:', apiError);
                devicesData = testData;
            }
            
            console.log('Raw API response:', devicesData);
            console.log('API response type:', typeof devicesData);
            console.log('Is array:', Array.isArray(devicesData));
            
            if (!devicesData || !Array.isArray(devicesData)) {
                console.error('Invalid device data structure:', {
                    data: devicesData,
                    type: typeof devicesData,
                    isArray: Array.isArray(devicesData)
                });
                throw new Error('Invalid device data received from API');
            }
            
            console.log(`API returned ${devicesData.length} devices`);
            
            // Validasi dan filter devices yang valid
            console.log('Processing devices for validation...');
            const validDevices = devicesData.filter((device, index) => {
                console.log(`Processing device ${index + 1}:`, device);
                
                const hasName = device && (device.name || device.device_name || device.station_name);
                const hasCoordinates = device && (
                    (device.latitude && device.longitude) || 
                    (device.coordinates && Array.isArray(device.coordinates))
                );
                const isValid = hasName && hasCoordinates;
                
                console.log(`Device ${index + 1} validation:`, {
                    name: device?.name || device?.device_name || device?.station_name,
                    hasName,
                    hasCoordinates,
                    coordinates: device?.coordinates,
                    latitude: device?.latitude,
                    longitude: device?.longitude,
                    isValid
                });
                
                if (!isValid) {
                    console.warn(`Device ${index + 1} filtered out - missing required fields`);
                }
                
                return isValid;
            });
            
            console.log(`Successfully fetched ${devicesData.length} devices`);
            console.log(`Valid devices: ${validDevices.length}`);
            console.log('Sample valid devices:', validDevices.slice(0, 2));
            console.log('Device structure sample:', validDevices[0]);
            console.log('Device IDs:', validDevices.map(d => d.id));
            console.log('Device names:', validDevices.map(d => d.name || d.device_name || d.station_name));
            
            if (validDevices.length === 0) {
                throw new Error('No valid devices found - all devices missing name or coordinates');
            }
            
            // Hanya update state jika data berubah untuk mencegah re-render tidak perlu
            const isSameLength = devices.length === validDevices.length;
            const sameIds = isSameLength && devices.every((d, i) => d.id === validDevices[i].id);
            if (!isSameLength || !sameIds) {
                setDevices(validDevices);
            }
            setLastFetch(Date.now());
            console.log('Device data stored in context successfully');
            console.log('=== APP CONTEXT: FETCH COMPLETED ===');
            
        } catch (error) {
            console.error(`=== APP CONTEXT: FETCH ERROR ===`);
            console.error(`Attempt ${retryCount + 1} failed:`, error);
            
            if (retryCount < maxRetries) {
                const retryDelay = (retryCount + 1) * 2000;
                console.log(`Retrying in ${retryDelay}ms...`);
                setTimeout(() => {
                    loadDevices(retryCount + 1, { isBackground });
                }, retryDelay);
            } else {
                console.error(`All ${maxRetries + 1} attempts failed`);
                setDevicesError(`Failed to fetch devices after ${maxRetries + 1} attempts: ${error.message}`);
            }
            console.error('=== END APP CONTEXT: FETCH ERROR ===');
        } finally {
            isFetchingRef.current = false;
            setDevicesLoading(false);
            console.log('Devices loading state set to false');
        }
    }, [devices.length]);

    // Load test data
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

    // Load devices on mount
    useEffect(() => {
        loadDevices(0, { isBackground: false });
    }, [loadDevices]);

    // Auto refresh devices setiap 30 detik
    useEffect(() => {
        if (!REFRESH_ENABLED) return;
        const interval = setInterval(() => {
            if (document.visibilityState !== 'visible') return;
            console.log('Auto refreshing devices data...');
            loadDevices(0, { isBackground: true });
        }, REFRESH_MS);

        return () => clearInterval(interval);
    }, [loadDevices, REFRESH_ENABLED, REFRESH_MS]);

    const value = {
        // Test data
        testData,
        loading,
        error,
        
        // Devices data
        devices,
        devicesLoading,
        devicesError,
        lastFetch,
        refreshDevices: loadDevices,
        hasDevices: devices.length > 0
    };

    return <AppContext.Provider value={value}>{children}</AppContext.Provider>;
};
