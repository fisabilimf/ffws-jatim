// stationdetail.jsx
import React, { useState, useEffect } from 'react';
import Chart from './Chart';
import DetailPanel from './DetailPanel';

const SidebarTemplate = ({ 
  isOpen, onClose, title, subtitle, width = "w-[380px]", position = "fixed", 
  zIndex = "z-[60]", topPosition = "top-20", children, headerContent, statusDot,
  showDetailToggle = false, isDetailOpen = false, onDetailToggle = () => {}, status = 'safe'
}) => {
  const [isVisible, setIsVisible] = useState(false);
  
  useEffect(() => {
    if (isOpen) setTimeout(() => setIsVisible(true), 10);
    else setIsVisible(false);
  }, [isOpen]);
  
  const handleClose = () => {
    setIsVisible(false);
    setTimeout(() => onClose(), 300);
  };
  
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
      case 'safe': return 'bg-green-600';
      case 'warning': return 'bg-yellow-600';
      case 'alert': return 'bg-red-600';
      default: return 'bg-gray-600';
    }
  };
  
  if (!isOpen) return null;
  
  return (
    <div className={`${position} ${topPosition} left-0 h-[calc(100vh-5rem)] ${width} bg-gradient-to-b from-white to-gray-50 shadow-2xl ${zIndex} transform transition-transform duration-300 ease-in-out flex flex-col ${
      isVisible ? 'translate-x-0' : '-translate-x-full'
    } border-r border-gray-200`} style={{ willChange: 'transform' }}>
      <div className={`bg-gradient-to-r ${getStatusBgColor(status)} text-white p-4 flex-shrink-0`}>
        <div className="flex items-center justify-between">
          <div className="flex items-center space-x-3">
            <button onClick={handleClose} className="p-2 hover:bg-black hover:bg-opacity-10 rounded-full transition-colors">
              <svg className="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
              </svg>
            </button>
            <div className="flex-1">
              {headerContent || (
                <>
                  <h3 className="text-lg font-semibold text-white">{title}</h3>
                  {subtitle && <p className="text-white text-opacity-80 text-sm">{subtitle}</p>}
                </>
              )}
            </div>
            {statusDot && <div className={`w-3 h-3 rounded-full ${statusDot}`}></div>}
          </div>
          
          {showDetailToggle && (
            <button onClick={onDetailToggle} className="px-2 py-1 bg-opacity-10 hover:bg-opacity-10 rounded-lg transition-colors text-xs font-medium text-white flex items-center cursor-pointer hover:underline" aria-label={isDetailOpen ? "Tutup panel detail" : "Buka panel detail"}>
              <span className="cursor-pointer">Informasi Detail</span>
              <svg className={`w-3 h-3 ml-1.5 transition-transform duration-300 ${isDetailOpen ? 'rotate-180' : ''} cursor-pointer`} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M9 5l7 7-7 7" />
              </svg>
            </button>
          )}
        </div>
      </div>
      
      <div className="flex-1 overflow-y-auto overflow-x-hidden custom-scrollbar">
        {children}
      </div>
    </div>
  );
};

