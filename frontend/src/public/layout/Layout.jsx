import React, { useState, useRef } from 'react'
import Dashboard from '../pages/Dashboard'
import GoogleMapsSearchbar from '../components/ui/GoogleMapsSearchbar'
import MapboxMap from '../components/devices/MapboxMap'
import FloatingLegend from '../components/ui/FloatingLegend'
import FloodRunningBar from '../components/predictions/FloodRunningBar'
import StationDetail from '../components/sensors/StationDetail'
import DetailPanel from '../components/sensors/DetailPanel'
import AutoSwitchToggle from '../components/ui/AutoSwitchToggle'

const Layout = ({ children }) => {
  const [tickerData, setTickerData] = useState(null)
  const [searchQuery, setSearchQuery] = useState('')
  const [selectedStation, setSelectedStation] = useState(null)
  const [isSidebarOpen, setIsSidebarOpen] = useState(false)
  const [isDetailPanelOpen, setIsDetailPanelOpen] = useState(false)
  const [currentStationIndex, setCurrentStationIndex] = useState(0)
  const [isAutoSwitchOn, setIsAutoSwitchOn] = useState(false)
  const mapRef = useRef(null)

  const handleSearch = (query) => {
    setSearchQuery(query)
  }

  const handleStationSelect = (station) => {
    setSelectedStation(station)
    setIsSidebarOpen(true)
  }

  const handleAutoSwitchToggle = (isOn) => {
    setIsAutoSwitchOn(isOn)
    // If auto switch is turned off, close sidebar
    if (!isOn) {
      setIsSidebarOpen(false)
      setSelectedStation(null)
    } else {
      // Jika auto switch diaktifkan, tutup detail panel
      setIsDetailPanelOpen(false)
    }
  }

  const handleCloseStationDetail = () => {
    setSelectedStation(null)
    setIsSidebarOpen(false)
  }

  const handleToggleDetailPanel = () => {
    if (isDetailPanelOpen) {
      // Jika panel terbuka, tutup dengan animasi
      handleCloseDetailPanel()
    } else {
      // Jika panel tertutup, buka langsung
      setIsDetailPanelOpen(true)
    }
  }

  const handleCloseDetailPanel = () => {
    setIsDetailPanelOpen(false)
  }


  const handleAutoSwitch = (station, index) => {
    setCurrentStationIndex(index)
    setSelectedStation(station)
    // Auto open sidebar when auto switching
    setIsSidebarOpen(true)
  }

  const handleStationChange = (station, index) => {
    setCurrentStationIndex(index)
    // Buka panel detail saat auto switch
    setSelectedStation(station)
    setIsSidebarOpen(true)
    // Tutup detail panel jika auto switch sedang berjalan
    if (isAutoSwitchOn) {
      setIsDetailPanelOpen(false)
    }
    // Trigger map auto switch
    if (window.mapboxAutoSwitch) {
      window.mapboxAutoSwitch(station, index)
    }
  }

  return (
    <div className="h-screen bg-gray-50 relative overflow-hidden">
      {/* Full Screen Map */}
      <div className="w-full h-full relative z-0">
        <MapboxMap 
          ref={mapRef}
          tickerData={tickerData} 
          onStationSelect={handleStationSelect}
          onAutoSwitch={handleAutoSwitch}
          isAutoSwitchOn={isAutoSwitchOn}
          onCloseSidebar={() => {
            if (!isAutoSwitchOn) {
              setIsSidebarOpen(false)
              setSelectedStation(null)
            }
          }}
        />
      </div>
      
      {/* Google Maps Style Searchbar - fixed position */}
      <div className="absolute top-4 left-4 right-4 z-20">
        <div className="max-w-2xl mx-auto">
          <GoogleMapsSearchbar 
            onSearch={handleSearch}
            placeholder="Cari stasiun monitoring banjir..."
          />
        </div>
      </div>
      
      {/* Flood Running Bar */}
      <FloodRunningBar 
        onDataUpdate={setTickerData}
        onStationSelect={handleStationSelect}
        isSidebarOpen={isSidebarOpen}
      />
      
      {/* Bottom-right stack container for AutoSwitch and Legend */}
      <div className="absolute bottom-2 right-2 sm:bottom-4 sm:right-4 z-10 space-y-2">
        {/* Auto Switch Card */}
        <div className="w-64 bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-2 sm:p-4 border border-gray-200">
          <AutoSwitchToggle
            tickerData={tickerData}
            onStationChange={handleStationChange}
            currentStationIndex={currentStationIndex}
            onAutoSwitchToggle={handleAutoSwitchToggle}
          />
        </div>

        {/* Floating Legend */}
        <FloatingLegend />
      </div>
      
      {/* Station Detail Modal */}
      <StationDetail 
        selectedStation={selectedStation}
        onClose={handleCloseStationDetail}
        tickerData={tickerData}
        isAutoSwitchOn={isAutoSwitchOn}
        showArrow={true}
        onArrowToggle={handleToggleDetailPanel}
        isDetailPanelOpen={isDetailPanelOpen}
        onCloseDetailPanel={handleCloseDetailPanel}
      />

      {/* Detail Panel */}
      <DetailPanel 
        isOpen={isDetailPanelOpen}
        onClose={handleCloseDetailPanel}
        stationData={selectedStation}
        chartHistory={selectedStation?.history || []}
        isAutoSwitchOn={isAutoSwitchOn}
      />

      
      {/* Main content - hidden in full screen mode */}
      <main className="hidden">
        {children || <Dashboard />}
      </main>
    </div>
  )
}

export default Layout
