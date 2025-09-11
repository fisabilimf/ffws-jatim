import React, { useState, useEffect, useRef } from 'react';
import { determineStatus, getThresholdInfo } from '../config/stationThresholds';
import Chart from './Chart';

const FloodRunningBar = ({ onDataUpdate, onStationSelect, isSidebarOpen = false }) => {
  // Function to generate detailed history data (20 data points representing 10 minutes)
  const generateDetailedHistory = (currentValue) => {
    const history = [];
    let baseValue = currentValue - (Math.random() * 0.5 + 0.2); // Start from slightly lower value
    
    for (let i = 0; i < 20; i++) {
      // Generate realistic water level changes
      const change = (Math.random() - 0.5) * 0.15; // Smaller, more realistic changes
      baseValue = Math.max(0.5, Math.min(5, baseValue + change));
      history.push(parseFloat(baseValue.toFixed(2)));
    }
    return history;
  };

  // Initialize station data with correct thresholds
  const initializeStationData = () => {
    const stations = [
      { id: 1, name: 'Stasiun Surabaya', value: 2.45, unit: 'm', location: 'Surabaya River' },
      { id: 2, name: 'Stasiun Malang', value: 1.85, unit: 'm', location: 'Malang City' },
      { id: 3, name: 'Stasiun Sidoarjo', value: 3.20, unit: 'm', location: 'Sidoarjo' },
      { id: 4, name: 'Stasiun Probolinggo', value: 1.65, unit: 'm', location: 'Probolinggo' },
      { id: 5, name: 'Stasiun Pasuruan', value: 2.10, unit: 'm', location: 'Pasuruan' },
      { id: 6, name: 'Stasiun Mojokerto', value: 2.75, unit: 'm', location: 'Mojokerto' },
      { id: 7, name: 'Stasiun Lamongan', value: 1.95, unit: 'm', location: 'Lamongan' },
      { id: 8, name: 'Stasiun Gresik', value: 3.45, unit: 'm', location: 'Gresik' },
      { id: 9, name: 'Stasiun Tuban', value: 2.30, unit: 'm', location: 'Tuban' },
      { id: 10, name: 'Stasiun Bojonegoro', value: 1.80, unit: 'm', location: 'Bojonegoro' }
    ];

    return stations.map(station => ({
      ...station,
      status: determineStatus(station.value, station.name),
      history: generateDetailedHistory(station.value)
    }));
  };

  const [tickerData, setTickerData] = useState(initializeStationData());

  const tickerRef = useRef(null);
  const [scrollPosition, setScrollPosition] = useState(0);

  // Update data secara real-time dengan threshold per stasiun
  useEffect(() => {
    const updateTickerData = () => {
      setTickerData(prev => prev.map(item => {
        let newValue = item.value;
        
        // Generate random changes untuk water level
        newValue = Math.max(0.5, Math.min(5, item.value + (Math.random() - 0.5) * 0.2));
        
        // Gunakan threshold per stasiun untuk menentukan status
        const newStatus = determineStatus(newValue, item.name);

        // Update history - add new value and remove oldest
        const newHistory = [...item.history.slice(1), newValue];

        return {
          ...item,
          value: newValue,
          status: newStatus,
          history: newHistory
        };
      }));
    };

    const interval = setInterval(updateTickerData, 3000);
    return () => clearInterval(interval);
  }, []);

  // Send data to parent component whenever tickerData changes
  useEffect(() => {
    if (onDataUpdate && tickerData) {
      onDataUpdate(tickerData);
    }
  }, [tickerData, onDataUpdate]);

  // Auto-scroll ticker
  useEffect(() => {
    const scrollTicker = () => {
      if (tickerRef.current) {
        setScrollPosition(prev => {
          const newPosition = prev + 0.5;
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

  const getStatusColor = (status) => {
    switch (status) {
      case 'safe': return 'text-green-500';
      case 'warning': return 'text-yellow-500';
      case 'alert': return 'text-red-500';
      default: return 'text-gray-400';
    }
  };

  const getStatusBgColor = (status) => {
    switch (status) {
      case 'safe': return 'bg-green-500';
      case 'warning': return 'bg-yellow-500';
      case 'alert': return 'bg-red-500';
      default: return 'bg-gray-500';
    }
  };

  const formatValue = (value) => {
    return new Intl.NumberFormat('id-ID', {
      minimumFractionDigits: 1,
      maximumFractionDigits: 1
    }).format(value);
  };

  return (
    <div className={`absolute top-4 z-[70] transition-all duration-300 ease-in-out ${
      isSidebarOpen ? 'transform translate-x-80' : 'transform translate-x-0'
    }`}
    style={{ left: 'calc(320px + 2rem)', right: '1rem' }}>
      <div className="w-full">
        <div 
          ref={tickerRef}
          className="flex space-x-2 sm:space-x-3 overflow-hidden whitespace-nowrap bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-1.5 sm:p-2"
          style={{ scrollBehavior: 'smooth' }}
        >
          {tickerData.map((item) => (
            <div 
              key={item.id} 
              className="flex items-center space-x-1.5 sm:space-x-2 rounded-lg px-1.5 sm:px-2 py-1 sm:py-1.5 min-w-max transition-all duration-300 hover:bg-gray-50 hover:scale-105 cursor-pointer border border-gray-200"
              onClick={() => onStationSelect && onStationSelect(item)}
            >
              <div className={`w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full ${getStatusBgColor(item.status)}`}></div>
              <span className="text-xs text-gray-700 font-medium truncate max-w-12 sm:max-w-16">{item.name.replace('Stasiun ', '')}</span>
              <div className="flex items-center space-x-0.5 sm:space-x-1">
                <span className="text-xs font-bold text-gray-900">{formatValue(item.value)}</span>
                <span className="text-xs text-gray-500">{item.unit}</span>
              </div>
              <Chart
                data={item.history}
                width={48}
                height={24}
                showTooltip={false}
                miniMode={true}
                status={item.status}
                canvasId={`chart-${item.id}`}
                className="w-12 h-6 rounded"
              />
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};

export default FloodRunningBar;
