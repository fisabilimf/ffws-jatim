import React, { useState, useRef } from 'react'
import Dashboard from '../pages/Dashboard'
import GoogleMapsSearchbar from '../components/GoogleMapsSearchbar'
import MapboxMap from '../components/MapboxMap'
import FloatingLegend from '../components/FloatingLegend'
import FloodInfoDetail from '../components/FloodInfoDetail'
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
    if (mapRef.current && mapRef.current.moveToStation) {
      mapRef.current.moveToStation(station, index)
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
      
      {/* Google Maps Style Searchbar with Auto Switch Toggle */}
      <div className={`absolute top-4 left-4 right-4 z-10 transition-all duration-300 ease-in-out ${
        isSidebarOpen ? 'transform translate-x-80' : 'transform translate-x-0'
      }`}>
        <div className="max-w-2xl mx-auto">
          <div className="flex items-center space-x-4">
            {/* Search Bar */}
            <div className="flex-1">
              <GoogleMapsSearchbar 
                onSearch={handleSearch}
                placeholder="Cari stasiun monitoring banjir..."
              />
            </div>
            
            {/* Auto Switch Toggle */}
            <div className="flex-shrink-0">
              <AutoSwitchToggle
                tickerData={tickerData}
                onStationChange={handleStationChange}
                currentStationIndex={currentStationIndex}
              />
            </div>
          </div>
        </div>
      </div>
      
      {/* Flood Ticker Categories */}
      <FloodInfoDetail 
        onDataUpdate={setTickerData}
        onStationSelect={handleStationSelect}
        isSidebarOpen={isSidebarOpen}
      />
      
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
