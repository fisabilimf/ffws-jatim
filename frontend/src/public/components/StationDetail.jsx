import React, { useState, useEffect } from 'react';
import Chart from './Chart';
import DetailPanel from './DetailPanel'; // Import DetailPanel

// Reusable Sidebar Template Component
const SidebarTemplate = ({ 
  isOpen, 
  onClose, 
  title, 
  subtitle, 
  width = "w-[380px]", // Lebar sidebar disesuaikan
  position = "fixed", 
  zIndex = "z-[60]",
  topPosition = "top-20", 
  children,
  headerContent,
  statusDot
}) => {
  const [isVisible, setIsVisible] = useState(false);
  
  useEffect(() => {
    if (isOpen) {
      setTimeout(() => {
        setIsVisible(true);
      }, 10);
    } else {
      setIsVisible(false);
    }
  }, [isOpen]);
  
  const handleClose = () => {
    setIsVisible(false);
    setTimeout(() => {
      onClose();
    }, 300);
  };
  
  if (!isOpen) return null;
  
  return (
    <div 
      className={`${position} ${topPosition} left-0 h-[calc(100vh-5rem)] ${width} bg-white shadow-2xl ${zIndex} transform transition-transform duration-300 ease-in-out flex flex-col ${
        isVisible ? 'translate-x-0' : '-translate-x-full'
      }`}
      style={{ willChange: 'transform' }}
    >
      {/* Header */}
      <div className="bg-white border-b border-gray-200 p-4 flex-shrink-0">
        <div className="flex items-center space-x-3">
          <button
            onClick={handleClose}
            className="p-2 hover:bg-gray-100 rounded-full transition-colors"
          >
            <svg className="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
            </svg>
          </button>
          <div className="flex-1">
            {headerContent || (
              <>
                <h3 className="text-lg font-semibold text-gray-900">{title}</h3>
                {subtitle && <p className="text-gray-500 text-sm">{subtitle}</p>}
              </>
            )}
          </div>
          {statusDot && <div className={`w-3 h-3 rounded-full ${statusDot}`}></div>}
        </div>
      </div>
      
      {/* Content - Scrollable Area */}
      <div className="flex-1 overflow-y-auto overflow-x-hidden">
        {children}
      </div>
    </div>
  );
};

