import React, { useState, useEffect, useRef } from "react";
import { fetchDevices } from "@/services/devices";        
const AutoSwitchToggle = ({
    tickerData,
    onDeviceChange,
    currentDeviceIndex,
    onAutoSwitchToggle,
    interval = 5000,
}) => {
    const [isPlaying, setIsPlaying] = useState(false);
    const [currentIndex, setCurrentIndex] = useState(0);
    const [apiData, setApiData] = useState([]);
    const [loading, setLoading] = useState(false);
    const intervalRef = useRef(null);

    // Fetch data from API
    const fetchData = async () => {
        try {
            setLoading(true);
            const data = await fetchDevices();
            console.log(data);
            setApiData(data.data || []);
        } catch (error) {
            console.error("Error fetching devices data:", error);
            setApiData([]);
        } finally {
            setLoading(false);
        }
    };

    // Load data on component mount
    useEffect(() => {
        fetchData();
    }, []);

    useEffect(() => {
        // Function to handle the interval logic
        const tick = async () => {
            // Use API data if available, fallback to tickerData
            const dataSource = apiData.length > 0 ? apiData : tickerData;
            
            if (!dataSource || dataSource.length === 0) {
                // Try to fetch fresh data if no data available
                if (apiData.length === 0) {
                    await fetchData();
                }
                return;
            }

            setCurrentIndex((prevIndex) => {
                const nextIndex = (prevIndex + 1) % dataSource.length;
                const nextDevice = dataSource[nextIndex];

                if (onDeviceChange) {
                    onDeviceChange(nextDevice, nextIndex);
                }

                return nextIndex;
            });
        };

        if (isPlaying) {
            intervalRef.current = setInterval(tick, interval);

            // Dispatch event when auto switch starts
            document.dispatchEvent(
                new CustomEvent("autoSwitchActivated", {
                    detail: { active: true },
                })
            );
        } else {
            if (intervalRef.current) {
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
                clearInterval(intervalRef.current);
            }
        };
    }, [isPlaying, tickerData, apiData, interval, onDeviceChange]); // Dependency updated

    // Start auto switch
    const startAutoSwitch = async () => {
        // Ensure we have data before starting
        if (apiData.length === 0 && (!tickerData || tickerData.length === 0)) {
            await fetchData();
        }
        setIsPlaying(true);
    };

    // Stop auto switch
    const stopAutoSwitch = () => {
        setIsPlaying(false);
    };

    // Toggle play/pause
    const togglePlayPause = async () => {
        const newIsPlaying = !isPlaying;
        
        // If starting and no data available, fetch data first
        if (newIsPlaying && apiData.length === 0 && (!tickerData || tickerData.length === 0)) {
            await fetchData();
        }
        
        setIsPlaying(newIsPlaying);
        if (onAutoSwitchToggle) {
            onAutoSwitchToggle(newIsPlaying);
        }
    };

    useEffect(() => {
        if (currentDeviceIndex !== undefined && currentDeviceIndex !== currentIndex) {
            setCurrentIndex(currentDeviceIndex);
        }
    }, [currentDeviceIndex]); // Dependency updated

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
    const hasData = (apiData && apiData.length > 0) || (tickerData && tickerData.length > 0);

    return (
        <div className="flex items-center justify-between w-full">
            <div className="flex items-center gap-2">
                <span className="text-sm sm:text-base lg:text-lg font-semibold text-gray-800">Auto Switch Device</span> {/* Updated UI text */}
                {loading && (
                    <div className="flex items-center gap-1">
                        <div className="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                        <span className="text-xs text-gray-500">Loading...</span>
                    </div>
                )}
                {isPlaying && !loading && (
                    <div className="flex items-center gap-1">
                        <div className="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <span className="text-xs text-green-600">Running</span>
                    </div>
                )}
            </div>

            {/* macOS Style Toggle */}
            <button
                onClick={togglePlayPause}
                disabled={!hasData}
                className={`relative inline-flex items-center transition-all duration-200 ease-in-out focus:outline-none select-none ${
                    !hasData ? "opacity-50 cursor-not-allowed" : "cursor-pointer"
                }`}
                title={isPlaying ? "Pause Auto Switch" : "Start Auto Switch"}
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