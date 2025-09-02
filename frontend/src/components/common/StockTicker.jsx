import React, { useState, useEffect, useRef } from 'react';

const FloodWarningTicker = () => {
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

  const [tickerData, setTickerData] = useState([
    { id: 1, name: 'Stasiun Surabaya', value: 2.45, unit: 'm', status: 'warning', location: 'Surabaya River', history: [2.1, 2.3, 2.4, 2.45] },
    { id: 2, name: 'Stasiun Malang', value: 1.85, unit: 'm', status: 'alert', location: 'Malang City', history: [1.2, 1.5, 1.7, 1.85] },
    { id: 3, name: 'Stasiun Sidoarjo', value: 3.20, unit: 'm', status: 'safe', location: 'Sidoarjo', history: [2.8, 3.0, 3.1, 3.20] },
    { id: 4, name: 'Stasiun Probolinggo', value: 1.65, unit: 'm', status: 'warning', location: 'Probolinggo', history: [1.4, 1.5, 1.6, 1.65] },
    { id: 5, name: 'Stasiun Pasuruan', value: 2.10, unit: 'm', status: 'safe', location: 'Pasuruan', history: [1.9, 2.0, 2.05, 2.10] },
    { id: 6, name: 'Stasiun Mojokerto', value: 2.75, unit: 'm', status: 'safe', location: 'Mojokerto', history: [2.5, 2.6, 2.7, 2.75] },
    { id: 7, name: 'Stasiun Lamongan', value: 1.95, unit: 'm', status: 'safe', location: 'Lamongan', history: [1.7, 1.8, 1.9, 1.95] },
    { id: 8, name: 'Stasiun Gresik', value: 3.45, unit: 'm', status: 'alert', location: 'Gresik', history: [3.0, 3.2, 3.3, 3.45] },
    { id: 9, name: 'Stasiun Tuban', value: 2.30, unit: 'm', status: 'warning', location: 'Tuban', history: [2.1, 2.2, 2.25, 2.30] },
    { id: 10, name: 'Stasiun Bojonegoro', value: 1.80, unit: 'm', status: 'safe', location: 'Bojonegoro', history: [1.6, 1.7, 1.75, 1.80] },
    { id: 11, name: 'Stasiun Jombang', value: 2.60, unit: 'm', status: 'safe', location: 'Jombang', history: [2.4, 2.5, 2.55, 2.60] },
    { id: 12, name: 'Stasiun Nganjuk', value: 2.15, unit: 'm', status: 'safe', location: 'Nganjuk', history: [1.9, 2.0, 2.1, 2.15] },
    { id: 13, name: 'Stasiun Kediri', value: 1.70, unit: 'm', status: 'safe', location: 'Kediri', history: [1.5, 1.6, 1.65, 1.70] },
    { id: 14, name: 'Stasiun Blitar', value: 2.90, unit: 'm', status: 'warning', location: 'Blitar', history: [2.6, 2.7, 2.8, 2.90] },
    { id: 15, name: 'Stasiun Tulungagung', value: 2.05, unit: 'm', status: 'safe', location: 'Tulungagung', history: [1.8, 1.9, 2.0, 2.05] }
  ]);

  const tickerRef = useRef(null);
  const [scrollPosition, setScrollPosition] = useState(0);

  // Update data secara real-time
  useEffect(() => {
    const updateTickerData = () => {
      setTickerData(prev => prev.map(item => {
        let newValue = item.value;
        let newStatus = item.status;
        
        // Generate random changes untuk water level
        newValue = Math.max(0.5, Math.min(5, item.value + (Math.random() - 0.5) * 0.2));
        newStatus = newValue > 4 ? 'alert' : newValue > 2.5 ? 'warning' : 'safe';

        // Update history data - add new value and keep last 20 points
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

  // Draw line charts when data updates
  useEffect(() => {
    tickerData.forEach(item => {
      drawLineChart(item.history, item.status, `chart-${item.id}`);
    });
  }, [tickerData]);

  // Initial chart drawing
  useEffect(() => {
    const timer = setTimeout(() => {
      tickerData.forEach(item => {
        drawLineChart(item.history, item.status, `chart-${item.id}`);
      });
    }, 500);
    
    return () => clearTimeout(timer);
  }, []);

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

  // Function to draw line chart
  const drawLineChart = (history, status, canvasId) => {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    const width = canvas.width;
    const height = canvas.height;
    
    // Clear canvas
    ctx.clearRect(0, 0, width, height);
    
    if (history.length < 2) return;
    
    // Find min and max values for scaling
    const minValue = Math.min(...history);
    const maxValue = Math.max(...history);
    const range = maxValue - minValue || 1;
    
    // Set line color based on status
    let lineColor;
    switch (status) {
      case 'safe': lineColor = '#10B981'; break; // green
      case 'warning': lineColor = '#F59E0B'; break; // yellow
      case 'alert': lineColor = '#EF4444'; break; // red
      default: lineColor = '#6B7280'; break;
    }
    
    // Draw line chart
    ctx.beginPath();
    ctx.strokeStyle = lineColor;
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    
    history.forEach((value, index) => {
      const x = (index / (history.length - 1)) * width;
      const y = height - ((value - minValue) / range) * height;
      
      if (index === 0) {
        ctx.moveTo(x, y);
      } else {
        ctx.lineTo(x, y);
      }
    });
    
    ctx.stroke();
  };

  return (
    <div className="bg-gradient-to-r from-blue-900 via-blue-800 to-blue-900 text-white p-2 shadow-lg">
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
                 {tickerData.map((item) => (
           <div key={item.id} className="flex items-center space-x-3 bg-blue-800/50 rounded-lg px-3 py-2 min-w-max">
             <div className={`w-3 h-3 rounded-full ${getStatusBgColor(item.status)}`}></div>
             <span className="text-xs text-blue-200 font-medium">{item.name}</span>
             <div className="flex items-center space-x-1">
               <span className="text-sm font-bold">{formatValue(item.value)}</span>
               <span className="text-xs text-blue-300">{item.unit}</span>
             </div>
                           <canvas
                id={`chart-${item.id}`}
                width="64"
                height="32"
                className="w-16 h-8 bg-blue-700 rounded"
              />
           </div>
         ))}
      </div>
    </div>
  );
};

export default FloodWarningTicker;
