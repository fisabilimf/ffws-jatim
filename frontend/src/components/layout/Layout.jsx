import React, { useState, useRef, useCallback, memo, lazy, Suspense, useEffect } from "react";
import { useDevices } from "@/hooks/useAppContext";
import { useAutoSwitch } from "@/hooks/useAutoSwitch";
const GoogleMapsSearchbar = lazy(() => import("@components/common/GoogleMapsSearchbar"));
const MapboxMap = lazy(() => import("@/components/MapboxMap"));
const FloatingLegend = lazy(() => import("@components/common/FloatingLegend"));
const FloodRunningBar = lazy(() => import("@/components/common/FloodRunningBar"));
const StationDetail = lazy(() => import("@components/layout/StationDetail"));
const DetailPanel = lazy(() => import("@components/layout/DetailPanel"));
const FilterPanel = lazy(() => import("@components/common/FilterPanel"));
const AutoSwitchToggle = lazy(() => import("@components/common/AutoSwitchToggle"));
const Layout = ({ children }) => {
    // Get devices data from context
    const { devices, loading: devicesLoading, error: devicesError, hasDevices } = useDevices();
    
    // Debug logging untuk devices dari context
    console.log('=== LAYOUT DEVICES DEBUG ===');
    console.log('devices from context:', devices);
    console.log('devices type:', typeof devices);
    console.log('devices length:', devices?.length);
    console.log('devicesLoading:', devicesLoading);
    console.log('devicesError:', devicesError);
    console.log('hasDevices:', hasDevices);
    console.log('=== END LAYOUT DEVICES DEBUG ===');
    
    // Local state
    const [searchQuery, setSearchQuery] = useState("");
    const [selectedStation, setSelectedStation] = useState(null);
    const [isSidebarOpen, setIsSidebarOpen] = useState(false);
    const [isDetailPanelOpen, setIsDetailPanelOpen] = useState(false);
    const mapRef = useRef(null);
    const [isFilterOpen, setIsFilterOpen] = useState(false);

    // Memoize event handlers untuk mencegah re-render yang tidak perlu
    const handleSearch = useCallback((query) => {
        setSearchQuery(query);
    }, []);

    const handleStationSelect = useCallback((station) => {
        console.log('=== HANDLE STATION SELECT DEBUG ===');
        console.log('Selected station:', station);
        console.log('Station ID:', station?.id);
        console.log('Station name:', station?.name);
        
        setSelectedStation(station);
        setIsSidebarOpen(true);
        
        console.log('selectedStation state set');
        console.log('isSidebarOpen state set to true');
        console.log('=== END HANDLE STATION SELECT DEBUG ===');
    }, []);

    // Create a ref to store the current isAutoSwitchOn value
    const isAutoSwitchOnRef = useRef(false);

    const handleStationChange = useCallback(
        (device, index) => {
            const deviceName = device?.name || device?.device_name || device?.station_name;
            console.log('Layout: Device change requested:', deviceName, 'index:', index);
            
            // Validasi input
            if (!device || index === undefined) {
                console.warn('Layout: Invalid device or index provided');
                return;
            }
            
            // Update state dengan debouncing untuk mencegah rapid changes
            const timeoutId = setTimeout(() => {
                console.log('Layout: Opening station detail for:', deviceName);
                // Buka panel detail saat auto switch
                setSelectedStation(device);
                setIsSidebarOpen(true);
                // Tutup detail panel jika auto switch sedang berjalan
                if (isAutoSwitchOnRef.current) {
                    setIsDetailPanelOpen(false);
                }
            }, 500); // Delay sedikit lebih lama untuk sinkronisasi dengan map animation
            
            // Trigger map auto switch (tidak perlu debounce karena ini external call)
            if (window.mapboxAutoSwitch) {
                try {
                    window.mapboxAutoSwitch(device, index);
                } catch (error) {
                    console.error('Layout: Error calling mapboxAutoSwitch:', error);
                }
            }
            
            return () => clearTimeout(timeoutId);
        },
        [] // Remove isAutoSwitchOn dependency
    );

    // Use autoswitch hook
    const {
        isPlaying: isAutoSwitchOn,
        currentIndex: autoSwitchIndex,
        startAutoSwitch,
        stopAutoSwitch,
        togglePlayPause,
        hasData: autoSwitchHasData,
        error: autoSwitchError,
        isLoadingDevices: autoSwitchLoading
    } = useAutoSwitch({
        onStationChange: handleStationChange,
        interval: 8000, // 8 detik untuk memberikan waktu melihat station detail
        stopDelay: 5000
    });

    // Update ref when isAutoSwitchOn changes
    useEffect(() => {
        isAutoSwitchOnRef.current = isAutoSwitchOn;
    }, [isAutoSwitchOn]);

    const handleAutoSwitchToggle = useCallback((isOn) => {
        console.log('=== LAYOUT: AUTO SWITCH TOGGLE REQUESTED ===');
        console.log('Requested state:', isOn);
        console.log('Current isAutoSwitchOn:', isAutoSwitchOn);
        console.log('Current devices length:', devices?.length || 0);
        
        // Use autoswitch hook methods
        if (isOn) {
            console.log('Starting auto switch...');
            startAutoSwitch();
            // Jika auto switch diaktifkan, tutup detail panel
            setIsDetailPanelOpen(false);
        } else {
            console.log('Stopping auto switch...');
            stopAutoSwitch();
            // If auto switch is turned off, close sidebar
            setIsSidebarOpen(false);
            setSelectedStation(null);
        }
    }, [isAutoSwitchOn, devices, startAutoSwitch, stopAutoSwitch]);

    const handleCloseStationDetail = useCallback(() => {
        setSelectedStation(null);
        setIsSidebarOpen(false);
    }, []);

    const handleToggleDetailPanel = useCallback(() => {
        if (isDetailPanelOpen) {
            // Jika panel terbuka, tutup dengan animasi
            handleCloseDetailPanel();
        } else {
            // Jika panel tertutup, buka langsung
            setIsDetailPanelOpen(true);
        }
    }, [isDetailPanelOpen]);

    const handleCloseDetailPanel = useCallback(() => {
        setIsDetailPanelOpen(false);
    }, []);

    const handleLayerToggle = useCallback((layerId, enabled) => {
        console.log(`Layer ${layerId} toggled: ${enabled}`);
        // Implementasi logic untuk toggle layer map
        // Bisa dikomunikasikan dengan MapboxMap component
    }, []);

    const handleAutoSwitch = useCallback((station, index) => {
        setCurrentStationIndex(index);
        setSelectedStation(station);
        // Auto open sidebar when auto switching
        setIsSidebarOpen(true);
    }, []);

    return (
        <div className="h-screen bg-gray-50 relative overflow-hidden">
            {/* Full Screen Map */}
            <div className="w-full h-full relative z-0">
                <Suspense
                    fallback={
                        <div className="w-full h-full bg-gray-200 animate-pulse flex items-center justify-center">
                            Loading Map...
                        </div>
                    }
                >
                    {devicesLoading ? (
                        <div className="w-full h-full bg-gray-200 animate-pulse flex items-center justify-center">
                            <div className="text-center">
                                <div className="text-lg font-semibold mb-2">Loading Devices...</div>
                                <div className="text-sm text-gray-600">Fetching station data from API</div>
                            </div>
                        </div>
                    ) : devicesError ? (
                        <div className="w-full h-full bg-red-100 flex items-center justify-center">
                            <div className="text-center text-red-600">
                                <div className="text-lg font-semibold mb-2">Error Loading Devices</div>
                                <div className="text-sm">{devicesError}</div>
                            </div>
                        </div>
                    ) : !hasDevices ? (
                        <div className="w-full h-full bg-yellow-100 flex items-center justify-center">
                            <div className="text-center text-yellow-600">
                                <div className="text-lg font-semibold mb-2">No Devices Found</div>
                                <div className="text-sm">No valid devices available for mapping</div>
                            </div>
                        </div>
                    ) : (
                        <MapboxMap
                            ref={mapRef}
                            devicesData={devices}
                            onStationSelect={handleStationSelect}
                            onStationChange={handleStationChange}
                            isAutoSwitchOn={isAutoSwitchOn}
                            onCloseSidebar={() => {
                                if (!isAutoSwitchOn) {
                                    setIsSidebarOpen(false);
                                    setSelectedStation(null);
                                }
                            }}
                        />
                    )}
                </Suspense>
            </div>

            {/* Google Maps Style Searchbar - fixed position */}
            <div className="absolute top-4 left-4 right-4 z-20">
                <div className="max-w-2xl mx-auto">
                    <Suspense fallback={<div className="h-12 bg-white/80 rounded-lg animate-pulse"></div>}>
                        <GoogleMapsSearchbar onSearch={handleSearch} placeholder="Cari stasiun monitoring banjir..." />
                    </Suspense>
                </div>
            </div>


            {/* Flood Running Bar */}
            <Suspense fallback={<div className="h-16 bg-white/80 animate-pulse"></div>}>
                <FloodRunningBar
                    onStationSelect={handleStationSelect}
                    isSidebarOpen={isSidebarOpen}
                />
            </Suspense>

            {/* Bottom-right container for Floating Legend only */}
            <div className="absolute bottom-2 right-2 sm:bottom-4 sm:right-2 z-20">
                <Suspense fallback={<div className="h-20 bg-white/80 rounded animate-pulse"></div>}>
                    <FloatingLegend />
                </Suspense>
            </div>

            {/* Station Detail Modal */}
            <Suspense
                fallback={
                    <div className="fixed inset-0 bg-black/50 flex items-center justify-center">
                        <div className="bg-white rounded-lg p-8 animate-pulse">Loading...</div>
                    </div>
                }
            >
                <StationDetail
                    selectedStation={selectedStation}
                    onClose={handleCloseStationDetail}
                    devicesData={devices}
                    isAutoSwitchOn={isAutoSwitchOn}
                    showArrow={true}
                    onArrowToggle={handleToggleDetailPanel}
                    isDetailPanelOpen={isDetailPanelOpen}
                    onCloseDetailPanel={handleCloseDetailPanel}
                />
            </Suspense>

            {/* Detail Panel */}
            <Suspense
                fallback={<div className="fixed right-0 top-0 h-full w-80 bg-white shadow-lg animate-pulse"></div>}
            >
                <DetailPanel
                    isOpen={isDetailPanelOpen}
                    onClose={handleCloseDetailPanel}
                    stationData={selectedStation}
                    chartHistory={selectedStation?.history || []}
                    isAutoSwitchOn={isAutoSwitchOn}
                />
            </Suspense>

            {/* Right-side Filter Panel */}
            <Suspense fallback={<div className="fixed right-0 top-0 h-full w-80 bg-white shadow-lg animate-pulse"></div>}>
                <FilterPanel
                    isOpen={isFilterOpen}
                    onOpen={() => setIsFilterOpen(true)}
<<<<<<< HEAD
                    onClose={() => setIsFilterOpen(false)}
                    devicesData={devices}
                    handleStationChange={handleStationChange}
                    currentStationIndex={autoSwitchIndex}
                    handleAutoSwitchToggle={handleAutoSwitchToggle}
=======
                    onClose={() => setIsFilterOpen(false)} // Tambahkan handler untuk menutup panel
>>>>>>> 9f9c665b932e2f075a960cada8468378ee0d7a87
                />
            </Suspense>
        </div>
    );
};

// Memoize Layout component untuk mencegah re-render yang tidak perlu
export default memo(Layout);
