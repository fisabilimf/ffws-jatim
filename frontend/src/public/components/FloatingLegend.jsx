import React from 'react';

const FloatingLegend = () => {
  return (
    <div className="absolute bottom-2 right-2 sm:bottom-4 sm:right-4 z-10">
      <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-2 sm:p-4 border border-gray-200">
        <h3 className="text-xs sm:text-sm font-semibold text-gray-800 mb-2 sm:mb-3">Status Monitoring</h3>
        <div className="space-y-1 sm:space-y-2">
          <div className="flex items-center space-x-1.5 sm:space-x-2">
            <div className="w-2 h-2 sm:w-3 sm:h-3 rounded-full bg-green-500"></div>
            <span className="text-xs text-gray-700">Aman</span>
          </div>
          <div className="flex items-center space-x-1.5 sm:space-x-2">
            <div className="w-2 h-2 sm:w-3 sm:h-3 rounded-full bg-yellow-500"></div>
            <span className="text-xs text-gray-700">Waspada</span>
          </div>
          <div className="flex items-center space-x-1.5 sm:space-x-2">
            <div className="w-2 h-2 sm:w-3 sm:h-3 rounded-full bg-red-500"></div>
            <span className="text-xs text-gray-700">Bahaya</span>
          </div>
        </div>
      </div>
    </div>
  );
};

export default FloatingLegend;
