// DetailPanel.js
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
  const [activeTab, setActiveTab] = useState('sensor');
  
  useEffect(() => {
    if (isOpen) {
      setTimeout(() => {
        setIsVisible(true);
      }, 10);
    } else {
      setIsVisible(false);
    }
  }, [isOpen]);
  
  // Don't render if sidebar is closed or panel is not open
  if (!isSidebarOpen || !isOpen) return null;
  
  return (
    <div 
      className="fixed top-20 left-[390px] h-[calc(100vh-5rem)] w-[590px] bg-white shadow-2xl z-[65] transform transition-transform duration-300 ease-in-out flex flex-col"
      style={{ 
        willChange: 'transform',
        left: isSidebarOpen ? '400px' : '11px' // 390px = 380px (sidebar) + 10px (jarak)
      }}
    >
      {/* Header */}
      <div className="bg-white border-b border-gray-200 p-4 flex-shrink-0">
        <div className="flex items-center justify-between">
          <h3 className="text-lg font-semibold text-gray-900">Detail Informasi Stasiun</h3>
          <button
            onClick={onClose}
            className="p-2 hover:bg-gray-100 rounded-full transition-colors"    >
          </button>
        </div>
        
        {/* Tabs */}
        <div className="flex mt-4 border-b border-gray-200">
          {[
            { id: 'sensor', label: 'Sensor' },
            { id: 'quality', label: 'Kualitas Air' },
            { id: 'weather', label: 'Cuaca' },
            { id: 'history', label: 'Riwayat' }
          ].map((tab) => (
            <button
              key={tab.id}
              className={`px-11 py-3 font-medium text-sm ${
                activeTab === tab.id
                  ? 'text-blue-600 border-b-2 border-blue-600'
                  : 'text-gray-500 hover:text-gray-700'
              }`}
              onClick={() => setActiveTab(tab.id)}
            >
              {tab.label}
            </button>
          ))}
        </div>
      </div>
      
      {/* Content */}
      <div className="flex-1 overflow-y-auto p-5">
        {/* Sensor Tab */}
        {activeTab === 'sensor' && (
          <div className="space-y-5">
            <h4 className="text-base font-medium text-gray-900 mb-3">Informasi Sensor</h4>
            
            <div className="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
              <div className="flex justify-between items-center py-3 border-b border-gray-100">
                <span className="text-gray-600">Tipe Sensor</span>
                <span className="text-gray-900 font-medium">{sensorData.sensorType}</span>
              </div>
              <div className="flex justify-between items-center py-3 border-b border-gray-100">
                <span className="text-gray-600">Kedalaman Maksimum</span>
                <span className="text-gray-900 font-medium">{sensorData.sensorDepth}</span>
              </div>
              <div className="flex justify-between items-center py-3 border-b border-gray-100">
                <span className="text-gray-600">Tanggal Pemasangan</span>
                <span className="text-gray-900 font-medium">{sensorData.installationDate}</span>
              </div>
              <div className="flex justify-between items-center py-3 border-b border-gray-100">
                <span className="text-gray-600">Perawatan Terakhir</span>
                <span className="text-gray-900 font-medium">{sensorData.lastMaintenance}</span>
              </div>
              <div className="flex justify-between items-center py-3 border-b border-gray-100">
                <span className="text-gray-600">Level Baterai</span>
                <span className="text-gray-900 font-medium">{sensorData.batteryLevel}</span>
              </div>
              <div className="flex justify-between items-center py-3 border-b border-gray-100">
                <span className="text-gray-600">Kekuatan Sinyal</span>
                <span className="text-gray-900 font-medium">{sensorData.signalStrength}</span>
              </div>
              <div className="flex justify-between items-center py-3">
                <span className="text-gray-600">Akurasi</span>
                <span className="text-gray-900 font-medium">{sensorData.accuracy}</span>
              </div>
            </div>
            
            <div className="bg-blue-50 rounded-xl p-4 border border-blue-200">
              <div className="flex items-start">
                <svg className="w-5 h-5 text-blue-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p className="text-sm text-blue-700">Sensor dikalibrasi setiap 3 bulan untuk memastikan akurasi data. Perawatan terakhir dilakukan pada {sensorData.lastMaintenance}.</p>
              </div>
            </div>
          </div>
        )}
        
        {/* Water Quality Tab */}
        {activeTab === 'quality' && (
          <div className="space-y-5">
            <h4 className="text-base font-medium text-gray-900 mb-3">Kualitas Air</h4>
            
            <div className="grid grid-cols-2 gap-4">
              <div className="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <p className="text-sm text-gray-500 mb-1">pH</p>
                <p className="text-2xl font-bold text-gray-900">{waterQualityData.pH}</p>
                <div className="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                  <div 
                    className="h-full bg-green-500" 
                    style={{ width: `${(waterQualityData.pH / 14) * 100}%` }}
                  ></div>
                </div>
              </div>
              
              <div className="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <p className="text-sm text-gray-500 mb-1">Kekeruhan (NTU)</p>
                <p className="text-2xl font-bold text-gray-900">{waterQualityData.turbidity}</p>
                <div className="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                  <div 
                    className="h-full bg-yellow-500" 
                    style={{ width: `${Math.min(waterQualityData.turbidity / 50 * 100, 100)}%` }}
                  ></div>
                </div>
              </div>
              
              <div className="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <p className="text-sm text-gray-500 mb-1">Suhu (°C)</p>
                <p className="text-2xl font-bold text-gray-900">{waterQualityData.temperature}</p>
                <div className="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                  <div 
                    className="h-full bg-blue-500" 
                    style={{ width: `${(waterQualityData.temperature / 40) * 100}%` }}
                  ></div>
                </div>
              </div>
              
              <div className="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <p className="text-sm text-gray-500 mb-1">Oksigen Terlarut (mg/L)</p>
                <p className="text-2xl font-bold text-gray-900">{waterQualityData.dissolvedOxygen}</p>
                <div className="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                  <div 
                    className="h-full bg-purple-500" 
                    style={{ width: `${(waterQualityData.dissolvedOxygen / 10) * 100}%` }}
                  ></div>
                </div>
              </div>
              
              <div className="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <p className="text-sm text-gray-500 mb-1">Konduktivitas (µS/cm)</p>
                <p className="text-2xl font-bold text-gray-900">{waterQualityData.conductivity}</p>
              </div>
              
              <div className="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <p className="text-sm text-gray-500 mb-1">TDS (ppm)</p>
                <p className="text-2xl font-bold text-gray-900">{waterQualityData.tds}</p>
              </div>
            </div>
            
            <div className="bg-green-50 rounded-xl p-4 border border-green-200">
              <div className="flex items-start">
                <svg className="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p className="text-sm text-green-700">Kualitas air saat ini dalam kondisi baik dan aman untuk keperluan domestik.</p>
              </div>
            </div>
          </div>
        )}
        
        {/* Weather Tab */}
        {activeTab === 'weather' && (
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
                    <p className="text-lg font-medium text-gray-900">{weatherData.current.temperature}°C</p>
                  </div>
                </div>
                
                <div className="flex items-center">
                  <svg className="w-8 h-8 text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4 4 0 003 15z" />
                  </svg>
                  <div>
                    <p className="text-sm text-gray-500">Kelembaban</p>
                    <p className="text-lg font-medium text-gray-900">{weatherData.current.humidity}%</p>
                  </div>
                </div>
                
                <div className="flex items-center">
                  <svg className="w-8 h-8 text-blue-300 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4 4 0 003 15z" />
                  </svg>
                  <div>
                    <p className="text-sm text-gray-500">Curah Hujan</p>
                    <p className="text-lg font-medium text-gray-900">{weatherData.current.rainfall} mm</p>
                  </div>
                </div>
                
                <div className="flex items-center">
                  <svg className="w-8 h-8 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                  <div>
                    <p className="text-sm text-gray-500">Kecepatan Angin</p>
                    <p className="text-lg font-medium text-gray-900">{weatherData.current.windSpeed} km/j</p>
                  </div>
                </div>
              </div>
            </div>
            
            <div className="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
              <h5 className="font-medium text-gray-900 mb-3">Prakiraan Cuaca</h5>
              <div className="space-y-3">
                {weatherData.forecast.map((item, index) => (
                  <div key={index} className="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                    <span className="text-gray-600">{item.time}</span>
                    <div className="flex items-center">
                      <div className="w-24 mr-3 h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div 
                          className="h-full bg-blue-400" 
                          style={{ width: `${item.rain}%` }}
                        ></div>
                      </div>
                      <span className="text-gray-900 font-medium w-12 text-right">{item.temp}°C</span>
                    </div>
                  </div>
                ))}
              </div>
            </div>
            
            <div className="bg-yellow-50 rounded-xl p-4 border border-yellow-200">
              <div className="flex items-start">
                <svg className="w-5 h-5 text-yellow-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p className="text-sm text-yellow-700">Potensi hujan tinggi dalam beberapa jam ke depan. Waspadai potensi peningkatan level air.</p>
              </div>
            </div>
          </div>
        )}
        
        {/* History Tab */}
        {activeTab === 'history' && (
          <div className="space-y-5">
            <h4 className="text-base font-medium text-gray-900 mb-3">Riwayat Banjir</h4>
            
            <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
              {floodHistory.map((item, index) => (
                <div 
                  key={index} 
                  className={`p-4 ${index < floodHistory.length - 1 ? 'border-b border-gray-100' : ''}`}
                >
                  <div className="flex justify-between items-start">
                    <div>
                      <p className="font-medium text-gray-900">{item.date}</p>
                      <p className="text-sm text-gray-500 mt-1">Durasi: {item.duration}</p>
                    </div>
                    <div className="text-right">
                      <p className="text-lg font-bold text-red-600">{item.level}m</p>
                      <p className="text-xs text-gray-500">Level puncak</p>
                    </div>
                  </div>
                </div>
              ))}
            </div>
            
            <div className="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
              <h5 className="font-medium text-gray-900 mb-3">Statistik Riwayat</h5>
              <div className="grid grid-cols-2 gap-4">
                <div className="bg-gray-50 rounded-lg p-3">
                  <p className="text-sm text-gray-500">Jumlah Kejadian</p>
                  <p className="text-xl font-bold text-gray-900">{floodHistory.length} kali</p>
                </div>
                <div className="bg-gray-50 rounded-lg p-3">
                  <p className="text-sm text-gray-500">Level Tertinggi</p>
                  <p className="text-xl font-bold text-gray-900">
                    {Math.max(...floodHistory.map(h => h.level)).toFixed(1)}m
                  </p>
                </div>
                <div className="bg-gray-50 rounded-lg p-3">
                  <p className="text-sm text-gray-500">Rata-rata Level</p>
                  <p className="text-xl font-bold text-gray-900">
                    {(floodHistory.reduce((sum, h) => sum + h.level, 0) / floodHistory.length).toFixed(1)}m
                  </p>
                </div>
                <div className="bg-gray-50 rounded-lg p-3">
                  <p className="text-sm text-gray-500">Durasi Rata-rata</p>
                  <p className="text-xl font-bold text-gray-900">
                    {(
                      floodHistory.reduce((sum, h) => {
                        return sum + parseFloat(h.duration);
                      }, 0) / floodHistory.length
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
                <p className="text-sm text-purple-700">Berdasarkan data historis, stasiun ini mengalami banjir rata-rata 3-4 kali setahun dengan level air mencapai 4+ meter.</p>
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default DetailPanel;