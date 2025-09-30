// src/components/map/FilterSidebar.js
import React from 'react';
import AutoSwitchToggle from './AutoSwitchToggle';

const FilterSidebar = ({ 
    isVisible, 
    onClose, 
    tickerData, 
    onStationChange, 
    currentStationIndex, 
    onAutoSwitchToggle 
}) => {
    return (
        <div 
            className={`fixed top-20 right-0 h-[calc(100vh-4rem)] w-full md:w-96 bg-white shadow-xl z-40 transform transition-transform duration-300 ease-in-out ${
                isVisible ? 'translate-x-0' : 'translate-x-full'
            }`}
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
                    <AutoSwitchToggle
                        tickerData={tickerData}
                        onStationChange={onStationChange}
                        currentStationIndex={currentStationIndex}
                        onAutoSwitchToggle={onAutoSwitchToggle}
                    />
                </div>
                
                <div className="space-y-4">
                    <div className="p-4 bg-gray-50 rounded-lg">
                        <h3 className="font-medium text-gray-700 mb-2">Status Stasiun</h3>
                        <div className="space-y-2">
                            <label className="flex items-center">
                                <input type="checkbox" className="form-checkbox h-4 w-4 text-blue-600" />
                                <span className="ml-2 text-gray-700">Aman</span>
                            </label>
                            <label className="flex items-center">
                                <input type="checkbox" className="form-checkbox h-4 w-4 text-yellow-500" />
                                <span className="ml-2 text-gray-700">Peringatan</span>
                            </label>
                            <label className="flex items-center">
                                <input type="checkbox" className="form-checkbox h-4 w-4 text-red-500" />
                                <span className="ml-2 text-gray-700">Bahaya</span>
                            </label>
                        </div>
                    </div>
                    
                    <div className="p-4 bg-gray-50 rounded-lg">
                        <h3 className="font-medium text-gray-700 mb-2">Rentang Waktu</h3>
                        <div className="grid grid-cols-2 gap-2">
                            <input 
                                type="date" 
                                className="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <input 
                                type="date" 
                                className="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>
                    </div>
                    
                    <button className="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors">
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </div>
    );
};

export default FilterSidebar;