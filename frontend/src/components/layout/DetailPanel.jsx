import React, { useState, useEffect, useCallback, lazy, Suspense } from "react";
import { getStatusText } from "@/utils/statusUtils";

// Lazy load komponen chart yang berat untuk optimasi bundle
const MonitoringChart = lazy(() => import("@/components/sensors/RechartsDualLineChart"));
const TanggulAktual = lazy(() => import("@/components/sensors/TanggulAktual"));
const PredictionChart = lazy(() => import("@/components/sensors/TanggulPrediksi"));

// Daftar tab konstan agar tidak dibuat ulang setiap render
const DETAIL_TABS = [
    { key: "sensor", label: "Sensor" },
    { key: "cuaca", label: "Cuaca" },
    { key: "monitoring", label: "Monitoring" },
    { key: "riwayat", label: "Riwayat" }
];

// Komponen Status Badge
const StatusBadge = ({ status }) => {
    const getStatusClasses = (status) => {
        switch (status) {
            case "safe":
                return "bg-gradient-to-r from-green-100 to-green-200 text-green-800 border-green-300";
            case "warning":
                return "bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800 border-yellow-300";
            case "alert":
                return "bg-gradient-to-r from-red-100 to-red-200 text-red-800 border-red-300";
            default:
                return "bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 border-gray-300";
        }
    };

    return (
        <div className={`text-sm font-bold px-4 py-2 rounded-xl border-2 shadow-lg ${getStatusClasses(status)}`}>
            {getStatusText(status)}
        </div>
    );
};

/**
 * Komponen panel detail dengan layout two column
 * Menampilkan informasi lengkap tentang stasiun monitoring banjir
 */
