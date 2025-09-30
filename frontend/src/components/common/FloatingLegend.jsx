import React from 'react';

const FloatingLegend = () => {
  return (
    <div className="fixed bottom-6 right-1   p-2  space-x-4">
      <h3 className="text-xs font-semibold mr-2"></h3>
      <div className="flex items-center space-x-3">
        <div className="flex items-center space-x-1">
          <div className="w-3 h-3 rounded-full bg-green-500"></div>
          <span className="text-xs text-gray-700">Aman</span>
        </div>
        <div className="flex items-center space-x-1">
          <div className="w-3 h-3 rounded-full bg-yellow-500"></div>
          <span className="text-xs text-gray-700">Waspada</span>
        </div>
        <div className="flex items-center space-x-1">
          <div className="w-3 h-3 rounded-full bg-red-500"></div>
          <span className="text-xs text-gray-700">Bahaya</span>
        </div>
      </div>
    </div>
  );
};

export default FloatingLegend;