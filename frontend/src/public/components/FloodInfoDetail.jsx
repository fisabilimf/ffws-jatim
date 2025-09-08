import React, { useState, useEffect, useRef } from 'react';

const FloodInfoDetail = ({ onDataUpdate, onStationSelect }) => {
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
    { id: 1, name: 'Stasiun Surabaya', value: 2.45, unit: 'm', status: 'warning', location: 'Surabaya River', history: generateDetailedHistory(2.45) },
    { id: 2, name: 'Stasiun Malang', value: 1.85, unit: 'm', status: 'alert', location: 'Malang City', history: generateDetailedHistory(1.85) },
    { id: 3, name: 'Stasiun Sidoarjo', value: 3.20, unit: 'm', status: 'safe', location: 'Sidoarjo', history: generateDetailedHistory(3.20) },
    { id: 4, name: 'Stasiun Probolinggo', value: 1.65, unit: 'm', status: 'warning', location: 'Probolinggo', history: generateDetailedHistory(1.65) },
    { id: 5, name: 'Stasiun Pasuruan', value: 2.10, unit: 'm', status: 'safe', location: 'Pasuruan', history: generateDetailedHistory(2.10) },
    { id: 6, name: 'Stasiun Mojokerto', value: 2.75, unit: 'm', status: 'safe', location: 'Mojokerto', history: generateDetailedHistory(2.75) },
    { id: 7, name: 'Stasiun Lamongan', value: 1.95, unit: 'm', status: 'safe', location: 'Lamongan', history: generateDetailedHistory(1.95) },
    { id: 8, name: 'Stasiun Gresik', value: 3.45, unit: 'm', status: 'alert', location: 'Gresik', history: generateDetailedHistory(3.45) },
    { id: 9, name: 'Stasiun Tuban', value: 2.30, unit: 'm', status: 'warning', location: 'Tuban', history: generateDetailedHistory(2.30) },
    { id: 10, name: 'Stasiun Bojonegoro', value: 1.80, unit: 'm', status: 'safe', location: 'Bojonegoro', history: generateDetailedHistory(1.80) }
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
    
    // Create gradient for area fill
    const gradient = ctx.createLinearGradient(0, 0, 0, height);
    gradient.addColorStop(0, lineColor + '50'); // 50% opacity at top (near line)
    gradient.addColorStop(0.3, lineColor + '40'); // 40% opacity near line
    gradient.addColorStop(0.6, lineColor + '25'); // 25% opacity at middle
    gradient.addColorStop(1, lineColor + '05'); // 5% opacity at bottom
    
    // Draw area fill below the line
    ctx.beginPath();
    ctx.fillStyle = gradient;
    
    // Start from bottom-left
    ctx.moveTo(0, height);
    
    // Draw line to each data point
    history.forEach((value, index) => {
      const x = (index / (history.length - 1)) * width;
      const y = height - ((value - minValue) / range) * height;
      ctx.lineTo(x, y);
    });
    
    // Complete the path to bottom-right and fill
    ctx.lineTo(width, height);
    ctx.closePath();
    ctx.fill();
    
    // Draw the line chart on top
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

  return (
    <div className="absolute top-16 sm:top-20 left-2 right-2 sm:left-4 sm:right-4 z-10">
      <div className="max-w-2xl mx-auto">
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
              <canvas
                id={`chart-${item.id}`}
                width="48"
                height="24"
                className="w-12 h-6 rounded transition-all duration-300 hover:scale-105"
              />
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};

export default FloodInfoDetail;
