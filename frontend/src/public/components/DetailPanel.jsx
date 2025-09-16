import React, { useState, useEffect } from 'react';
import Chart from './Chart';

/**
 * Komponen panel detail dengan layout two column
 * Menampilkan informasi lengkap tentang stasiun monitoring banjir
 */
const DetailPanel = ({ 
  isOpen, 
  onClose, 
  stationData, 
  chartHistory 
}) => {
  const [isVisible, setIsVisible] = useState(false);

  // Mengatur animasi visibility saat panel dibuka/ditutup
  useEffect(() => {
    if (isOpen) {
      setTimeout(() => {
        setIsVisible(true);
      }, 10);
    } else {
      // Animasi close - geser ke kiri
      setIsVisible(false);
    }
  }, [isOpen]);

  // Handler untuk close dengan animasi
  const handleClose = () => {
    setIsVisible(false);
    setTimeout(() => {
      onClose();
    }, 300); // Sama dengan durasi animasi
  };
  // Fungsi untuk mendapatkan warna status
  const getStatusColor = (status) => {
    switch (status) {
      case 'safe': return 'text-green-600';
      case 'warning': return 'text-yellow-600';
      case 'alert': return 'text-red-600';
      default: return 'text-gray-600';
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

  // Tidak render jika panel tidak dibuka atau data tidak ada
  if (!isOpen || !stationData) return null;

  return (
    <div className={`fixed top-20 left-[26 rem] right-80 bottom-0 z-[50] bg-white shadow-2xl transform transition-transform duration-300 ease-in-out ${
      isVisible ? 'translate-x-0' : '-translate-x-full'
    }`}
    style={{ willChange: 'transform' }}>
      <div className="h-full flex flex-col">
        {/* Header Panel */}
        <div className="bg-white border-b border-gray-200 p-4 flex-shrink-0">
          <div className="flex items-center space-x-3">
            <button
              onClick={handleClose}
              className="p-2 hover:bg-gray-100 rounded-full transition-colors group"
            >
              <svg className="w-5 h-5 text-gray-600 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 17l-5-5m0 0l5-5m-5 5h12" />
              </svg>
            </button>
            <div className="flex-1">
              <h3 className="text-lg font-semibold text-gray-900">Informasi Detail</h3>
              <p className="text-gray-500 text-sm">{stationData.name}</p>
            </div>
          </div>
        </div>

        {/* Konten Panel - Layout Two Column */}
        <div className="flex-1 overflow-y-auto p-4">
          <div className="grid grid-cols-1 xl:grid-cols-2 gap-6 h-full">
            {/* Kolom Kiri - Informasi Stasiun */}
            <div className="space-y-4">
              {/* Informasi Stasiun */}
              <div>
                <h3 className="text-base font-medium text-gray-900 mb-3">Informasi Stasiun</h3>
                <div className="bg-gray-50 rounded-lg p-3 space-y-2">
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600">ID Stasiun:</span>
                    <span className="font-medium">#{stationData.id}</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600">Nama:</span>
                    <span className="font-medium">{stationData.name}</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600">Lokasi:</span>
                    <span className="font-medium text-right text-xs">{stationData.location}</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600">Status:</span>
                    <span className={`font-medium text-sm ${getStatusColor(stationData.status)}`}>
                      {getStatusText(stationData.status)}
                    </span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600">Update Terakhir:</span>
                    <span className="font-medium text-xs">{new Date().toLocaleTimeString('id-ID')}</span>
                  </div>
                </div>
              </div>

              {/* Data Level Air */}
              <div>
                <h3 className="text-base font-medium text-gray-900 mb-3">Data Level Air</h3>
                <div className="bg-blue-50 rounded-lg p-3 space-y-2">
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600">Level Saat Ini:</span>
                    <span className="font-bold text-blue-600">{stationData.value.toFixed(1)} {stationData.unit}</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600">Level Tertinggi:</span>
                    <span className="font-bold text-red-600">{Math.max(...chartHistory).toFixed(1)} {stationData.unit}</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600">Level Terendah:</span>
                    <span className="font-bold text-green-600">{Math.min(...chartHistory).toFixed(1)} {stationData.unit}</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600">Rata-rata:</span>
                    <span className="font-bold text-gray-600">
                      {(chartHistory.reduce((a, b) => a + b, 0) / chartHistory.length).toFixed(1)} {stationData.unit}
                    </span>
                  </div>
                </div>
              </div>

              {/* Statistik Tambahan */}
              <div>
                <h3 className="text-base font-medium text-gray-900 mb-3">Statistik Tambahan</h3>
                <div className="grid grid-cols-2 gap-3">
                  <div className="bg-gray-50 rounded-lg p-3 text-center">
                    <div className="text-xl font-bold text-gray-900">
                      {chartHistory.length}
                    </div>
                    <div className="text-xs text-gray-600">Data Points</div>
                  </div>
                  <div className="bg-gray-50 rounded-lg p-3 text-center">
                    <div className="text-xl font-bold text-gray-900">
                      {((Math.max(...chartHistory) - Math.min(...chartHistory)) / Math.min(...chartHistory) * 100).toFixed(1)}%
                    </div>
                    <div className="text-xs text-gray-600">Variasi</div>
                  </div>
                </div>
              </div>
            </div>

            {/* Kolom Kanan - Grafik dan Informasi Tambahan */}
            <div className="space-y-4">
              {/* Grafik Level Air */}
              <div>
                <h3 className="text-base font-medium text-gray-900 mb-3">Grafik Level Air</h3>
                <div className="bg-gray-50 rounded-lg p-3">
                  <Chart 
                    data={chartHistory}
                    width={350}
                    height={160}
                    showTooltip={true}
                    className="h-40"
                    canvasId="detail-side-panel-chart"
                    status={stationData.status}
                  />
                </div>
              </div>

              {/* Informasi Teknis */}
              <div>
                <h3 className="text-base font-medium text-gray-900 mb-3">Informasi Teknis</h3>
                <div className="bg-gray-50 rounded-lg p-3 space-y-2">
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600">Sumber Data:</span>
                    <span className="font-medium text-xs">Sistem Monitoring Banjir</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600">Interval Update:</span>
                    <span className="font-medium text-xs">10 menit</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600">Akurasi:</span>
                    <span className="font-medium text-xs">±0.1 {stationData.unit}</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600">Status Sensor:</span>
                    <span className="font-medium text-green-600 text-xs">Normal</span>
                  </div>
                </div>
              </div>

              {/* Riwayat Status */}
              <div>
                <h3 className="text-base font-medium text-gray-900 mb-3">Riwayat Status</h3>
                <div className="bg-gray-50 rounded-lg p-3 space-y-2">
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600">Status Aman:</span>
                    <span className="font-medium text-green-600 text-xs">85%</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600">Status Waspada:</span>
                    <span className="font-medium text-yellow-600 text-xs">12%</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600">Status Bahaya:</span>
                    <span className="font-medium text-red-600 text-xs">3%</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default DetailPanel;
