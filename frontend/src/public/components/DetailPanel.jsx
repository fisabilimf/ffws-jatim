import React, { useState } from 'react';

const DetailPanel = ({ 
  stationData, 
  sensorData, 
  waterQualityData, 
  weatherData, 
  floodHistory 
}) => {
  const [isOpen, setIsOpen] = useState(false);
  
  const togglePanel = () => {
    setIsOpen(!isOpen);
  };

  return (
    <div className={`fixed top-20 right-0 h-[calc(100vh-5rem)] bg-white shadow-2xl z-[50] transition-all duration-300 ease-in-out ${
      isOpen ? 'w-[380px]' : 'w-[60px]'
    }`}>
      {/* Toggle Button */}
      <button
        onClick={togglePanel}
        className="absolute -left-10 top-1/2 transform -translate-y-1/2 bg-white p-2 rounded-l-lg shadow-md border border-gray-200 z-10"
      >
        <svg 
          className={`w-5 h-5 text-gray-700 transition-transform duration-300 ${isOpen ? 'rotate-180' : ''}`} 
          fill="none" 
          stroke="currentColor" 
          viewBox="0 0 24 24"
        >
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
        </svg>
      </button>
      
      {/* Panel Content */}
      <div className="h-full flex flex-col overflow-hidden">
        {isOpen ? (
          <div className="flex-1 overflow-y-auto p-5 space-y-6">
            {/* Header */}
            <div className="border-b border-gray-200 pb-4">
              <h3 className="text-lg font-semibold text-gray-900">Detail Panel</h3>
              <p className="text-sm text-gray-500">Informasi lengkap stasiun</p>
            </div>
            
            {/* Sensor Information */}
            <div>
              <h4 className="text-base font-medium text-gray-900 mb-3">Informasi Sensor</h4>
              <div className="bg-gray-50 rounded-lg p-4 space-y-3">
                <div className="flex justify-between">
                  <span className="text-sm text-gray-600">Tipe Sensor</span>
                  <span className="text-sm font-medium">{sensorData.sensorType}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-sm text-gray-600">Kedalaman Maks</span>
                  <span className="text-sm font-medium">{sensorData.sensorDepth}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-sm text-gray-600">Tanggal Instalasi</span>
                  <span className="text-sm font-medium">{sensorData.installationDate}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-sm text-gray-600">Perawatan Terakhir</span>
                  <span className="text-sm font-medium">{sensorData.lastMaintenance}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-sm text-gray-600">Level Baterai</span>
                  <div className="flex items-center">
                    <div className="w-16 h-2 bg-gray-200 rounded-full mr-2">
                      <div 
                        className="h-full bg-green-500 rounded-full" 
                        style={{ width: sensorData.batteryLevel }}
                      ></div>
                    </div>
                    <span className="text-sm font-medium">{sensorData.batteryLevel}</span>
                  </div>
                </div>
                <div className="flex justify-between">
                  <span className="text-sm text-gray-600">Kekuatan Sinyal</span>
                  <div className="flex items-center">
                    <div className="flex mr-2">
                      {[...Array(4)].map((_, i) => (
                        <div 
                          key={i}
                          className={`w-1 h-3 mx-px rounded-sm ${
                            i < 3 ? 'bg-green-500' : 'bg-gray-300'
                          }`}
                        ></div>
                      ))}
                    </div>
                    <span className="text-sm font-medium">{sensorData.signalStrength}</span>
                  </div>
                </div>
                <div className="flex justify-between">
                  <span className="text-sm text-gray-600">Akurasi</span>
                  <span className="text-sm font-medium">{sensorData.accuracy}</span>
                </div>
              </div>
            </div>
            
            {/* Water Quality Data */}
            <div>
              <h4 className="text-base font-medium text-gray-900 mb-3">Kualitas Air</h4>
              <div className="bg-blue-50 rounded-lg p-4">
                <div className="grid grid-cols-2 gap-3">
                  <div className="bg-white p-3 rounded-lg">
                    <p className="text-xs text-blue-700 mb-1">pH</p>
                    <p className="text-lg font-bold text-blue-900">{waterQualityData.pH}</p>
                  </div>
                  <div className="bg-white p-3 rounded-lg">
                    <p className="text-xs text-blue-700 mb-1">Kekeruhan (NTU)</p>
                    <p className="text-lg font-bold text-blue-900">{waterQualityData.turbidity}</p>
                  </div>
                  <div className="bg-white p-3 rounded-lg">
                    <p className="text-xs text-blue-700 mb-1">Suhu (°C)</p>
                    <p className="text-lg font-bold text-blue-900">{waterQualityData.temperature}</p>
                  </div>
                  <div className="bg-white p-3 rounded-lg">
                    <p className="text-xs text-blue-700 mb-1">Oksigen Terlarut</p>
                    <p className="text-lg font-bold text-blue-900">{waterQualityData.dissolvedOxygen} mg/L</p>
                  </div>
                  <div className="bg-white p-3 rounded-lg">
                    <p className="text-xs text-blue-700 mb-1">Konduktivitas</p>
                    <p className="text-lg font-bold text-blue-900">{waterQualityData.conductivity} µS/cm</p>
                  </div>
                  <div className="bg-white p-3 rounded-lg">
                    <p className="text-xs text-blue-700 mb-1">TDS</p>
                    <p className="text-lg font-bold text-blue-900">{waterQualityData.tds} ppm</p>
                  </div>
                </div>
              </div>
            </div>
            
            {/* Weather Information */}
            <div>
              <h4 className="text-base font-medium text-gray-900 mb-3">Informasi Cuaca</h4>
              <div className="bg-gray-50 rounded-lg p-4 space-y-4">
                <div>
                  <p className="text-sm text-gray-500 mb-2">Kondisi Saat Ini</p>
                  <div className="grid grid-cols-2 gap-2">
                    <div className="flex items-center space-x-2 bg-white p-2 rounded">
                      <svg className="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                      </svg>
                      <span className="text-xs">{weatherData.current.rainfall}mm/h</span>
                    </div>
                    <div className="flex items-center space-x-2 bg-white p-2 rounded">
                      <svg className="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                      </svg>
                      <span className="text-xs">{weatherData.current.temperature}°C</span>
                    </div>
                    <div className="flex items-center space-x-2 bg-white p-2 rounded">
                      <svg className="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                      </svg>
                      <span className="text-xs">{weatherData.current.humidity}%</span>
                    </div>
                    <div className="flex items-center space-x-2 bg-white p-2 rounded">
                      <svg className="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 10V3L4 14h7v7l9-11h-7z" />
                      </svg>
                      <span className="text-xs">{weatherData.current.windSpeed} km/jam</span>
                    </div>
                  </div>
                </div>
                <div>
                  <p className="text-sm text-gray-500 mb-2">Prakiraan Hujan</p>
                  <div className="flex space-x-2">
                    {weatherData.forecast.map((item, index) => (
                      <div key={index} className="flex-1 text-center bg-white p-2 rounded">
                        <p className="text-xs text-gray-500">{item.time}</p>
                        <div className="flex justify-center my-1">
                          <svg className="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5.5 16a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 16h-8z" />
                          </svg>
                        </div>
                        <p className="text-xs font-medium">{item.rain}%</p>
                      </div>
                    ))}
                  </div>
                </div>
              </div>
            </div>
            
            {/* Flood History */}
            <div>
              <h4 className="text-base font-medium text-gray-900 mb-3">Riwayat Banjir</h4>
              <div className="bg-gray-50 rounded-lg p-4">
                {floodHistory.length > 0 ? (
                  <div className="space-y-3">
                    {floodHistory.map((flood, index) => (
                      <div key={index} className="bg-white p-3 rounded-lg">
                        <div className="flex justify-between">
                          <p className="text-sm font-medium">{flood.date}</p>
                          <p className="text-sm font-bold">{flood.level}m</p>
                        </div>
                        <p className="text-xs text-gray-500">{flood.duration}</p>
                      </div>
                    ))}
                  </div>
                ) : (
                  <p className="text-sm text-gray-500 text-center py-2">Tidak ada riwayat banjir</p>
                )}
              </div>
            </div>
          </div>
        ) : (
          <div className="flex items-center justify-center h-full">
            <div className="text-center">
              <svg className="w-8 h-8 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
              <p className="text-xs text-gray-500 mt-2">Detail Panel</p>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default DetailPanel;