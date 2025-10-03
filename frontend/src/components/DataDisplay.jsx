import React from "react";
import { useAppContext } from "@/hooks/useAppContext";

const DataDisplay = () => {
    const { provinces, cities, devices, loading, error } = useAppContext();

    if (loading) {
        return (
            <div className="p-4 bg-blue-50 rounded-lg">
                <div className="flex items-center justify-center">
                    <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <span className="ml-2 text-blue-600">Loading data from backend...</span>
                </div>
            </div>
        );
    }

    if (error) {
        return (
            <div className="p-4 bg-red-50 border border-red-200 rounded-lg">
                <h3 className="text-red-800 font-semibold">Error loading data:</h3>
                <p className="text-red-600">{error}</p>
            </div>
        );
    }

    return (
        <div className="p-4 space-y-4">
            <div className="bg-green-50 border border-green-200 rounded-lg p-4">
                <h2 className="text-green-800 font-bold text-xl mb-4">
                    🎉 Backend Integration Successful!
                </h2>
                <p className="text-green-700">
                    Data is successfully loading from Laravel backend API at http://localhost:8000
                </p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                {/* Provinces Card */}
                <div className="bg-white shadow-md rounded-lg p-4 border">
                    <h3 className="font-semibold text-lg mb-2 text-blue-600">
                        📍 Provinces ({provinces.length})
                    </h3>
                    <div className="space-y-1">
                        {provinces.map((province) => (
                            <div key={province.id} className="text-sm text-gray-600 border-b pb-1">
                                <strong>{province.provinces_name}</strong>
                                <br />
                                <span className="text-xs text-gray-500">Code: {province.provinces_code}</span>
                            </div>
                        ))}
                    </div>
                </div>

                {/* Cities Card */}
                <div className="bg-white shadow-md rounded-lg p-4 border">
                    <h3 className="font-semibold text-lg mb-2 text-green-600">
                        🏙️ Cities ({cities.length})
                    </h3>
                    <div className="space-y-1">
                        {cities.map((city) => (
                            <div key={city.id} className="text-sm text-gray-600 border-b pb-1">
                                <strong>{city.cities_name}</strong>
                                <br />
                                <span className="text-xs text-gray-500">Code: {city.cities_code}</span>
                            </div>
                        ))}
                    </div>
                </div>

                {/* Devices Card */}
                <div className="bg-white shadow-md rounded-lg p-4 border">
                    <h3 className="font-semibold text-lg mb-2 text-purple-600">
                        📱 Devices ({devices.length})
                    </h3>
                    {devices.length > 0 ? (
                        <div className="space-y-1">
                            {devices.slice(0, 5).map((device) => (
                                <div key={device.id} className="text-sm text-gray-600 border-b pb-1">
                                    <strong>{device.name}</strong>
                                    <br />
                                    <span className="text-xs text-gray-500">
                                        Code: {device.code} | Status: {device.status}
                                    </span>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <p className="text-gray-500 text-sm">No devices available</p>
                    )}
                </div>
            </div>

            <div className="bg-gray-50 border rounded-lg p-4">
                <h3 className="font-semibold text-gray-700 mb-2">🔧 API Endpoints Available:</h3>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-600">
                    <div>• GET /api/public/provinces</div>
                    <div>• GET /api/public/cities</div>
                    <div>• GET /api/public/regencies</div>
                    <div>• GET /api/public/villages</div>
                    <div>• GET /api/public/devices</div>
                    <div>• GET /api/public/sensors</div>
                </div>
                <p className="text-xs text-gray-500 mt-2">
                    Total: 165+ API endpoints available (authentication required for full access)
                </p>
            </div>
        </div>
    );
};

export default DataDisplay;