const StationDetail = ({ selectedStation, onClose, tickerData }) => {
  const [stationData, setStationData] = useState(null);
  const [chartHistory, setChartHistory] = useState([]);
  const [isDetailOpen, setIsDetailOpen] = useState(false);
  
  useEffect(() => {
    if (selectedStation && tickerData) {
      const foundStation = tickerData.find(station => station.id === selectedStation.id);
      if (foundStation) {
        setStationData(foundStation);
        setChartHistory(foundStation.history);
      }
    }
  }, [selectedStation, tickerData]);
  
  useEffect(() => {
    if (!selectedStation) setIsDetailOpen(false);
  }, [selectedStation]);
  
  useEffect(() => {
    if (!stationData || !tickerData) return;
    const currentStationData = tickerData.find(station => station.id === stationData.id);
    if (currentStationData) {
      setStationData(currentStationData);
      setChartHistory(currentStationData.history);
    }
  }, [tickerData, stationData]);

  useEffect(() => {
    const handleAutoSwitchActivated = () => { if (selectedStation) onClose(); };
    document.addEventListener('autoSwitchActivated', handleAutoSwitchActivated);
    return () => { document.removeEventListener('autoSwitchActivated', handleAutoSwitchActivated); };
  }, [selectedStation, onClose]);

  const handleDetailToggle = () => setIsDetailOpen(!isDetailOpen);
  
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
      case 'safe': return '#10B981';
      case 'warning': return '#F59E0B';
      case 'alert': return '#EF4444';
      default: return '#6B7280';
    }
  };
  
  const sensorData = {
    sensorType: "Ultrasonic Sensor",
    sensorDepth: "5 meter",
    installationDate: "15 Januari 2023",
    lastMaintenance: "10 Oktober 2023",
    batteryLevel: "87%",
    signalStrength: "Strong",
    accuracy: "±2cm"
  };
  
  const waterQualityData = {
    pH: 7.2, turbidity: 12.5, temperature: 26.8,
    dissolvedOxygen: 6.8, conductivity: 450, tds: 225
  };
  
  const weatherData = {
    current: { temperature: 28, humidity: 78, rainfall: 2.4, windSpeed: 12, windDirection: "Barat Daya" },
    forecast: [
      { time: "14:00", rain: 80, temp: 29 },
      { time: "17:00", rain: 60, temp: 27 },
      { time: "20:00", rain: 40, temp: 25 },
      { time: "23:00", rain: 20, temp: 24 }
    ]
  };
  
  const floodHistory = [
    { date: "12 Nov 2023", level: 4.2, duration: "3 jam" },
    { date: "5 Okt 2023", level: 3.8, duration: "2 jam" },
    { date: "18 Sep 2023", level: 4.5, duration: "4 jam" }
  ];
  
  if (!selectedStation || !stationData) return null;
  
  return (
    <>
      <SidebarTemplate
        isOpen={!!selectedStation}
        onClose={onClose}
        title={stationData.name}
        subtitle={stationData.location}
        topPosition="top-20"
        showDetailToggle={true}
        isDetailOpen={isDetailOpen}
        onDetailToggle={handleDetailToggle}
        status={stationData.status}
      >
        <div className="p-5 space-y-6 pb-6">
          <div className={`p-5 rounded-xl border-2 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 ${getStatusBgColor(stationData.status)}`}>
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-gray-600 mb-1">Status Saat Ini</p>
                <p className={`text-2xl font-bold ${getStatusColor(stationData.status)}`}>{getStatusText(stationData.status)}</p>
              </div>
              <div className="text-right">
                <p className="text-4xl font-bold text-gray-900">{stationData.value.toFixed(1)}</p>
                <p className="text-sm text-gray-500">{stationData.unit}</p>
              </div>
            </div>
          </div>
          
          <div className="border-t border-gray-200"></div>
          
          <div>
            <div className="flex items-center mb-2">
              <div className="bg-blue-100 p-2 rounded-lg mr-3">
                <svg className="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
              </div>
              <h4 className="text-base font-medium text-gray-900">Grafik Level Air</h4>
            </div>
            <p className="text-sm text-gray-500 mb-4">Data 10 menit terakhir</p>
            <div className="bg-white rounded-xl p-4 border border-gray-200 shadow-lg hover:shadow-xl transition-shadow">
              <Chart data={chartHistory} width={380} height={180} showTooltip={true} className="h-44" canvasId="station-detail-chart" status={stationData.status} color={getChartColor(stationData.status)} />
            </div>
          </div>
          
          <div className="border-t border-gray-200"></div>
          
          <div>
            <div className="flex items-center mb-4">
              <div className="bg-blue-100 p-2 rounded-lg mr-3">
                <svg className="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
              </div>
              <h4 className="text-base font-medium text-gray-900">Statistik</h4>
            </div>
            <div className="grid grid-cols-2 gap-4">
              <div className="bg-white rounded-xl p-4 border border-gray-200 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <p className="text-sm text-gray-500 mb-1">Level Tertinggi</p>
                <p className="text-2xl font-bold text-gray-900">{Math.max(...chartHistory).toFixed(1)}m</p>
              </div>
              <div className="bg-white rounded-xl p-4 border border-gray-200 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <p className="text-sm text-gray-500 mb-1">Level Terendah</p>
                <p className="text-2xl font-bold text-gray-900">{Math.min(...chartHistory).toFixed(1)}m</p>
              </div>
            </div>
          </div>
          
          <div className="border-t border-gray-200"></div>
          
          <div>
            <div className="flex items-center mb-4">
              <div className="bg-blue-100 p-2 rounded-lg mr-3">
                <svg className="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <h4 className="text-base font-medium text-gray-900">Informasi Stasiun</h4>
            </div>
            <div className="bg-white rounded-xl p-4 border border-gray-200 shadow-lg hover:shadow-xl transition-shadow space-y-3">
              <div className="flex justify-between items-center py-2 border-b border-gray-100 group">
                <div className="flex items-center">
                  <div className="bg-gray-100 p-2 rounded-lg mr-3 group-hover:bg-blue-100 transition-colors">
                    <svg className="w-4 h-4 text-gray-600 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                  </div>
                  <span className="text-gray-600 font-medium">ID Stasiun</span>
                </div>
                <span className="text-gray-900 font-semibold bg-blue-50 px-3 py-1 rounded-lg">#{stationData.id}</span>
              </div>
              <div className="flex justify-between items-center py-2 border-b border-gray-100 group">
                <div className="flex items-center">
                  <div className="bg-gray-100 p-2 rounded-lg mr-3 group-hover:bg-blue-100 transition-colors">
                    <svg className="w-4 h-4 text-gray-600 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                  </div>
                  <span className="text-gray-600 font-medium">Lokasi</span>
                </div>
                <span className="text-gray-900 font-semibold bg-blue-50 px-3 py-1 rounded-lg text-right max-w-48">{stationData.location}</span>
              </div>
              <div className="flex justify-between items-center py-2 group">
                <div className="flex items-center">
                  <div className="bg-gray-100 p-2 rounded-lg mr-3 group-hover:bg-blue-100 transition-colors">
                    <svg className="w-4 h-4 text-gray-600 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                  <span className="text-gray-600 font-medium">Update Terakhir</span>
                </div>
                <span className="text-gray-900 font-semibold bg-blue-50 px-3 py-1 rounded-lg">{new Date().toLocaleTimeString('id-ID')}</span>
              </div>
            </div>
          </div>
          
          <div className={`rounded-xl p-4 border transition-all duration-300 shadow-md ${
            stationData.status === 'alert' ? 'bg-gradient-to-r from-yellow-50 to-amber-50 border-yellow-200' : 
            stationData.status === 'warning' ? 'bg-gradient-to-r from-blue-50 to-indigo-50 border-blue-200' :
            'bg-gradient-to-r from-green-50 to-emerald-50 border-green-200'
          }`}>
            <div className="flex items-start">
              <div className={`p-2 rounded-lg mr-3 ${
                stationData.status === 'alert' ? 'bg-yellow-100' : 
                stationData.status === 'warning' ? 'bg-blue-100' :
                'bg-green-100'
              }`}>
                <svg className={`w-5 h-5 transition-colors duration-300 ${
                  stationData.status === 'alert' ? 'text-yellow-600' : 
                  stationData.status === 'warning' ? 'text-blue-600' :
                  'text-green-600'
                }`} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <p className={`text-sm font-medium transition-colors duration-300 ${
                stationData.status === 'alert' ? 'text-yellow-700' : 
                stationData.status === 'warning' ? 'text-blue-700' :
                'text-green-700'
              }`}>
                Klik tombol "Informasi Detail" di pojok kanan atas untuk melihat detail informasi sensor, kualitas air, dan cuaca
              </p>
            </div>
          </div>
        </div>
      </SidebarTemplate>
      
      <DetailPanel 
        stationData={stationData}
        sensorData={sensorData}
        waterQualityData={waterQualityData}
        weatherData={weatherData}
        floodHistory={floodHistory}
        isSidebarOpen={!!selectedStation}
        isOpen={isDetailOpen}
        onClose={() => setIsDetailOpen(false)}
      />
    </>
  );
};

export default StationDetail;
export { SidebarTemplate };