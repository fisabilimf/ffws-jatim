// src/components/map/AutoSwitchToggle.js
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
            intervalRef.current = setInterval(tick, interval);
        } else {
            if (intervalRef.current) {
                clearInterval(intervalRef.current);
                intervalRef.current = null;
            }
        }

        return () => {
            if (intervalRef.current) {
                clearInterval(intervalRef.current);
            }
        };
    }, [isPlaying, tickerData, interval, onStationChange]);

    const togglePlayPause = () => {
        const newIsPlaying = !isPlaying;
        setIsPlaying(newIsPlaying);
        if (onAutoSwitchToggle) {
            onAutoSwitchToggle(newIsPlaying);
        }
    };

    useEffect(() => {
        if (currentStationIndex !== undefined && currentStationIndex !== currentIndex) {
            setCurrentIndex(currentStationIndex);
        }
    }, [currentStationIndex]);

    useEffect(() => {
        const handleUserInteraction = (event) => {
            if (isPlaying) {
                setIsPlaying(false);
                if (onAutoSwitchToggle) {
                    onAutoSwitchToggle(false);
                }
            }
        };

        document.addEventListener("userInteraction", handleUserInteraction);

        return () => {
            document.removeEventListener("userInteraction", handleUserInteraction);
        };
    }, [isPlaying, onAutoSwitchToggle]);

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

            <button
                onClick={togglePlayPause}
                disabled={!hasData}
                className={`relative inline-flex items-center transition-all duration-200 ease-in-out focus:outline-none select-none ${
                    !hasData ? "opacity-50 cursor-not-allowed" : "cursor-pointer"
                }`}
                title={isPlaying ? "Pause Auto Switch" : "Start Auto Switch"}
            >
                <div
                    className={`relative w-11 h-6 rounded-full transition-all duration-200 ease-in-out ${
                        isPlaying ? "bg-green-500 shadow-inner" : "bg-gray-300 shadow-inner"
                    }`}
                >
                    <div
                        className={`absolute top-0.5 left-0.5 w-5 h-5 rounded-full transition-all duration-200 ease-in-out ${
                            isPlaying ? "translate-x-5" : "translate-x-0"
                        }`}
                    >
                        <div className="absolute inset-0 rounded-full bg-gradient-to-b from-white via-gray-50 to-gray-200 shadow-sm border border-gray-200"></div>
                        <div className="absolute inset-0 rounded-full bg-gradient-to-b from-white/80 to-transparent"></div>
                    </div>
                </div>
            </button>
        </div>
    );
};

export default AutoSwitchToggle;