import React, { useState, useEffect, useRef } from "react";

const AutoSwitchToggle = ({
    tickerData,
    onStationChange,
    currentStationIndex,
    onAutoSwitchToggle,
    interval = 5000,
}) => {
    const [isPlaying, setIsPlaying] = useState(false);
    const [currentIndex, setCurrentIndex] = useState(0);
    const intervalRef = useRef(null);

    useEffect(() => {
        // Function to handle the interval logic
        const tick = () => {
            if (!tickerData || tickerData.length === 0) return;

            setCurrentIndex((prevIndex) => {
                const nextIndex = (prevIndex + 1) % tickerData.length;
                const nextStation = tickerData[nextIndex];

                if (onStationChange) {
                    onStationChange(nextStation, nextIndex);
                }

                return nextIndex;
            });
        };

        if (isPlaying) {
            // Start the interval
            intervalRef.current = setInterval(tick, interval);

            // Dispatch event when auto switch starts
            document.dispatchEvent(
                new CustomEvent("autoSwitchActivated", {
                    detail: { active: true },
                })
            );
        } else {
            // Clear the interval if it exists
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
    }, [isPlaying, tickerData, interval, onStationChange]);

    // Start auto switch
    const startAutoSwitch = () => {
        if (!tickerData || tickerData.length === 0) return;
        setIsPlaying(true);
    };

    // Stop auto switch
    const stopAutoSwitch = () => {
        setIsPlaying(false);
    };

    // Toggle play/pause
    const togglePlayPause = () => {
        const newIsPlaying = !isPlaying;
        setIsPlaying(newIsPlaying);
        if (onAutoSwitchToggle) {
            onAutoSwitchToggle(newIsPlaying);
        }
    };

    // Cleanup on unmount - this can be removed as the main useEffect now handles it
    // useEffect(() => {
    //   return () => {
    //     if (intervalRef.current) {
    //       clearInterval(intervalRef.current);
    //     }
    //   };
    // }, []);

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
    const hasData = tickerData && tickerData.length > 0;

    return (
        <div className="flex items-center justify-between w-full">
            <div className="flex items-center gap-2">
                <span className="text-xs sm:text-sm font-semibold text-gray-800">Auto Switch</span>
                {isPlaying && (
                    <div className="flex items-center gap-1">
                        <div className="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    </div>
                )}
            </div>

            {/* iOS/macOS Style Toggle */}
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
