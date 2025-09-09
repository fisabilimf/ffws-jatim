import React from 'react';

const SideCard = ({ 
  isOpen, 
  onClose, 
  selectedStation,
  className = '' 
}) => {
  // Helper functions untuk styling status
  const getStatusText = (status) => {
    switch (status) {
      case 'safe': return 'Aman';
      case 'warning': return 'Waspada';
      case 'alert': return 'Bahaya';
      default: return 'Tidak Diketahui';
    }
  };

  const getStatusTextColor = (status) => {
    switch (status) {
      case 'safe': return 'text-green-600';
      case 'warning': return 'text-yellow-600';
      case 'alert': return 'text-red-600';
      default: return 'text-gray-600';
    }
  };

  const getStatusBgColor = (status) => {
    switch (status) {
      case 'safe': return 'bg-green-100';
      case 'warning': return 'bg-yellow-100';
      case 'alert': return 'bg-red-100';
      default: return 'bg-gray-100';
    }
  };

  // Don't render if not open or no station selected
  if (!isOpen || !selectedStation) return null;

  return (
    <div className={`absolute top-0 left-0 w-80 h-full bg-white shadow-2xl transform transition-transform duration-300 ease-in-out z-10 ${className}`}>
      {/* Header */}
      <div className="bg-white p-4 border-b border-gray-200">
        <div className="flex items-center justify-between">
          <button
            onClick={onClose}
            className="p-2 hover:bg-gray-100 rounded-full transition-colors"
          >
            <svg className="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
          <h3 className="text-lg font-semibold text-gray-800">Detail Stasiun</h3>
          <div className="w-5"></div>
        </div>
      </div>

      {/* Content */}
      <div className="p-4 space-y-4">
        {/* Station Image Placeholder */}
        <div className="w-full h-48 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center">
          <div className="text-center">
            <div className="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-2">
              <svg className="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            <p className="text-blue-600 font-medium">Stasiun Monitoring</p>
          </div>
        </div>

        {/* Station Info */}
        <div>
          <h2 className="text-xl font-bold text-gray-900 mb-2">{selectedStation.name}</h2>
          <div className="flex items-center space-x-2 mb-3">
            <div className={`w-3 h-3 rounded-full ${getStatusBgColor(selectedStation.status)}`}></div>
            <span className={`text-sm font-medium ${getStatusTextColor(selectedStation.status)}`}>
              {getStatusText(selectedStation.status)}
            </span>
          </div>
          <p className="text-gray-600 text-sm">{selectedStation.location}</p>
        </div>

        {/* Water Level Info */}
        <div className="bg-gray-50 p-4 rounded-lg">
          <h3 className="font-semibold text-gray-800 mb-3">Informasi Level Air</h3>
          <div className="space-y-3">
            <div className="flex justify-between items-center">
              <span className="text-gray-600">Level Saat Ini:</span>
              <span className="text-2xl font-bold text-blue-600">{selectedStation.value} {selectedStation.unit}</span>
            </div>
            <div className="flex justify-between items-center">
              <span className="text-gray-600">Status:</span>
              <span className={`px-3 py-1 rounded-full text-sm font-medium ${getStatusBgColor(selectedStation.status)} ${getStatusTextColor(selectedStation.status)}`}>
                {getStatusText(selectedStation.status)}
              </span>
            </div>
          </div>
        </div>

        {/* Action Buttons */}
        <div className="space-y-2">
          <button className="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors font-medium">
            Lihat Detail Lengkap
          </button>
          <button className="w-full bg-gray-100 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-200 transition-colors font-medium">
            Simpan ke Favorit
          </button>
        </div>

        {/* Additional Info */}
        <div className="border-t pt-4">
          <h4 className="font-medium text-gray-800 mb-2">Informasi Tambahan</h4>
          <div className="space-y-2 text-sm text-gray-600">
            <p>• Stasiun aktif 24/7</p>
            <p>• Update data real-time</p>
            <p>• Monitoring otomatis</p>
          </div>
        </div>
      </div>
    </div>
  );
};

export default SideCard;
