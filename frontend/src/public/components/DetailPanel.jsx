import React, { useState, useEffect, useRef } from 'react';

const DetailPanel = ({ stationData, isSidebarOpen, isOpen, onClose }) => {
  const [isVisible, setIsVisible] = useState(false);
  const [isPanelOpen, setIsPanelOpen] = useState(false);
  const [activeTab, setActiveTab] = useState('sensor');
  const [dynamicData, setDynamicData] = useState({ sensorInfo: {}, waterQuality: {}, weatherInfo: {}, floodHistoryData: [] });
  const [mousePosition, setMousePosition] = useState({ x: 0, y: 0 });
  const [isMouseOverWater, setIsMouseOverWater] = useState(false);
  const [fishRotation, setFishRotation] = useState(0);
  const waterRef = useRef(null);
  const prevMousePosition = useRef({ x: 0, y: 0 });
  
  useEffect(() => {
    if (isOpen) {
      setIsPanelOpen(true);
      setTimeout(() => setIsVisible(true), 10);
    } else {
      setIsVisible(false);
      setTimeout(() => setIsPanelOpen(false), 300);
    }
  }, [isOpen]);
  
  useEffect(() => {
    if (!stationData) return;
    
    const generateData = () => {
      const sensorTypes = ["Ultrasonic Sensor", "Pressure Sensor", "Radar Sensor", "Float Sensor"];
      const depths = ["3 meter", "5 meter", "8 meter", "10 meter"];
      const dates = ["15 Jan 2023", "20 Feb 2023", "10 Mar 2023", "05 Apr 2023"];
      const idx = stationData.id % 4;
      let battery = stationData.status === 'alert' ? 20 + Math.random() * 30 : stationData.status === 'warning' ? 40 + Math.random() * 40 : 80 + Math.random() * 20;
      
      const sensorInfo = {
        sensorType: sensorTypes[idx],
        sensorDepth: depths[idx],
        installationDate: dates[idx],
        lastMaintenance: `${5 + idx} Mei 2023`,
        batteryLevel: `${Math.floor(battery)}%`,
        signalStrength: battery > 70 ? "Strong" : battery > 40 ? "Medium" : "Weak",
        accuracy: `±${1 + idx}cm`
      };
      
      const waterQuality = {
        pH: 6.5 + idx * 0.5 + (Math.random() - 0.5),
        turbidity: 5 + idx * 2 + Math.random() * 5,
        temperature: 25 + idx + Math.random() * 2,
        dissolvedOxygen: 6 + Math.random() * 2,
        conductivity: 300 + idx * 50,
        tds: 150 + idx * 25
      };
      
      const weatherInfo = {
        current: {
          temperature: 25 + Math.floor(Math.random() * 8),
          humidity: 60 + Math.floor(Math.random() * 30),
          rainfall: Math.random() * 5,
          windSpeed: 5 + Math.floor(Math.random() * 15),
          windDirection: ["Utara", "Timur", "Selatan", "Barat"][idx]
        },
        forecast: Array(4).fill(0).map((_, i) => ({
          time: `${((new Date().getHours() + (i + 1) * 3) % 24).toString().padStart(2, '0')}:00`,
          rain: Math.min(100, Math.random() * 100),
          temp: 25 + Math.floor(Math.random() * 5)
        }))
      };
      
      const floodHistoryData = Array(3).fill(0).map((_, i) => ({
        date: `${1 + i * 10} Jan ${2023 - i}`,
        level: 2 + Math.random() * 3,
        duration: `${3 + i} jam`
      }));
      
      setDynamicData({ sensorInfo, waterQuality, weatherInfo, floodHistoryData });
    };
    
    generateData();
  }, [stationData]);
  
  const handleWaterMouseMove = (e) => {
    if (!waterRef.current) return;
    const rect = waterRef.current.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    setMousePosition({ x, y });
    const deltaX = x - prevMousePosition.current.x;
    const deltaY = y - prevMousePosition.current.y;
    if (Math.abs(deltaX) > 2 || Math.abs(deltaY) > 2) {
      setFishRotation(Math.atan2(deltaY, deltaX) * 180 / Math.PI);
    }
    prevMousePosition.current = { x, y };
  };
  
  if (!isSidebarOpen || !isPanelOpen) return null;
  
  const waterLevelPercentage = Math.min(95, Math.max(5, (stationData.value / parseInt(dynamicData.sensorInfo.sensorDepth || 10)) * 100));
  
  const tabs = [
    { id: 'sensor', label: 'Sensor', icon: 'M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z' },
    { id: 'quality', label: 'Kualitas Air', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
    { id: 'weather', label: 'Cuaca', icon: 'M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4 4 0 003 15z' },
    { id: 'history', label: 'Riwayat', icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' }
  ];
  
  const InfoRow = ({ icon, label, value, color = "gray" }) => (
    <div className="flex justify-between items-center py-3 border-b border-gray-100 group">
      <div className="flex items-center">
        <div className={`bg-${color}-100 p-2 rounded-lg mr-3 group-hover:bg-blue-100 transition-colors`}>
          <svg className={`w-5 h-5 text-${color}-600 group-hover:text-blue-600 transition-colors`} fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d={icon} />
          </svg>
        </div>
        <span className="text-gray-600 font-medium">{label}</span>
      </div>
      <span className="text-gray-900 font-semibold bg-blue-50 px-3 py-1 rounded-lg">{value}</span>
    </div>
  );
  
  const QualityCard = ({ title, value, unit, max, color }) => (
    <div className="bg-white rounded-xl p-4 border border-gray-200 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
      <p className="text-sm text-gray-600 font-medium mb-2">{title}</p>
      <p className="text-2xl font-bold text-gray-900">{typeof value === 'number' ? value.toFixed(1) : value} {unit}</p>
      <div className="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
        <div className={`h-full ${color} rounded-full transition-all duration-500`} style={{ width: `${(value / max) * 100}%` }}></div>
      </div>
    </div>
  );
  
  return (
    <div 
      className="fixed top-20 h-[calc(100vh-5rem)] w-[590px] bg-gradient-to-b from-white to-gray-50 shadow-2xl flex flex-col border-r border-gray-200 z-40"
      style={{ 
        left: isSidebarOpen ? '0' : '-590px',
        transform: isVisible ? 'translateX(380px)' : 'translateX(0)',
        opacity: isVisible ? 1 : 0,
        transition: 'transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94), opacity 0.3s ease-in-out'
      }}
    >
      <div className="bg-gradient-to-r from-blue-600 to-blue-500 text-white p-4 flex-shrink-0">
        <div className="flex items-center justify-between mb-4">
          <h3 className="text-lg font-semibold">Detail Informasi Stasiun</h3>
          <button onClick={onClose} className="p-2 hover:bg-blue-700 rounded-full transition-colors">
            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <div className="flex border-b border-blue-400 relative">
          {tabs.map((tab) => (
            <button
              key={tab.id}
              className={`flex-1 py-3 font-medium text-sm transition-all ${activeTab === tab.id ? 'text-white' : 'text-blue-200 hover:text-white'}`}
              onClick={() => setActiveTab(tab.id)}
            >
              {tab.label}
            </button>
          ))}
          <div className="absolute bottom-0 h-0.5 bg-white transition-all duration-300" style={{ width: '25%', left: `${tabs.findIndex(t => t.id === activeTab) * 25}%` }}></div>
        </div>
      </div>
      
      <div className="flex-1 overflow-y-auto p-5">
        {activeTab === 'sensor' && (
          <div className="space-y-5">
            <div className="bg-white rounded-xl p-5 border border-gray-200 shadow-lg">
              <InfoRow icon="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" label="Tipe Sensor" value={dynamicData.sensorInfo.sensorType} />
              
              <div className="py-3 border-b border-gray-100">
                <div className="relative w-full h-52 bg-gradient-to-b from-gray-100 to-gray-200 rounded-lg overflow-hidden">
                  <div 
                    ref={waterRef}
                    className="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-blue-600 to-blue-400"
                    style={{ height: `${waterLevelPercentage}%` }}
                    onMouseMove={handleWaterMouseMove}
                    onMouseEnter={() => setIsMouseOverWater(true)}
                    onMouseLeave={() => setIsMouseOverWater(false)}
                  >
                    {isMouseOverWater && (
                      <div className="absolute w-4 h-4 bg-orange-500 rounded-full" style={{ left: mousePosition.x, top: mousePosition.y, transform: `translate(-50%, -50%) rotate(${fishRotation}deg)` }}></div>
                    )}
                  </div>
                  <div className="absolute right-4" style={{ bottom: `${waterLevelPercentage}%` }}>
                    <div className="w-6 h-6 bg-red-500 rounded-full border-2 border-white shadow-md animate-pulse"></div>
                  </div>
                </div>
                <div className="text-center mt-3">
                  <span className="px-3 py-1 bg-blue-500 text-white rounded-lg font-bold">Level: {stationData.value.toFixed(1)}m / {dynamicData.sensorInfo.sensorDepth}</span>
                </div>
              </div>
              
              <InfoRow icon="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" label="Tanggal Pemasangan" value={dynamicData.sensorInfo.installationDate} />
              <InfoRow icon="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 00-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" label="Level Baterai" value={dynamicData.sensorInfo.batteryLevel} />
              <InfoRow icon="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" label="Kekuatan Sinyal" value={dynamicData.sensorInfo.signalStrength} />
            </div>
          </div>
        )}
        
        {activeTab === 'quality' && (
          <div className="grid grid-cols-2 gap-4">
            <QualityCard title="pH" value={dynamicData.waterQuality.pH} unit="" max={14} color="bg-green-500" />
            <QualityCard title="Kekeruhan" value={dynamicData.waterQuality.turbidity} unit="NTU" max={50} color="bg-yellow-500" />
            <QualityCard title="Suhu" value={dynamicData.waterQuality.temperature} unit="°C" max={40} color="bg-blue-500" />
            <QualityCard title="Oksigen" value={dynamicData.waterQuality.dissolvedOxygen} unit="mg/L" max={10} color="bg-purple-500" />
            <QualityCard title="Konduktivitas" value={dynamicData.waterQuality.conductivity} unit="µS/cm" max={1000} color="bg-indigo-500" />
            <QualityCard title="TDS" value={dynamicData.waterQuality.tds} unit="ppm" max={500} color="bg-cyan-500" />
          </div>
        )}
        
        {activeTab === 'weather' && (
          <div className="space-y-5">
            <div className="bg-white rounded-xl p-5 border border-gray-200 shadow-lg">
              <h5 className="font-medium text-gray-900 mb-4">Kondisi Saat Ini</h5>
              <div className="grid grid-cols-2 gap-4">
                <div className="bg-blue-50 p-3 rounded-lg">
                  <p className="text-sm text-gray-600">Suhu</p>
                  <p className="text-lg font-semibold">{dynamicData.weatherInfo.current.temperature}°C</p>
                </div>
                <div className="bg-blue-50 p-3 rounded-lg">
                  <p className="text-sm text-gray-600">Kelembaban</p>
                  <p className="text-lg font-semibold">{dynamicData.weatherInfo.current.humidity}%</p>
                </div>
                <div className="bg-blue-50 p-3 rounded-lg">
                  <p className="text-sm text-gray-600">Curah Hujan</p>
                  <p className="text-lg font-semibold">{dynamicData.weatherInfo.current.rainfall.toFixed(1)} mm</p>
                </div>
                <div className="bg-blue-50 p-3 rounded-lg">
                  <p className="text-sm text-gray-600">Kecepatan Angin</p>
                  <p className="text-lg font-semibold">{dynamicData.weatherInfo.current.windSpeed} km/j</p>
                </div>
              </div>
            </div>
            <div className="bg-white rounded-xl p-5 border border-gray-200 shadow-lg">
              <h5 className="font-medium text-gray-900 mb-4">Prakiraan</h5>
              {dynamicData.weatherInfo.forecast.map((item, idx) => (
                <div key={idx} className="flex justify-between py-2 border-b border-gray-100">
                  <span>{item.time}</span>
                  <span className="font-semibold">{item.temp}°C - {item.rain.toFixed(0)}% hujan</span>
                </div>
              ))}
            </div>
          </div>
        )}
        
        {activeTab === 'history' && (
          <div className="space-y-5">
            <div className="bg-white rounded-xl border border-gray-200 shadow-lg">
              {dynamicData.floodHistoryData.map((item, idx) => (
                <div key={idx} className="p-4 border-b border-gray-100">
                  <div className="flex justify-between">
                    <div>
                      <p className="font-semibold">{item.date}</p>
                      <p className="text-sm text-gray-500">Durasi: {item.duration}</p>
                    </div>
                    <p className="text-lg font-bold text-red-600">{item.level.toFixed(1)}m</p>
                  </div>
                </div>
              ))}
            </div>
            <div className="bg-gradient-to-r from-purple-50 to-violet-50 rounded-xl p-5 border border-purple-200">
              <p className="text-sm text-purple-700">Berdasarkan data historis, stasiun ini mengalami banjir rata-rata {dynamicData.floodHistoryData.length} kali dengan level tertinggi {Math.max(...dynamicData.floodHistoryData.map(h => h.level)).toFixed(1)}m.</p>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default DetailPanel;