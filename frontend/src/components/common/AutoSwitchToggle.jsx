import React, { useEffect } from "react";
import { useAutoSwitch } from "@/hooks/useAutoSwitch";

const AutoSwitchToggle = ({
    tickerData,
    onStationChange,
    currentStationIndex,
    onAutoSwitchToggle,
    interval = 5000,
    fetchInterval = 10000,
    isAutoSwitchOn = false,
}) => {
    const {
        isPlaying,
        currentIndex,
        devicesData,
        isLoadingDevices,
        error,
        tickCounter,
        isUserInteracting,
        isPaused,
        pauseAutoSwitch,
        resumeAutoSwitch,
        togglePlayPause,
        hasData
    } = useAutoSwitch({
        tickerData,
        onStationChange,
        interval,
        fetchInterval,
        isAutoSwitchOn
    });
    // Sync dengan external currentStationIndex
    useEffect(() => {
        if (currentStationIndex !== undefined && currentStationIndex !== currentIndex) {
            // Logic untuk sync dengan external index jika diperlukan
        }
    }, [currentStationIndex, currentIndex]);

    // Handle toggle dengan notifikasi ke parent
    const handleToggle = () => {
        if (!hasData) {
            console.warn("Button disabled - no data available");
            return;
        }
        
        // Toggle internal state
        togglePlayPause();
        
        // Notify parent dengan status yang tepat
        if (onAutoSwitchToggle) {
            if (isPlaying && !isPaused) {
                // Sedang playing, akan di-stop
                onAutoSwitchToggle(false);
            } else if (!isPlaying) {
                // Tidak playing, akan di-start
                onAutoSwitchToggle(true);
            }
            // Jika paused, tidak perlu notify parent karena ini auto resume
        }
    };

    // Add event listener for user interactions
    useEffect(() => {
        const handleUserInteraction = (event) => {
            if (isPlaying && !isPaused) {
                console.log("User interaction detected, pausing auto switch:", event.detail);
                // Pause instead of stop completely
                pauseAutoSwitch();
            }
        };

        document.addEventListener("userInteraction", handleUserInteraction);
        
        return () => {
            document.removeEventListener("userInteraction", handleUserInteraction);
        };
    }, [isPlaying, isPaused, pauseAutoSwitch]);

    return (
        <div className="auto-switch-component flex items-center justify-between w-full">
            <div className="flex items-center gap-2">
                <span className="text-xs sm:text-sm font-semibold text-gray-800">
                    Auto Switch
                </span>
                
                {isLoadingDevices && (
                    <div className="flex items-center gap-1">
                        <div className="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                        <span className="text-xs text-blue-600">Loading...</span>
                    </div>
                )}
                
                {error && (
                    <div className="flex items-center gap-1">
                        <div className="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                        <span className="text-xs text-red-600">
                            Connection Error
                        </span>
                    </div>
                )}
                
                {!hasData && !isLoadingDevices && !error && (
                    <div className="flex items-center gap-1">
                        <div className="w-2 h-2 bg-gray-400 rounded-full"></div>
                        <span className="text-xs text-gray-500">No data</span>
                    </div>
                )}
                
                {isPlaying && !isPaused && (
                    <div className="flex items-center gap-1">
                        <div className="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <span className="text-xs text-green-600 font-medium">
                            Running
                        </span>
                        {devicesData && devicesData.length > 0 && (
                            <span className="text-xs text-green-500">
                                ({devicesData.length})
                            </span>
                        )}
                    </div>
                )}
                
                {isPlaying && isPaused && (
                    <div className="flex items-center gap-1">
                        <div className="w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></div>
                        <span className="text-xs text-yellow-600 font-medium">
                            Paused
                        </span>
                    </div>
                )}
                
                {isUserInteracting && !isPlaying && (
                    <div className="flex items-center gap-1">
                        <div className="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                        <span className="text-xs text-red-600 font-medium">
                            Stopped
                        </span>
                    </div>
                )}
                
                {!isPlaying && !isUserInteracting && hasData && (
                    <div className="flex items-center gap-1">
                        <div className="w-2 h-2 bg-gray-400 rounded-full"></div>
                        <span className="text-xs text-gray-500 font-medium">
                            Ready
                        </span>
                    </div>
                )}
            </div>

            {/* macOS Style Toggle */}
            <button
                onClick={handleToggle}
                disabled={!hasData}
                className={`relative inline-flex items-center transition-all duration-200 ease-in-out focus:outline-none select-none ${
                    !hasData ? "opacity-50 cursor-not-allowed" : "cursor-pointer"
                }`}
                title={
                    !hasData 
                        ? "No data available"
                        : !isPlaying 
                            ? `Start Auto Switch (${devicesData?.length || tickerData?.length || 0} devices)` 
                            : isPaused 
                                ? "Resume Auto Switch"
                                : "Stop Auto Switch"
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