const StationDetail = ({ selectedStation, onClose, tickerData }) => {
  const [stationData, setStationData] = useState(null);
  const [chartHistory, setChartHistory] = useState([]);
  
  // Initialize station data when selected
  useEffect(() => {
    if (selectedStation && tickerData) {
      // Find the station data from tickerData
      const foundStation = tickerData.find(station => station.id === selectedStation.id);
      if (foundStation) {
        setStationData(foundStation);
        setChartHistory(foundStation.history);
      }
    }
  }, [selectedStation, tickerData]);
  
  // Update chart data in real-time from tickerData
  useEffect(() => {
    if (!stationData || !tickerData) return;
    // Find the current station data from tickerData
    const currentStationData = tickerData.find(station => station.id === stationData.id);
    if (currentStationData) {
      setStationData(currentStationData);
      setChartHistory(currentStationData.history);
    }
  }, [tickerData, stationData]);

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
      case 'safe': return 'bg-green-50 border-green-200';
      case 'warning': return 'bg-yellow-50 border-yellow-200';
      case 'alert': return 'bg-red-50 border-red-200';
      default: return 'bg-gray-50 border-gray-200';
    }
  };
  
  const getStatusDotColor = (status) => {
    switch (status) {
      case 'safe': return 'bg-green-500';
      case 'warning': return 'bg-yellow-500';
      case 'alert': return 'bg-red-500';
      default: return 'bg-gray-500';
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
  
  const getChartColor = (status) => {
    switch (status) {
      case 'safe': return '#10B981'; // Green
      case 'warning': return '#F59E0B'; // Yellow
      case 'alert': return '#EF4444'; // Red
      default: return '#6B7280'; // Gray
    }
  };
  
  // Data dummy untuk sensor air
  const sensorData = {
    sensorType: "Ultrasonic Sensor",
    sensorDepth: "5 meter",
    installationDate: "15 Januari 2023",
    lastMaintenance: "10 Oktober 2023",
    batteryLevel: "87%",
    signalStrength: "Strong",
    accuracy: "±2cm"
  };
  
  // Data dummy untuk kualitas air
  const waterQualityData = {
    pH: 7.2,
    turbidity: 12.5,
    temperature: 26.8,
    dissolvedOxygen: 6.8,
    conductivity: 450,
    tds: 225
  };
  
  // Data dummy untuk informasi cuaca
  const weatherData = {
    current: {
      temperature: 28,
      humidity: 78,
      rainfall: 2.4,
      windSpeed: 12,
      windDirection: "Barat Daya"
    },
    forecast: [
      { time: "14:00", rain: 80, temp: 29 },
      { time: "17:00", rain: 60, temp: 27 },
      { time: "20:00", rain: 40, temp: 25 },
      { time: "23:00", rain: 20, temp: 24 }
    ]
  };
  
  // Data dummy untuk riwayat banjir
  const floodHistory = [
    { date: "12 Nov 2023", level: 4.2, duration: "3 jam" },
    { date: "5 Okt 2023", level: 3.8, duration: "2 jam" },
    { date: "18 Sep 2023", level: 4.5, duration: "4 jam" }
  ];
  
  if (!selectedStation || !stationData) {
    return null;
  }
  
  return (
    <>
      <SidebarTemplate
        isOpen={!!selectedStation}
        onClose={onClose}
        title={stationData.name}
        subtitle={stationData.location}
        statusDot={getStatusDotColor(stationData.status)}
        topPosition="top-20"
      >
        <div className="p-5 space-y-6 pb-6">
          {/* Status Card */}
          <div className={`p-5 rounded-xl border-2 ${getStatusBgColor(stationData.status)} shadow-sm`}>
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-gray-600 mb-1">Status Saat Ini</p>
                <p className={`text-2xl font-bold ${getStatusColor(stationData.status)}`}>
                  {getStatusText(stationData.status)}
                </p>
              </div>
              <div className="text-right">
                <p className="text-4xl font-bold text-gray-900">
                  {stationData.value.toFixed(1)}
                </p>
                <p className="text-sm text-gray-500">{stationData.unit}</p>
              </div>
            </div>
          </div>
          
          {/* Divider */}
          <div className="border-t border-gray-200"></div>
          
          {/* Chart Section */}
          <div>
            <h4 className="text-base font-medium text-gray-900 mb-2">Grafik Level Air</h4>
            <p className="text-sm text-gray-500 mb-4">Data 10 menit terakhir</p>
            <div className="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
              <Chart 
                data={chartHistory}
                width={380}
                height={180}
                showTooltip={true}
                className="h-44"
                canvasId="station-detail-chart"
                status={stationData.status}
                color={getChartColor(stationData.status)}
              />
            </div>
          </div>
          
          {/* Divider */}
          <div className="border-t border-gray-200"></div>
          
          {/* Statistics Grid */}
          <div>
            <h4 className="text-base font-medium text-gray-900 mb-4">Statistik</h4>
            <div className="grid grid-cols-2 gap-4">
              <div className="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <p className="text-sm text-gray-500 mb-1">Level Tertinggi</p>
                <p className="text-2xl font-bold text-gray-900">
                  {Math.max(...chartHistory).toFixed(1)}m
                </p>
              </div>
              <div className="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <p className="text-sm text-gray-500 mb-1">Level Terendah</p>
                <p className="text-2xl font-bold text-gray-900">
                  {Math.min(...chartHistory).toFixed(1)}m
                </p>
              </div>
            </div>
          </div>
          
          {/* Divider */}
          <div className="border-t border-gray-200"></div>
          
          {/* Additional Info */}
          <div>
            <h4 className="text-base font-medium text-gray-900 mb-4">Informasi Stasiun</h4>
            <div className="bg-white rounded-xl p-4 border border-gray-200 shadow-sm space-y-3">
              <div className="flex justify-between items-center py-2">
                <span className="text-gray-600">ID Stasiun</span>
                <span className="text-gray-900 font-medium">#{stationData.id}</span>
              </div>
              <div className="flex justify-between items-center py-2">
                <span className="text-gray-600">Lokasi</span>
                <span className="text-gray-900 font-medium text-right max-w-48">{stationData.location}</span>
              </div>
              <div className="flex justify-between items-center py-2">
                <span className="text-gray-600">Update Terakhir</span>
                <span className="text-gray-900 font-medium">{new Date().toLocaleTimeString('id-ID')}</span>
              </div>
            </div>
          </div>
          
          {/* Divider */}
          <div className="border-t border-gray-200"></div>
          
          {/* Hint for Detail Panel */}
          <div className="bg-blue-50 rounded-xl p-4 border border-blue-200">
            <div className="flex items-center">
              <svg className="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <p className="text-sm text-blue-700">Klik panah di sebelah kanan untuk melihat detail informasi sensor, kualitas air, dan cuaca</p>
            </div>
          </div>
        </div>
      </SidebarTemplate>
      
      {/* Detail Panel - Ditempatkan di sebelah kanan sidebar */}
      <DetailPanel 
        stationData={stationData}
        sensorData={sensorData}
        waterQualityData={waterQualityData}
        weatherData={weatherData}
        floodHistory={floodHistory}
        isSidebarOpen={!!selectedStation} // Kirim status sidebar
      />
    </>
  );
};

export default StationDetail;
export { SidebarTemplate };