const DetailPanel = ({ isOpen, onClose, stationData, chartHistory, isAutoSwitchOn = false }) => {
    const [isVisible, setIsVisible] = useState(false);
    const [isNavbarVisible, setIsNavbarVisible] = useState(false);
    const [activeTab, setActiveTab] = useState("sensor"); // 'sensor' | 'cuaca' | 'monitoring' | 'riwayat'
    const [isTabChanging, setIsTabChanging] = useState(false);
    const [previousTab, setPreviousTab] = useState(null);
    const [isDotAnimating, setIsDotAnimating] = useState(false);


    console.log('=== DETAIL PANEL DEBUG ===');
    console.log('isOpen:', isOpen);
    console.log('stationData:', stationData);
    console.log('isAutoSwitchOn:', isAutoSwitchOn);
    console.log('=== END DETAIL PANEL DEBUG ===');

    // Mengatur animasi visibility saat panel dibuka/ditutup
    useEffect(() => {
        if (isOpen) {
            setTimeout(() => {
                setIsVisible(true);
            }, 10);

            // Delay untuk navbar - muncul setelah panel terbuka
            setTimeout(() => {
                setIsNavbarVisible(true);
            }, 200); // 200ms delay untuk navbar
        } else {
            // Animasi close - geser ke kiri dengan fade out
            setIsVisible(false);
            setIsNavbarVisible(false);
        }
    }, [isOpen]);

    // CSS untuk animasi - menggunakan CSS modules atau styled-components lebih baik
    useEffect(() => {
        const styleId = "detail-panel-animations";

        // Cek apakah style sudah ada
        if (document.getElementById(styleId)) return;

        const style = document.createElement("style");
        style.id = styleId;
        style.textContent = `
      @keyframes tabSlideIn {
        0% {
          transform: translateY(-10px) scale(0.95);
          opacity: 0;
        }
        100% {
          transform: translateY(0) scale(1);
          opacity: 1;
        }
      }
      
      @keyframes tabHover {
        0% {
          transform: scale(1);
        }
        100% {
          transform: scale(1.02);
        }
      }
      
      @keyframes tabActive {
        0% {
          transform: scale(1);
          box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        100% {
          transform: scale(1.05);
          box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3);
        }
      }
      
      .tab-slide-in {
        animation: tabSlideIn 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
      }
      
      .tab-hover {
        animation: tabHover 0.2s cubic-bezier(0.4, 0, 0.2, 1) forwards;
      }
      
      .tab-active {
        animation: tabActive 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
      }
    `;
        document.head.appendChild(style);

        return () => {
            const existingStyle = document.getElementById(styleId);
            if (existingStyle) {
                document.head.removeChild(existingStyle);
            }
        };
    }, []);

    // Auto close detail panel saat auto switch berjalan
    useEffect(() => {
        if (isAutoSwitchOn && isOpen) {
            handleClose();
        }
    }, [isAutoSwitchOn]);

    // Handler untuk close dengan animasi - menggunakan useCallback untuk optimasi
    const handleClose = useCallback(() => {
        setIsVisible(false);
        setTimeout(() => {
            onClose();
        }, 300); // Sama dengan durasi animasi
    }, [onClose]);

    // Handler untuk tab click dengan animasi clean - menggunakan useCallback untuk optimasi
    const handleTabClick = useCallback(
        (tabKey) => {
            if (isTabChanging || activeTab === tabKey) return; // Prevent multiple clicks during transition

            setIsTabChanging(true);
            setIsDotAnimating(true);
            setPreviousTab(activeTab);

            // Animasi underline menutup dan muncul underline baru
            setTimeout(() => {
                setActiveTab(tabKey);

                // Reset animasi state setelah transisi selesai
                setTimeout(() => {
                    setIsTabChanging(false);
                    setIsDotAnimating(false);
                    setPreviousTab(null);
                }, 400); // 400ms untuk animasi underline baru
            }, 400); // 400ms delay animasi underline 
        },
        [isTabChanging, activeTab]
    );
    if (!isOpen) return null;

    // Fallback jika stationData tidak ada
    if (!stationData) {
        return (
            <div
                className={`fixed top-20 left-96 right-150 bottom-0 z-[50] bg-white shadow-2xl transform transition-all duration-300 ease-in-out ${
                    isVisible ? "translate-x-0 opacity-100" : "-translate-x-full opacity-0"
                }`}
            >
                <div className="h-full flex items-center justify-center">
                    <div className="text-center">
                        <div className="text-gray-500 text-lg">Tidak ada data stasiun</div>
                        <div className="text-gray-400 text-sm mt-2">Pilih stasiun untuk melihat detail</div>
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className={`fixed top-20 left-96 right-80 bottom-0 z-50 bg-white shadow-2xl rounded-tr-lg transform transition-all duration-300 ${
            isVisible ? "translate-x-0 opacity-100" : "-translate-x-full opacity-0"
        }`}>
            <div className="h-full flex flex-col">
                <div className="rounded-tr-lg p-4 flex-shrink-0 border-b border-gray-200 bg-gray-50/50">
 
                    <div className="flex items-center justify-between">
                     <div className="flex items-center gap-3">
                            <button
                                onClick={handleClose}
                                className="p-2 rounded-full transition-all duration-200 group focus:outline-none hover:bg-gray-100 hover:shadow-md"
                                aria-label="Kembali"
                            >
                                <svg
                                    className="w-6 h-6 text-gray-600 group-hover:text-gray-800 transition-colors duration-300"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2.5}
                                        d="M11 17l-5-5m0 0l5-5m-5 5h14"
                                    />
                                </svg>
                            </button>
                            <div className="min-w-0">
                                <h3 className="text-2xl font-bold text-gray-900 tracking-tight">
                                    Detail Informasi
                                </h3>
                                <p className="text-base text-gray-700 mt-1 font-semibold">
                                    {stationData.name}
                                </p>
                            </div>
                        </div>

                        {/* Bagian kanan - status info */}
                        <div className="text-right">
                            <div className="flex items-center justify-end gap-3 mb-2">
                                {isAutoSwitchOn && (
                                    <div className="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 rounded-xl text-sm font-semibold border border-blue-300 shadow-lg">
                                        <div className="w-2.5 h-2.5 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full animate-pulse"></div>
                                        Auto Switch
                                    </div>
                                )}
                                <StatusBadge status={stationData.status} />
                            </div>
                            <div className="text-sm text-gray-600 font-semibold">
                                Update {new Date().toLocaleTimeString("id-ID")}
                            </div>
                        </div>
                    </div>
                    <div
                        className={`mt-6 transition-all duration-500 ease-out ${
                            isNavbarVisible ? "opacity-100 translate-y-0" : "opacity-0 translate-y-4"
                        }`}
                    >
                        <nav className="relative">
                            <div className="bg-white/90 backdrop-blur-md border border-gray-200/50 rounded-2xl p-4 shadow-lg shadow-gray-200/50">
                                <div className="grid grid-cols-2 sm:grid-cols-4 gap-5">
                                    {DETAIL_TABS.map((tab) => (
                                        <button
                                            key={tab.key}
                                            onClick={() => handleTabClick(tab.key)}
                                            disabled={isTabChanging}
                                            className={`relative w-full px-4 py-3 mx-1 rounded-xl transition-all duration-300 ease-out group flex items-center justify-center ${
                                                activeTab === tab.key
                                                    ? "bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg shadow-blue-500/30 transform scale-105 tab-active"
                                                    : "text-gray-600 hover:text-gray-800 hover:bg-gray-50/80 hover:shadow-md hover:shadow-gray-200/50 hover:tab-hover"
                                            } ${isTabChanging ? "opacity-70 cursor-wait" : "cursor-pointer"} tab-slide-in`}
                                            role="tab"
                                            aria-selected={activeTab === tab.key}
                                        >
                                            <span className="relative z-10 whitespace-nowrap text-sm sm:text-base font-semibold leading-tight">
                                                {tab.label}
                                            </span>
                                            
                                            {activeTab === tab.key && (
                                                <div className="absolute inset-0 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg" />
                                            )}
                                            
                                            {activeTab !== tab.key && (
                                                <div className="absolute inset-0 bg-gradient-to-r from-gray-100 to-gray-50 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-200" />
                                            )}
                                            
                                            {isTabChanging && activeTab === tab.key && (
                                                <div className="absolute inset-0 bg-gradient-to-r from-blue-400 to-blue-500 rounded-xl animate-pulse" />
                                            )}
                                        </button>
                                    ))}
                                </div>
                            </div>
                        </nav>
                    </div>
                </div>
                <div className="flex-1 overflow-y-auto p-6">
                    <div className={`space-y-6 transition-all duration-500 ease-out ${
                        isTabChanging ? "opacity-50 scale-95" : "opacity-100 scale-100"
                    }`}>
                        {/* Chart utama - hanya untuk tab selain sensor */}
                        {activeTab !== "sensor" && (
                            <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                                <div className="px-8 pt-8 pb-6 bg-gray-50/50 border-b border-gray-200">
                                    <div className="flex items-center justify-between mb-3">
                                        <h3 className="text-2xl font-bold text-gray-900 tracking-tight">
                                            {activeTab === "riwayat" && "Riwayat Data"}
                                            {activeTab === "cuaca" && "Cuaca"}
                                            {activeTab === "monitoring" && "Aktual & Prediksi"}
                                        </h3>
                                    </div>
                                    <p className="text-base text-gray-600 font-semibold">
                                        {stationData.location}
                                    </p>
                                </div>
                                <div className="px-8 pb-8">
                                    {activeTab === "riwayat" && (
                                        <div className="bg-gray-50 rounded-lg p-4 text-sm text-gray-600">
                                            Riwayat data akan tersedia di sini.
                                        </div>
                                    )}
                                    {activeTab === "cuaca" && (
                                        <div className="space-y-4">
                                            {/* Cuaca Saat Ini */}
                                            <div className="mb-3">
                                                <h4 className="text-lg font-semibold text-gray-900 mb-4">
                                                    Cuaca Saat Ini
                                                </h4>
                                                <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                                                    <div className="text-center">
                                                        <div className="flex justify-center mb-2">
                                                            <svg
                                                                className="w-8 h-8 text-blue-500"
                                                                fill="currentColor"
                                                                viewBox="0 0 24 24"
                                                            >
                                                                <path d="M19.35 10.04A7.49 7.49 0 0 0 12 4C9.11 4 6.6 5.64 5.35 8.04A5.994 5.994 0 0 0 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM14 13v4h-4v-4H7l5-5 5 5h-3z" />
                                                            </svg>
                                                        </div>
                                                        <div className="text-sm text-gray-600">Hujan Ringan</div>
                                                        <div className="text-lg font-semibold text-blue-600">
                                                            2.5 mm/jam
                                                        </div>
                                                    </div>
                                                    <div className="text-center">
                                                        <div className="flex justify-center mb-2">
                                                            <svg
                                                                className="w-8 h-8 text-orange-500"
                                                                fill="currentColor"
                                                                viewBox="0 0 24 24"
                                                            >
                                                                <path d="M15 13V5c0-1.66-1.34-3-3-3S9 3.34 9 5v8c-1.21.91-2 2.37-2 4 0 2.76 2.24 5 5 5s5-2.24 5-5c0-1.63-.79-3.09-2-4zm-4-2V5c0-.55.45-1 1-1s1 .45 1 1v6h-2z" />
                                                            </svg>
                                                        </div>
                                                        <div className="text-sm text-gray-600">Suhu</div>
                                                        <div className="text-lg font-semibold text-orange-600">
                                                            28°C
                                                        </div>
                                                    </div>
                                                    <div className="text-center">
                                                        <div className="flex justify-center mb-2">
                                                            <svg
                                                                className="w-8 h-8 text-green-500"
                                                                fill="currentColor"
                                                                viewBox="0 0 24 24"
                                                            >
                                                                <path d="M2 17h20v2H2zm1.15-4.05L4 12l.85.95L6 11.5l.85.95L8 11l-.85-.95L6 12.5l-.85-.95L4 13l-.85-.95L2 12l.85-.95L4 11.5l.85.95L6 11l-.85-.95L4 12.5l-.85-.95L2 13zm0-8.95L4 3l.85.95L6 2.5l.85.95L8 2l-.85-.95L6 3.5 5.15 2.5 4 3l-.85-.95L2 3.05z" />
                                                            </svg>
                                                        </div>
                                                        <div className="text-sm text-gray-600">Kecepatan Angin</div>
                                                        <div className="text-lg font-semibold text-green-600">
                                                            12 km/jam
                                                        </div>
                                                    </div>
                                                    <div className="text-center">
                                                        <div className="flex justify-center mb-2">
                                                            <svg
                                                                className="w-8 h-8 text-blue-500"
                                                                fill="currentColor"
                                                                viewBox="0 0 24 24"
                                                            >
                                                                <path d="M12 2C9.24 2 7 4.24 7 7c0 1.5.62 2.85 1.61 3.82L12 14.17l3.39-3.35C16.38 9.85 17 8.5 17 7c0-2.76-2.24-5-5-5zm0 7.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 4.5 12 4.5s2.5 1.12 2.5 2.5S13.38 9.5 12 9.5zM12 16c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                                                            </svg>
                                                        </div>
                                                        <div className="text-sm text-gray-600">Kelembaban</div>
                                                        <div className="text-lg font-semibold text-blue-600">85%</div>
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Indikator Risiko Banjir */}
                                            <div className="bg-white rounded-lg p-4 shadow-sm">
                                                <h4 className="text-lg font-semibold text-gray-900 mb-3">
                                                    Indikator Risiko Banjir
                                                </h4>
                                                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                    <div className="text-center p-3 bg-yellow-50 rounded-lg shadow-sm">
                                                        <div className="flex justify-center mb-2">
                                                            <svg
                                                                className="w-6 h-6 text-yellow-600"
                                                                fill="currentColor"
                                                                viewBox="0 0 24 24"
                                                            >
                                                                <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" />
                                                            </svg>
                                                        </div>
                                                        <div className="text-sm font-medium text-yellow-800">
                                                            Risiko Sedang
                                                        </div>
                                                        <div className="text-xs text-yellow-600 mt-1">
                                                            Curah hujan tinggi
                                                        </div>
                                                    </div>
                                                    <div className="text-center p-3 bg-green-50 rounded-lg shadow-sm">
                                                        <div className="flex justify-center mb-2">
                                                            <svg
                                                                className="w-6 h-6 text-green-600"
                                                                fill="currentColor"
                                                                viewBox="0 0 24 24"
                                                            >
                                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                                            </svg>
                                                        </div>
                                                        <div className="text-sm font-medium text-green-800">
                                                            Drainase Normal
                                                        </div>
                                                        <div className="text-xs text-green-600 mt-1">
                                                            Sistem berfungsi baik
                                                        </div>
                                                    </div>
                                                    <div className="text-center p-3 bg-blue-50 rounded-lg shadow-sm">
                                                        <div className="flex justify-center mb-2">
                                                            <svg
                                                                className="w-6 h-6 text-blue-600"
                                                                fill="currentColor"
                                                                viewBox="0 0 24 24"
                                                            >
                                                                <path d="M3 3v18h18v-2H5V3H3zm4 12h2v2H7v-2zm0-4h2v2H7v-2zm0-4h2v2H7V7zm4 8h2v2h-2v-2zm0-4h2v2h-2v-2zm0-4h2v2h-2V7zm4 8h2v2h-2v-2zm0-4h2v2h-2v-2zm0-4h2v2h-2V7z" />
                                                            </svg>
                                                        </div>
                                                        <div className="text-sm font-medium text-blue-800">
                                                            Monitoring Aktif
                                                        </div>
                                                        <div className="text-xs text-blue-600 mt-1">
                                                            Update setiap 15 menit
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Rekomendasi */}
                                            <div className="bg-amber-50 rounded-lg p-4 shadow-sm">
                                                <h4 className="text-lg font-semibold text-amber-800 mb-2">
                                                    Rekomendasi
                                                </h4>
                                                <ul className="text-sm text-amber-700 space-y-1">
                                                    <li>
                                                        • Waspada terhadap peningkatan intensitas hujan pada sore hari
                                                    </li>
                                                    <li>• Pantau terus level air sungai setiap 15 menit</li>
                                                    <li>• Siapkan rencana evakuasi jika level air mencapai 2.5m</li>
                                                    <li>• Koordinasi dengan tim darurat jika diperlukan</li>
                                                </ul>
                                            </div>
                                        </div>
                                    )}
                                    {activeTab === "monitoring" && (
                                        <Suspense
                                            fallback={
                                                <div className="w-full h-[320px] bg-gray-100 rounded-lg animate-pulse flex items-center justify-center">
                                                    <span className="text-gray-500">Loading chart...</span>
                                                </div>
                                            }
                                        >
                                            <MonitoringChart
                                                actualData={chartHistory || []}
                                                predictedData={chartHistory ? chartHistory.slice(1) : []}
                                                width="100%"
                                                height={320}
                                            />
                                        </Suspense>
                                    )}
                                </div>
                            </div>
                        )}
                        
                        {/* Tab Sensor - Perkembangan Air Sungai Aktual */}
                        {activeTab === "sensor" && (
                            <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden p-4">
                                <Suspense
                                    fallback={
                                        <div className="w-full h-[220px] bg-gray-100 rounded-lg animate-pulse flex items-center justify-center">
                                            <span className="text-gray-500">Loading chart...</span>
                                        </div>
                                    }
                                >
                                    <TanggulAktual
                                        stationData={stationData}
                                        chartHistory={chartHistory}
                                        width={560}
                                        height={220}
                                        className="w-full"
                                    />
                                </Suspense>
                            </div>
                        )}

                        {/* Kartu kedua: Konfigurasi Prediksi - hanya untuk tab sensor */}
                        {activeTab === "sensor" && (
                            <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden p-4">
                                <Suspense
                                    fallback={
                                        <div className="w-full h-[220px] bg-gray-100 rounded-lg animate-pulse flex items-center justify-center">
                                            <span className="text-gray-500">Loading chart...</span>
                                        </div>
                                    }
                                >
                                    <PredictionChart
                                        stationData={stationData}
                                        chartHistory={chartHistory}
                                        width={560}
                                        height={220}
                                        className="w-full"
                                    />
                                </Suspense>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
};

export default DetailPanel;
