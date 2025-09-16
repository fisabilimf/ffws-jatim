import React, { useState, useEffect } from 'react';
import Chart from './Chart';
import DetailPanel from './DetailPanel';

/**
 * Komponen utama untuk menampilkan detail stasiun monitoring banjir
 * Menampilkan sidebar dan panel detail dalam satu komponen
 */
const StationDetail = ({ selectedStation, onClose, tickerData, isAutoSwitchOn }) => {
  const [stationData, setStationData] = useState(null);
  const [chartHistory, setChartHistory] = useState([]);
  const [isDetailModalOpen, setIsDetailModalOpen] = useState(false);
  
  // Inisialisasi data stasiun saat dipilih
  useEffect(() => {
    if (selectedStation && tickerData) {
      // Mencari data stasiun dari tickerData
      const foundStation = tickerData.find(station => station.id === selectedStation.id);
      if (foundStation) {
        setStationData(foundStation);
        setChartHistory(foundStation.history || []);
      }
    }
  }, [selectedStation, tickerData]);
  
  // Update data chart secara real-time dari tickerData
  useEffect(() => {
    if (!stationData || !tickerData) return;
    // Mencari data stasiun terkini dari tickerData
    const currentStationData = tickerData.find(station => station.id === stationData.id);
    if (currentStationData) {
      setStationData(currentStationData);
      setChartHistory(currentStationData.history || []);
    }
  }, [tickerData, stationData]);

  // Menutup panel detail saat auto switch diaktifkan
  useEffect(() => {
    if (isAutoSwitchOn && isDetailModalOpen) {
      setIsDetailModalOpen(false);
    }
  }, [isAutoSwitchOn, isDetailModalOpen]);

  // Fungsi untuk mendapatkan warna status
  const getStatusColor = (status) => {
    switch (status) {
      case 'safe': return 'text-green-600';
      case 'warning': return 'text-yellow-600';
      case 'alert': return 'text-red-600';
      default: return 'text-gray-600';
    }
  };
  
  // Fungsi untuk mendapatkan warna background status
  const getStatusBgColor = (status) => {
    switch (status) {
      case 'safe': return 'bg-green-100 border-green-200';
      case 'warning': return 'bg-yellow-100 border-yellow-200';
      case 'alert': return 'bg-red-100 border-red-200';
      default: return 'bg-gray-100 border-gray-200';
    }
  };
  
  // Fungsi untuk mendapatkan teks status
  const getStatusText = (status) => {
    switch (status) {
      case 'safe': return 'Aman';
      case 'warning': return 'Waspada';
      case 'alert': return 'Bahaya';
      default: return 'Tidak Diketahui';
    }
  };
  
  // Tidak render jika tidak ada stasiun yang dipilih atau data tidak ada
  if (!selectedStation || !stationData) {
    return null;
  }
  
  return (
    <>
      {/* Sidebar */}
      <div className={`fixed top-20 left-0 h-[calc(100vh-5rem)] w-96 bg-white shadow-2xl z-[70] transform transition-transform duration-300 ease-in-out flex flex-col ${
        selectedStation ? 'translate-x-0' : '-translate-x-full'
      }`}>
        {/* Header Sidebar */}
        <div className="bg-white border-b border-gray-200 p-4 flex-shrink-0">
          <div className="flex items-center space-x-3">
            {/* Tombol Tutup */}
            <button
              onClick={onClose}
              className="p-2 hover:bg-gray-100 rounded-full transition-colors"
            >
              <svg className="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
              </svg>
            </button>
            
            {/* Konten Header */}
            <div className="flex-1">
              <h3 className="text-lg font-semibold text-gray-900">{stationData.name}</h3>
              <p className="text-gray-500 text-sm">{stationData.location}</p>
            </div>
          </div>
        </div>
        
        {/* Area Konten - Scrollable */}
        <div className="flex-1 overflow-y-auto overflow-x-hidden scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 hover:scrollbar-thumb-gray-500">
        <div className="p-4 space-y-6 pb-6">
          {/* Card Status */}
          <div className={`p-4 rounded-lg border-2 ${getStatusBgColor(stationData.status)}`}>
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-gray-600 mb-1">Status Saat Ini</p>
                <p className={`text-xl font-bold ${getStatusColor(stationData.status)}`}>
                  {getStatusText(stationData.status)}
                </p>
              </div>
              <div className="text-right">
                <p className="text-3xl font-bold text-gray-900">
                  {stationData.value.toFixed(1)}
                </p>
                <p className="text-sm text-gray-500">{stationData.unit}</p>
              </div>
            </div>
          </div>
          
          {/* Divider */}
          <div className="border-t border-gray-200"></div>
          
          {/* Section Grafik */}
          <div>
            <h4 className="text-base font-medium text-gray-900 mb-2">Grafik Level Air</h4>
            <p className="text-sm text-gray-500 mb-4">Data 10 menit terakhir</p>
            <div className="bg-gray-50 rounded-lg p-4">
              <Chart 
                data={chartHistory}
                width={320}
                height={160}
                showTooltip={true}
                className="h-40"
                canvasId="station-detail-chart"
                status={stationData.status}
              />
            </div>
          </div>
          
          {/* Divider */}
          <div className="border-t border-gray-200"></div>
          
          {/* Grid Statistik */}
          <div>
            <h4 className="text-base font-medium text-gray-900 mb-4">Statistik</h4>
            <div className="grid grid-cols-2 gap-4">
              <div className="bg-gray-50 rounded-lg p-4">
                <p className="text-sm text-gray-500 mb-1">Level Tertinggi</p>
                <p className="text-2xl font-bold text-gray-900">
                  {chartHistory.length > 0 ? Math.max(...chartHistory).toFixed(1) : '0.0'}m
                </p>
              </div>
              <div className="bg-gray-50 rounded-lg p-4">
                <p className="text-sm text-gray-500 mb-1">Level Terendah</p>
                <p className="text-2xl font-bold text-gray-900">
                  {chartHistory.length > 0 ? Math.min(...chartHistory).toFixed(1) : '0.0'}m
                </p>
              </div>
            </div>
          </div>
          
          {/* Divider */}
          <div className="border-t border-gray-200"></div>
          
          {/* Informasi Tambahan */}
          <div>
            <h4 className="text-base font-medium text-gray-900 mb-4">Informasi Stasiun</h4>
            <div className="space-y-3">
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
        </div>
        </div>
      </div>

      {/* Tombol Arrow untuk membuka panel detail */}
      {!isAutoSwitchOn && (
        <button
          onClick={() => setIsDetailModalOpen(true)}
          className={`
            fixed left-96 z-[80] 
            w-6 h-12 
            ${isDetailModalOpen 
              ? 'bg-gray-800 text-white hover:bg-gray-900' 
              : 'bg-white text-gray-600 hover:bg-gray-50 hover:text-gray-800'
            }
            border-l-0 border-r border-t border-b border-gray-300
            rounded-r-md
            shadow-md hover:shadow-lg
            flex items-center justify-center 
            transition-all duration-300 ease-in-out
            ${selectedStation ? 'opacity-100' : 'opacity-0'}
          `}
          style={{ 
            willChange: 'transform, opacity',
            top: '50%',
            transform: 'translateY(-50%)'
          }}
          title={isDetailModalOpen ? "Tutup Info Detail" : "Buka Info Detail"}
        >
          <svg 
            className={`w-3 h-3 transition-transform duration-300 ${isDetailModalOpen ? 'rotate-180' : 'rotate-0'}`}
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
          >
            <path 
              strokeLinecap="round" 
              strokeLinejoin="round" 
              strokeWidth={2.5} 
              d="M9 5l7 7-7 7" 
            />
          </svg>
        </button>
      )}

      {/* Panel Detail - hanya muncul jika auto switch mati */}
      <DetailPanel 
        isOpen={isDetailModalOpen && !isAutoSwitchOn}
        onClose={() => setIsDetailModalOpen(false)}
        stationData={stationData}
        chartHistory={chartHistory}
      />
    </>
  );
};

export default StationDetail;

