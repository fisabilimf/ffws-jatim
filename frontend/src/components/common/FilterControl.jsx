import React, { useState, useEffect, useCallback } from "react";
import { X, Filter, Layers, ToggleLeft, ToggleRight, Keyboard, Sliders } from "lucide-react";

const FilterControl = ({
    tickerData,
    onStationChange,
    currentStationIndex,
    onAutoSwitchToggle,
    onLayerToggle
}) => {
    const [isFilterPanelOpen, setIsFilterPanelOpen] = useState(false);
    const [isVisible, setIsVisible] = useState(false);
    const [activeTab, setActiveTab] = useState("controls");
    const [layers, setLayers] = useState([
        { id: "stations", name: "Stasiun Monitoring", enabled: true, color: "#3B82F6" },
        { id: "rivers", name: "Sungai", enabled: true, color: "#06B6D4" },
        { id: "flood-risk", name: "Area Risiko Banjir", enabled: false, color: "#F59E0B" },
        { id: "rainfall", name: "Data Curah Hujan", enabled: false, color: "#10B981" },
        { id: "elevation", name: "Elevasi Terrain", enabled: false, color: "#8B5CF6" },
        { id: "administrative", name: "Batas Administrasi", enabled: true, color: "#6B7280" }
    ]);

    const handleFilterToggle = useCallback(() => {
        const newState = !isFilterPanelOpen;
        setIsFilterPanelOpen(newState);
        if (newState) {
            setTimeout(() => setIsVisible(true), 10);
        } else {
            setIsVisible(false);
        }
    }, [isFilterPanelOpen]);

    const handleCloseFilterPanel = useCallback(() => {
        setIsVisible(false);
        setTimeout(() => setIsFilterPanelOpen(false), 300);
    }, []);

    const handleLayerToggle = useCallback((layerId) => {
        setLayers(prevLayers => 
            prevLayers.map(layer => 
                layer.id === layerId 
                    ? { ...layer, enabled: !layer.enabled }
                    : layer
            )
        );
        
        if (onLayerToggle) {
            const layer = layers.find(l => l.id === layerId);
            onLayerToggle(layerId, !layer.enabled);
        }
    }, [layers, onLayerToggle]);

    const handleTabSwitch = useCallback((tab) => {
        setActiveTab(tab);
    }, []);

    // Keyboard shortcuts
    useEffect(() => {
        const handleKeyDown = (event) => {
            if (isFilterPanelOpen) {
                if (event.key === "Escape") {
                    handleCloseFilterPanel();
                } else if (event.key === "Tab" && event.ctrlKey) {
                    event.preventDefault();
                    setActiveTab(prev => prev === "controls" ? "layers" : "controls");
                }
            }
        };

        document.addEventListener("keydown", handleKeyDown);
        return () => document.removeEventListener("keydown", handleKeyDown);
    }, [isFilterPanelOpen, handleCloseFilterPanel]);

    return (
        <>
            {/* Filter Button */}
            <button
                onClick={handleFilterToggle}
                className="fixed top-4 right-6 z-[1000] w-11 h-11 bg-white/90 border border-gray-200 rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition-all duration-200 hover:bg-white/95 active:scale-95 group"
                title="Filter & Controls (F)"
            >
                <Filter className="w-4 h-4 text-gray-600 group-hover:text-blue-600 transition-colors" />
            </button>

            {/* Filter Panel */}
            {isFilterPanelOpen && (
                <div className="fixed inset-0 z-[999] flex justify-end">
                    {/* Backdrop */}
                    <div 
                    
                        onClick={handleCloseFilterPanel}
                    />
                    
                    {/* Panel */}
                    <div className={`
                        relative w-80 bg-white shadow-2xl border-l border-gray-200 flex flex-col
                        top-20 h-[calc(100vh-5rem)]
                        transform transition-all duration-300 ease-out
                        ${isVisible 
                            ? 'translate-x-0 opacity-100' 
                            : 'translate-x-full opacity-0'
                        }
                    `}>
                        {/* Header */}
                        <div className="flex items-center justify-between p-4 border-b border-gray-200 bg-gray-50/50">
                            <div className="flex items-center gap-2">
                                <Sliders className="w-5 h-5 text-blue-600" />
                                <h2 className="text-lg font-semibold text-gray-800">Filter & Controls</h2>
                            </div>
                            <button
                                onClick={handleCloseFilterPanel}
                                className="p-1 hover:bg-gray-200 rounded-md transition-colors"
                                title="Close (ESC)"
                            >
                                <X className="w-5 h-5 text-gray-500" />
                            </button>
                        </div>

                        {/* Tab Navigation */}
                        <div className="flex border-b border-gray-200">
                            <button
                                onClick={() => handleTabSwitch("controls")}
                                className={`flex-1 py-3 px-4 text-sm font-medium border-b-2 transition-colors ${
                                    activeTab === "controls"
                                        ? "border-blue-500 text-blue-600 bg-blue-50/50"
                                        : "border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50"
                                }`}
                            >
                                <div className="flex items-center justify-center gap-2">
                                    <ToggleLeft className="w-4 h-4" />
                                    Controls
                                </div>
                            </button>
                            <button
                                onClick={() => handleTabSwitch("layers")}
                                className={`flex-1 py-3 px-4 text-sm font-medium border-b-2 transition-colors ${
                                    activeTab === "layers"
                                        ? "border-blue-500 text-blue-600 bg-blue-50/50"
                                        : "border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50"
                                }`}
                            >
                                <div className="flex items-center justify-center gap-2">
                                    <Layers className="w-4 h-4" />
                                    Layers
                                </div>
                            </button>
                        </div>

                        {/* Content */}
                        <div className="flex-1 overflow-y-auto">
                            {activeTab === "controls" && (
                                <div className="p-4 space-y-6">
                                    {/* Auto Switch Section - Removed duplicate instance */}
                                    <div className="space-y-3">
                                        <h3 className="text-sm font-semibold text-gray-700 flex items-center gap-2">
                                            <ToggleRight className="w-4 h-4 text-blue-600" />
                                            Station Auto Switch
                                        </h3>
                                        <div className="bg-gray-50 rounded-lg p-3">
                                            <div className="text-sm text-gray-600 text-center py-4">
                                                Auto Switch control is available in the bottom-right corner of the map.
                                            </div>
                                        </div>
                                    </div>

                                    {/* Station Info */}
                                    {tickerData && tickerData.length > 0 && (
                                        <div className="space-y-3">
                                            <h3 className="text-sm font-semibold text-gray-700">Current Station</h3>
                                            <div className="bg-blue-50 rounded-lg p-3 border-l-4 border-blue-400">
                                                <div className="text-sm">
                                                    <div className="font-medium text-blue-900">
                                                        {tickerData[currentStationIndex]?.station_name || "N/A"}
                                                    </div>
                                                    <div className="text-blue-700 text-xs mt-1">
                                                        Station {currentStationIndex + 1} of {tickerData.length}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    )}
                                </div>
                            )}

                            {activeTab === "layers" && (
                                <div className="p-4 space-y-4">
                                    <h3 className="text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <Layers className="w-4 h-4 text-blue-600" />
                                        Map Layers
                                    </h3>
                                    
                                    <div className="space-y-3">
                                        {layers.map((layer) => (
                                            <div
                                                key={layer.id}
                                                className="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
                                            >
                                                <div className="flex items-center gap-3">
                                                    <div
                                                        className="w-3 h-3 rounded-full"
                                                        style={{ backgroundColor: layer.color }}
                                                    />
                                                    <span className="text-sm font-medium text-gray-700">
                                                        {layer.name}
                                                    </span>
                                                </div>
                                                <button
                                                    onClick={() => handleLayerToggle(layer.id)}
                                                    className={`relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 ${
                                                        layer.enabled ? 'bg-blue-600' : 'bg-gray-300'
                                                    }`}
                                                >
                                                    <span
                                                        className={`inline-block h-3 w-3 transform rounded-full bg-white transition-transform ${
                                                            layer.enabled ? 'translate-x-5' : 'translate-x-1'
                                                        }`}
                                                    />
                                                </button>
                                            </div>
                                        ))}
                                    </div>

                                    <div className="mt-6 p-3 bg-amber-50 rounded-lg border border-amber-200">
                                        <div className="flex items-start gap-2">
                                            <div className="text-amber-600 mt-0.5">
                                                <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fillRule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
                                                </svg>
                                            </div>
                                            <div className="text-xs text-amber-800">
                                                <div className="font-medium">Layer Control</div>
                                                <div className="mt-1">Beberapa layer mungkin memerlukan waktu loading tambahan.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            )}
                        </div>

                        {/* Footer */}
                        <div className="border-t border-gray-200 p-4 bg-gray-50/50">
                            <div className="flex items-center justify-between text-xs text-gray-500">
                                <div className="flex items-center gap-4">
                                    <div className="flex items-center gap-1">
                                        <Keyboard className="w-3 h-3" />
                                        <span>ESC to close</span>
                                    </div>
                                    <div className="flex items-center gap-1">
                                        <span>Ctrl+Tab to switch</span>
                                    </div>
                                </div>
                                <div className="text-gray-400">
                                    Filter v1.0
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </>
    );
};

export default FilterControl;