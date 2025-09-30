import React from 'react';

const FloatingLegend = () => {

  return (
    <div className="w-80 bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 sm:p-4 border border-gray-200">
      <h3 className="text-xs sm:text-sm font-semibold text-gray-800 mb-3 sm:mb-4 text-center">Ketinggian Air</h3>
      
      {/* Gradient Color Bar */}
      <div className="relative mb-0 -mx-2">
        <div 
          className="h-5 rounded-lg shadow-sm"
          style={{
            background: 'linear-gradient(to right, #10b981, #f59e0b, #ef4444)'
          }}
        ></div>
        
        
        {/* Markers and Labels */}
        <div className="relative mt-1">
          {/* Marker 1 - Safe */}
          <div className="absolute left-0 transform -translate-x-1/2">
            <div className="w-0 h-0 border-l-[3px] border-r-[3px] border-t-[4px] border-l-transparent border-r-transparent border-t-white"></div>
            <div className="text-[10px] text-white font-medium mt-1 text-center whitespace-nowrap" style={{ textShadow: '1px 1px 1px rgba(0,0,0,0.9)' }}>
              Safe
            </div>
          </div>
          
          {/* Marker 2 - Warning */}
          <div className="absolute left-1/2 transform -translate-x-1/2">
            <div className="w-0 h-0 border-l-[3px] border-r-[3px] border-t-[4px] border-l-transparent border-r-transparent border-t-white"></div>
            <div className="text-[10px] text-white font-medium mt-1 text-center whitespace-nowrap" style={{ textShadow: '1px 1px 1px rgba(0,0,0,0.9)' }}>
              Warning
            </div>
          </div>
          
          {/* Marker 3 - Danger */}
          <div className="absolute right-0 transform translate-x-1/2">
            <div className="w-0 h-0 border-l-[3px] border-r-[3px] border-t-[4px] border-l-transparent border-r-transparent border-t-white"></div>
            <div className="text-[10px] text-white font-medium mt-1 text-center whitespace-nowrap" style={{ textShadow: '1px 1px 1px rgba(0,0,0,0.9)' }}>
              Danger
            </div>
          </div>
        </div>
      </div>
      
    </div>
  );
};

export default FloatingLegend;