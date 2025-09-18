import React, { useState, useEffect, useMemo } from 'react';
import Chart from '../ui/Chart';
import SidebarTemplate from '../SidebarTemplate';
import { getStatusColor, getStatusBgColor, getStatusText } from '../../utils/statusUtils';

const StationDetail = ({ selectedStation, onClose, tickerData, showArrow = false, onArrowToggle, isDetailPanelOpen = false, onCloseDetailPanel }) => {
  const [stationData, setStationData] = useState(null);
  
  const chartHistory = useMemo(() => {
    return stationData?.history || [];
  }, [stationData]);
  
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
      subtitle={stationData.location}
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
        
        {/* Divider */}
        <div className="border-t border-gray-200"></div>
        
        {/* Chart Section */}
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
        
        {/* Statistics Grid */}
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
        
        {/* Additional Info */}
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
    </SidebarTemplate>
  );
};

export default StationDetail;
