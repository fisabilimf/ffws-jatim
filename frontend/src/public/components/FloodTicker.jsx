import React, { useState, useEffect, useRef } from 'react';
import Chart from './Chart';

const FloodWarningTicker = ({ tickerData, onStationSelect }) => {
  const tickerRef = useRef(null);
  const [scrollPosition, setScrollPosition] = useState(0);

  // Auto-scroll ticker
  useEffect(() => {
    const scrollTicker = () => {
      if (tickerRef.current) {
        setScrollPosition(prev => {
          const newPosition = prev + 1;
          if (newPosition >= tickerRef.current.scrollWidth - tickerRef.current.clientWidth) {
            return 0;
          }
          return newPosition;
        });
      }
    };

    const scrollInterval = setInterval(scrollTicker, 50);
    return () => clearInterval(scrollInterval);
  }, []);

  // Apply scroll position
  useEffect(() => {
    if (tickerRef.current) {
      tickerRef.current.scrollLeft = scrollPosition;
    }
  }, [scrollPosition]);

  const getStatusBgColor = (status) => {
    switch (status) {
      case 'safe': return 'bg-green-500';
      case 'warning': return 'bg-yellow-500';
      case 'alert': return 'bg-red-500';
      default: return 'bg-gray-500';
    }
  };

  const formatValue = (value) => {
    if (value >= 1000) {
      return new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      }).format(value);
    }
    return new Intl.NumberFormat('id-ID', {
      minimumFractionDigits: 1,
      maximumFractionDigits: 1
    }).format(value);
  };


  // Don't render if no data
  if (!tickerData) return null;

  return (
    <div 
      className="sticky-ticker sticky top-0 z-50 bg-gradient-to-r from-blue-900 via-blue-800 to-blue-900 text-white p-2 shadow-lg"
      style={{
        position: 'sticky',
        top: 0,
        zIndex: 50
      }}
    >
      <div className="flex items-center justify-between mb-2 px-6">
        <div className="flex items-center space-x-2">
          <div className="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
          <span className="text-xs font-medium">FLOOD WARNING SYSTEM - LIVE MONITORING</span>
        </div>
        <span className="text-xs text-blue-200">REAL-TIME DATA</span>
      </div>
      
      <div 
        ref={tickerRef}
        className="flex space-x-6 overflow-hidden whitespace-nowrap px-6"
        style={{ scrollBehavior: 'smooth' }}
      >
                 {tickerData && tickerData.map((item) => (
           <div 
             key={item.id} 
             className="flex items-center space-x-3 rounded-lg px-3 py-2 min-w-max transition-all duration-300 hover:bg-blue-700/30 hover:scale-105 hover:shadow-lg cursor-pointer"
             onClick={() => onStationSelect && onStationSelect(item)}
           >
             <div className={`w-3 h-3 rounded-full ${getStatusBgColor(item.status)}`}></div>
             <span className="text-xs text-blue-200 font-medium">{item.name}</span>
             <div className="flex items-center space-x-1">
               <span className="text-sm font-bold">{formatValue(item.value)}</span>
               <span className="text-xs text-blue-300">{item.unit}</span>
             </div>
              <Chart
                data={item.history}
                width={64}
                height={32}
                showTooltip={false}
                className="w-16 h-8 rounded transition-all duration-300 hover:scale-105"
              />
           </div>
         ))}
      </div>
    </div>
  );
};

export default FloodWarningTicker;
