// DetailPanel.jsx
import React, { useState, useEffect } from 'react';

const DetailPanel = ({ 
  stationData, 
  sensorData, 
  waterQualityData, 
  weatherData, 
  floodHistory, 
  isSidebarOpen,
  isOpen,
  onClose
}) => {
  const [isVisible, setIsVisible] = useState(false);
  const [isPanelOpen, setIsPanelOpen] = useState(false);
  const [activeTab, setActiveTab] = useState('sensor');
  const [dynamicData, setDynamicData] = useState({
    sensorInfo: {},
    waterQuality: {},
    weatherInfo: {},
    floodHistoryData: []
  });
  
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
    if (stationData) {
      const generateSensorData = () => {
        const sensorTypes = ["Ultrasonic Sensor", "Pressure Sensor", "Radar Sensor", "Float Sensor"];
        const depths = ["3 meter", "5 meter", "8 meter", "10 meter"];
        const installationDates = ["15 Jan 2023", "20 Feb 2023", "10 Mar 2023", "05 Apr 2023"];
        
        const sensorIndex = stationData.id % sensorTypes.length;
        const depthIndex = stationData.id % depths.length;
        const dateIndex = stationData.id % installationDates.length;
        
        let batteryLevel;
        if (stationData.status === 'alert') {
          batteryLevel = Math.floor(Math.random() * 30) + 20;
        } else if (stationData.status === 'warning') {
          batteryLevel = Math.floor(Math.random() * 40) + 40;
        } else {
          batteryLevel = Math.floor(Math.random() * 20) + 80;
        }
        
        return {
          sensorType: sensorTypes[sensorIndex],
          sensorDepth: depths[depthIndex],
          installationDate: installationDates[dateIndex],
          lastMaintenance: getRandomDate(installationDates[dateIndex]),
          batteryLevel: `${batteryLevel}%`,
          signalStrength: batteryLevel > 70 ? "Strong" : batteryLevel > 40 ? "Medium" : "Weak",
          accuracy: "±" + (Math.floor(Math.random() * 3) + 1) + "cm"
        };
      };
      
      const generateWaterQualityData = () => {
        const basePh = 6.5 + (stationData.id % 4) * 0.5;
        const baseTurbidity = 5 + (stationData.id % 10) * 2;
        const baseTemp = 25 + (stationData.id % 5);
        
        let ph = basePh;
        let turbidity = baseTurbidity;
        let temperature = baseTemp;
        
        if (stationData.status === 'alert') {
          ph += Math.random() * 0.5 - 0.25;
          turbidity += Math.random() * 10;
          temperature += Math.random() * 2;
        } else if (stationData.status === 'warning') {
          ph += Math.random() * 0.3 - 0.15;
          turbidity += Math.random() * 5;
          temperature += Math.random() * 1;
        }
        
        return {
          pH: parseFloat(ph.toFixed(1)),
          turbidity: parseFloat(turbidity.toFixed(1)),
          temperature: parseFloat(temperature.toFixed(1)),
          dissolvedOxygen: parseFloat((6 + Math.random() * 2).toFixed(1)),
          conductivity: 300 + (stationData.id % 200),
          tds: 150 + (stationData.id % 100)
        };
      };
      
      const generateWeatherData = () => {
        const baseTemp = 25 + Math.floor(Math.random() * 8);
        const baseHumidity = 60 + Math.floor(Math.random() * 30);
        const baseRainfall = Math.random() * 5;
        
        let temperature = baseTemp;
        let humidity = baseHumidity;
        let rainfall = baseRainfall;
        
        if (stationData.status === 'alert') {
          rainfall += Math.random() * 10;
          humidity += Math.random() * 10;
        }
        
        const forecast = [];
        const currentHour = new Date().getHours();
        
        for (let i = 0; i < 4; i++) {
          const hour = (currentHour + (i + 1) * 3) % 24;
          const forecastHour = hour < 10 ? `0${hour}:00` : `${hour}:00`;
          
          let rainProb = Math.min(100, rainfall * 20);
          if (stationData.status === 'alert') rainProb = Math.min(100, rainProb + 20);
          if (stationData.status === 'warning') rainProb = Math.min(100, rainProb + 10);
          
          let forecastTemp = temperature;
          if (hour >= 6 && hour <= 15) {
            forecastTemp += 2;
          } else {
            forecastTemp -= 2;
          }
          
          forecast.push({
            time: forecastHour,
            rain: Math.floor(rainProb),
            temp: Math.floor(forecastTemp)
          });
        }
        
        return {
          current: {
            temperature: Math.floor(temperature),
            humidity: Math.floor(humidity),
            rainfall: parseFloat(rainfall.toFixed(1)),
            windSpeed: 5 + Math.floor(Math.random() * 15),
            windDirection: ["Utara", "Timur Laut", "Timur", "Tenggara", "Selatan", "Barat Daya", "Barat", "Barat Laut"][stationData.id % 8]
          },
          forecast
        };
      };
      
      const generateFloodHistory = () => {
        const historyCount = 2 + Math.floor(Math.random() * 3);
        const history = [];
        
        const months = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
        const currentYear = new Date().getFullYear();
        
        for (let i = 0; i < historyCount; i++) {
          const monthIndex = Math.floor(Math.random() * 12);
          const day = 1 + Math.floor(Math.random() * 28);
          const year = currentYear - Math.floor(Math.random() * 3);
          
          let level;
          if (stationData.status === 'alert') {
            level = 3.5 + Math.random() * 2;
          } else if (stationData.status === 'warning') {
            level = 2.5 + Math.random() * 2;
          } else {
            level = 1.5 + Math.random() * 2;
          }
          
          const duration = Math.floor(level) + Math.floor(Math.random() * 3);
          
          history.push({
            date: `${day} ${months[monthIndex]} ${year}`,
            level: parseFloat(level.toFixed(1)),
            duration: `${duration} jam`
          });
        }
        
        return history.sort((a, b) => {
          const dateA = new Date(a.date.replace(/(\d+) (\w+) (\d+)/, '$3 $2 $1'));
          const dateB = new Date(b.date.replace(/(\d+) (\w+) (\d+)/, '$3 $2 $1'));
          return dateB - dateA;
        });
      };
      
      setDynamicData({
        sensorInfo: generateSensorData(),
        waterQuality: generateWaterQualityData(),
        weatherInfo: generateWeatherData(),
        floodHistoryData: generateFloodHistory()
      });
    }
  }, [stationData]);
  
  const getRandomDate = (installDate) => {
    const install = new Date(installDate);
    const monthsSinceInstall = Math.floor(Math.random() * 6) + 1;
    const maintenanceDate = new Date(install);
    maintenanceDate.setMonth(maintenanceDate.getMonth() + monthsSinceInstall);
    
    if (maintenanceDate.getMonth() !== (install.getMonth() + monthsSinceInstall) % 12) {
      maintenanceDate.setDate(0);
    }
    
    const day = maintenanceDate.getDate();
    const month = maintenanceDate.toLocaleString('id-ID', { month: 'short' });
    const year = maintenanceDate.getFullYear();
    
    return `${day} ${month} ${year}`;
  };
  
  const calculateWaterLevelPercentage = () => {
    if (!stationData) return 30;
    
    const maxDepth = parseInt(dynamicData.sensorInfo.sensorDepth);
    const currentLevel = stationData.value;
    
    const percentage = Math.min(95, (currentLevel / maxDepth) * 100);
    return Math.max(5, percentage);
  };
  
  const handleTabChange = (tabId) => {
    if (tabId !== activeTab) {
      setActiveTab(tabId);
    }
  };
  
  if (!isSidebarOpen || !isPanelOpen) return null;
  
  const waterLevelPercentage = calculateWaterLevelPercentage();
  
  return (
    <div 
      className="fixed top-20 h-[calc(100vh-5rem)] w-[590px] bg-white shadow-2xl flex flex-col"
      style={{ 
        willChange: 'transform, opacity',
        left: isSidebarOpen ? '400px' : '11px',
        transform: isVisible ? 'translateX(0)' : 'translateX(-600px)',
        opacity: isVisible ? 1 : 0,
        transition: 'transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94), opacity 0.3s ease-in-out',
        transformOrigin: 'left center'
      }}
    >
      <div className="bg-white border-b border-gray-200 p-4 flex-shrink-0">
        <div className="flex items-center justify-between">
          <h3 className="text-lg font-semibold text-gray-900">Detail Informasi Stasiun</h3>
          <button onClick={onClose} className="p-2 hover:bg-gray-100 rounded-full transition-colors">
            <svg className="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <div className="flex mt-4 border-b border-gray-200 relative">
          {[
            { id: 'sensor', label: 'Sensor' },
            { id: 'quality', label: 'Kualitas Air' },
            { id: 'weather', label: 'Cuaca' },
            { id: 'history', label: 'Riwayat' }
          ].map((tab) => (
            <button
              key={tab.id}
              className={`px-11 py-3 font-medium text-sm transition-colors duration-200 ${
                activeTab === tab.id ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'
              }`}
              onClick={() => handleTabChange(tab.id)}
            >
              {tab.label}
            </button>
          ))}
          <div 
            className="absolute bottom-0 h-0.5 bg-blue-600 transition-all duration-300 ease-in-out"
            style={{ 
              width: '25%',
              left: `${['sensor', 'quality', 'weather', 'history'].indexOf(activeTab) * 25}%`
            }}
          ></div>
        </div>
      </div>
      
      <div className="flex-1 overflow-hidden">
        <div 
          className={`h-full overflow-y-auto p-5 custom-scrollbar ${
            activeTab === 'sensor' ? 'block' : 'hidden'
          }`}
        >
          <div className="space-y-5">
            <h4 className="text-base font-medium text-gray-900 mb-3">Informasi Sensor</h4>
            
            <div className="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
              <div className="flex justify-between items-center py-3 border-b border-gray-100">
                <span className="text-gray-600">Tipe Sensor</span>
                <span className="text-gray-900 font-medium">{dynamicData.sensorInfo.sensorType}</span>
              </div>
              
              <div className="flex justify-between items-center py-3 border-b border-gray-100">
                <span className="text-gray-600">Kedalaman Maksimum</span>
                <div className="flex items-center space-x-3">
                  <span className="text-gray-900 font-medium">{dynamicData.sensorInfo.sensorDepth}</span>
                  <div className="relative flex flex-col items-center">
                    <div className="relative w-24 h-32 border-2 border-gray-300 rounded-b-lg overflow-hidden shadow-inner">
                      <div className="absolute bottom-0 w-full bg-gradient-to-t from-blue-500 to-blue-400" style={{ height: `${waterLevelPercentage}%` }}></div>
                      <div className="absolute bottom-0 w-full" style={{ height: `${waterLevelPercentage}%` }}>
                        <div className="absolute top-0 left-0 w-full h-3 bg-blue-300 rounded-full opacity-50 animate-wave animate-wave-vertical"></div>
                        <div className="absolute top-1 left-0 w-full h-3 bg-blue-300 rounded-full opacity-30 animate-wave animate-wave-vertical delay-300"></div>
                        <div className="absolute top-2 left-0 w-full h-3 bg-blue-300 rounded-full opacity-20 animate-wave animate-wave-vertical delay-700"></div>
                      </div>
                      <div className="absolute right-1" style={{ bottom: `${waterLevelPercentage}%` }}>
                        <div className="w-4 h-4 bg-red-500 rounded-full border-2 border-white shadow-sm animate-pulse"></div>
                      </div>
                    </div>
                    <div className="flex justify-between w-full mt-1">
                      <span className="text-xs text-gray-500">0m</span>
                      <span className="text-xs font-medium text-blue-600">{stationData.value.toFixed(1)}m</span>
                      <span className="text-xs text-gray-500">{dynamicData.sensorInfo.sensorDepth}</span>
                    </div>
                  </div>
                </div>
              </div>
              
              <div className="flex justify-between items-center py-3 border-b border-gray-100">
                <span className="text-gray-600">Tanggal Pemasangan</span>
                <span className="text-gray-900 font-medium">{dynamicData.sensorInfo.installationDate}</span>
              </div>
              <div className="flex justify-between items-center py-3 border-b border-gray-100">
                <span className="text-gray-600">Perawatan Terakhir</span>
                <span className="text-gray-900 font-medium">{dynamicData.sensorInfo.lastMaintenance}</span>
              </div>
              
              <div className="flex justify-between items-center py-3 border-b border-gray-100">
                <span className="text-gray-600">Level Baterai</span>
                <div className="flex items-center space-x-3">
                  <span className="text-gray-900 font-medium">{dynamicData.sensorInfo.batteryLevel}</span>
                  <div className="relative flex flex-col items-center">
                    <div className="relative w-20 h-10 border-2 border-gray-300 rounded-md shadow-inner">
                      <div className="absolute top-1 right-0 w-2 h-8 bg-gray-300 rounded-r-md"></div>
                      <div 
                        className="absolute top-0 left-0 h-full rounded-md transition-all duration-500" 
                        style={{ 
                          width: `${parseInt(dynamicData.sensorInfo.batteryLevel)}%`,
                          background: parseInt(dynamicData.sensorInfo.batteryLevel) > 70 ? 
                            'linear-gradient(90deg, #10B981, #34D399)' : 
                            parseInt(dynamicData.sensorInfo.batteryLevel) > 40 ? 
                            'linear-gradient(90deg, #F59E0B, #FCD34D)' : 
                            'linear-gradient(90deg, #EF4444, #F87171)'
                        }}
                      >
                        <div className="absolute top-0 left-0 w-full h-full bg-gradient-to-r from-white to-transparent opacity-30 rounded-md"></div>
                      </div>
                      <div className="absolute inset-0 flex items-center justify-center">
                        <span className="text-xs font-bold text-white drop-shadow">{dynamicData.sensorInfo.batteryLevel}</span>
                      </div>
                    </div>
                    <span className="text-xs text-gray-500 mt-1">Baterai</span>
                  </div>
                </div>
              </div>
              
              <div className="flex justify-between items-center py-3 border-b border-gray-100">
                <span className="text-gray-600">Kekuatan Sinyal</span>
                <div className="flex items-center space-x-2">
                  <span className="text-gray-900 font-medium">{dynamicData.sensorInfo.signalStrength}</span>
                  <div className="flex space-x-1">
                    {[1, 2, 3, 4].map((bar) => (
                      <div 
                        key={bar}
                        className={`w-2 rounded-sm transition-all duration-300 ${
                          dynamicData.sensorInfo.signalStrength === 'Strong' ? 'bg-green-500' :
                          dynamicData.sensorInfo.signalStrength === 'Medium' ? 'bg-yellow-500' : 'bg-red-500'
                        }`}
                        style={{ 
                          height: `${bar * 5}px`,
                          opacity: dynamicData.sensorInfo.signalStrength === 'Strong' ? 1 :
                                  dynamicData.sensorInfo.signalStrength === 'Medium' ? bar <= 3 ? 1 : 0.3 :
                                  bar <= 2 ? 1 : 0.3
                        }}
                      ></div>
                    ))}
                  </div>
                </div>
              </div>
              <div className="flex justify-between items-center py-3">
                <span className="text-gray-600">Akurasi</span>
                <span className="text-gray-900 font-medium">{dynamicData.sensorInfo.accuracy}</span>
              </div>
            </div>
            
            <div className="bg-blue-50 rounded-xl p-4 border border-blue-200">
              <div className="flex items-start">
                <svg className="w-5 h-5 text-blue-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p className="text-sm text-blue-700">Sensor dikalibrasi setiap 3 bulan untuk memastikan akurasi data. Perawatan terakhir dilakukan pada {dynamicData.sensorInfo.lastMaintenance}.</p>
              </div>
            </div>
          </div>
        </div>
        
        <div 
          className={`h-full overflow-y-auto p-5 custom-scrollbar ${
            activeTab === 'quality' ? 'block' : 'hidden'
          }`}
        >
          <div className="space-y-5">
            <h4 className="text-base font-medium text-gray-900 mb-3">Kualitas Air</h4>
            
            <div className="grid grid-cols-2 gap-4">
              <div className="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <p className="text-sm text-gray-500 mb-1">pH</p>
                <p className="text-2xl font-bold text-gray-900">{dynamicData.waterQuality.pH}</p>
                <div className="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                  <div 
                    className="h-full rounded-full transition-all duration-500" 
                    style={{ 
                      width: `${(dynamicData.waterQuality.pH / 14) * 100}%`,
                      backgroundColor: dynamicData.waterQuality.pH >= 6.5 && dynamicData.waterQuality.pH <= 8.5 ? '#10B981' : '#EF4444'
                    }}
                  ></div>
                </div>
                <div className="mt-1 flex justify-between text-xs">
                  <span className="text-gray-500">0</span>
                  <span className="text-gray-500">14</span>
                </div>
              </div>
              
              <div className="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <p className="text-sm text-gray-500 mb-1">Kekeruhan (NTU)</p>
                <p className="text-2xl font-bold text-gray-900">{dynamicData.waterQuality.turbidity}</p>
                <div className="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                  <div 
                    className="h-full rounded-full transition-all duration-500" 
                    style={{ 
                      width: `${Math.min(dynamicData.waterQuality.turbidity / 50 * 100, 100)}%`,
                      backgroundColor: dynamicData.waterQuality.turbidity <= 5 ? '#10B981' : 
                                      dynamicData.waterQuality.turbidity <= 15 ? '#F59E0B' : '#EF4444'
                    }}
                  ></div>
                </div>
                <div className="mt-1 flex justify-between text-xs">
                  <span className="text-gray-500">0</span>
                  <span className="text-gray-500">50</span>
                </div>
              </div>
              
              <div className="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <p className="text-sm text-gray-500 mb-1">Suhu (°C)</p>
                <p className="text-2xl font-bold text-gray-900">{dynamicData.waterQuality.temperature}</p>
                <div className="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                  <div 
                    className="h-full bg-gradient-to-r from-blue-400 to-blue-600 rounded-full transition-all duration-500" 
                    style={{ width: `${(dynamicData.waterQuality.temperature / 40) * 100}%` }}
                  ></div>
                </div>
                <div className="mt-1 flex justify-between text-xs">
                  <span className="text-gray-500">0</span>
                  <span className="text-gray-500">40</span>
                </div>
              </div>
              
              <div className="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <p className="text-sm text-gray-500 mb-1">Oksigen Terlarut (mg/L)</p>
                <p className="text-2xl font-bold text-gray-900">{dynamicData.waterQuality.dissolvedOxygen}</p>
                <div className="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                  <div 
                    className="h-full bg-gradient-to-r from-purple-400 to-purple-600 rounded-full transition-all duration-500" 
                    style={{ width: `${(dynamicData.waterQuality.dissolvedOxygen / 10) * 100}%` }}
                  ></div>
                </div>
                <div className="mt-1 flex justify-between text-xs">
                  <span className="text-gray-500">0</span>
                  <span className="text-gray-500">10</span>
                </div>
              </div>
              
              <div className="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <p className="text-sm text-gray-500 mb-1">Konduktivitas (µS/cm)</p>
                <p className="text-2xl font-bold text-gray-900">{dynamicData.waterQuality.conductivity}</p>
                <div className="mt-2 flex items-center">
                  <div className="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div 
                      className="h-full bg-gradient-to-r from-indigo-400 to-indigo-600 rounded-full transition-all duration-500" 
                      style={{ width: `${Math.min(dynamicData.waterQuality.conductivity / 1000 * 100, 100)}%` }}
                    ></div>
                  </div>
                  <span className="ml-2 text-xs text-gray-500">1000</span>
                </div>
              </div>
              
              <div className="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <p className="text-sm text-gray-500 mb-1">TDS (ppm)</p>
                <p className="text-2xl font-bold text-gray-900">{dynamicData.waterQuality.tds}</p>
                <div className="mt-2 flex items-center">
                  <div className="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div 
                      className="h-full bg-gradient-to-r from-cyan-400 to-cyan-600 rounded-full transition-all duration-500" 
                      style={{ width: `${Math.min(dynamicData.waterQuality.tds / 500 * 100, 100)}%` }}
                    ></div>
                  </div>
                  <span className="ml-2 text-xs text-gray-500">500</span>
                </div>
              </div>
            </div>
            
            <div className="bg-green-50 rounded-xl p-4 border border-green-200">
              <div className="flex items-start">
                <svg className="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p className="text-sm text-green-700">
                  {dynamicData.waterQuality.pH >= 6.5 && dynamicData.waterQuality.pH <= 8.5 && 
                   dynamicData.waterQuality.turbidity <= 5 && 
                   dynamicData.waterQuality.dissolvedOxygen >= 6
                    ? "Kualitas air saat ini dalam kondisi baik dan aman untuk keperluan domestik."
                    : "Kualitas air memerlukan perhatian khusus. Beberapa parameter berada di luar batas normal."
                  }
                </p>
              </div>
            </div>
          </div>
        </div>
        
        <div 
          className={`h-full overflow-y-auto p-5 custom-scrollbar ${
            activeTab === 'weather' ? 'block' : 'hidden'
          }`}
        >
          <div className="space-y-5">
            <h4 className="text-base font-medium text-gray-900 mb-3">Informasi Cuaca</h4>
            
            <div className="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
              <h5 className="font-medium text-gray-900 mb-3">Kondisi Saat Ini</h5>
              <div className="grid grid-cols-2 gap-4">
                <div className="flex items-center">
                  <svg className="w-8 h-8 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4 4 0 003 15z" />
                  </svg>
                  <div>
                    <p className="text-sm text-gray-500">Suhu</p>
                    <p className="text-lg font-medium text-gray-900">{dynamicData.weatherInfo.current.temperature}°C</p>
                  </div>
                </div>
                
                <div className="flex items-center">
                  <svg className="w-8 h-8 text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4 4 0 003 15z" />
                  </svg>
                  <div>
                    <p className="text-sm text-gray-500">Kelembaban</p>
                    <p className="text-lg font-medium text-gray-900">{dynamicData.weatherInfo.current.humidity}%</p>
                  </div>
                </div>
                
                <div className="flex items-center">
                  <svg className="w-8 h-8 text-blue-300 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4 4 0 003 15z" />
                  </svg>
                  <div>
                    <p className="text-sm text-gray-500">Curah Hujan</p>
                    <p className="text-lg font-medium text-gray-900">{dynamicData.weatherInfo.current.rainfall} mm</p>
                  </div>
                </div>
                
                <div className="flex items-center">
                  <svg className="w-8 h-8 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                  <div>
                    <p className="text-sm text-gray-500">Kecepatan Angin</p>
                    <p className="text-lg font-medium text-gray-900">{dynamicData.weatherInfo.current.windSpeed} km/j</p>
                  </div>
                </div>
              </div>
            </div>
            
            <div className="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
              <h5 className="font-medium text-gray-900 mb-3">Prakiraan Cuaca</h5>
              <div className="space-y-3">
                {dynamicData.weatherInfo.forecast.map((item, index) => (
                  <div key={index} className="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                    <span className="text-gray-600">{item.time}</span>
                    <div className="flex items-center">
                      <div className="w-24 mr-3 h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div 
                          className="h-full rounded-full transition-all duration-500" 
                          style={{ 
                            width: `${item.rain}%`,
                            backgroundColor: item.rain >= 70 ? '#EF4444' : 
                                            item.rain >= 40 ? '#F59E0B' : '#10B981'
                          }}
                        ></div>
                      </div>
                      <span className="text-gray-900 font-medium w-12 text-right">{item.temp}°C</span>
                    </div>
                  </div>
                ))}
              </div>
            </div>
            
            <div className={`rounded-xl p-4 border transition-colors duration-300 ${
              dynamicData.weatherInfo.current.rainfall > 5 || dynamicData.weatherInfo.forecast.some(f => f.rain >= 70)
                ? 'bg-yellow-50 border-yellow-200' 
                : 'bg-blue-50 border-blue-200'
            }`}>
              <div className="flex items-start">
                <svg className={`w-5 h-5 mr-2 mt-0.5 transition-colors duration-300 ${
                  dynamicData.weatherInfo.current.rainfall > 5 || dynamicData.weatherInfo.forecast.some(f => f.rain >= 70)
                    ? 'text-yellow-500' 
                    : 'text-blue-500'
                }`} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  {dynamicData.weatherInfo.current.rainfall > 5 || dynamicData.weatherInfo.forecast.some(f => f.rain >= 70) ? (
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3,1.732 3z" />
                  ) : (
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  )}
                </svg>
                <p className={`text-sm transition-colors duration-300 ${
                  dynamicData.weatherInfo.current.rainfall > 5 || dynamicData.weatherInfo.forecast.some(f => f.rain >= 70)
                    ? 'text-yellow-700' 
                    : 'text-blue-700'
                }`}>
                  {dynamicData.weatherInfo.current.rainfall > 5 || dynamicData.weatherInfo.forecast.some(f => f.rain >= 70)
                    ? "Potensi hujan tinggi dalam beberapa jam ke depan. Waspadai potensi peningkatan level air."
                    : "Kondisi cuaca saat ini stabil dengan potensi hujan rendah."
                  }
                </p>
              </div>
            </div>
          </div>
        </div>
        
        <div 
          className={`h-full overflow-y-auto p-5 custom-scrollbar ${
            activeTab === 'history' ? 'block' : 'hidden'
          }`}
        >
          <div className="space-y-5">
            <h4 className="text-base font-medium text-gray-900 mb-3">Riwayat Banjir</h4>
            
            <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
              {dynamicData.floodHistoryData.map((item, index) => (
                <div 
                  key={index} 
                  className={`p-4 transition-colors duration-300 ${
                    index < dynamicData.floodHistoryData.length - 1 ? 'border-b border-gray-100' : ''
                  }`}
                >
                  <div className="flex justify-between items-start">
                    <div>
                      <p className="font-medium text-gray-900">{item.date}</p>
                      <p className="text-sm text-gray-500 mt-1">Durasi: {item.duration}</p>
                    </div>
                    <div className="text-right">
                      <p className={`text-lg font-bold transition-colors duration-300 ${
                        item.level >= 4.5 ? 'text-red-600' : 
                        item.level >= 3.5 ? 'text-orange-600' : 'text-yellow-600'
                      }`}>{item.level}m</p>
                      <p className="text-xs text-gray-500">Level puncak</p>
                    </div>
                  </div>
                </div>
              ))}
            </div>
            
            <div className="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
              <h5 className="font-medium text-gray-900 mb-3">Statistik Riwayat</h5>
              <div className="grid grid-cols-2 gap-4">
                <div className="bg-gray-50 rounded-lg p-3 transition-all duration-300 hover:bg-gray-100">
                  <p className="text-sm text-gray-500">Jumlah Kejadian</p>
                  <p className="text-xl font-bold text-gray-900">{dynamicData.floodHistoryData.length} kali</p>
                </div>
                <div className="bg-gray-50 rounded-lg p-3 transition-all duration-300 hover:bg-gray-100">
                  <p className="text-sm text-gray-500">Level Tertinggi</p>
                  <p className="text-xl font-bold text-gray-900">
                    {Math.max(...dynamicData.floodHistoryData.map(h => h.level)).toFixed(1)}m
                  </p>
                </div>
                <div className="bg-gray-50 rounded-lg p-3 transition-all duration-300 hover:bg-gray-100">
                  <p className="text-sm text-gray-500">Rata-rata Level</p>
                  <p className="text-xl font-bold text-gray-900">
                    {(dynamicData.floodHistoryData.reduce((sum, h) => sum + h.level, 0) / dynamicData.floodHistoryData.length).toFixed(1)}m
                  </p>
                </div>
                <div className="bg-gray-50 rounded-lg p-3 transition-all duration-300 hover:bg-gray-100">
                  <p className="text-sm text-gray-500">Durasi Rata-rata</p>
                  <p className="text-xl font-bold text-gray-900">
                    {(
                      dynamicData.floodHistoryData.reduce((sum, h) => {
                        return sum + parseFloat(h.duration);
                      }, 0) / dynamicData.floodHistoryData.length
                    ).toFixed(1)} jam
                  </p>
                </div>
              </div>
            </div>
            
            <div className="bg-purple-50 rounded-xl p-4 border border-purple-200">
              <div className="flex items-start">
                <svg className="w-5 h-5 text-purple-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p className="text-sm text-purple-700">
                  Berdasarkan data historis, stasiun ini mengalami banjir rata-rata {dynamicData.floodHistoryData.length} kali dalam 3 tahun terakhir dengan level air mencapai {Math.max(...dynamicData.floodHistoryData.map(h => h.level)).toFixed(1)} meter.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <style jsx>{`
        @keyframes wave {
          0% { transform: translateX(-100%); }
          100% { transform: translateX(100%); }
        }
        
        @keyframes wave-vertical {
          0% { transform: translateY(0); }
          50% { transform: translateY(-5px); }
          100% { transform: translateY(0); }
        }
        
        .animate-wave {
          animation: wave 3s linear infinite;
        }
        
        .animate-wave-vertical {
          animation: wave-vertical 2s ease-in-out infinite;
        }
        
        .delay-300 {
          animation-delay: 0.3s;
        }
        
        .delay-700 {
          animation-delay: 0.7s;
        }
        
        .animate-pulse {
          animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
          0%, 100% { opacity: 1; }
          50% { opacity: .7; }
        }
        
        .custom-scrollbar::-webkit-scrollbar {
          width: 8px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
          background: #f1f1f1;
          border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
          background: #c5c5c5;
          border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
          background: #a8a8a8;
        }
        
        .drop-shadow {
          text-shadow: 0 1px 1px rgba(0, 0, 0, 0.3);
        }
      `}</style>
    </div>
  );
};

export default DetailPanel;