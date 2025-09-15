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
    10: [111.8816, -7.1500],   // Bojonegoro
    11: [112.2333, -7.5500],   // Jombang
    12: [111.8833, -7.6000],   // Nganjuk
    13: [112.0167, -7.8167],   // Kediri
    14: [112.1667, -8.1000],   // Blitar
    15: [111.9000, -8.0667],   // Tulungagung
    16: [112.7333, -7.6000],   // Bangil
    17: [112.6833, -7.8333],   // Lawang
    18: [112.6500, -7.9000],   // Singosari
    19: [110.3569, -7.9133],   // Wates
    20: [110.3739, -7.7884],   // Lempuyangan
    21: [110.3633, -7.7956],   // Tugu
    22: [111.4167, -7.6500],   // Magetan
    23: [111.5167, -7.6333],   // Madiun
    24: [111.6667, -7.5333],   // Caruban
    25: [111.7167, -7.4667],   // Ngrowot
    26: [111.9833, -7.4167],   // Kertosono
    27: [112.4000, -7.0667],   // Babat
    28: [112.4333, -7.0500],   // Sumari
    29: [112.4667, -7.0333],   // Duduk
    30: [112.5000, -7.0167],   // Plabuhan
    31: [113.4000, -7.9333],   // Kalisat
    32: [113.6833, -8.2333],   // Jember
    33: [113.7167, -8.3000],   // Rambipuji
    34: [113.7000, -7.8000],   // Probolinggo Baru
    35: [113.4667, -8.2833],   // Tanggul
    36: [113.3667, -8.0500],   // Klakah
    37: [113.3333, -8.0167],   // Ranuyoso
    38: [113.5333, -8.1500],   // Sukowono
    39: [113.5833, -8.2167],   // Arjasa
    40: [113.6333, -8.2667]    // Kalisetail
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
      { id: 15, name: 'Stasiun Tulungagung', value: 2.05, unit: 'm', location: 'Tulungagung' },
      { id: 16, name: 'Stasiun Bangil', value: 1.95, unit: 'm', location: 'Bangil' },
      { id: 17, name: 'Stasiun Lawang', value: 2.15, unit: 'm', location: 'Lawang' },
      { id: 18, name: 'Stasiun Singosari', value: 1.75, unit: 'm', location: 'Singosari' },
      { id: 19, name: 'Stasiun Wates', value: 2.25, unit: 'm', location: 'Wates' },
      { id: 20, name: 'Stasiun Lempuyangan', value: 1.85, unit: 'm', location: 'Lempuyangan' },
      { id: 21, name: 'Stasiun Tugu', value: 2.35, unit: 'm', location: 'Tugu' },
      { id: 22, name: 'Stasiun Magetan', value: 1.65, unit: 'm', location: 'Magetan' },
      { id: 23, name: 'Stasiun Madiun', value: 2.45, unit: 'm', location: 'Madiun' },
      { id: 24, name: 'Stasiun Caruban', value: 1.55, unit: 'm', location: 'Caruban' },
      { id: 25, name: 'Stasiun Ngrowot', value: 2.05, unit: 'm', location: 'Ngrowot' },
      { id: 26, name: 'Stasiun Kertosono', value: 1.95, unit: 'm', location: 'Kertosono' },
      { id: 27, name: 'Stasiun Babat', value: 2.15, unit: 'm', location: 'Babat' },
      { id: 28, name: 'Stasiun Sumari', value: 1.75, unit: 'm', location: 'Sumari' },
      { id: 29, name: 'Stasiun Duduk', value: 2.25, unit: 'm', location: 'Duduk' },
      { id: 30, name: 'Stasiun Plabuhan', value: 1.85, unit: 'm', location: 'Plabuhan' },
      { id: 32, name: 'Stasiun Jember', value: 1.65, unit: 'm', location: 'Jember' },
      { id: 33, name: 'Stasiun Rambipuji', value: 2.45, unit: 'm', location: 'Rambipuji' },
      { id: 34, name: 'Stasiun Probolinggo Baru', value: 1.55, unit: 'm', location: 'Probolinggo Baru' },
      { id: 35, name: 'Stasiun Tanggul', value: 2.05, unit: 'm', location: 'Tanggul' },
      { id: 36, name: 'Stasiun Klakah', value: 1.95, unit: 'm', location: 'Klakah' },
      { id: 37, name: 'Stasiun Ranuyoso', value: 2.15, unit: 'm', location: 'Ranuyoso' },
      { id: 38, name: 'Stasiun Sukowono', value: 1.75, unit: 'm', location: 'Sukowono' },
      { id: 39, name: 'Stasiun Arjasa', value: 2.25, unit: 'm', location: 'Arjasa' },
      { id: 40, name: 'Stasiun Kalisetail', value: 1.85, unit: 'm', location: 'Kalisetail' }
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
    
    // Trigger custom event to stop auto switch
    const event = new CustomEvent('userInteraction', { 
      detail: { source: 'runningBar', stationId: station.id }
    });
    document.dispatchEvent(event);
    
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
    <div className={`absolute top-4 z-[70] transition-all duration-300 ease-in-out`}
      style={{ 
        left: '400px', 
        right: '58px'
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