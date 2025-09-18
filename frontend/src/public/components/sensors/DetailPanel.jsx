import React, { useState, useEffect } from 'react';
import LeveeChart from '../ui/LeveeChart';
import DualLineChart from '../ui/DualLineChart';
import { getStationThreshold } from '../../config/stationThresholds';
import { getStatusColor, getStatusText } from '../../utils/statusUtils';

/**
 * Komponen panel detail dengan layout two column
 * Menampilkan informasi lengkap tentang stasiun monitoring banjir
 */
const DetailPanel = ({ 
  isOpen, 
  onClose, 
  stationData, 
  chartHistory,
  isAutoSwitchOn = false
}) => {
  const [isVisible, setIsVisible] = useState(false);
  const [activeTab, setActiveTab] = useState('sensor'); // 'sensor' | 'kualitas' | 'cuaca' | 'riwayat'

  // Mengatur animasi visibility saat panel dibuka/ditutup
  useEffect(() => {
    if (isOpen) {
      setTimeout(() => {
        setIsVisible(true);
      }, 10);
    } else {
      // Animasi close - geser ke kiri dengan fade out
      setIsVisible(false);
    }
  }, [isOpen]);

  // Auto close detail panel saat auto switch berjalan
  useEffect(() => {
    if (isAutoSwitchOn && isOpen) {
      // Tutup detail panel dengan animasi saat auto switch aktif
      handleClose();
    }
  }, [isAutoSwitchOn]);

  // Handler untuk close dengan animasi
  const handleClose = () => {
    setIsVisible(false);
    setTimeout(() => {
      onClose();
    }, 300); // Sama dengan durasi animasi
  };

  // Tidak render jika panel tidak dibuka atau data tidak ada
  if (!isOpen || !stationData) return null;

  return (
    <div className={`fixed top-20 left-96 right-80 bottom-0 z-[50] bg-white shadow-2xl transform transition-all duration-300 ease-in-out ${
      isVisible ? 'translate-x-0 opacity-100' : '-translate-x-full opacity-0'
    }`}
    style={{ willChange: 'transform, opacity' }}>
      <div className="h-full flex flex-col">
        {/* Header Panel */}
        <div className="bg-white border-b border-gray-200 p-4 flex-shrink-0">
          {/* Baris judul + tombol close dengan alignment yang rapi */}
          <div className="flex items-center justify-between">
            {/* Bagian kiri - tombol back + judul */}
            <div className="flex items-center gap-3">
              <button
                onClick={handleClose}
                className="p-2 hover:bg-gray-100 rounded-full transition-colors group"
                aria-label="Kembali"
              >
                <svg className="w-5 h-5 text-gray-600 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 17l-5-5m0 0l5-5m-5 5h14" />
                </svg>
              </button>
              <div className="min-w-0">
                <h3 className="text-lg font-semibold text-gray-900">Detail Informasi Stasiun</h3>
                <p className="text-sm text-gray-500 mt-0.5">{stationData.name}</p>
              </div>
            </div>
            
            {/* Bagian kanan - status info */}
            <div className="text-right">
              <div className="flex items-center justify-end gap-2 mb-1">
                {isAutoSwitchOn && (
                  <div className="flex items-center gap-1 px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                    <div className="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                    Auto Switch
                  </div>
                )}
                <div className={`text-sm font-medium ${getStatusColor(stationData.status)}`}>{getStatusText(stationData.status)}</div>
              </div>
              <div className="text-xs text-gray-500">Update {new Date().toLocaleTimeString('id-ID')}</div>
            </div>
          </div>
          {/* Navigation tabs yang disempurnakan */}
          <div className="mt-6">
            <nav className="relative">
              <div className="flex items-center justify-center space-x-20 text-sm">
                {[
                  { key: 'sensor', label: 'Sensor' },
                  { key: 'kualitas', label: 'Cuaca' },
                  { key: 'cuaca', label: 'Monitoring' },
                  { key: 'riwayat', label: 'Riwayat' }
                ].map((tab) => (
                  <button
                    key={tab.key}
                    onClick={() => setActiveTab(tab.key)}
                    className={`relative py-4 px-4 transition-all duration-200 ease-in-out ${
                      activeTab === tab.key 
                        ? 'text-blue-700 font-semibold' 
                        : 'text-gray-500 hover:text-gray-700 font-medium'
                    }`}
                    role="tab"
                    aria-selected={activeTab === tab.key}
                  >
                    <span className="relative z-10 whitespace-nowrap">{tab.label}</span>
                    {/* Active indicator yang lebih clean */}
                    {activeTab === tab.key && (
                      <div className="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-8 h-1 bg-blue-600 rounded-full" />
                    )}
                  </button>
                ))}
              </div>
              {/* Clean bottom border */}
              <div className="absolute bottom-0 left-0 right-0 h-px bg-gray-100" />
            </nav>
          </div>
        </div>

        {/* Konten Panel - Layout yang lebih rapi */}
        <div className="flex-1 overflow-y-auto p-6">
          <div className="space-y-6">
            {/* Chart utama */}
            <div className="bg-white border border-gray-200 rounded-xl shadow-sm">
              <div className="px-6 pt-6 pb-4">
                <div className="flex items-center justify-between mb-2">
                  <h3 className="text-lg font-semibold text-gray-900">
                    {activeTab === 'sensor' && 'Perkembangan Air Sungai Aktual'}
                    {activeTab === 'riwayat' && 'Riwayat Data'}
                    {activeTab === 'kualitas' && 'Cuaca'}
                    {activeTab === 'cuaca' && 'Aktual & Prediksi'}
                  </h3>
                  {activeTab === 'sensor' && (
                    <div className="flex items-center space-x-2">
                      <div className="flex items-center space-x-1">
                        <div className={`w-3 h-3 rounded-full ${getStatusColor(stationData.status).replace('text-', 'bg-')}`}></div>
                        <span className={`text-sm font-medium ${getStatusColor(stationData.status)}`}>
                          {getStatusText(stationData.status)}
                        </span>
                      </div>
                    </div>
                  )}
                </div>
                <p className="text-sm text-gray-500 mt-1">{stationData.location}</p>
              </div>
              <div className="px-6 pb-6">
                {activeTab === 'sensor' && (() => {
                  const th = getStationThreshold(stationData.name);
                  const maxH = Math.max(th.alert ?? 4, stationData.value + 1);
                  return (
                    <LeveeChart
                      currentLevel={stationData.value}
                      unit={stationData.unit || 'm'}
                      maxHeight={maxH}
                      width={560}
                      height={220}
                      className="w-full"
                    />
                  );
                })()}
                {activeTab === 'riwayat' && (
                  <div className="bg-gray-50 rounded-lg p-4 text-sm text-gray-600">Riwayat data akan tersedia di sini.</div>
                )}
                {activeTab === 'kualitas' && (
                  <div className="space-y-4">
                    {/* Cuaca Saat Ini */}
                    <div className="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-lg p-4 border border-blue-200">
                      <div className="flex items-center justify-between mb-3">
                        <h4 className="text-lg font-semibold text-gray-900">Cuaca Saat Ini</h4>
                        <div className="text-sm text-gray-500">{new Date().toLocaleString('id-ID')}</div>
                      </div>
                      <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div className="text-center">
                          <div className="text-2xl mb-1">🌧️</div>
                          <div className="text-sm text-gray-600">Hujan Ringan</div>
                          <div className="text-lg font-semibold text-blue-600">2.5 mm/jam</div>
                        </div>
                        <div className="text-center">
                          <div className="text-2xl mb-1">🌡️</div>
                          <div className="text-sm text-gray-600">Suhu</div>
                          <div className="text-lg font-semibold text-orange-600">28°C</div>
                        </div>
                        <div className="text-center">
                          <div className="text-2xl mb-1">💨</div>
                          <div className="text-sm text-gray-600">Kecepatan Angin</div>
                          <div className="text-lg font-semibold text-green-600">12 km/jam</div>
                        </div>
                        <div className="text-center">
                          <div className="text-2xl mb-1">💧</div>
                          <div className="text-sm text-gray-600">Kelembaban</div>
                          <div className="text-lg font-semibold text-blue-600">85%</div>
                        </div>
                      </div>
                    </div>

                    {/* Prakiraan Cuaca 24 Jam */}
                    <div className="bg-white border border-gray-200 rounded-lg p-4">
                      <h4 className="text-lg font-semibold text-gray-900 mb-3">Prakiraan Cuaca 24 Jam</h4>
                      <div className="space-y-2">
                        {[
                          { time: '00:00', condition: 'Hujan Ringan', temp: '26°C', rain: '1.2mm', icon: '🌧️' },
                          { time: '06:00', condition: 'Berawan', temp: '25°C', rain: '0mm', icon: '☁️' },
                          { time: '12:00', condition: 'Hujan Sedang', temp: '28°C', rain: '4.5mm', icon: '⛈️' },
                          { time: '18:00', condition: 'Hujan Lebat', temp: '27°C', rain: '8.2mm', icon: '🌧️' },
                        ].map((forecast, index) => (
                          <div key={index} className="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg">
                            <div className="flex items-center gap-3">
                              <span className="text-lg">{forecast.icon}</span>
                              <div>
                                <div className="font-medium text-gray-900">{forecast.time}</div>
                                <div className="text-sm text-gray-600">{forecast.condition}</div>
                              </div>
                            </div>
                            <div className="flex items-center gap-4 text-sm">
                              <div className="text-gray-600">{forecast.temp}</div>
                              <div className="text-blue-600 font-medium">{forecast.rain}</div>
                            </div>
                          </div>
                        ))}
                      </div>
                    </div>

                    {/* Indikator Risiko Banjir */}
                    <div className="bg-white border border-gray-200 rounded-lg p-4">
                      <h4 className="text-lg font-semibold text-gray-900 mb-3">Indikator Risiko Banjir</h4>
                      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div className="text-center p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                          <div className="text-2xl mb-2">⚠️</div>
                          <div className="text-sm font-medium text-yellow-800">Risiko Sedang</div>
                          <div className="text-xs text-yellow-600 mt-1">Curah hujan tinggi</div>
                        </div>
                        <div className="text-center p-3 bg-green-50 border border-green-200 rounded-lg">
                          <div className="text-2xl mb-2">✅</div>
                          <div className="text-sm font-medium text-green-800">Drainase Normal</div>
                          <div className="text-xs text-green-600 mt-1">Sistem berfungsi baik</div>
                        </div>
                        <div className="text-center p-3 bg-blue-50 border border-blue-200 rounded-lg">
                          <div className="text-2xl mb-2">📊</div>
                          <div className="text-sm font-medium text-blue-800">Monitoring Aktif</div>
                          <div className="text-xs text-blue-600 mt-1">Update setiap 15 menit</div>
                        </div>
                      </div>
                    </div>

                    {/* Rekomendasi */}
                    <div className="bg-amber-50 border border-amber-200 rounded-lg p-4">
                      <h4 className="text-lg font-semibold text-amber-800 mb-2">Rekomendasi</h4>
                      <ul className="text-sm text-amber-700 space-y-1">
                        <li>• Waspada terhadap peningkatan intensitas hujan pada sore hari</li>
                        <li>• Pantau terus level air sungai setiap 15 menit</li>
                        <li>• Siapkan rencana evakuasi jika level air mencapai 2.5m</li>
                        <li>• Koordinasi dengan tim darurat jika diperlukan</li>
                      </ul>
                    </div>
                  </div>
                )}
                {activeTab === 'cuaca' && (() => {
                  const actual = chartHistory || [];
                  const predicted = (chartHistory || []).map((v, i) => {
                    const prev = i === 0 ? v : chartHistory[i - 1];
                    const drift = (v - prev) * 0.6;
                    return Math.max(0, v + drift);
                  });
                  return (
                    <DualLineChart
                      actual={actual}
                      predicted={predicted}
                      width={640}
                      height={320}
                      className="w-full"
                      canvasId="dual-line-detail"
                    />
                  );
                })()}
              </div>
            </div>

            {/* Kartu kedua: Konfigurasi Prediksi - hanya untuk tab sensor */}
            {activeTab === 'sensor' && (
              <div className="bg-white border border-gray-200 rounded-xl shadow-sm">
                <div className="px-6 pt-6 pb-4">
                  <h3 className="text-lg font-semibold text-gray-900">Konfigurasi Prediksi</h3>
                  <p className="text-sm text-gray-500 mt-1">Parameter dan threshold untuk prediksi banjir</p>
                </div>
                <div className="px-6 pb-6">
                  {(() => {
                    const th = getStationThreshold(stationData.name);
                    const maxH = Math.max(th.alert ?? 4, stationData.value + 1);
                    return (
                      <LeveeChart
                        currentLevel={stationData.value}
                        unit={stationData.unit || 'm'}
                        maxHeight={maxH}
                        width={560}
                        height={220}
                        className="w-full"
                      />
                    );
                  })()}
                </div>
              </div>
            )}

          </div>
        </div>
      </div>
    </div>
  );
};

export default DetailPanel;
