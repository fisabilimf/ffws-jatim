import React, { useState } from 'react'
import Dashboard from '../pages/Dashboard'
import GoogleMapsSearchbar from '../components/GoogleMapsSearchbar'
import MapboxMap from '../components/MapboxMap'
import FloatingLegend from '../components/FloatingLegend'
import FloodInfoDetail from '../components/FloodInfoDetail'

const Layout = ({ children }) => {
  const [tickerData, setTickerData] = useState(null)
  const [searchQuery, setSearchQuery] = useState('')

  const handleSearch = (query) => {
    setSearchQuery(query)
    // Implementasi pencarian bisa ditambahkan di sini
    console.log('Searching for:', query)
  }

  return (
    <div className="h-screen bg-gray-50 relative overflow-hidden">
      {/* Full Screen Map */}
      {tickerData && (
        <div className="w-full h-full">
          <MapboxMap tickerData={tickerData} />
        </div>
      )}
      
      {/* Google Maps Style Searchbar */}
      <GoogleMapsSearchbar 
        onSearch={handleSearch}
        placeholder="Cari stasiun monitoring banjir..."
      />
      
      {/* Flood Ticker Categories */}
      <FloodInfoDetail onDataUpdate={setTickerData} />
      
      {/* Floating Legend */}
      <FloatingLegend />
      
      {/* Main content - hidden in full screen mode */}
      <main className="hidden">
        {children || <Dashboard />}
      </main>
    </div>
  )
}

export default Layout
