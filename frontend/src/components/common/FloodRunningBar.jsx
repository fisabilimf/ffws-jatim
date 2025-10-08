import React, { useState, useEffect, lazy, Suspense } from "react";
import { useDevices } from "@/hooks/useAppContext";

// Lazy load Chart component untuk optimasi bundle
const Chart = lazy(() => import("@/components/common/Chart"));

const FloodRunningBar = ({ onStationSelect, onMapFocus, isSidebarOpen = false }) => {
    const [tickerData, setTickerData] = useState([]);

    // Ambil devices dari context dan bangun ticker dari data API saja (tanpa ticker acak)
    const { devices } = useDevices();

    useEffect(() => {
        if (!Array.isArray(devices) || devices.length === 0) {
            setTickerData([]);
            return;
        }

        setTickerData((prev) => {
            return devices.map((device) => {
                const id = device.id;
                const name = device.name || device.device_name || device.station_name || "Unknown";
                const unit = device.unit || "m";
                const status = device.status || "unknown";
                const latitude = device.latitude;
                const longitude = device.longitude;
                const coordinates = (latitude && longitude)
                    ? [parseFloat(longitude), parseFloat(latitude)]
                    : undefined;

                const prevItem = prev.find((p) => p.id === id);
                const apiValue = typeof device.value === "number"
                    ? device.value
                    : (typeof device.value === "string" && device.value.trim() !== "" && !isNaN(parseFloat(device.value))
                        ? parseFloat(device.value)
                        : null);

                const value = apiValue !== null ? apiValue : (prevItem ? prevItem.value : null);
                const history = value !== null
                    ? [
                        ...((prevItem && Array.isArray(prevItem.history)) ? prevItem.history.slice(-19) : []),
                        value
                    ]
                    : ((prevItem && Array.isArray(prevItem.history)) ? prevItem.history : []);

                return {
                    id,
                    name,
                    value,
                    unit,
                    location: device.river_basin ? device.river_basin.name : "Unknown",
                    coordinates,
                    status,
                    history,
                };
            });
        });
    }, [devices]);

    // Removed onDataUpdate dependency - no longer needed

    const getStatusColor = (status) => {
        switch (status) {
            case "safe":
                return "text-green-500";
            case "warning":
                return "text-yellow-500";
            case "alert":
                return "text-red-500";
            default:
                return "text-gray-400";
        }
    };

    const getStatusBgColor = (status) => {
        switch (status) {
            case "safe":
                return "bg-green-500";
            case "warning":
                return "bg-yellow-500";
            case "alert":
                return "bg-red-500";
            default:
                return "bg-gray-500";
        }
    };

    const formatValue = (value) => {
        return new Intl.NumberFormat("id-ID", {
            minimumFractionDigits: 1,
            maximumFractionDigits: 1,
        }).format(value);
    };

    // Hitung durasi animasi berdasarkan jumlah data
    if (tickerData.length === 0) {
        return null; // atau tampilkan placeholder jika diperlukan
    }
    const animationDuration = tickerData.length * 4; // 4 detik per item

    return (
        <div
            className={`fixed top-4 z-[70] transition-all duration-300 ease-in-out left-[calc(368px+2rem)] flood-running-bar`}
            // className={`fixed top-4 z-[70] transition-all duration-300 ease-in-out left-[calc(368px+2rem)] right-[calc(1rem+48px+1rem)] sm:right-[calc(1.5rem+48px+1rem)] flood-running-bar`}
        >
            <div className="w-full">
                <div className="overflow-hidden bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-1.5 sm:p-2">
                    <div
                        className="flex space-x-2 sm:space-x-2 whitespace-nowrap"
                        style={{
                            animation: `infiniteScroll ${animationDuration}s linear infinite`,
                            width: "fit-content",
                        }}
                    >
                        {/* Konten pertama */}
                        {tickerData.map((item) => (
                            <div
                                key={`first-${item.id}`}
                                className="flex items-center space-x-1.5 sm:space-x-2 rounded-lg px-1.5 sm:px-2 py-1 sm:py-1.5 min-w-max border border-gray-200"
                            >
                                <div
                                    className={`w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full ${getStatusBgColor(
                                        item.status
                                    )}`}
                                ></div>
                                <span className="text-xs text-gray-700 font-medium truncate max-w-12 sm:max-w-16">
                                    {item.name.replace("Stasiun ", "")}
                                </span>
                                <div className="flex items-center space-x-0.5 sm:space-x-1">
                                    <span className="text-xs font-bold text-gray-900">{formatValue(item.value)}</span>
                                    <span className="text-xs text-gray-500">{item.unit}</span>
                                </div>
                                <Suspense fallback={<div className="w-12 h-6 bg-gray-200 rounded animate-pulse"></div>}>
                                    <Chart
                                        data={item.history}
                                        width={48}
                                        height={22}
                                        showTooltip={false}
                                        miniMode={true}
                                        status={item.status}
                                        canvasId={`chart-first-${item.id}`}
                                        className="w-12 h-6 rounded"
                                    />
                                </Suspense>
                            </div>
                        ))}

                        {/* Konten duplikat untuk seamless scrolling */}
                        {tickerData.map((item) => (
                            <div
                                key={`second-${item.id}`}
                                className="flex items-center space-x-1.5 sm:space-x-2 rounded-lg px-1.5 sm:px-2 py-1 sm:py-1.5 min-w-max border border-gray-200"
                            >
                                <div
                                    className={`w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full ${getStatusBgColor(
                                        item.status
                                    )}`}
                                ></div>
                                <span className="text-xs text-gray-700 font-medium truncate max-w-12 sm:max-w-16">
                                    {item.name.replace("Stasiun ", "")}
                                </span>
                                <div className="flex items-center space-x-0.5 sm:space-x-1">
                                    <span className="text-xs font-bold text-gray-900">{formatValue(item.value)}</span>
                                    <span className="text-xs text-gray-500">{item.unit}</span>
                                </div>
                                <Suspense fallback={<div className="w-12 h-6 bg-gray-200 rounded animate-pulse"></div>}>
                                    <Chart
                                        data={item.history}
                                        width={48}
                                        height={22}
                                        showTooltip={false}
                                        miniMode={true}
                                        status={item.status}
                                        canvasId={`chart-second-${item.id}`}
                                        className="w-12 h-6 rounded"
                                    />
                                </Suspense>
                            </div>
                        ))}
                    </div>
                </div>
            </div>

            <style>{`
                @keyframes infiniteScroll {
                    0% {
                        transform: translateX(0);
                    }
                    100% {
                        transform: translateX(-50%);
                    }
                }
                
                .flood-running-bar {
                    right: calc(1.5rem + 40px + 2rem);
                }
                
                @media (min-width: 640px) {
                    .flood-running-bar {
                        right: calc(5rem); 
                    }
                }
            `}</style>
        </div>
    );
};

export default FloodRunningBar;
