import React, { useState, useEffect, useRef } from "react";
import { fetchDevices } from "@/services/devices";

const AutoSwitchToggle = ({
    tickerData,
    onStationChange,
    currentStationIndex,
    onAutoSwitchToggle,
    interval = 5000, // 5 detik
}) => {
    const [isPlaying, setIsPlaying] = useState(false);
    const [currentIndex, setCurrentIndex] = useState(0);
    const [devices, setDevices] = useState([]);
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState(null);
    const intervalRef = useRef(null);

    // Fetch devices from API
    useEffect(() => {
        const loadDevices = async () => {
            console.log("Starting to fetch devices from API...");
            setIsLoading(true);
            setError(null);
            try {
                const devicesData = await fetchDevices();
                console.log("Raw API response:", devicesData);
                console.log("Devices array length:", devicesData ? devicesData.length : 0);
                
                if (devicesData && Array.isArray(devicesData) && devicesData.length > 0) {
                    console.log("First device sample:", devicesData[0]);
                    setDevices(devicesData);
                } else {
                    console.warn("No devices data received or empty array");
                    // Fallback data for testing
                    const fallbackDevices = [
                        {
                            id: 1,
                            name: "Test Device 1",
                            latitude: -7.5,
                            longitude: 112.5,
                            river_basin: { name: "Test Basin" }
                        },
                        {
                            id: 2,
                            name: "Test Device 2", 
                            latitude: -7.6,
                            longitude: 112.6,
                            river_basin: { name: "Test Basin" }
                        }
                    ];
                    console.log("Using fallback devices for testing");
                    setDevices(fallbackDevices);
                    setError("Menggunakan data test - API tidak tersedia");
                }
            } catch (err) {
                console.error("Error fetching devices:", err);
                // Fallback data for testing when API fails
                const fallbackDevices = [
                    {
                        id: 1,
                        name: "Test Device 1",
                        latitude: -7.5,
                        longitude: 112.5,
                        river_basin: { name: "Test Basin" }
                    },
                    {
                        id: 2,
                        name: "Test Device 2", 
                        latitude: -7.6,
                        longitude: 112.6,
                        river_basin: { name: "Test Basin" }
                    }
                ];
                console.log("API failed, using fallback devices for testing");
                setDevices(fallbackDevices);
                setError("API Error - Menggunakan data test: " + err.message);
            } finally {
                setIsLoading(false);
            }
        };

        loadDevices();
    }, []);

    useEffect(() => {
        console.log("🔄 useEffect triggered - isPlaying:", isPlaying, "devices:", devices.length, "tickerData:", tickerData ? tickerData.length : 0);
        
        // Function to handle the interval logic
        const tick = () => {
            console.log("🔄 AUTO SWITCH TICK EXECUTED!");
            // Prioritize devices from API, fallback to tickerData
            const dataSource = devices.length > 0 ? devices : tickerData;
            if (!dataSource || dataSource.length === 0) {
                console.log("❌ No data source available for auto switch");
                return;
            }

            console.log("✅ Auto switch tick - data source:", dataSource.length, "devices");

            setCurrentIndex((prevIndex) => {
                const nextIndex = (prevIndex + 1) % dataSource.length;
                const nextStation = dataSource[nextIndex];

                console.log("🎯 Switching to device:", nextIndex, nextStation);

                // Convert device data to station format if needed
                let stationData = nextStation;
                if (devices.length > 0 && nextStation.latitude && nextStation.longitude) {
                    // Convert device to station format
                    stationData = {
                        id: nextStation.id,
                        name: nextStation.name,
                        coordinates: [parseFloat(nextStation.longitude), parseFloat(nextStation.latitude)],
                        latitude: parseFloat(nextStation.latitude),
                        longitude: parseFloat(nextStation.longitude),
                        status: "safe", // Default status
                        value: 0, // Default value
                        unit: "m",
                        location: nextStation.river_basin ? nextStation.river_basin.name : "Unknown"
                    };
                    console.log("🔄 Converted device to station format:", stationData);
                }

                if (onStationChange) {
                    console.log("📞 Calling onStationChange with:", stationData);
                    onStationChange(stationData, nextIndex);
                }

                // Trigger Mapbox auto switch if available
                if (window.mapboxAutoSwitch) {
                    console.log("🗺️ Calling mapboxAutoSwitch with:", stationData);
                    window.mapboxAutoSwitch(stationData, nextIndex);
                } else {
                    console.warn("⚠️ mapboxAutoSwitch function not available");
                }

                return nextIndex;
            });
        };

        if (isPlaying) {
            // Start the interval
            console.log("🚀 Starting auto switch interval with", interval, "ms");
            intervalRef.current = setInterval(tick, interval);
            console.log("✅ Auto switch started with interval:", interval);
            console.log("🆔 Interval ID:", intervalRef.current);

            // Test interval immediately
            console.log("🧪 Testing interval immediately...");
            setTimeout(() => {
                console.log("🧪 Manual test tick after 1 second");
                tick();
            }, 1000);

            // Dispatch event when auto switch starts
            document.dispatchEvent(
                new CustomEvent("autoSwitchActivated", {
                    detail: { active: true },
                })
            );
        } else {
            // Clear the interval if it exists
            if (intervalRef.current) {
                console.log("🛑 Stopping auto switch interval");
                clearInterval(intervalRef.current);
                intervalRef.current = null;
            }

            // Dispatch event when auto switch stops
            document.dispatchEvent(
                new CustomEvent("autoSwitchDeactivated", {
                    detail: { active: false },
                })
            );
        }

        // Cleanup function to clear the interval when the component unmounts or dependencies change
        return () => {
            if (intervalRef.current) {
                console.log("🧹 Cleaning up interval:", intervalRef.current);
                clearInterval(intervalRef.current);
            }
        };
    }, [isPlaying, devices, tickerData, interval, onStationChange]);

    // Start auto switch
    const startAutoSwitch = () => {
        const dataSource = devices.length > 0 ? devices : tickerData;
        if (!dataSource || dataSource.length === 0) {
            console.log("No data available for auto switch");
            return;
        }
        console.log("Starting auto switch with", dataSource.length, "devices");
        setIsPlaying(true);
    };

    // Stop auto switch
    const stopAutoSwitch = () => {
        setIsPlaying(false);
    };

    // Toggle play/pause
    const togglePlayPause = () => {
        const newIsPlaying = !isPlaying;
        console.log("🎮 Toggle auto switch:", newIsPlaying ? "ON" : "OFF");
        console.log("📊 Current devices state:", devices);
        console.log("📊 Current tickerData state:", tickerData);
        console.log("✅ hasData:", hasData);
        console.log("🔄 Current isPlaying state:", isPlaying);
        console.log("🔄 New isPlaying state:", newIsPlaying);
        
        setIsPlaying(newIsPlaying);
        
        if (onAutoSwitchToggle) {
            console.log("📞 Calling onAutoSwitchToggle with:", newIsPlaying);
            onAutoSwitchToggle(newIsPlaying);
        }
    };

    // Sync with external currentStationIndex
    useEffect(() => {
        if (currentStationIndex !== undefined && currentStationIndex !== currentIndex) {
            setCurrentIndex(currentStationIndex);
        }
    }, [currentStationIndex]);

    // Add event listener for user interactions
    useEffect(() => {
        const handleUserInteraction = (event) => {
            if (isPlaying) {
                console.log("User interaction detected, stopping auto switch:", event.detail);
                stopAutoSwitch();
            }
        };

        // Listen for custom event from other components
        document.addEventListener("userInteraction", handleUserInteraction);

        // Cleanup
        return () => {
            document.removeEventListener("userInteraction", handleUserInteraction);
        };
    }, [isPlaying]);

    // Selalu render komponen; nonaktifkan toggle jika tidak ada data
    const dataSource = devices.length > 0 ? devices : tickerData;
    const hasData = dataSource && dataSource.length > 0;
    
    console.log("🎨 Render check - devices:", devices.length, "tickerData:", tickerData ? tickerData.length : 0, "hasData:", hasData, "isPlaying:", isPlaying);

    return (
        <div className="flex items-center justify-between w-full">
            <div className="flex items-center gap-2">
                <span className="text-xs sm:text-sm font-semibold text-gray-800">Auto Switch</span>
                {isLoading && (
                    <div className="flex items-center gap-1">
                        <div className="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                        <span className="text-xs text-blue-600">Loading...</span>
                    </div>
                )}
                {isPlaying && !isLoading && (
                    <div className="flex items-center gap-1">
                        <div className="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <span className="text-xs text-green-600">Berjalan</span>
                    </div>
                )}
                {error && (
                    <div className="flex items-center gap-1">
                        <div className="w-2 h-2 bg-red-500 rounded-full"></div>
                        <span className="text-xs text-red-600">Error</span>
                    </div>
                )}
            </div>

            {/* iOS/macOS Style Toggle */}
            <button
                onClick={() => {
                    console.log("🖱️ Button clicked - hasData:", hasData, "isLoading:", isLoading);
                    togglePlayPause();
                }}
                disabled={!hasData || isLoading}
                className={`relative inline-flex items-center transition-all duration-200 ease-in-out focus:outline-none select-none ${
                    !hasData || isLoading ? "opacity-50 cursor-not-allowed" : "cursor-pointer"
                }`}
                title={
                    isLoading 
                        ? "Loading devices..." 
                        : isPlaying 
                            ? "Pause Auto Switch" 
                            : "Start Auto Switch"
                }
            >
                {/* Toggle Track */}
                <div
                    className={`relative w-11 h-6 rounded-full transition-all duration-200 ease-in-out ${
                        isPlaying ? "bg-green-500 shadow-inner" : "bg-gray-300 shadow-inner"
                    }`}
                >
                    {/* Toggle Thumb */}
                    <div
                        className={`absolute top-0.5 left-0.5 w-5 h-5 rounded-full transition-all duration-200 ease-in-out ${
                            isPlaying ? "translate-x-5" : "translate-x-0"
                        }`}
                    >
                        {/* Thumb dengan gradient dan shadow seperti iOS */}
                        <div className="absolute inset-0 rounded-full bg-gradient-to-b from-white via-gray-50 to-gray-200 shadow-sm border border-gray-200"></div>
                        {/* Inner highlight */}
                        <div className="absolute inset-0 rounded-full bg-gradient-to-b from-white/80 to-transparent"></div>
                    </div>
                </div>
            </button>
        </div>
    );
};

export default AutoSwitchToggle;
