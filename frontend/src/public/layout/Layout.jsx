import React, { useState } from 'react'
import Dashboard from '../pages/Dashboard'
import GoogleMapsSearchbar from '../components/GoogleMapsSearchbar'
import MapboxMap from '../components/MapboxMap'
import FloatingLegend from '../components/FloatingLegend'
import FloodInfoDetail from '../components/FloodInfoDetail'
import StationDetail from '../components/StationDetail'

const Layout = ({ children }) => {
  const [tickerData, setTickerData] = useState(null)
  const [searchQuery, setSearchQuery] = useState('')
  const [selectedStation, setSelectedStation] = useState(null)

  const handleSearch = (query) => {
    setSearchQuery(query)
    // Implementasi pencarian bisa ditambahkan di sini
    console.log('Searching for:', query)
  }

  const handleStationSelect = (station) => {
    setSelectedStation(station)
  }

  const handleCloseStationDetail = () => {
    setSelectedStation(null)
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
      />
      
      {/* Flood Ticker Categories */}
      <FloodInfoDetail 
        onDataUpdate={setTickerData}
        onStationSelect={handleStationSelect}
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
