import React, { useState } from 'react'
import Dashboard from '../pages/Dashboard'
import GoogleMapsSearchbar from '../components/GoogleMapsSearchbar'
import MapboxMap from '../components/MapboxMap'
import FloatingLegend from '../components/FloatingLegend'
import FloodRunningBar from '../components/FloodRunningBar'
import StationDetail from '../components/StationDetail'

const Layout = ({ children }) => {
  const [tickerData, setTickerData] = useState(null)
  const [searchQuery, setSearchQuery] = useState('')
  const [selectedStation, setSelectedStation] = useState(null)
  const [isSidebarOpen, setIsSidebarOpen] = useState(false)

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

  return (
    <div className="h-screen bg-gray-50 relative overflow-hidden">
      {/* Full Screen Map */}
      <div className="w-full h-full relative z-0">
        <MapboxMap 
          tickerData={tickerData} 
          onStationSelect={handleStationSelect}
        />
      </div>
      
      {/* Google Maps Style Searchbar */}
      <GoogleMapsSearchbar 
        onSearch={handleSearch}
        placeholder="Cari stasiun monitoring banjir..."
        isSidebarOpen={isSidebarOpen}
      />
      
      {/* Flood Running Bar */}
      <FloodRunningBar 
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
