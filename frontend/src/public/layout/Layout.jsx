import React, { useState, useRef } from 'react'
import Dashboard from '../pages/Dashboard'
import GoogleMapsSearchbar from '../components/GoogleMapsSearchbar'
import MapboxMap from '../components/MapboxMap'
import FloatingLegend from '../components/FloatingLegend'
import FloodRunningBar from '../components/FloodRunningBar'
import StationDetail from '../components/StationDetail'
import AutoSwitchToggle from '../components/AutoSwitchToggle'

const Layout = ({ children }) => {
  const [tickerData, setTickerData] = useState(null)
  const [searchQuery, setSearchQuery] = useState('')
  const [selectedStation, setSelectedStation] = useState(null)
  const [isSidebarOpen, setIsSidebarOpen] = useState(false)
  const [currentStationIndex, setCurrentStationIndex] = useState(0)
  const mapRef = useRef(null)

  const handleSearch = (query) => {
    setSearchQuery(query)
    // Implementasi pencarian bisa ditambahkan di sini
    console.log('Searching for:', query)
  }

  const handleStationSelect = (station) => {
    setSelectedStation(station)
    setIsSidebarOpen(true)
  }

  const handleCloseStationDetail = () => {
    setSelectedStation(null)
    setIsSidebarOpen(false)
  }

  const handleAutoSwitch = (station, index) => {
    setCurrentStationIndex(index)
    setSelectedStation(station)
    // Auto open sidebar when auto switching
    setIsSidebarOpen(true)
  }

  const handleStationChange = (station, index) => {
    setCurrentStationIndex(index)
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
      
      {/* Auto Switch Toggle - positioned above Floating Legend */}
      <div className="absolute bottom-24 right-2 sm:bottom-28 sm:right-4 z-10">
        <AutoSwitchToggle
          tickerData={tickerData}
          onStationChange={handleStationChange}
          currentStationIndex={currentStationIndex}
        />
      </div>
      
      {/* Floating Legend */}
      <FloatingLegend />
      
      {/* Station Detail Modal */}
      <StationDetail 
        selectedStation={selectedStation}
        onClose={handleCloseStationDetail}
        tickerData={tickerData}
      />

      
      {/* Main content - hidden in full screen mode */}
      <main className="hidden">
        {children || <Dashboard />}
      </main>
    </div>
  )
}

export default Layout
