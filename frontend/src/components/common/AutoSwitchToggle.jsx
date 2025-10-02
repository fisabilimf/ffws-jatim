import React, { useState, useEffect, useRef, useCallback } from "react";

const AutoSwitchToggle = ({
    tickerData,
    onStationChange,
    currentStationIndex,
    onAutoSwitchToggle,
    interval = 5000,
    stopDelay = 5000, // Delay before actually stopping (5 seconds)
}) => {
    const [isPlaying, setIsPlaying] = useState(false);
    const [currentIndex, setCurrentIndex] = useState(0);
    const [isPendingStop, setIsPendingStop] = useState(false);
    const intervalRef = useRef(null);
    const stopTimeoutRef = useRef(null);
    const tickerDataRef = useRef(tickerData);
    
    // Selalu perbarui ref saat tickerData berubah
    useEffect(() => {
        tickerDataRef.current = tickerData;
    }, [tickerData]);
    
    // Function to handle the interval logic - menggunakan useCallback dan ref untuk menghindari masalah closure
    const tick = useCallback(() => {
        const currentTickerData = tickerDataRef.current;
        if (!currentTickerData || currentTickerData.length === 0) {
            console.warn("Tick called but no ticker data available");
            return;
        }

        // Ambil currentIndex terkini
        setCurrentIndex(prevIndex => {
            const nextIndex = (prevIndex + 1) % currentTickerData.length;
            const nextStation = currentTickerData[nextIndex];

            console.log(`Tick: Switching from index ${prevIndex} to ${nextIndex}, station: ${nextStation?.name}`);

            // Memanggil fungsi autoSwitch dari window yang telah didefinisikan di MapboxMap
            if (typeof window.mapboxAutoSwitch === 'function' && nextStation) {
                console.log("Auto switching to next station:", nextStation.name);
                try {
                    window.mapboxAutoSwitch(nextStation, nextIndex);
                } catch (error) {
                    console.error("Error calling mapboxAutoSwitch:", error);
                }
            } else {
                console.warn("mapboxAutoSwitch is not available or station is undefined");
            }

            if (onStationChange) {
                onStationChange(nextStation, nextIndex);
            }

            return nextIndex;
        });
    }, [onStationChange]);

    // Effect utama untuk mengatur interval
    useEffect(() => {
        console.log("AutoSwitchToggle effect running, isPlaying:", isPlaying);
        
        // Bersihkan interval yang ada untuk menghindari duplikasi
        if (intervalRef.current) {
            console.log("Clearing existing interval");
            clearInterval(intervalRef.current);
            intervalRef.current = null;
        }
        
        if (isPlaying) {
            // Clear any pending stop timeout
            if (stopTimeoutRef.current) {
                console.log("Clearing pending stop timeout");
                clearTimeout(stopTimeoutRef.current);
                stopTimeoutRef.current = null;
                setIsPendingStop(false);
            }

            // Jalankan tick segera jika memulai autoswitch
            if (tickerDataRef.current && tickerDataRef.current.length > 0) {
                const currentData = tickerDataRef.current;
                const firstStation = currentData[currentIndex];
                
                if (typeof window.mapboxAutoSwitch === 'function' && firstStation) {
                    console.log("Initial auto switch to station:", firstStation.name, "at index:", currentIndex);
                    try {
                        window.mapboxAutoSwitch(firstStation, currentIndex);
                    } catch (error) {
                        console.error("Error on initial switch:", error);
                    }
                } else {
                    console.warn("Cannot perform initial switch: mapboxAutoSwitch not available or no station data");
                }
                
                // Mulai interval baru - menggunakan arrow function untuk menghindari masalah closure
                console.log(`Starting new interval with ${interval}ms delay`);
                intervalRef.current = setInterval(() => tick(), interval);
                
                // Dispatch event when auto switch starts
                document.dispatchEvent(
                    new CustomEvent("autoSwitchActivated", {
                        detail: { active: true, currentIndex, stationCount: currentData.length },
                    })
                );
            } else {
                console.warn("Cannot start auto switch: No ticker data available");
            }
        } else if (!isPendingStop) {
            // Clear the interval if it exists and no stop delay is pending
            if (intervalRef.current) {
                clearInterval(intervalRef.current);
                intervalRef.current = null;
                
                console.log("Auto switch interval cleared due to isPlaying = false");
                
                // Dispatch event when auto switch stops
                document.dispatchEvent(
                    new CustomEvent("autoSwitchDeactivated", {
                        detail: { active: false },
                    })
                );
            }
        }

        // Cleanup function to clear the interval when the component unmounts or dependencies change
        return () => {
            if (intervalRef.current) {
                console.log("Cleanup: clearing interval");
                clearInterval(intervalRef.current);
                intervalRef.current = null;
            }
            if (stopTimeoutRef.current) {
                console.log("Cleanup: clearing stop timeout");
                clearTimeout(stopTimeoutRef.current);
                stopTimeoutRef.current = null;
            }
        };
    // Penting: hanya masukkan isPlaying dan isPendingStop dalam dependency array untuk mencegah reset interval yang tidak diinginkan
    }, [isPlaying, isPendingStop, interval, tick]);

    // Start auto switch
    const startAutoSwitch = () => {
        const currentData = tickerDataRef.current;
        if (!currentData || currentData.length === 0) {
            console.warn("Cannot start auto switch: No ticker data available");
            return;
        }
        
        // Clear any pending stop timeout
        if (stopTimeoutRef.current) {
            clearTimeout(stopTimeoutRef.current);
            stopTimeoutRef.current = null;
            setIsPendingStop(false);
        }
        
        console.log("Auto switch starting. Available stations:", currentData.length);
        if (currentData.length > 0) {
            console.log("Station names:", currentData.map(s => s.name).join(", "));
        }
        console.log("Starting with station index:", currentIndex);
        
        // Verifikasi bahwa fungsi mapboxAutoSwitch tersedia
        if (typeof window.mapboxAutoSwitch !== 'function') {
            console.warn("mapboxAutoSwitch function is not available on window object!");
            console.log("Window object functions:", Object.keys(window).filter(key => typeof window[key] === 'function'));
        }
        
        // Hanya set isPlaying ke true, efek akan menangani interval dan switch awal
        setIsPlaying(true);
    };

    // Stop auto switch with delay
    const stopAutoSwitch = () => {
        // If we're already pending stop, don't restart the timer
        if (isPendingStop) {
            console.log("Already pending stop, ignoring stopAutoSwitch call");
            return;
        }
        
        console.log("Auto switch will stop in", stopDelay/1000, "seconds");
        setIsPendingStop(true);
        
        // Set a timeout to stop after the delay
        if (stopTimeoutRef.current) {
            clearTimeout(stopTimeoutRef.current);
        }
        
        stopTimeoutRef.current = setTimeout(() => {
            // Bersihkan interval
            if (intervalRef.current) {
                clearInterval(intervalRef.current);
                intervalRef.current = null;
            }
            
            setIsPlaying(false);
            setIsPendingStop(false);
            stopTimeoutRef.current = null;
            console.log("Auto switch stopped after delay");
            
            // Dispatch event when auto switch stops
            document.dispatchEvent(
                new CustomEvent("autoSwitchDeactivated", {
                    detail: { active: false },
                })
            );
        }, stopDelay);
    };

    // Toggle play/pause
    const togglePlayPause = () => {
        console.log("Toggle play/pause called. Current state - isPlaying:", isPlaying, "isPendingStop:", isPendingStop);
        
        // If pending stop, cancel it and continue playing
        if (isPendingStop) {
            if (stopTimeoutRef.current) {
                clearTimeout(stopTimeoutRef.current);
                stopTimeoutRef.current = null;
            }
            setIsPendingStop(false);
            console.log("Auto switch stop cancelled, continuing");
            return;
        }
        
        const newIsPlaying = !isPlaying;
        console.log("Setting isPlaying to:", newIsPlaying);
        
        if (newIsPlaying) {
            startAutoSwitch();
        } else {
            stopAutoSwitch();
        }
        
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
            console.log("Syncing with external currentStationIndex:", currentStationIndex);
            setCurrentIndex(currentStationIndex);
            
            // Jika sedang aktif, langsung tampilkan stasiun yang sesuai dengan index baru
            if (isPlaying && tickerDataRef.current && tickerDataRef.current.length > 0) {
                const station = tickerDataRef.current[currentStationIndex];
                if (station && typeof window.mapboxAutoSwitch === 'function') {
                    console.log("Auto updating to new index station:", station.name);
                    window.mapboxAutoSwitch(station, currentStationIndex);
                }
            }
        }
    }, [currentStationIndex, isPlaying, currentIndex]);

    // Add event listener for user interactions and event confirmations
    useEffect(() => {
        const handleUserInteraction = (event) => {
            if (isPlaying && !isPendingStop) {
                console.log("User interaction detected, starting stop delay:", event.detail);
                stopAutoSwitch();
            }
        };

        // Listen for custom event from other components
        document.addEventListener("userInteraction", handleUserInteraction);
        
        // Tambahkan debug untuk memantau event
        const logAutoSwitchEvent = (event) => {
            console.log("Auto switch event received:", event.type, event.detail);
        };
        
        // Konfirmasi dari Mapbox bahwa auto switch siap
        const handleMapboxReady = (event) => {
            console.log("Received mapboxReadyForAutoSwitch event:", event.detail);
            
            // Jika auto switch aktif tapi interval belum berjalan, coba restart
            if (isPlaying && !intervalRef.current && tickerDataRef.current?.length > 0) {
                console.log("Restarting tick interval after mapbox confirmation");
                
                // Jalankan tick sekali untuk memulai
                tick();
                
                // Restart interval
                intervalRef.current = setInterval(() => tick(), interval);
            }
        };
        
        document.addEventListener("autoSwitchActivated", logAutoSwitchEvent);
        document.addEventListener("autoSwitchDeactivated", logAutoSwitchEvent);
        document.addEventListener("mapboxReadyForAutoSwitch", handleMapboxReady);

        // Cleanup
        return () => {
            document.removeEventListener("userInteraction", handleUserInteraction);
            document.removeEventListener("autoSwitchActivated", logAutoSwitchEvent);
            document.removeEventListener("autoSwitchDeactivated", logAutoSwitchEvent);
            document.removeEventListener("mapboxReadyForAutoSwitch", handleMapboxReady);
        };
    }, [isPlaying, isPendingStop, interval, tick]);

    // Selalu render komponen; nonaktifkan toggle jika tidak ada data
    const hasData = tickerData && tickerData.length > 0;

    return (
        <div className="flex items-center justify-between w-full">
            <div className="flex items-center gap-2">
                <span className="text-xs sm:text-sm font-semibold text-gray-800">Auto Switch</span>
                {isPlaying && !isPendingStop && (
                    <div className="flex items-center gap-1">
                        <div className="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    </div>
                )}
                {isPendingStop && (
                    <div className="flex items-center gap-1">
                        <div className="w-2 h-2 bg-yellow-500 rounded-full animate-ping"></div>
                        <span className="text-xs text-yellow-600">stopping...</span>
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
                title={isPendingStop ? "Cancel stop" : isPlaying ? "Pause Auto Switch" : "Start Auto Switch"}
            >
                {/* Toggle Track */}
                <div
                    className={`relative w-11 h-6 rounded-full transition-all duration-200 ease-in-out ${
                        isPendingStop ? "bg-yellow-500 shadow-inner" : 
                        isPlaying ? "bg-green-500 shadow-inner" : "bg-gray-300 shadow-inner"
                    }`}
                >
                    {/* Toggle Thumb */}
                    <div
                        className={`absolute top-0.5 left-0.5 w-5 h-5 rounded-full transition-all duration-200 ease-in-out ${
                            isPlaying || isPendingStop ? "translate-x-5" : "translate-x-0"
                        } ${isPendingStop ? "animate-pulse" : ""}`}
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