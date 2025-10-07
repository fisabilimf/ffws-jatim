import React, { useState, useEffect } from "react";
import SidebarTemplate from "@components/layout/SidebarTemplate";
import { getStatusColor, getStatusBgColor, getStatusText } from "@/utils/statusUtils";

const StationDetail = ({
    selectedStation,
    onClose,
    devicesData,
    showArrow = false,
    onArrowToggle,
    isDetailPanelOpen = false,
    onCloseDetailPanel,
}) => {
    const [stationData, setStationData] = useState(null);

    useEffect(() => {
        console.log('=== STATION DETAIL DEBUG ===');
        console.log('selectedStation:', selectedStation);
        console.log('devicesData length:', devicesData?.length || 0);
        console.log('devicesData sample:', devicesData?.slice(0, 2));
        
        if (selectedStation && devicesData) {
            const foundStation = devicesData.find((station) => station.id === selectedStation.id);
            console.log('Found station:', foundStation);
            
            if (foundStation) {
                setStationData(foundStation);
            } else {
                console.warn('Station not found in devicesData. Selected station ID:', selectedStation.id);
                console.log('Available station IDs:', devicesData.map(s => s.id));
            }
        }
        console.log('=== END STATION DETAIL DEBUG ===');
    }, [selectedStation, devicesData]);

    if (!selectedStation || !stationData) {
        return null;
    }

    return (
        <SidebarTemplate
            isOpen={!!selectedStation}
            onClose={onClose}
            title={stationData.name || stationData.device_name || stationData.station_name || 'Unknown Station'}
            showArrow={showArrow}
            onArrowToggle={onArrowToggle}
            isDetailPanelOpen={isDetailPanelOpen}
            onCloseDetailPanel={onCloseDetailPanel}
        >
            <div className="p-4 space-y-6 pb-6">
                {/* Status Card */}
                <div className={`p-4 rounded-lg border-2 ${getStatusBgColor(stationData.status || 'safe')}`}>
                    <div className="flex items-center justify-between">
                        <div>
                            <p className="text-sm text-gray-600 mb-1">Status Saat Ini</p>
                            <p className={`text-xl font-bold ${getStatusColor(stationData.status || 'safe')}`}>
                                {getStatusText(stationData.status || 'safe')}
                            </p>
                        </div>
                        <div className="text-right">
                            <p className="text-3xl font-bold text-gray-900">
                                {stationData.value ? stationData.value.toFixed(1) : 'N/A'}
                            </p>
                            <p className="text-sm text-gray-500">{stationData.unit || 'm'}</p>
                        </div>
                    </div>
                </div>
            </div>
        </SidebarTemplate>
    );
};

export default StationDetail;
