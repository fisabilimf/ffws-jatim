// src/components/map/FilterSidebar.js
import React from 'react';
import AutoSwitchToggle from './AutoSwitchToggle';

const FilterSidebar = ({ 
    isVisible, 
    onClose, 
    tickerData, 
    onStationChange, 
    currentStationIndex, 
    onAutoSwitchToggle,
    autoSwitchActive
}) => {
    // Debug log
    console.log("FilterSidebar rendered with:", { 
        isVisible, 
        currentStationIndex, 
        autoSwitchActive,
        tickerDataLength: tickerData?.length 
    });

    return (
        <div 
            className={`fixed top-20 right-0 h-[calc(100vh-9rem)] w-full md:w-90 bg-white shadow-xl transform transition-transform duration-300 ease-in-out ${
                isVisible ? 'translate-x-0' : 'translate-x-full'
            }`}
            style={{ zIndex: 9999 }}
        >
            <div className="p-4 border-b border-gray-200 flex justify-between items-center">
                <h2 className="text-xl font-semibold text-gray-800">Filter Peta</h2>
                <button 
                    onClick={onClose}
                    className="text-gray-500 hover:text-gray-700 focus:outline-none"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div className="p-4 overflow-y-auto h-[calc(100%-4rem)]">
                {/* Auto Switch Toggle Section */}
                <div className="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-100">
                    <h3 className="font-medium text-blue-800 mb-3">Auto Switch Stasiun</h3>
                    <AutoSwitchToggle
                        tickerData={tickerData}
                        onStationChange={onStationChange}
                        currentStationIndex={currentStationIndex}
                        onAutoSwitchToggle={onAutoSwitchToggle}
                        isPlaying={autoSwitchActive}
                    />
                    <div className="mt-2 text-xs text-blue-600">
                        {autoSwitchActive 
                            ? `Stasiun aktif: ${tickerData?.[currentStationIndex]?.name || 'N/A'} (${currentStationIndex + 1}/${tickerData?.length || 0})` 
                            : 'Auto switch dinonaktifkan'}
                    </div>
                </div>
                
                <div className="space-y-4">
                    <div className="p-4 bg-gray-50 rounded-lg">
                        <h3 className="font-medium text-gray-700 mb-2">Status Stasiun</h3>
                        <div className="space-y-2">
                            <label className="flex items-center p-2 hover:bg-white rounded transition-colors cursor-pointer">
                                <input type="checkbox" className="form-checkbox h-4 w-4 text-green-500" defaultChecked />
                                <div className="flex items-center ml-2">
                                    <div className="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
                                    <span className="text-gray-700">Aman</span>
                                </div>
                            </label>
                            <label className="flex items-center p-2 hover:bg-white rounded transition-colors cursor-pointer">
                                <input type="checkbox" className="form-checkbox h-4 w-4 text-yellow-500" defaultChecked />
                                <div className="flex items-center ml-2">
                                    <div className="w-3 h-3 rounded-full bg-yellow-500 mr-2"></div>
                                    <span className="text-gray-700">Peringatan</span>
                                </div>
                            </label>
                            <label className="flex items-center p-2 hover:bg-white rounded transition-colors cursor-pointer">
                                <input type="checkbox" className="form-checkbox h-4 w-4 text-red-500" defaultChecked />
                                <div className="flex items-center ml-2">
                                    <div className="w-3 h-3 rounded-full bg-red-500 mr-2"></div>
                                    <span className="text-gray-700">Bahaya</span>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <div className="p-4 bg-gray-50 rounded-lg">
                        <h3 className="font-medium text-gray-700 mb-2">Rentang Waktu</h3>
                        <div className="grid grid-cols-2 gap-2 mb-2">
                            <div>
                                <label className="block text-xs text-gray-500 mb-1">Dari</label>
                                <input 
                                    type="date" 
                                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>
                            <div>
                                <label className="block text-xs text-gray-500 mb-1">Sampai</label>
                                <input 
                                    type="date" 
                                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>
                        </div>
                        <div className="flex space-x-2">
                            <button className="flex-1 py-1 px-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded text-xs transition-colors">
                                Hari Ini
                            </button>
                            <button className="flex-1 py-1 px-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded text-xs transition-colors">
                                7 Hari
                            </button>
                            <button className="flex-1 py-1 px-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded text-xs transition-colors">
                                30 Hari
                            </button>
                        </div>
                    </div>

                    <div className="p-4 bg-gray-50 rounded-lg">
                        <h3 className="font-medium text-gray-700 mb-2">Tipe Layer</h3>
                        <div className="space-y-2">
                            <label className="flex items-center p-2 hover:bg-white rounded transition-colors cursor-pointer">
                                <input type="checkbox" className="form-checkbox h-4 w-4 text-blue-500" defaultChecked />
                                <span className="ml-2 text-gray-700">Stasiun</span>
                            </label>
                            <label className="flex items-center p-2 hover:bg-white rounded transition-colors cursor-pointer">
                                <input type="checkbox" className="form-checkbox h-4 w-4 text-blue-500" defaultChecked />
                                <span className="ml-2 text-gray-700">Sungai</span>
                            </label>
                            <label className="flex items-center p-2 hover:bg-white rounded transition-colors cursor-pointer">
                                <input type="checkbox" className="form-checkbox h-4 w-4 text-blue-500" />
                                <span className="ml-2 text-gray-700">Area Aman</span>
                            </label>
                        </div>
                    </div>
                    
                    <button className="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors shadow-sm">
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </div>
    );
};

export default FilterSidebar;