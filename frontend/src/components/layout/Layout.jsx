import React, { useState, useRef, useCallback, memo, lazy, Suspense, useEffect } from "react";

// Lazy load komponen yang tidak critical untuk initial load
const Dashboard = lazy(() => import("@/pages/Dashboard"));
const GoogleMapsSearchbar = lazy(() => import("@components/common/GoogleMapsSearchbar"));
const MapboxMap = lazy(() => import("@components/devices/MapboxMap"));
const FloatingLegend = lazy(() => import("@components/common/FloatingLegend"));
const FloodRunningBar = lazy(() => import("@components/FloodRunningBar"));
const StationDetail = lazy(() => import("@components/sensors/StationDetail"));
const DetailPanel = lazy(() => import("@components/sensors/DetailPanel"));
const AutoSwitchToggle = lazy(() => import("@components/common/AutoSwitchToggle"));
const FilterPanel = lazy(() => import("@components/common/FilterPanel"));

const Layout = ({ children }) => {
    const [tickerData, setTickerData] = useState(null);
    const [searchQuery, setSearchQuery] = useState("");
    const [selectedStation, setSelectedStation] = useState(null);
    const [isSidebarOpen, setIsSidebarOpen] = useState(false);
    const [isDetailPanelOpen, setIsDetailPanelOpen] = useState(false);
    const [currentStationIndex, setCurrentStationIndex] = useState(0);
    const [isAutoSwitchOn, setIsAutoSwitchOn] = useState(false);
    const mapRef = useRef(null);
    const [isFilterOpen, setIsFilterOpen] = useState(false);

    // Memoize event handlers untuk mencegah re-render yang tidak perlu
    const handleSearch = useCallback((query) => {
        setSearchQuery(query);
    }, []);

    const handleStationSelect = useCallback((station) => {
        setSelectedStation(station);
        setIsSidebarOpen(true);
    }, []);

    const handleAutoSwitchToggle = useCallback((isOn) => {
        setIsAutoSwitchOn(isOn);
        // If auto switch is turned off, close sidebar
        if (!isOn) {
            setIsSidebarOpen(false);
            setSelectedStation(null);
        } else {
            // Jika auto switch diaktifkan, tutup detail panel
            setIsDetailPanelOpen(false);
        }
    }, []);

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

    const handleStationChange = useCallback(
        (station, index) => {
            setCurrentStationIndex(index);
            // Buka panel detail saat auto switch
            setSelectedStation(station);
            setIsSidebarOpen(true);
            // Tutup detail panel jika auto switch sedang berjalan
            if (isAutoSwitchOn) {
                setIsDetailPanelOpen(false);
            }
            // Trigger map auto switch
            if (window.mapboxAutoSwitch) {
                window.mapboxAutoSwitch(station, index);
            }
        },
        [isAutoSwitchOn]
    );

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
                    <MapboxMap
                        ref={mapRef}
                        tickerData={tickerData}
                        onStationSelect={handleStationSelect}
                        onAutoSwitch={handleAutoSwitch}
                        isAutoSwitchOn={isAutoSwitchOn}
                        onCloseSidebar={() => {
                            if (!isAutoSwitchOn) {
                                setIsSidebarOpen(false);
                                setSelectedStation(null);
                            }
                        }}
                    />
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
                    onDataUpdate={setTickerData}
                    onStationSelect={handleStationSelect}
                    isSidebarOpen={isSidebarOpen}
                />
            </Suspense>

            {/* Top-right container for Filter button */}
            <div className="absolute top-4 right-4 z-[80]">
                <button
                    onClick={() => setIsFilterOpen(true)}
                    className="relative inline-flex items-center justify-center w-12 h-12 rounded-full shadow-xl hover:bg-gray-100 transition-colors isolate focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    title="Buka Filter"
                    aria-label="Buka Filter"
                >
                    <span aria-hidden="true" className="absolute inset-0 rounded-full bg-white ring-1 ring-gray-200 shadow-xl"></span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="relative z-10 w-6 h-6 text-blue-600 mix-blend-normal pointer-events-none">
                        <path d="M22 3H2l8 9v7l4 2v-9l8-9z"></path>
                    </svg>
                </button>
            </div>

            {/* Bottom-right container for Floating Legend only */}
            <div className="absolute bottom-2 right-2 sm:bottom-4 sm:right-4 z-10">
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
                    tickerData={tickerData}
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
                    onClose={() => setIsFilterOpen(false)}
                    title="Filter"
                    subtitle="Pengaturan tampilan & Auto Switch"
                >
                    <div className="space-y-6">
                        {/* Auto Switch Section */}
                        <section className="p-4 rounded-xl border border-gray-200 bg-white shadow-sm">
                            <h4 className="text-sm font-semibold text-gray-700 mb-3">Auto Switch</h4>
                            <div className="border-t border-gray-100 pt-3">
                                <AutoSwitchToggle
                                    tickerData={tickerData}
                                    onStationChange={handleStationChange}
                                    currentStationIndex={currentStationIndex}
                                    onAutoSwitchToggle={handleAutoSwitchToggle}
                                />
                            </div>
                        </section>

                        {/* Placeholder for other filters */}
                        <section className="p-4 rounded-xl border border-gray-200 bg-white shadow-sm">
                            <h4 className="text-sm font-semibold text-gray-700 mb-3">Layer Peta</h4>
                            <div className="space-y-2 text-sm text-gray-600">
                                <div className="flex items-center justify-between">
                                    <span>Elevasi Muka Air</span>
                                    <input type="checkbox" className="toggle toggle-sm" onChange={(e) => {
                                        // integrate with map layer toggle when available
                                        // handleLayerToggle('waterLevel', e.target.checked)
                                    }} />
                                </div>
                                <div className="flex items-center justify-between">
                                    <span>Curah Hujan</span>
                                    <input type="checkbox" className="toggle toggle-sm" onChange={(e) => {
                                        // handleLayerToggle('rainfall', e.target.checked)
                                    }} />
                                </div>
                            </div>
                        </section>
                    </div>
                </FilterPanel>
            </Suspense>
        </div>
    );
};

// Memoize Layout component untuk mencegah re-render yang tidak perlu
export default memo(Layout);
