import React, { useState, useEffect, useRef, useCallback } from "react";

const AutoSwitchToggle = ({
    devices,
    onStationChange,
    currentStationIndex,
    onAutoSwitchToggle,
    interval = 5000,
    stopDelay = 5000,
}) => {
    // Simplified state - focus on UI only
    const [isPlaying, setIsPlaying] = useState(false);
    const [isPendingStop, setIsPendingStop] = useState(false);
    const [isAtMarker, setIsAtMarker] = useState(true);
    const devicesRef = useRef(devices);
    
    // Update ref when devices data changes
    useEffect(() => {
        devicesRef.current = devices || [];
    }, [devices]);
    
    // Listen for autoswitch events from useAutoSwitch hook
    useEffect(() => {
        const handleAutoSwitchActivated = (event) => {
            console.log('AutoSwitchToggle: Auto switch activated event received', event.detail);
            setIsPlaying(true);
            setIsPendingStop(false);
            setIsAtMarker(false);
            
            // Set marker as ready after animation delay - sinkron dengan fly to
            setTimeout(() => {
                setIsAtMarker(true);
            }, 1500);
        };
        
        const handleAutoSwitchDeactivated = (event) => {
            console.log('AutoSwitchToggle: Auto switch deactivated event received', event.detail);
            setIsPlaying(false);
            setIsPendingStop(false);
            setIsAtMarker(true);
        };
        
        const handleAutoSwitchPaused = (event) => {
            console.log('AutoSwitchToggle: Auto switch paused event received', event.detail);
            setIsPendingStop(true);
            setIsAtMarker(true);
        };
        
        const handleAutoSwitchResumed = (event) => {
            console.log('AutoSwitchToggle: Auto switch resumed event received', event.detail);
            setIsPendingStop(false);
            setIsAtMarker(false);
            
            // Set marker as ready after animation delay - sinkron dengan fly to
            setTimeout(() => {
                setIsAtMarker(true);
            }, 1500);
        };
        
        const handleAutoSwitchError = (event) => {
            console.error('AutoSwitchToggle: Auto switch error event received', event.detail);
            setIsPlaying(false);
            setIsPendingStop(false);
            setIsAtMarker(true);
        };
        
        const handleAutoSwitchSuccess = (event) => {
            console.log('AutoSwitchToggle: Auto switch success event received', event.detail);
            setIsAtMarker(false);
            
            // Set marker as ready after animation delay - sinkron dengan fly to
            setTimeout(() => {
                setIsAtMarker(true);
            }, 1500);
        };
        
        // Register event listeners
        document.addEventListener('autoSwitchActivated', handleAutoSwitchActivated);
        document.addEventListener('autoSwitchDeactivated', handleAutoSwitchDeactivated);
        document.addEventListener('autoSwitchPaused', handleAutoSwitchPaused);
        document.addEventListener('autoSwitchResumed', handleAutoSwitchResumed);
        document.addEventListener('autoSwitchError', handleAutoSwitchError);
        document.addEventListener('autoSwitchSuccess', handleAutoSwitchSuccess);
        
        return () => {
            document.removeEventListener('autoSwitchActivated', handleAutoSwitchActivated);
            document.removeEventListener('autoSwitchDeactivated', handleAutoSwitchDeactivated);
            document.removeEventListener('autoSwitchPaused', handleAutoSwitchPaused);
            document.removeEventListener('autoSwitchResumed', handleAutoSwitchResumed);
            document.removeEventListener('autoSwitchError', handleAutoSwitchError);
            document.removeEventListener('autoSwitchSuccess', handleAutoSwitchSuccess);
        };
    }, []);
    
    // Handle user interaction to pause autoswitch
    useEffect(() => {
        const handleUserInteraction = (event) => {
            if (isPlaying && !isPendingStop) {
                console.log("AutoSwitchToggle: User interaction detected, pausing auto switch:", event.detail);
                setIsPendingStop(true);
                
                // Dispatch pause event to useAutoSwitch hook
                document.dispatchEvent(new CustomEvent('pauseAutoSwitch', {
                    detail: { reason: 'user_interaction', source: event.detail?.source }
                }));
                
                // Auto resume after delay
                setTimeout(() => {
                    if (isPlaying) {
                        console.log("AutoSwitchToggle: Auto resuming after user interaction");
                        document.dispatchEvent(new CustomEvent('resumeAutoSwitch', {
                            detail: { reason: 'auto_resume' }
                        }));
                    }
                }, stopDelay);
            }
        };

        document.addEventListener("userInteraction", handleUserInteraction);
        
        return () => {
            document.removeEventListener("userInteraction", handleUserInteraction);
        };
    }, [isPlaying, isPendingStop, stopDelay]);

    const togglePlayPause = () => {
        console.log("AutoSwitchToggle: Toggle play/pause called. Current state - isPlaying:", isPlaying, "isPendingStop:", isPendingStop);
        
        if (isPendingStop) {
            // Cancel pending stop
            console.log("AutoSwitchToggle: Cancelling pending stop");
            setIsPendingStop(false);
            document.dispatchEvent(new CustomEvent('resumeAutoSwitch', {
                detail: { reason: 'user_cancel' }
            }));
            return;
        }
        
        const newIsPlaying = !isPlaying;
        console.log("AutoSwitchToggle: Setting isPlaying to:", newIsPlaying);
        
        // Call parent callback
        if (onAutoSwitchToggle) {
            onAutoSwitchToggle(newIsPlaying);
        }
    };

    const hasData = devices && devices.length > 0;

    return (
        <div className="flex flex-col sm:flex-row items-center justify-between w-full p-3 bg-white rounded-lg shadow-sm border border-gray-200">
            <div className="flex items-center gap-3 mb-2 sm:mb-0">
                <span className="text-sm font-semibold text-gray-800">Auto Switch</span>
                
                <div className="flex items-center gap-2">
                    {isPlaying && (
                        <div className="flex items-center gap-1">
                            <div className={`w-2.5 h-2.5 rounded-full ${isAtMarker ? 'bg-green-500 animate-pulse' : 'bg-yellow-500 animate-ping'}`}></div>
                            <span className={`text-xs font-medium ${isAtMarker ? 'text-green-600' : 'text-yellow-600'}`}>
                                {isAtMarker ? 'Active' : 'Moving...'}
                            </span>
                        </div>
                    )}
                    {!isPlaying && (
                        <div className="flex items-center gap-1">
                            <div className="w-2.5 h-2.5 bg-gray-400 rounded-full"></div>
                            <span className="text-xs font-medium text-gray-500">Inactive</span>
                        </div>
                    )}
                </div>
                
                {/* Device count indicator */}
                {hasData && (
                    <div className="flex items-center gap-1">
                        <span className="text-xs text-gray-500">
                            {devices.length} devices
                        </span>
                    </div>
                )}
            </div>

            <button
                onClick={togglePlayPause}   
                disabled={!hasData}
                className={`relative inline-flex items-center h-7 rounded-full transition-all duration-200 ease-in-out focus:outline-none select-none ${
                    !hasData ? "opacity-50 cursor-not-allowed" : "cursor-pointer hover:opacity-90"
                }`}
                title={isPlaying && isAtMarker ? "Stop Auto Switch" : isPlaying && !isAtMarker ? "Moving to device..." : "Start Auto Switch"}
                aria-label={isPlaying && isAtMarker ? "Stop auto switch" : isPlaying && !isAtMarker ? "Moving to device" : "Start auto switch"}
            >
                <div
                    className={`relative w-12 h-7 rounded-full transition-all duration-200 ease-in-out ${
                        isPlaying && isAtMarker ? "bg-green-500" : 
                        isPlaying && !isAtMarker ? "bg-yellow-400" : "bg-gray-300"
                    }`}
                >
                    <div
                        className={`absolute top-0.5 left-0.5 w-6 h-6 rounded-full transition-all duration-200 ease-in-out ${
                            isPlaying || isPendingStop ? "translate-x-5" : "translate-x-0"
                        } ${isPendingStop ? "animate-pulse" : ""}`}
                    >
                        <div className="absolute inset-0 rounded-full bg-gradient-to-b from-white via-gray-50 to-gray-100 shadow-md border border-gray-200"></div>
                        <div className="absolute inset-0 rounded-full bg-gradient-to-b from-white/90 to-transparent"></div>
                    </div>
                </div>
            </button>
        </div>
    );
};

export default AutoSwitchToggle;