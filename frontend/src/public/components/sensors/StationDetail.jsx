import React, { useState, useEffect } from 'react';
import SidebarTemplate from '../SidebarTemplate';
import { getStatusColor, getStatusBgColor, getStatusText } from '../../../utils/statusUtils';

const StationDetail = ({ selectedStation, onClose, tickerData, showArrow = false, onArrowToggle, isDetailPanelOpen = false, onCloseDetailPanel }) => {
  const [stationData, setStationData] = useState(null);
  
  useEffect(() => {
    if (selectedStation && tickerData) {
      const foundStation = tickerData.find(station => station.id === selectedStation.id);
      if (foundStation) {
        setStationData(foundStation);
      }
    }
  }, [selectedStation, tickerData]);

  
  if (!selectedStation || !stationData) {
    return null;
  }
  
  return (
    <SidebarTemplate
      isOpen={!!selectedStation}
      onClose={onClose}
      title={stationData.name}
      showArrow={showArrow}
      onArrowToggle={onArrowToggle}
      isDetailPanelOpen={isDetailPanelOpen}
      onCloseDetailPanel={onCloseDetailPanel}
    >
      <div className="p-4 space-y-6 pb-6">
        {/* Status Card */}
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
        
      </div>
    </SidebarTemplate>
  );
};

export default StationDetail;
