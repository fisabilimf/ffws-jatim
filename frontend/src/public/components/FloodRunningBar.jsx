import React, { useState, useEffect, useRef } from 'react';
import { determineStatus, getThresholdInfo } from '../config/stationThresholds';
import Chart from './Chart';

const FloodRunningBar = ({ onDataUpdate, onStationSelect, onMapFocus, isSidebarOpen = false }) => {
  // Station coordinates - format [lng, lat] sesuai MapboxMap
  const stationCoordinates = {
    1: [112.7508, -7.2575],    // Surabaya
    2: [112.6308, -7.9831],    // Malang
    3: [112.7183, -7.4478],    // Sidoarjo
    4: [113.7156, -7.7764],    // Probolinggo
    5: [112.6909, -7.6461],    // Pasuruan
    6: [112.4694, -7.4706],    // Mojokerto
    7: [112.3333, -7.1167],    // Lamongan
    8: [112.5729, -7.1554],    // Gresik
    9: [112.0483, -6.8976],    // Tuban
    10:[111.8816, -7.1500],   // Bojonegoro
    11:[112.2333, -7.5500],   // Jombang
    12:[111.8833, -7.6000],   // Nganjuk
    13:[112.0167, -7.8167],   // Kediri
    14:[112.1667, -8.1000],   // Blitar
    15:[111.9000, -8.0667]    // Tulungagung
  };

  const generateDetailedHistory = (currentValue) => {
    const history = [];
    let baseValue = currentValue - (Math.random() * 0.5 + 0.2);
    
    for (let i = 0; i < 20; i++) {
      const change = (Math.random() - 0.5) * 0.15;
      baseValue = Math.max(0.5, Math.min(5, baseValue + change));
      history.push(parseFloat(baseValue.toFixed(2)));
    }
    return history;
  };

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
      { id: 10, name: 'Stasiun Bojonegoro', value: 1.80, unit: 'm', location: 'Bojonegoro' },
      { id: 11, name: 'Stasiun Jombang', value: 2.15, unit: 'm', location: 'Jombang' },
      { id: 12, name: 'Stasiun Nganjuk', value: 1.90, unit: 'm', location: 'Nganjuk' },
      { id: 13, name: 'Stasiun Kediri', value: 2.25, unit: 'm', location: 'Kediri' },
      { id: 14, name: 'Stasiun Blitar', value: 1.75, unit: 'm', location: 'Blitar' },
      { id: 15, name: 'Stasiun Tulungagung', value: 2.05, unit: 'm', location: 'Tulungagung' }
    ];
    
    return stations.map(station => ({
      ...station,
      coordinates: stationCoordinates[station.id],
      status: determineStatus(station.value, station.name),
      history: generateDetailedHistory(station.value)
    }));
  };

  const [tickerData, setTickerData] = useState(initializeStationData());
  const [selectedStationId, setSelectedStationId] = useState(null);
  const tickerRef = useRef(null);
  const [scrollPosition, setScrollPosition] = useState(0);

  const handleStationClick = (station) => {
    console.log('Station clicked:', station);
    
    setSelectedStationId(station.id);
    
    if (onStationSelect) {
      onStationSelect(station);
    }
    
    // Panggil langsung handleMapFocus via window.mapboxAutoFocus
    if (window.mapboxAutoFocus) {
      console.log('Directly calling mapboxAutoFocus');
      window.mapboxAutoFocus({
        lat: station.coordinates[1],
        lng: station.coordinates[0],
        zoom: 14,
        stationId: station.id,
        stationName: station.name
      });
    } else {
      console.error('mapboxAutoFocus is not available');
    }
    
    setTimeout(() => {
      setSelectedStationId(null);
    }, 3000);
  };

  useEffect(() => {
    const updateTickerData = () => {
      setTickerData(prev => prev.map(item => {
        let newValue = Math.max(0.5, Math.min(5, item.value + (Math.random() - 0.5) * 0.2));
        const newStatus = determineStatus(newValue, item.name);
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

  useEffect(() => {
    if (onDataUpdate && tickerData) {
      onDataUpdate(tickerData);
    }
  }, [tickerData, onDataUpdate]);

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
      isSidebarOpen ? 'transform translate-x-0' : 'transform translate-x-0'
    }`}
      style={{ 
        left: '400px', 
        right: '5rem'
      }}>
      <div className="w-full">
        <div 
          ref={tickerRef}
          className="flex space-x-2 sm:space-x-2 overflow-hidden whitespace-nowrap bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-1.5 sm:p-2"
          style={{ scrollBehavior: 'smooth' }}
        >
          {tickerData.map((item) => (
            <div 
              key={item.id} 
              className={`flex items-center space-x-1.5 sm:space-x-2 rounded-lg px-1.5 sm:px-2 py-1 sm:py-1.5 min-w-max transition-all duration-300 cursor-pointer border border-gray-200 ${
                selectedStationId === item.id 
                  ? 'bg-blue-100 border-blue-400 scale-105 shadow-md' 
                  : 'hover:bg-gray-50 hover:scale-105'
              }`}
              onClick={() => handleStationClick(item)}
              title={`Klik untuk pindah ke ${item.name.replace('Stasiun ', '')}`}
            >
              <div className={`w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full ${getStatusBgColor(item.status)} ${
                selectedStationId === item.id ? 'animate-pulse' : ''
              }`}></div>
              <span className="text-xs text-gray-700 font-medium truncate max-w-12 sm:max-w-16">
                {item.name.replace('Stasiun ', '')}
              </span>
              <div className="flex items-center space-x-0.5 sm:space-x-1">
                <span className="text-xs font-bold text-gray-900">{formatValue(item.value)}</span>
                <span className="text-xs text-gray-500">{item.unit}</span>
              </div>
              <Chart
                data={item.history}
                width={48}
                height={22}
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