import React, { useState, useEffect } from 'react';

const StationDetail = ({ selectedStation, onClose }) => {
  const [stationData, setStationData] = useState(null);
  const [chartHistory, setChartHistory] = useState([]);

  // Function to generate detailed history data
  const generateDetailedHistory = (currentValue) => {
    const history = [];
    let baseValue = currentValue - (Math.random() * 0.5 + 0.2);
    
    for (let i = 0; i < 50; i++) {
      const change = (Math.random() - 0.5) * 0.15;
      baseValue = Math.max(0.5, Math.min(5, baseValue + change));
      history.push(parseFloat(baseValue.toFixed(2)));
    }
    return history;
  };

  // Initialize station data when selected
  useEffect(() => {
    if (selectedStation) {
      const history = generateDetailedHistory(selectedStation.value);
      setStationData({
        ...selectedStation,
        history: history
      });
      setChartHistory(history);
    }
  }, [selectedStation]);

  // Update chart data in real-time
  useEffect(() => {
    if (!stationData) return;

    const updateInterval = setInterval(() => {
      setChartHistory(prev => {
        const newValue = Math.max(0.5, Math.min(5, 
          prev[prev.length - 1] + (Math.random() - 0.5) * 0.2
        ));
        const newHistory = [...prev.slice(1), newValue];
        
        setStationData(prevData => ({
          ...prevData,
          value: newValue,
          status: newValue > 4 ? 'alert' : newValue > 2.5 ? 'warning' : 'safe'
        }));
        
        return newHistory;
      });
    }, 2000);

    return () => clearInterval(updateInterval);
  }, [stationData]);

  // Draw detailed chart
  useEffect(() => {
    if (chartHistory.length === 0) return;

    const canvas = document.getElementById('station-detail-chart');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    const width = canvas.width;
    const height = canvas.height;

    // Clear canvas
    ctx.clearRect(0, 0, width, height);

    if (chartHistory.length < 2) return;

    // Find min and max values
    const minValue = Math.min(...chartHistory);
    const maxValue = Math.max(...chartHistory);
    const range = maxValue - minValue || 1;

    // Set colors based on status
    let lineColor, bgColor;
    switch (stationData?.status) {
      case 'safe': 
        lineColor = '#10B981'; 
        bgColor = 'rgba(16, 185, 129, 0.1)';
        break;
      case 'warning': 
        lineColor = '#F59E0B'; 
        bgColor = 'rgba(245, 158, 11, 0.1)';
        break;
      case 'alert': 
        lineColor = '#EF4444'; 
        bgColor = 'rgba(239, 68, 68, 0.1)';
        break;
      default: 
        lineColor = '#6B7280'; 
        bgColor = 'rgba(107, 114, 128, 0.1)';
    }

    // Draw grid lines
    ctx.strokeStyle = '#E5E7EB';
    ctx.lineWidth = 1;
    for (let i = 0; i <= 4; i++) {
      const y = (i / 4) * height;
      ctx.beginPath();
      ctx.moveTo(0, y);
      ctx.lineTo(width, y);
      ctx.stroke();
    }

    // Draw area fill
    ctx.beginPath();
    ctx.fillStyle = bgColor;
    ctx.moveTo(0, height);
    
    chartHistory.forEach((value, index) => {
      const x = (index / (chartHistory.length - 1)) * width;
      const y = height - ((value - minValue) / range) * height;
      ctx.lineTo(x, y);
    });
    
    ctx.lineTo(width, height);
    ctx.closePath();
    ctx.fill();

    // Draw main line
    ctx.beginPath();
    ctx.strokeStyle = lineColor;
    ctx.lineWidth = 3;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';

    chartHistory.forEach((value, index) => {
      const x = (index / (chartHistory.length - 1)) * width;
      const y = height - ((value - minValue) / range) * height;
      
      if (index === 0) {
        ctx.moveTo(x, y);
      } else {
        ctx.lineTo(x, y);
      }
    });

    ctx.stroke();

    // Draw data points
    ctx.fillStyle = lineColor;
    chartHistory.forEach((value, index) => {
      if (index % 5 === 0) { // Show every 5th point
        const x = (index / (chartHistory.length - 1)) * width;
        const y = height - ((value - minValue) / range) * height;
        
        ctx.beginPath();
        ctx.arc(x, y, 4, 0, 2 * Math.PI);
        ctx.fill();
      }
    });

  }, [chartHistory, stationData]);

  const getStatusColor = (status) => {
    switch (status) {
      case 'safe': return 'text-green-600';
      case 'warning': return 'text-yellow-600';
      case 'alert': return 'text-red-600';
      default: return 'text-gray-600';
    }
  };

  const getStatusBgColor = (status) => {
    switch (status) {
      case 'safe': return 'bg-green-100 border-green-200';
      case 'warning': return 'bg-yellow-100 border-yellow-200';
      case 'alert': return 'bg-red-100 border-red-200';
      default: return 'bg-gray-100 border-gray-200';
    }
  };

  const getStatusText = (status) => {
    switch (status) {
      case 'safe': return 'Aman';
      case 'warning': return 'Waspada';
      case 'alert': return 'Bahaya';
      default: return 'Tidak Diketahui';
    }
  };

  if (!selectedStation || !stationData) {
    return null;
  }

  return (
    <div className="fixed top-4 right-4 z-50 w-80 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden">
      {/* Header */}
      <div className="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4">
        <div className="flex items-center justify-between">
          <div>
            <h3 className="text-lg font-semibold">{stationData.name}</h3>
            <p className="text-blue-100 text-sm">{stationData.location}</p>
          </div>
          <button
            onClick={onClose}
            className="text-white hover:text-gray-200 transition-colors"
          >
            <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      {/* Content */}
      <div className="p-4 space-y-4">
        {/* Status Card */}
        <div className={`p-3 rounded-lg border-2 ${getStatusBgColor(stationData.status)}`}>
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm text-gray-600">Status Saat Ini</p>
              <p className={`text-lg font-bold ${getStatusColor(stationData.status)}`}>
                {getStatusText(stationData.status)}
              </p>
            </div>
            <div className="text-right">
              <p className="text-2xl font-bold text-gray-900">
                {stationData.value.toFixed(1)}
              </p>
              <p className="text-sm text-gray-500">{stationData.unit}</p>
            </div>
          </div>
        </div>

        {/* Chart */}
        <div className="bg-gray-50 rounded-lg p-3">
          <h4 className="text-sm font-medium text-gray-700 mb-2">Grafik Level Air (10 Menit Terakhir)</h4>
          <canvas
            id="station-detail-chart"
            width="320"
            height="120"
            className="w-full h-30 rounded"
          />
        </div>

        {/* Statistics */}
        <div className="grid grid-cols-2 gap-3">
          <div className="bg-gray-50 rounded-lg p-3">
            <p className="text-xs text-gray-500">Level Tertinggi</p>
            <p className="text-lg font-bold text-gray-900">
              {Math.max(...chartHistory).toFixed(1)}m
            </p>
          </div>
          <div className="bg-gray-50 rounded-lg p-3">
            <p className="text-xs text-gray-500">Level Terendah</p>
            <p className="text-lg font-bold text-gray-900">
              {Math.min(...chartHistory).toFixed(1)}m
            </p>
          </div>
        </div>

        {/* Last Update */}
        <div className="text-center">
          <p className="text-xs text-gray-500">
            Terakhir diperbarui: {new Date().toLocaleTimeString('id-ID')}
          </p>
        </div>
      </div>
    </div>
  );
};

export default StationDetail;
